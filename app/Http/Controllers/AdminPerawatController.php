<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\PerawatProfile;
use App\Models\PerawatPendidikan;
use App\Models\PerawatPelatihan;
use App\Models\PerawatPekerjaan;
use App\Models\PerawatKeluarga;
use App\Models\PerawatOrganisasi;
use App\Models\PerawatTandaJasa;
use App\Models\PerawatSip;
use App\Models\PerawatStr;
use App\Models\PerawatDataTambahan;
use App\Models\PerawatLisensi;

class AdminPerawatController extends Controller
{
    // LIST SEMUA PERAWAT
    public function index(Request $request)
    {
        $search = $request->input('search');

        $perawat = User::where('role', 'perawat')
            ->with('profile')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhereHas('profile', function ($q) use ($search) {
                        $q->where('nik', 'like', "%$search%")
                            ->orWhere('no_hp', 'like', "%$search%")
                            ->orWhere('alamat', 'like', "%$search%");
                    });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.perawat.index', compact('perawat', 'search'));
    }

    // DETAIL DRH PER PERAWAT
    public function show($id)
    {
        $user = User::where('role', 'perawat')->findOrFail($id);

        $profile   = PerawatProfile::where('user_id', $user->id)->first();
        $pendidikan = PerawatPendidikan::where('user_id', $user->id)->get();
        $pelatihan  = PerawatPelatihan::where('user_id', $user->id)->get();
        $pekerjaan  = PerawatPekerjaan::where('user_id', $user->id)->get();
        $keluarga   = PerawatKeluarga::where('user_id', $user->id)->get();
        $organisasi = PerawatOrganisasi::where('user_id', $user->id)->get();
        $tandajasa  = PerawatTandaJasa::where('user_id', $user->id)->get();

        // Tambahkan data sertifikat
        $sip = PerawatSip::where('user_id', $user->id)->get();
        $str = PerawatStr::where('user_id', $user->id)->get();
        $dataTambahan = PerawatDataTambahan::where('user_id', $user->id)->get();
        $lisensi = PerawatLisensi::where('user_id', $user->id)->get();

        return view('admin.perawat.show', compact(
            'user',
            'profile',
            'pendidikan',
            'pelatihan',
            'pekerjaan',
            'keluarga',
            'organisasi',
            'tandajasa',
            'sip',
            'str',
            'dataTambahan',
            'lisensi'
        ));
    }

    public function edit($id)
    {
        $user = User::where('role', 'perawat')->findOrFail($id);

        // Gunakan firstOrCreate untuk menghindari error jika profil belum dibuat user
        $profile = PerawatProfile::firstOrCreate(['user_id' => $user->id]);

        // AMBIL DATA RELASI
        $pendidikan = PerawatPendidikan::where('user_id', $id)->orderBy('tahun_lulus', 'desc')->get();
        $pelatihan  = PerawatPelatihan::where('user_id', $id)->orderBy('tanggal_mulai', 'desc')->get();
        $pekerjaan  = PerawatPekerjaan::where('user_id', $id)->orderBy('tahun_mulai', 'desc')->get();
        $keluarga   = PerawatKeluarga::where('user_id', $id)->get();
        $organisasi = PerawatOrganisasi::where('user_id', $id)->orderBy('tahun_mulai', 'desc')->get();
        $tandajasa  = PerawatTandaJasa::where('user_id', $id)->orderBy('tahun', 'desc')->get();

        // Tambahkan data sertifikat
        $sip = PerawatSip::where('user_id', $id)->orderBy('tgl_expired', 'desc')->get();
        $str = PerawatStr::where('user_id', $id)->orderBy('tgl_expired', 'desc')->get();
        $dataTambahan = PerawatDataTambahan::where('user_id', $id)->orderBy('tgl_expired', 'desc')->get();
        $lisensi = PerawatLisensi::where('user_id', $id)->orderBy('tgl_expired', 'desc')->get();

        return view('admin.perawat.edit', compact(
            'user',
            'profile',
            'pendidikan',
            'pelatihan',
            'pekerjaan',
            'keluarga',
            'organisasi',
            'tandajasa',
            'sip',
            'str',
            'dataTambahan',
            'lisensi'
        ));
    }

