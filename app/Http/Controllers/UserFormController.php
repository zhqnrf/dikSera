<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Support\Facades\DB;
use App\Models\UserAnswer;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserFormController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $now = Carbon::now();

        $forms = Form::where('status', 'publish')
            ->where(function ($query) use ($user) {
                $query->where('target_peserta', 'semua')
                    ->orWhereHas('participants', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            })
            ->with(['examResults' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->orderBy('waktu_mulai', 'desc')
            ->get();

        return view('perawat.ujian_aktif.index', compact('forms', 'now'));
    }

    public function show(Form $form)
    {
        if ($form->target_peserta == 'khusus') {
            if (!$form->participants->contains(auth()->user()->id)) {
                abort(403, 'Anda tidak terdaftar untuk ujian ini.');
            }
        }

        return view('perawat.ujian_aktif.show', compact('form'));
    }

    public function kerjakan(Form $form)
    {
        // Validasi akses peserta khusus
        if ($form->target_peserta == 'khusus') {
            if (!$form->participants->contains(auth()->user()->id)) {
                abort(403, 'Anda tidak terdaftar.');
            }
        }

        // Cek apakah sudah mengerjakan (Agar tidak bisa masuk lagi)
        $alreadySubmitted = ExamResult::where('user_id', auth()->id())
            ->where('form_id', $form->id)
            ->exists();

        if ($alreadySubmitted) {
            return redirect()->route('perawat.ujian.index')
                ->with('error', 'Anda sudah menyelesaikan ujian ini.');
        }

        // Validasi waktu
        $now = Carbon::now();
        if ($now->lessThan($form->waktu_mulai)) {
            return back()->with('error', 'Ujian belum dimulai.');
        }
        if ($now->greaterThan($form->waktu_selesai)) {
            return back()->with('error', 'Waktu ujian telah habis.');
        }

        // Ambil soal acak
        $questions = $form->questions()
            ->withPivot('bobot')
            ->inRandomOrder()
            ->get();

        return view('perawat.ujian_aktif.kerjakan', compact('form', 'questions'));
    }

    public function submit(Request $request, Form $form)
    {
        $now = Carbon::now();

        // Toleransi keterlambatan 5 menit
        if ($now->greaterThan($form->waktu_selesai->addMinutes(5))) {
            return redirect()->route('perawat.ujian.index')
                ->with('error', 'Waktu ujian sudah habis, jawaban ditolak.');
        }

        // Cek double submit
        $alreadySubmitted = ExamResult::where('user_id', auth()->id())
            ->where('form_id', $form->id)
            ->exists();

        if ($alreadySubmitted) {
            return redirect()->route('perawat.ujian.index')
                ->with('error', 'Anda sudah mengerjakan ujian ini sebelumnya.');
        }

        $questions = $form->questions()->withPivot('bobot')->get();
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
                if ($jawabanUser && strtolower($jawabanUser) === strtolower($question->kunci_jawaban)) {
                    $isCorrect = true;
                    $totalBenar++;
                    $totalNilaiBobot += $bobotSoal;
                } else {
                    $totalSalah++;
                }

                UserAnswer::create([
                    'user_id' => auth()->id(),
                    'form_id' => $form->id,
                    'bank_soal_id' => $question->id,
                    'jawaban_user' => $jawabanUser,
                    'is_correct' => $isCorrect,
                    'nilai_diperoleh' => $isCorrect ? $bobotSoal : 0,
                ]);
            }

            // Hitung skor akhir (0-100)
            $finalScore = 0;
            if ($maxBobot > 0) {
                $finalScore = round(($totalNilaiBobot / $maxBobot) * 100);
            }

            $result = ExamResult::create([
                'user_id' => auth()->id(),
                'form_id' => $form->id,
                'total_nilai' => $finalScore,
                'total_benar' => $totalBenar,
                'total_salah' => $totalSalah,
                'waktu_selesai' => now(),
            ]);

            DB::commit();

            return redirect()->route('perawat.ujian.selesai', [
                'form' => $form->slug,
                'result_id' => $result->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function selesai(Form $form, Request $request)
    {
        $result = ExamResult::where('id', $request->query('result_id'))
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('perawat.ujian_aktif.selesai', compact('form', 'result'));
    }
}
