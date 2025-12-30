<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\UserAnswer;
use App\Models\ExamResult;
use App\Models\PengajuanSertifikat;
use App\Models\PerawatLisensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserFormController extends Controller
{
    /**
     * Helper: Cek apakah user punya akses ke form berdasarkan KFK
     */
    private function checkKfkAccess($user, $form)
    {
        // 1. Ambil Lisensi Terakhir User
        $lastLicense = PerawatLisensi::where('user_id', $user->id)
            ->orderBy('tgl_terbit', 'desc') // Ambil yang paling baru
            ->first();

        // 2. Ambil KFK User (Decode JSON ke Array)
        // Jika lisensi tidak ada atau kfk null, set array kosong
        $userKfkList = ($lastLicense && $lastLicense->kfk) ? json_decode($lastLicense->kfk, true) : [];
        if (!is_array($userKfkList)) $userKfkList = [];

        // 3. Ambil Target KFK Form
        $formTargets = $form->kfk_target ?? [];
        if (!is_array($formTargets)) $formTargets = [];

        // 4. Cek Irisan (Intersection)
        // Jika ada minimal satu KFK user yang sama dengan target form, return true
        $intersection = array_intersect($userKfkList, $formTargets);

        return count($intersection) > 0;
    }

    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // Ambil semua form yang statusnya Publish
        // Eager load examResults untuk cek apakah user sudah mengerjakan
        $forms = Form::where('status', 'publish')
            ->with(['examResults' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->orderBy('waktu_mulai', 'desc')
            ->get()
            ->filter(function ($form) use ($user) {

                // --- FILTER LOGIC TAMPILAN ---

                // 1. Jika Target Semua -> TAMPIL
                if ($form->target_peserta == 'semua') {
                    return true;
                }

                // 2. Jika Target Khusus -> Cek di tabel pivot (manual select user)
                if ($form->target_peserta == 'khusus') {
                    return $form->participants->contains('id', $user->id);
                }

                // 3. Jika Target KFK -> Cek KFK di Lisensi Terakhir
                if ($form->target_peserta == 'kfk') {
                    return $this->checkKfkAccess($user, $form);
                }

                return false;
            });

        // (Opsional) Fitur Prioritas: Taruh form yang sesuai dengan pengajuan sertifikat di paling atas
        $pengajuanAktif = PengajuanSertifikat::where('user_id', $user->id)
            ->where('status', 'method_selected')
            ->with('lisensiLama')
            ->latest()
            ->first();

        if ($pengajuanAktif && $pengajuanAktif->lisensiLama) {
            $namaLisensi = $pengajuanAktif->lisensiLama->nama;
            // Urutkan collection: Form yang judulnya mirip lisensi ditaruh di atas
            $forms = $forms->sortByDesc(function ($form) use ($namaLisensi) {
                return stripos($form->judul, $namaLisensi) !== false;
            });
        }

        return view('perawat.ujian_aktif.index', compact('forms', 'now'));
    }

    public function show(Form $form)
    {
        $user = Auth::user();

        // Validasi Akses (Security Layer 1)
        if ($form->target_peserta == 'khusus' && !$form->participants->contains($user->id)) {
            abort(403, 'Anda tidak terdaftar untuk ujian ini.');
        }

        if ($form->target_peserta == 'kfk' && !$this->checkKfkAccess($user, $form)) {
            abort(403, 'Kompetensi KFK Anda tidak sesuai untuk ujian ini.');
        }

        return view('perawat.ujian_aktif.show', compact('form'));
    }

    public function kerjakan(Form $form)
    {
        $user = Auth::user();

        // --- VALIDASI AKSES (PENTING AGAR TIDAK TEMBUS URL) ---

        // 1. Cek Peserta Khusus
        if ($form->target_peserta == 'khusus') {
            if (!$form->participants->contains($user->id)) {
                return redirect()->route('perawat.ujian.index')->with('error', 'Akses ditolak: Anda tidak terdaftar.');
            }
        }

        // 2. Cek Peserta KFK
        if ($form->target_peserta == 'kfk') {
            if (!$this->checkKfkAccess($user, $form)) {
                return redirect()->route('perawat.ujian.index')->with('error', 'Akses ditolak: KFK tidak sesuai.');
            }
        }

        // --- VALIDASI LOGIC UJIAN ---


        // 3. Cek apakah sudah mengerjakan
        $userResult = ExamResult::where('user_id', $user->id)
            ->where('form_id', $form->id)
            ->latest('id')
            ->first();

        if ($userResult && (!$userResult->remidi && $userResult->total_nilai >= 75)) {
            // Sudah mengerjakan dan TIDAK remidi (lulus)
            return redirect()->route('perawat.ujian.index')
                ->with('error', 'Anda sudah menyelesaikan ujian ini.');
        }

        // 4. Cek Waktu
        $now = Carbon::now();
        if ($now->lessThan($form->waktu_mulai)) {
            return back()->with('error', 'Ujian belum dimulai.');
        }
        if ($now->greaterThan($form->waktu_selesai)) {
            return back()->with('error', 'Waktu ujian telah habis.');
        }

        // 5. Ambil Soal (Acak)
        $questions = $form->questions()
            ->select('bank_soals.*')
            ->withPivot('bobot')
            ->inRandomOrder()
            ->get();

        return view('perawat.ujian_aktif.kerjakan', compact('form', 'questions'));
    }

    public function submit(Request $request, Form $form)
    {
        $user = Auth::user();
        $now = Carbon::now();

        // 1. Toleransi keterlambatan 5 menit
        if ($now->greaterThan($form->waktu_selesai->addMinutes(5))) {
            return redirect()->route('perawat.ujian.index')
                ->with('error', 'Waktu ujian sudah habis, jawaban ditolak.');
        }

        // 2. Cek double submit
        $userResult = ExamResult::where('user_id', $user->id)
            ->where('form_id', $form->id)
            ->latest('id')
            ->first();

        if ($userResult && (!$userResult->remidi && $userResult->total_nilai >= 75)) {
            // Sudah mengerjakan dan TIDAK remidi (lulus)
            return redirect()->route('perawat.ujian.index')
                ->with('error', 'Anda sudah mengerjakan ujian ini sebelumnya.');
        }

        // 3. Proses Jawaban
        $questions = $form->questions()
            ->select('bank_soals.*')
            ->withPivot('bobot')
            ->get();

        $userAnswers = $request->input('answers', []);

        $totalBenar = 0;
        $totalSalah = 0;
        $totalNilaiBobot = 0;
        $maxBobot = 0;

        DB::beginTransaction();
        try {
            foreach ($questions as $question) {
                $bobotSoal = $question->pivot->bobot ?? 1;
                $maxBobot += $bobotSoal;

                $jawabanUser = $userAnswers[$question->id] ?? null;
                $isCorrect = false;

                // Cek Jawaban (Case Insensitive)
                if ($jawabanUser && $question->kunci_jawaban && strtolower($jawabanUser) === strtolower($question->kunci_jawaban)) {
                    $isCorrect = true;
                    $totalBenar++;
                    $totalNilaiBobot += $bobotSoal;
                } else {
                    $totalSalah++;
                }

                // Simpan Detail Jawaban
                UserAnswer::create([
                    'user_id' => $user->id,
                    'form_id' => $form->id,
                    'bank_soal_id' => $question->id,
                    'jawaban_user' => $jawabanUser,
                    'is_correct' => $isCorrect,
                    'nilai_diperoleh' => $isCorrect ? $bobotSoal : 0,
                ]);
            }

            // Hitung Skor Akhir (Skala 0-100)
            $finalScore = 0;
            if ($maxBobot > 0) {
                $finalScore = round(($totalNilaiBobot / $maxBobot) * 100);
            }

            // Simpan Hasil Akhir
            $result = ExamResult::create([
                'user_id' => $user->id,
                'form_id' => $form->id,
                'total_nilai' => $finalScore,
                'total_benar' => $totalBenar,
                'total_salah' => $totalSalah,
                'remidi' => $finalScore < 75 ? true : false,
                'waktu_selesai' => now(),
            ]);

            DB::commit();

            return redirect()->route('perawat.ujian.selesai', [
                'form' => $form->slug,
                'result_id' => $result->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan jawaban: ' . $e->getMessage());
        }
    }

    public function selesai(Form $form, Request $request)
    {
        // Pastikan hanya pemilik hasil yang bisa lihat
        $result = ExamResult::where('id', $request->query('result_id'))
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('perawat.ujian_aktif.selesai', compact('form', 'result'));
    }
}
