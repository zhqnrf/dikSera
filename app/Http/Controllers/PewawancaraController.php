<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalWawancara;
use App\Models\WawancaraPenilaian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PewawancaraController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pewawancara = $user->penanggungJawab;

        // Jika Admin, dia tidak punya dashboard pewawancara, redirect ke dashboard admin
        if ($user->role === 'admin') {
            return redirect()->route('dashboard.admin');
        }

        if (!$pewawancara) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda tidak terdaftar sebagai Pewawancara.');
        }

        // ... (Sisa kode index tetap sama untuk pewawancara) ...
        // Agar tidak error saat admin buka dashboard pewawancara, kita skip logic statistik di sini untuk admin
        // Karena admin punya dashboard sendiri.

        $totalAntrian = JadwalWawancara::where('penanggung_jawab_id', $pewawancara->id)->where('status', 'approved')->count();
        $hariIni = JadwalWawancara::where('penanggung_jawab_id', $pewawancara->id)->where('status', 'approved')->whereDate('waktu_wawancara', now())->count();
        $selesai = JadwalWawancara::where('penanggung_jawab_id', $pewawancara->id)->where('status', 'completed')->count();

        $jadwalKalender = JadwalWawancara::with('pengajuan.user')
            ->where('penanggung_jawab_id', $pewawancara->id)
            ->where('status', 'approved')
            ->get()
            ->map(fn($j) => [
                'title' => $j->pengajuan->user->name,
                'start' => $j->waktu_wawancara->toIso8601String(),
                'url'   => route('pewawancara.penilaian', $j->id)
            ]);

        return view('dashboard.pewawancara', compact('pewawancara', 'totalAntrian', 'hariIni', 'selesai', 'jadwalKalender'));
    }

    public function antrian()
    {
        $user = Auth::user();

        // --- LOGIKA BARU: Admin Boleh Masuk ---
        if ($user->role === 'admin') {
            // Admin melihat SEMUA antrian jadwal wawancara
            $antrian = JadwalWawancara::with(['pengajuan.user', 'pewawancara'])
                ->where('status', 'approved')
                ->orderBy('waktu_wawancara', 'asc')
                ->paginate(10);

            return view('pewawancara.antrian', compact('antrian'));
        }
        // --------------------------------------

        $pewawancara = $user->penanggungJawab;
        if (!$pewawancara) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $antrian = JadwalWawancara::with(['pengajuan.user'])
            ->where('penanggung_jawab_id', $pewawancara->id)
            ->where('status', 'approved')
            ->orderBy('waktu_wawancara', 'asc')
            ->paginate(10);

        return view('pewawancara.antrian', compact('antrian'));
    }

    // --- FORM PENILAIAN (YANG PALING PENTING) ---
    public function showPenilaian($id)
    {
        $user = Auth::user();
        $jadwal = JadwalWawancara::with(['pengajuan.user', 'pengajuan.lisensiLama'])->findOrFail($id);

        // --- VALIDASI AKSES ---
        if ($user->role !== 'admin') {
            // Jika BUKAN Admin, maka harus Pewawancara ASLI
            $pewawancara = $user->penanggungJawab;

            if (!$pewawancara) {
                return redirect()->route('dashboard')->with('error', 'Bukan Pewawancara.');
            }

            if ($jadwal->penanggung_jawab_id != $pewawancara->id) {
                abort(403, 'Jadwal ini bukan milik Anda.');
            }
        }
        // Jika Admin, lolos langsung ke bawah (Bypass)
        // ----------------------

        // Cek status jadwal (Admin tetap bisa lihat meski status completed, opsional)
        if ($jadwal->status !== 'approved' && $jadwal->status !== 'completed') {
            // Jika status completed, admin mungkin mau lihat hasilnya, tapi form penilaian biasanya untuk 'approved'
            // Kita biarkan 'approved' saja untuk penilaian aktif.
            if ($user->role === 'admin' && $jadwal->status === 'completed') {
                // Admin boleh lihat yg completed (opsional, tapi kode di bawah mengarah ke view form)
            } else {
                return redirect()->back()->with('error', 'Sesi wawancara tidak valid/sudah selesai.');
            }
        }

        return view('pewawancara.penilaian', compact('jadwal'));
    }

    // --- SIMPAN NILAI ---
    public function storePenilaian(Request $request, $id)
    {
        $jadwal = JadwalWawancara::with('pengajuan')->findOrFail($id);
        $user = Auth::user();

        // Validasi Akses
        if ($user->role !== 'admin') {
            $pewawancara = $user->penanggungJawab;
            if (!$pewawancara || $jadwal->penanggung_jawab_id != $pewawancara->id) {
                abort(403, 'Akses Ditolak.');
            }
        }

        // Tentukan jenis penilaian: Kredensialing (interview_only) atau UjiKom (pg_interview)
        $isKredensial = $jadwal->pengajuan->metode === 'interview_only';

        DB::beginTransaction();
        try {
            // Inisialisasi data default
            $data = [
                'jadwal_wawancara_id' => $jadwal->id,
                'skor_kompetensi'     => 0,
                'skor_sikap'          => 0,
                'skor_pengetahuan'    => 0,
                'catatan_pewawancara' => null,
                'detail_penilaian'    => null, // Menyimpan JSON checklist
                'file_hasil'          => null, // Menyimpan path file
                'keputusan'           => null
            ];

            if ($isKredensial) {
                // --- Logika Kredensialing ---
                $request->validate([
                    'keputusan'      => 'required|in:valid,tidak_valid',
                    'file_hasil'     => 'required|file|mimes:pdf,doc,docx|max:5120',
                    'poin_penilaian' => 'nullable|array'
                ]);

                // Upload File
                if ($request->hasFile('file_hasil')) {
                    $data['file_hasil'] = $request->file('file_hasil')->store('dokumen_kredensial', 'public');
                }

                // Mapping Data
                $data['detail_penilaian'] = json_encode($request->poin_penilaian);
                // Konversi status UI (valid/invalid) ke DB Enum (lulus/tidak_lulus)
                $data['keputusan'] = ($request->keputusan == 'valid') ? 'lulus' : 'tidak_lulus';
                $data['catatan_pewawancara'] = 'Asesmen Kredensialing (Lihat Lampiran)';
            } else {
                // --- Logika Uji Kompetensi ---
                $request->validate([
                    'keputusan' => 'required|in:lulus,tidak_lulus',
                    'catatan'   => 'nullable|string'
                ]);

                $data['catatan_pewawancara'] = $request->catatan;
                $data['keputusan'] = $request->keputusan;
            }

            // Simpan Data Penilaian
            WawancaraPenilaian::create($data);

            // Update Status Jadwal
            $jadwal->update(['status' => 'completed']);

            // Update Status Pengajuan & Lisensi
            if ($data['keputusan'] == 'lulus') {
                $jadwal->pengajuan->update(['status' => 'completed']);

                if ($jadwal->pengajuan->lisensiLama) {
                    $jadwal->pengajuan->lisensiLama->update([
                        'tgl_terbit'  => now(),
                        'tgl_expired' => now()->addYears(3)
                    ]);
                }
            } else {
                $jadwal->pengajuan->update(['status' => 'interview_failed']);
            }

            DB::commit();

            $route = $user->role === 'admin' ? 'admin.pengajuan.index' : 'pewawancara.antrian';
            return redirect()->route($route)->with('success', 'Penilaian berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Bersihkan file jika upload sukses tapi transaksi DB gagal
            if (isset($data['file_hasil']) && Storage::disk('public')->exists($data['file_hasil'])) {
                Storage::disk('public')->delete($data['file_hasil']);
            }

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function riwayat()
    {
        $user = Auth::user();

        // Admin bisa lihat semua riwayat
        if ($user->role === 'admin') {
            $riwayat = JadwalWawancara::with(['pengajuan.user', 'penilaian', 'pewawancara'])
                ->whereIn('status', ['completed', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
            // View perlu disesuaikan jika ingin admin melihat nama pewawancara,
            // tapi untuk saat ini pakai view yg sama gpp.
            // Kita perlu pass variable $pewawancara fake atau null agar view tidak error
            return view('pewawancara.riwayat', ['riwayat' => $riwayat, 'pewawancara' => null]);
        }

        $pewawancara = $user->penanggungJawab;
        if (!$pewawancara) return redirect()->route('dashboard');

        $riwayat = JadwalWawancara::with(['pengajuan.user', 'penilaian'])
            ->where('penanggung_jawab_id', $pewawancara->id)
            ->whereIn('status', ['completed', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('pewawancara.riwayat', compact('riwayat', 'pewawancara'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $jadwal = JadwalWawancara::findOrFail($id);

        // --- VALIDASI AKSES ---
        if ($user->role !== 'admin') {
            $pewawancara = $user->penanggungJawab;
            if (!$pewawancara || $jadwal->penanggung_jawab_id != $pewawancara->id) {
                abort(403, 'Akses Ditolak');
            }
        }
        // ----------------------

        DB::beginTransaction();
        try {
            WawancaraPenilaian::where('jadwal_wawancara_id', $jadwal->id)->delete();
            $jadwal->update(['status' => 'approved']);

            // Logic status pengajuan
            $jadwal->pengajuan->update(['status' => 'interview_scheduled']);

            // Khusus interview_only, jika direset, status harusnya apa?
            // interview_scheduled aman untuk keduanya.

            DB::commit();
            return back()->with('success', 'Penilaian berhasil dihapus/direset.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function settings()
    {
        $user = Auth::user();
        return view('pewawancara.settings', compact('user'));
    }
}