    // UPDATE DATA PERAWAT
    public function update(Request $request, $id)
    {
        $user = User::where('role', 'perawat')->findOrFail($id);

        // 1. Validasi Input
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'foto_3x4' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nik'      => 'nullable|numeric',
        ]);

        // 2. Update Data Akun Utama (User)
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        // 3. Update Data Profil Lengkap
        $profile = PerawatProfile::firstOrCreate(['user_id' => $user->id]);

        if ($request->hasFile('foto_3x4')) {
            if ($profile->foto_3x4 && Storage::disk('public')->exists($profile->foto_3x4)) {
                Storage::disk('public')->delete($profile->foto_3x4);
            }
            $profile->foto_3x4 = $request->file('foto_3x4')->store('perawat_foto', 'public');
        }

        $profile->nik            = $request->nik;
        $profile->no_hp          = $request->no_hp;
        $profile->alamat         = $request->alamat;
        $profile->tempat_lahir   = $request->tempat_lahir;
        $profile->tanggal_lahir  = $request->tanggal_lahir;
        $profile->jenis_kelamin  = $request->jenis_kelamin;
        $profile->agama          = $request->agama;
        $profile->tinggi_badan   = $request->tinggi_badan;
        $profile->berat_badan    = $request->berat_badan;
        $profile->golongan_darah = $request->golongan_darah;
        $profile->save();

        // 4-7. Update Riwayat (sama seperti sebelumnya)
        PerawatPendidikan::where('user_id', $user->id)->delete();
        if ($request->has('pendidikan')) {
            foreach ($request->pendidikan as $row) {
                if (!empty($row['jenjang']) || !empty($row['nama_institusi']) || !empty($row['tahun_lulus'])) {
                    PerawatPendidikan::create([
                        'user_id' => $user->id,
                        'jenjang' => $row['jenjang'] ?? '',
                        'nama_institusi' => $row['nama_institusi'] ?? '',
                        'tahun_lulus' => $row['tahun_lulus'] ?? '',
                    ]);
                }
            }
        }

        PerawatPelatihan::where('user_id', $user->id)->delete();
        if ($request->has('pelatihan')) {
            foreach ($request->pelatihan as $row) {
                if (!empty($row['nama_pelatihan']) || !empty($row['penyelenggara']) || !empty($row['tanggal_mulai'])) {
                    PerawatPelatihan::create([
                        'user_id' => $user->id,
                        'nama_pelatihan' => $row['nama_pelatihan'] ?? '',
                        'penyelenggara' => $row['penyelenggara'] ?? '',
                        'tanggal_mulai' => $row['tanggal_mulai'] ?? '',
                    ]);
                }
            }
        }

        PerawatPekerjaan::where('user_id', $user->id)->delete();
        if ($request->has('pekerjaan')) {
            foreach ($request->pekerjaan as $row) {
                if (!empty($row['nama_instansi']) || !empty($row['jabatan']) || !empty($row['tahun_mulai']) || !empty($row['tahun_selesai'])) {
                    PerawatPekerjaan::create([
                        'user_id' => $user->id,
                        'nama_instansi' => $row['nama_instansi'] ?? '',
                        'jabatan' => $row['jabatan'] ?? '',
                        'tahun_mulai' => $row['tahun_mulai'] ?? '',
                        'tahun_selesai' => $row['tahun_selesai'] ?? '',
                    ]);
                }
            }
        }

        PerawatOrganisasi::where('user_id', $user->id)->delete();
        if ($request->has('organisasi')) {
            foreach ($request->organisasi as $row) {
                if (!empty($row['nama_organisasi']) || !empty($row['jabatan']) || !empty($row['tahun_mulai'])) {
                    PerawatOrganisasi::create([
                        'user_id' => $user->id,
                        'nama_organisasi' => $row['nama_organisasi'] ?? '',
                        'jabatan' => $row['jabatan'] ?? '',
                        'tahun_mulai' => $row['tahun_mulai'] ?? '',
                    ]);
                }
            }
        }

        return redirect()->route('admin.perawat.index')->with('success', 'Data perawat berhasil diperbarui.');
    }

    // HAPUS DATA PERAWAT
    public function destroy($id)
    {
        $user = User::where('role', 'perawat')->findOrFail($id);

        $profile = PerawatProfile::where('user_id', $id)->first();
        if ($profile && $profile->foto_3x4 && Storage::disk('public')->exists($profile->foto_3x4)) {
            Storage::disk('public')->delete($profile->foto_3x4);
        }

        // Hapus file sertifikat
        $sips = PerawatSip::where('user_id', $id)->get();
        foreach ($sips as $sip) {
            if ($sip->file_path && Storage::disk('public')->exists($sip->file_path)) {
                Storage::disk('public')->delete($sip->file_path);
            }
        }

        $strs = PerawatStr::where('user_id', $id)->get();
        foreach ($strs as $str) {
            if ($str->file_path && Storage::disk('public')->exists($str->file_path)) {
                Storage::disk('public')->delete($str->file_path);
            }
        }

        $lisensis = PerawatLisensi::where('user_id', $id)->get();
        foreach ($lisensis as $lis) {
            if ($lis->file_path && Storage::disk('public')->exists($lis->file_path)) {
                Storage::disk('public')->delete($lis->file_path);
            }
        }

        $dataTambahans = PerawatDataTambahan::where('user_id', $id)->get();
        foreach ($dataTambahans as $data) {
            if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
                Storage::disk('public')->delete($data->file_path);
            }
        }

        // Hapus records
        PerawatProfile::where('user_id', $id)->delete();
        PerawatPendidikan::where('user_id', $id)->delete();
        PerawatPelatihan::where('user_id', $id)->delete();
        PerawatPekerjaan::where('user_id', $id)->delete();
        PerawatKeluarga::where('user_id', $id)->delete();
        PerawatOrganisasi::where('user_id', $id)->delete();
        PerawatTandaJasa::where('user_id', $id)->delete();
        PerawatSip::where('user_id', $id)->delete();
        PerawatStr::where('user_id', $id)->delete();
        PerawatDataTambahan::where('user_id', $id)->delete();
        PerawatLisensi::where('user_id', $id)->delete();

        $user->delete();

        return redirect()->route('admin.perawat.index')->with('success', 'Data perawat berhasil dihapus.');
    }

    // TAMPILKAN HALAMAN SERTIFIKAT
    public function sertifikat($id)
    {
        $user = User::where('role', 'perawat')->findOrFail($id);

        $sip = PerawatSip::where('user_id', $id)->orderBy('tgl_expired', 'desc')->get();
        $str = PerawatStr::where('user_id', $id)->orderBy('tgl_expired', 'desc')->get();
        $dataTambahan = PerawatDataTambahan::where('user_id', $id)->orderBy('tgl_expired', 'desc')->get();
        $lisensi = PerawatLisensi::where('user_id', $id)->orderBy('tgl_expired', 'desc')->get();

        return view('admin.perawat.sertifikat', compact('user', 'sip', 'str', 'dataTambahan', 'lisensi'));
    }
}
