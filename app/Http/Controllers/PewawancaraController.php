<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalWawancara;
use App\Models\WawancaraPenilaian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
             if($user->role === 'admin' && $jadwal->status === 'completed'){
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
        $jadwal = JadwalWawancara::findOrFail($id);
        $user = Auth::user();

        // --- VALIDASI AKSES (Sama seperti show) ---
        if ($user->role !== 'admin') {
            $pewawancara = $user->penanggungJawab;
            if (!$pewawancara || $jadwal->penanggung_jawab_id != $pewawancara->id) {
                abort(403, 'Akses Ditolak.');
            }
        }
        // ------------------------------------------

        $request->validate([
            'skor_kompetensi'  => 'required|integer|min:0|max:100',
            'skor_sikap'       => 'required|integer|min:0|max:100',
            'skor_pengetahuan' => 'required|integer|min:0|max:100',
            'keputusan'        => 'required|in:lulus,tidak_lulus',
            'catatan'          => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            WawancaraPenilaian::create([
                'jadwal_wawancara_id' => $jadwal->id,
                'skor_kompetensi'     => $request->skor_kompetensi,
                'skor_sikap'          => $request->skor_sikap,
                'skor_pengetahuan'    => $request->skor_pengetahuan,
                'catatan_pewawancara' => $request->catatan,
                'keputusan'           => $request->keputusan
            ]);

            $jadwal->update(['status' => 'completed']);

            if ($request->keputusan == 'lulus') {
                $jadwal->pengajuan->update(['status' => 'completed']);

                if ($jadwal->pengajuan->lisensiLama) {
                    $jadwal->pengajuan->lisensiLama->update([
                        'tgl_terbit'  => now(),
                        'tgl_expired' => now()->addYears(3)
                    ]);
                }
            } else {
                $jadwal->pengajuan->update(['status' => 'rejected']);
            }

            DB::commit();

            // Redirect sesuai role
            if ($user->role === 'admin') {
                return redirect()->route('admin.pengajuan.index')->with('success', 'Penilaian berhasil disimpan oleh Admin.');
            } else {
                return redirect()->route('pewawancara.antrian')->with('success', 'Penilaian berhasil disimpan.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
