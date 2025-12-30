<?php

namespace App\Http\Controllers;

use App\Models\BankSoal;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\User;
use App\Models\PenanggungJawabUjian;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FormController extends Controller
{
    // --- LIST KFK SESUAI PERMINTAAN ---
    private function getKfkOptions()
    {
        $levels_bk = ['Pra BK', 'BK 1', 'BK 1.5', 'BK 2', 'BK 2.5', 'BK 3', 'BK 3.5', 'BK 4', 'BK 4.5', 'BK 5'];
        $levels_pk_all = ['Pra PK', 'PK 1', 'PK 1.5', 'PK 2', 'PK 2.5', 'PK 3', 'PK 3.5', 'PK 4', 'PK 4.5', 'PK 5'];
        $levels_pk_special = ['PK 2', 'PK 2.5', 'PK 3', 'PK 3.5', 'PK 4', 'PK 4.5', 'PK 5'];

        $kfk = [];

        // 1. Bidan
        foreach ($levels_bk as $lvl) $kfk['Bidan'][] = "Bidan $lvl";

        // 2. Perawat Umum
        foreach ($levels_pk_all as $lvl) $kfk['Perawat Umum'][] = "Perawat $lvl";

        // 3. Keperawatan Kritis
        $kritis_subs = ['ICU', 'ICVCU', 'Gawat Darurat', 'Anestesi'];
        foreach ($kritis_subs as $sub) {
            foreach ($levels_pk_special as $lvl) {
                $kfk["Keperawatan Kritis ($sub)"][] = "Keperawatan Kritis $sub $lvl";
            }
        }

        // 4. Keperawatan Anak
        $anak_subs = ['PICU', 'NICU', 'Neonatus', 'Pediatri'];
        foreach ($anak_subs as $sub) {
            foreach ($levels_pk_special as $lvl) {
                $kfk["Keperawatan Anak ($sub)"][] = "Keperawatan Anak $sub $lvl";
            }
        }

        // 5. KMB
        $kmb_subs = ['Interna', 'Bedah', 'Kamar Operasi', 'Isolasi'];
        foreach ($kmb_subs as $sub) {
            foreach ($levels_pk_special as $lvl) {
                $kfk["KMB ($sub)"][] = "Keperawatan Medikal Bedah $sub $lvl";
            }
        }

        return $kfk;
    }

    public function index()
    {
        $forms = Form::with('penanggungJawab')->latest()->get();
        return view('admin.form.index', compact('forms'));
    }

    public function create()
    {
        $pjs = PenanggungJawabUjian::all();
        $users = User::where('role', 'perawat')->get();
        $kfkOptions = $this->getKfkOptions(); // Load KFK

        return view('admin.form.create', compact('users', 'pjs', 'kfkOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'target_peserta' => 'required|in:semua,khusus,kfk', // Tambah 'kfk'
            
            // Validasi jika pilih khusus
            'participants' => 'required_if:target_peserta,khusus|array',
            
            // Validasi jika pilih kfk
            'kfk_target' => 'required_if:target_peserta,kfk|array',
        ]);

        $form = Form::create([
            'judul' => $request->judul,
            'slug' => Str::slug($request->judul) . '-' . Str::random(5),
            'deskripsi' => $request->deskripsi,
            'penanggung_jawab_id' => $request->penanggung_jawab_id,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'target_peserta' => $request->target_peserta,
            'kfk_target' => $request->target_peserta == 'kfk' ? $request->kfk_target : null, // Simpan KFK
            'status' => 'draft',
        ]);

        if ($request->target_peserta == 'khusus' && $request->has('participants')) {
            $form->participants()->attach($request->participants);
        }

        return redirect()->route('admin.form.index')->with('success', 'Form berhasil dibuat!');
    }

    public function edit(Form $form)
    {
        $pjs = PenanggungJawabUjian::all();
        $users = User::where('role', 'perawat')->get();
        $selectedParticipants = $form->participants->pluck('id')->toArray();
        $kfkOptions = $this->getKfkOptions(); // Load KFK
        
        // Ambil KFK yang tersimpan (jika ada)
        $selectedKfk = $form->kfk_target ?? [];

        return view('admin.form.edit', compact('form', 'users', 'selectedParticipants', 'pjs', 'kfkOptions', 'selectedKfk'));
    }

    public function update(Request $request, Form $form)
    {
        $request->validate([
            'target_peserta' => 'required|in:semua,khusus,kfk',
            'participants' => 'required_if:target_peserta,khusus|array',
            'kfk_target' => 'required_if:target_peserta,kfk|array',
        ]);

        $form->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'penanggung_jawab_id' => $request->penanggung_jawab_id,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'target_peserta' => $request->target_peserta,
            'kfk_target' => $request->target_peserta == 'kfk' ? $request->kfk_target : null,
        ]);

        // Sync Peserta Khusus
        if ($request->target_peserta == 'khusus') {
            $form->participants()->sync($request->participants ?? []);
        } else {
            $form->participants()->detach();
        }

        return redirect()->route('admin.form.index')->with('success', 'Form berhasil diperbarui!');
    }

    public function destroy(Form $form)
    {
        $form->delete();
        return back()->with('success', 'Form berhasil dihapus!');
    }

    // --- FITUR SOAL ---

    public function kelolaSoal(Form $form)
    {
        $allSoals = BankSoal::latest()->get();
        $existingSoalIds = $form->questions()->allRelatedIds()->toArray();

        return view('admin.form.kelola_soal', compact('form', 'allSoals', 'existingSoalIds'));
    }

    public function simpanSoal(Request $request, Form $form)
    {
        $request->validate([
            'soal_ids' => 'array',
            'soal_ids.*' => 'exists:bank_soals,id',
        ]);

        $form->questions()->sync($request->soal_ids ?? []);

        return redirect()->route('admin.form.index')->with('success', 'Soal berhasil diatur!');
    }

    public function generateSoal(Request $request, Form $form)
    {
        $request->validate([
            'jumlah_soal' => 'required|integer|min:1',
            'kategori'    => 'nullable|string',
        ]);

        $form->questions()->detach();

        $query = BankSoal::query();

        if ($request->filled('kategori') && $request->kategori != 'Semua') {
            $query->where('kategori', $request->kategori);
        }

        // Ambil ID secara acak
        $randomSoalIds = $query->inRandomOrder()
            ->take((int) $request->jumlah_soal)
            ->pluck('id')
            ->toArray();

        if (empty($randomSoalIds)) {
            return back()->with('error', 'Stok soal tidak cukup untuk kategori ini.');
        }

        // Siapkan Data Insert
        $dataInsert = [];
        $now = Carbon::now();

        foreach ($randomSoalIds as $soalId) {
            $dataInsert[] = [
                'form_id'      => $form->id,
                'bank_soal_id' => $soalId,
                'bobot'        => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        // Insert ke database
        DB::table('form_questions')->insert($dataInsert);

        return back()->with('success', 'Berhasil mereset dan mengambil ' . count($dataInsert) . ' soal acak.');
    }

    public function hasil(Form $form)
    {
        $results = $form->examResults()
            ->with('user')
            ->orderByDesc('total_nilai')
            ->get();

        return view('admin.form.hasil', compact('form', 'results'));
    }

    public function resetHasil($id)
    {
        $result = ExamResult::findOrFail($id);
        $result->delete();
        return back()->with('success', 'Data ujian peserta berhasil direset.');
    }
}
