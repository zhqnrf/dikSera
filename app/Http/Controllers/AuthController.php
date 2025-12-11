<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PerawatProfile;
use App\Models\PerawatPendidikan;
use App\Models\PerawatPelatihan;
use App\Models\PerawatPekerjaan;
use App\Models\PerawatTandaJasa;
use App\Models\PerawatKeluarga;
use App\Models\PerawatOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // ============================================================
            // CEK STATUS AKUN (LOGIKA TAMBAHAN)
            // ============================================================
            // Jika user BUKAN admin DAN status akunnya BUKAN 'active'
            // Pastikan kolom 'status_akun' sudah ada di tabel users (via migration)
            if ($user->role !== 'admin' && $user->status_akun !== 'active') {

                // Keluarkan user (Logout paksa agar sesi tidak tersimpan)
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Tentukan pesan error berdasarkan status
                $pesanError = 'Akun Anda sedang menunggu persetujuan Admin. Mohon tunggu verifikasi.';

                if ($user->status_akun === 'rejected') {
                    $pesanError = 'Mohon maaf, pendaftaran akun Anda telah ditolak oleh Admin.';
                }

                // Kembalikan ke halaman login dengan pesan error
                return back()
                    ->withInput()
                    ->with('swal', [
                        'icon'  => 'warning',
                        'title' => 'Akses Dibatasi',
                        'text'  => $pesanError,
                    ]);
            }
            // ============================================================

            // Jika lolos pengecekan, lanjutkan regenerasi session
            $request->session()->regenerate();

            // Redirect Admin
            if ($user->role === 'admin') {
                return redirect()
                    ->route('dashboard.admin')
                    ->with('swal', [
                        'icon'  => 'success',
                        'title' => 'Berhasil masuk',
                        'text'  => 'Selamat datang di DIKSERA, Admin ' . $user->name . '.',
                    ]);
            }

            // Redirect User Biasa (Perawat)
            return redirect()
                ->route('dashboard')
                ->with('swal', [
                    'icon'  => 'success',
                    'title' => 'Berhasil masuk',
                    'text'  => 'Selamat datang di DIKSERA, ' . $user->name . '.',
                ]);
        }

        // Jika password/email salah
        return back()
            ->withErrors([
                'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
            ])
            ->withInput()
            ->with('swal', [
                'icon'  => 'error',
                'title' => 'Gagal masuk',
                'text'  => 'Email atau password tidak cocok. Silakan cek kembali.',
            ]);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('landing')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Berhasil keluar',
                'text'  => 'Anda telah keluar dari DIKSERA.',
            ]);
    }

    /**
     * Tampilkan form registrasi perawat (stepper DRH).
     */
    public function showPerawatRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register-perawat');
    }

    /**
     * Proses registrasi perawat + simpan DRH.
     */
    public function registerPerawat(Request $request)
    {
        // VALIDASI
        $request->validate([
            // Step 1: akun + identitas utama
            'name'             => 'required|string|max:150',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|string|min:6|confirmed',

            'nik'              => 'nullable|string|max:30',
            'nip'              => 'nullable|string|max:50',
            'nirp'             => 'nullable|string|max:50',
            'tempat_lahir'     => 'nullable|string|max:100',
            'tanggal_lahir'    => 'nullable|date',
            'jenis_kelamin'    => 'nullable|string|max:20',
            'agama'            => 'nullable|string|max:30',
            'aliran_kepercayaan' => 'nullable|string|max:100',
            'status_perkawinan' => 'nullable|string|max:50',
            'jabatan'          => 'nullable|string|max:100',
            'pangkat'          => 'nullable|string|max:50',
            'golongan'         => 'nullable|string|max:20',
            'hobby'            => 'nullable|string|max:150',

            // Step 2: alamat + badan + foto
            'no_hp'            => 'required|string|max:30',
            'alamat_jalan'     => 'required|string|max:150',
            'alamat_kelurahan' => 'required|string|max:100',
            'alamat_kecamatan' => 'required|string|max:100',
            'alamat_kabkota'   => 'required|string|max:100',
            'alamat_provinsi'  => 'required|string|max:100',

            'golongan_darah'   => 'nullable|string|max:5',
            'tinggi_badan'     => 'nullable|integer',
            'berat_badan'      => 'nullable|integer',
            'rambut'           => 'nullable|string|max:100',
            'bentuk_muka'      => 'nullable|string|max:100',
            'warna_kulit'      => 'nullable|string|max:100',
            'ciri_khas'        => 'nullable|string|max:150',
            'cacat_tubuh'      => 'nullable|string|max:150',
            'foto_3x4'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            // Arrays Pendidikan
            'pendidikan_jenjang.*'     => 'nullable|string|max:50',
            'pendidikan_nama.*'        => 'nullable|string|max:150',
            'pendidikan_akreditasi.*'  => 'nullable|string|max:20',
            'pendidikan_tempat.*'      => 'nullable|string|max:100',
            'pendidikan_file.*'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',

            // Arrays Pelatihan / Kursus
            'pelatihan_nama.*'          => 'nullable|string|max:150',
            'pelatihan_durasi.*'        => 'nullable|string|max:50',
            'pelatihan_mulai.*'         => 'nullable|date',
            'pelatihan_selesai.*'       => 'nullable|date',
            'pelatihan_file.*'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',

            // Arrays Pekerjaan
            'pekerjaan_instansi.*' => 'nullable|string|max:150',
            'pekerjaan_jabatan.*'  => 'nullable|string|max:150',
            'pekerjaan_mulai.*'    => 'nullable|date',
            'pekerjaan_selesai.*'  => 'nullable|date',
            'pekerjaan_file.*'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',

            // Arrays Keluarga
            'keluarga_hubungan.*'  => 'nullable|string|max:50',
            'keluarga_nama.*'      => 'nullable|string|max:150',
            'keluarga_ttl.*'       => 'nullable|string|max:150',
            'keluarga_pekerjaan.*' => 'nullable|string|max:150',

            // Arrays Organisasi
            'organisasi_nama.*'    => 'nullable|string|max:150',
            'organisasi_jabatan.*' => 'nullable|string|max:150',
            'organisasi_mulai.*'   => 'nullable|date',
            'organisasi_selesai.*' => 'nullable|date',
            'organisasi_file.*'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',

            // Arrays Tanda Jasa
            'tandajasa_nama.*'     => 'nullable|string|max:150',
            'tandajasa_instansi.*' => 'nullable|string|max:150',
            'tandajasa_tahun.*'    => 'nullable|string|max:4',
            'tandajasa_file.*'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        DB::beginTransaction();

        try {
            // 1. Buat user dengan role perawat
            // Pastikan di database column status_akun defaultnya 'pending'
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'perawat',
                // 'status_akun' => 'pending' // ini otomatis jika default di migration sudah diset
            ]);

            // 2. Simpan foto 3x4
            $fotoPath = null;
            if ($request->hasFile('foto_3x4')) {
                $fotoPath = $request->file('foto_3x4')->store('perawat/foto', 'public');
            }

            // 3. Gabungkan alamat jadi satu string
            $alamatGabungan = trim(
                ($request->alamat_jalan ?? '') . ', Kel. ' . ($request->alamat_kelurahan ?? '') .
                    ', Kec. ' . ($request->alamat_kecamatan ?? '') . ', ' . ($request->alamat_kabkota ?? '') .
                    ', ' . ($request->alamat_provinsi ?? '')
            );

            // 4. Profile perawat
            PerawatProfile::create([
                'user_id'            => $user->id,
                'nik'                => $request->nik,
                'nip'                => $request->nip,
                'nirp'               => $request->nirp,
                'nama_lengkap'       => $request->name,
                'tempat_lahir'       => $request->tempat_lahir,
                'tanggal_lahir'      => $request->tanggal_lahir,
                'jenis_kelamin'      => $request->jenis_kelamin,
                'agama'              => $request->agama,
                'aliran_kepercayaan' => $request->aliran_kepercayaan,
                'status_perkawinan'  => $request->status_perkawinan,

                'jabatan'            => $request->jabatan,
                'pangkat'            => $request->pangkat,
                'golongan'           => $request->golongan,

                'alamat'             => $alamatGabungan ?: null,
                'kota'               => $request->alamat_kabkota,
                'no_hp'              => $request->no_hp,

                'golongan_darah'     => $request->golongan_darah,
                'tinggi_badan'       => $request->tinggi_badan,
                'berat_badan'        => $request->berat_badan,
                'rambut'             => $request->rambut,
                'bentuk_muka'        => $request->bentuk_muka,
                'warna_kulit'        => $request->warna_kulit,
                'ciri_khas'          => $request->ciri_khas,
                'cacat_tubuh'        => $request->cacat_tubuh,
                'hobby'              => $request->hobby,

                'foto_3x4'           => $fotoPath,
            ]);

            // ==== HANDLE FILE MULTI ====
            $pendFiles  = $request->file('pendidikan_file', []);
            $pelatFiles = $request->file('pelatihan_file', []);
            $pekFiles   = $request->file('pekerjaan_file', []);
            $orgFiles   = $request->file('organisasi_file', []);
            $tjFiles    = $request->file('tandajasa_file', []);

            /**
             * Pendidikan
             */
            $jenjangs    = $request->pendidikan_jenjang ?? [];
            $p_nama      = $request->pendidikan_nama ?? [];
            $p_akre      = $request->pendidikan_akreditasi ?? [];
            $p_tempat    = $request->pendidikan_tempat ?? [];

            foreach ($jenjangs as $i => $jenjang) {
                $nama    = $p_nama[$i]   ?? null;
                $akre    = $p_akre[$i]   ?? null;
                $tempat  = $p_tempat[$i] ?? null;

                $filePath = null;
                if (is_array($pendFiles) && array_key_exists($i, $pendFiles) && $pendFiles[$i]) {
                    $filePath = $pendFiles[$i]->store('perawat/pendidikan', 'public');
                }

                if (!$jenjang && !$nama && !$akre && !$tempat && !$filePath) {
                    continue;
                }

                PerawatPendidikan::create([
                    'user_id'          => $user->id,
                    'jenjang'          => $jenjang,
                    'nama_institusi'   => $nama,
                    'akreditasi'       => $akre,
                    'tempat'           => $tempat,
                    'dokumen_path'     => $filePath,
                ]);
            }

            /**
             * Pelatihan / Kursus
             */
            $pel_nama   = $request->pelatihan_nama ?? [];
            $pel_durasi = $request->pelatihan_durasi ?? [];
            $pel_mulai  = $request->pelatihan_mulai ?? [];
            $pel_selesai = $request->pelatihan_selesai ?? [];

            foreach ($pel_nama as $i => $namaPel) {
                $dur   = $pel_durasi[$i]  ?? null;
                $mulai = $pel_mulai[$i]   ?? null;
                $sel   = $pel_selesai[$i] ?? null;

                $filePath = null;
                if (is_array($pelatFiles) && array_key_exists($i, $pelatFiles) && $pelatFiles[$i]) {
                    $filePath = $pelatFiles[$i]->store('perawat/pelatihan', 'public');
                }

                if (!$namaPel && !$dur && !$mulai && !$sel && !$filePath) {
                    continue;
                }

                PerawatPelatihan::create([
                    'user_id'        => $user->id,
                    'nama_pelatihan' => $namaPel,
                    'durasi'         => $dur,
                    'tanggal_mulai'  => $mulai,
                    'tanggal_selesai' => $sel,
                    'dokumen_path'   => $filePath,
                ]);
            }

            /**
             * Riwayat Pekerjaan
             */
            $instansi = $request->pekerjaan_instansi ?? [];
            $jabatan  = $request->pekerjaan_jabatan ?? [];
            $mulai    = $request->pekerjaan_mulai ?? [];
            $selesai  = $request->pekerjaan_selesai ?? [];

            foreach ($instansi as $i => $ins) {
                $jab = $jabatan[$i] ?? null;
                $m   = $mulai[$i]   ?? null;
                $s   = $selesai[$i] ?? null;

                $filePath = null;
                if (is_array($pekFiles) && array_key_exists($i, $pekFiles) && $pekFiles[$i]) {
                    $filePath = $pekFiles[$i]->store('perawat/pekerjaan', 'public');
                }

                if (!$ins && !$jab && !$m && !$s && !$filePath) {
                    continue;
                }

                PerawatPekerjaan::create([
                    'user_id'       => $user->id,
                    'nama_instansi' => $ins,
                    'jabatan'       => $jab,
                    'tanggal_mulai' => $m,
                    'tanggal_selesai' => $s,
                    'dokumen_path'  => $filePath,
                ]);
            }

            /**
             * Riwayat Keluarga
             */
            $kHub  = $request->keluarga_hubungan ?? [];
            $kNama = $request->keluarga_nama ?? [];
            $kTTL  = $request->keluarga_ttl ?? [];
            $kPek  = $request->keluarga_pekerjaan ?? [];

            foreach ($kNama as $i => $namaKel) {
                $hub = $kHub[$i] ?? null;
                $ttl = $kTTL[$i] ?? null;
                $pek = $kPek[$i] ?? null;

                if (!$hub && !$namaKel && !$ttl && !$pek) {
                    continue;
                }

                PerawatKeluarga::create([
                    'user_id'  => $user->id,
                    'hubungan' => $hub,
                    'nama'     => $namaKel,
                    'ttl'      => $ttl,
                    'pekerjaan' => $pek,
                ]);
            }

            /**
             * Organisasi
             */
            $oNama    = $request->organisasi_nama ?? [];
            $oJab     = $request->organisasi_jabatan ?? [];
            $oMulai   = $request->organisasi_mulai ?? [];
            $oSelesai = $request->organisasi_selesai ?? [];

            foreach ($oNama as $i => $namaOrg) {
                $jab = $oJab[$i]     ?? null;
                $m   = $oMulai[$i]   ?? null;
                $s   = $oSelesai[$i] ?? null;

                $filePath = null;
                if (is_array($orgFiles) && array_key_exists($i, $orgFiles) && $orgFiles[$i]) {
                    $filePath = $orgFiles[$i]->store('perawat/organisasi', 'public');
                }

                if (!$namaOrg && !$jab && !$m && !$s && !$filePath) {
                    continue;
                }

                PerawatOrganisasi::create([
                    'user_id'         => $user->id,
                    'nama_organisasi' => $namaOrg,
                    'jabatan'         => $jab,
                    'tanggal_mulai'   => $m,
                    'tanggal_selesai' => $s,
                    'dokumen_path'    => $filePath,
                ]);
            }

            /**
             * Tanda Jasa
             */
            $tNama = $request->tandajasa_nama ?? [];
            $tIns  = $request->tandajasa_instansi ?? [];
            $tThn  = $request->tandajasa_tahun ?? [];

            foreach ($tNama as $i => $namaJasa) {
                $ins  = $tIns[$i]  ?? null;
                $thn  = $tThn[$i]  ?? null;

                $filePath = null;
                if (is_array($tjFiles) && array_key_exists($i, $tjFiles) && $tjFiles[$i]) {
                    $filePath = $tjFiles[$i]->store('perawat/tandajasa', 'public');
                }

                if (!$namaJasa && !$ins && !$thn && !$filePath) {
                    continue;
                }

                PerawatTandaJasa::create([
                    'user_id'          => $user->id,
                    'nama_penghargaan' => $namaJasa,
                    'instansi_pemberi' => $ins,
                    'tahun'            => $thn,
                    'dokumen_path'     => $filePath,
                ]);
            }

            DB::commit();

            // PENTING: Jangan Auth::login($user) di sini!
            // Agar user harus login manual dan melewati pengecekan status di fungsi login().

            return redirect()
                ->route('login') // Arahkan ke login, bukan dashboard
                ->with('swal', [
                    'icon'  => 'success',
                    'title' => 'Registrasi Berhasil',
                    'text'  => 'Data Anda telah tersimpan. Mohon tunggu verifikasi Admin sebelum bisa masuk.',
                ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            if (config('app.debug')) {
                return back()
                    ->withInput()
                    ->with('swal', [
                        'icon'  => 'error',
                        'title' => 'Terjadi kesalahan sistem',
                        'text'  => $th->getMessage(),
                    ]);
            }

            return back()
                ->withInput()
                ->withErrors([
                    'system' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi admin.',
                ])
                ->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Terjadi kesalahan',
                    'text'  => 'Silakan cek kembali isian Anda atau coba beberapa saat lagi.',
                ]);
        }
    }
}
