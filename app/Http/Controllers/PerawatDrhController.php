<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Tambahkan ini untuk manajemen file
use App\Models\PerawatProfile;
use App\Models\PerawatPendidikan;
use App\Models\PerawatPelatihan;
use App\Models\PerawatPekerjaan;
use App\Models\PerawatKeluarga;
use App\Models\PerawatOrganisasi;
use App\Models\PerawatTandaJasa;
use App\Models\PerawatLisensi;
use App\Models\PerawatStr;
use App\Models\PerawatSip;
use App\Models\PerawatDataTambahan;

class PerawatDrhController extends Controller
{
    protected function currentPerawat()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'perawat') {
            return null;
        }
        return $user;
    }

    /* ============ DRH SUMMARY (READ ONLY) ============ */
    public function index()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $profile     = PerawatProfile::where('user_id', $user->id)->first();
        $pendidikan  = PerawatPendidikan::where('user_id', $user->id)->orderBy('tahun_lulus','desc')->get();
        $pelatihan   = PerawatPelatihan::where('user_id', $user->id)->orderBy('tanggal_mulai','desc')->get();
        $pekerjaan   = PerawatPekerjaan::where('user_id', $user->id)->orderBy('tahun_mulai','desc')->get();
        $keluarga    = PerawatKeluarga::where('user_id', $user->id)->get();
        $organisasi  = PerawatOrganisasi::where('user_id', $user->id)->orderBy('tahun_mulai','desc')->get();
        $tandajasa   = PerawatTandaJasa::where('user_id', $user->id)->orderBy('tahun','desc')->get();

        return view('perawat.drh.index', compact(
            'user','profile',
            'pendidikan','pelatihan','pekerjaan',
            'keluarga','organisasi','tandajasa'
        ));
    }

    /* ============ IDENTITAS (Updated) ============ */
    public function editIdentitas()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        // Buat data dummy jika belum ada, agar form tidak error
        $profile = PerawatProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['nama_lengkap' => $user->name]
        );

        return view('perawat.drh.identitas', compact('user','profile'));
    }

    public function updateIdentitas(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        // Validasi disesuaikan dengan Migration agar lengkap
        $request->validate([
            // Identitas Utama
            'nama_lengkap'      => 'required|string|max:150',
            'nik'               => 'nullable|string|max:30',
            'nip'               => 'nullable|string|max:50',
            'nirp'              => 'nullable|string|max:50',

            // Biodata
            'tempat_lahir'      => 'nullable|string|max:100',
            'tanggal_lahir'     => 'nullable|date',
            'jenis_kelamin'     => 'nullable|string|in:L,P',
            'agama'             => 'nullable|string|max:30',
            'aliran_kepercayaan'=> 'nullable|string|max:100', // Added
            'status_perkawinan' => 'nullable|string|max:50',

            // Kontak
            'no_hp'             => 'nullable|string|max:30',
            'alamat'            => 'nullable|string|max:255',
            'kota'              => 'nullable|string|max:100',

            // Jabatan
            'jabatan'           => 'nullable|string|max:100',
            'pangkat'           => 'nullable|string|max:50',
            'golongan'          => 'nullable|string|max:20',

            // Fisik
            'tinggi_badan'      => 'nullable|integer',
            'berat_badan'       => 'nullable|integer',
            'golongan_darah'    => 'nullable|string|max:5',
            'rambut'            => 'nullable|string|max:100', // Adjusted size
            'bentuk_muka'       => 'nullable|string|max:100', // Adjusted size
            'warna_kulit'       => 'nullable|string|max:100', // Adjusted size
            'ciri_khas'         => 'nullable|string|max:150', // Adjusted size
            'cacat_tubuh'       => 'nullable|string|max:150', // Adjusted size

            // Lainnya
            'hobby'             => 'nullable|string|max:150', // Added
            'foto_3x4'          => 'nullable|image|max:2048', // Max 2MB
        ]);

        $profile = PerawatProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['nama_lengkap' => $user->name]
        );

        // Ambil semua input kecuali foto & token
        $data = $request->except(['foto_3x4', '_token', '_method']);

        // Handle Upload Foto
        if ($request->hasFile('foto_3x4')) {
            // Hapus foto lama jika ada (opsional, untuk hemat storage)
            if ($profile->foto_3x4 && Storage::disk('public')->exists($profile->foto_3x4)) {
                Storage::disk('public')->delete($profile->foto_3x4);
            }
            $data['foto_3x4'] = $request->file('foto_3x4')->store('perawat/foto', 'public');
        }

        $profile->update($data);

        return redirect()->route('perawat.drh')->with('swal', [
            'icon'  => 'success',
            'title' => 'Identitas Diperbarui',
            'text'  => 'Data profil lengkap berhasil disimpan.',
        ]);
    }

     /* ============ DATA LENGKAP ============ */
    public function showDataLengkap()
{
    $user = $this->currentPerawat();
    if (!$user) return redirect('/');

    $profile    = PerawatProfile::where('user_id', $user->id)->first();

    // Mengambil semua data relasi
    $pendidikan = PerawatPendidikan::where('user_id', $user->id)->orderBy('tahun_lulus', 'desc')->get();
    $pelatihan  = PerawatPelatihan::where('user_id', $user->id)->orderBy('tanggal_mulai', 'desc')->get();
    $pekerjaan  = PerawatPekerjaan::where('user_id', $user->id)->orderBy('tahun_mulai', 'desc')->get();
    $keluarga   = PerawatKeluarga::where('user_id', $user->id)->get(); // Biasanya tidak butuh order khusus
    $organisasi = PerawatOrganisasi::where('user_id', $user->id)->orderBy('tahun_mulai', 'desc')->get();
    $tandajasa  = PerawatTandaJasa::where('user_id', $user->id)->orderBy('tahun', 'desc')->get();

    return view('perawat.drh.data-lengkap', compact(
        'user', 'profile',
        'pendidikan', 'pelatihan', 'pekerjaan',
        'keluarga', 'organisasi', 'tandajasa'
    ));
}

    /* ============ PENDIDIKAN ============ */
    public function pendidikanIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pendidikan = PerawatPendidikan::where('user_id',$user->id)->orderBy('tahun_lulus','desc')->get();
        return view('perawat.pendidikan.index', compact('user','pendidikan'));
    }

    public function pendidikanCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.pendidikan.create', compact('user'));
    }

    public function pendidikanStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $request->validate([
            'jenjang'        => 'required|string|max:50',
            'nama_institusi' => 'required|string|max:150',
            'akreditasi'     => 'nullable|string|max:10',
            'tempat'         => 'nullable|string|max:100',
            'tahun_lulus'    => 'nullable|string|max:10',
            'jurusan'        => 'nullable|string|max:150',
            'tahun_masuk'    => 'nullable|string|max:10',
            'dokumen'        => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->except(['dokumen','_token']);
        $data['user_id'] = $user->id;

        if ($request->hasFile('dokumen')) {
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/pendidikan','public');
        }

        PerawatPendidikan::create($data);

        return redirect()->route('perawat.pendidikan.index')->with('swal', [
            'icon'=>'success','title'=>'Berhasil','text'=>'Pendidikan ditambahkan.'
        ]);
    }

    public function pendidikanEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pendidikan = PerawatPendidikan::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.pendidikan.edit', compact('user', 'pendidikan'));
    }

    public function pendidikanUpdate(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pendidikan = PerawatPendidikan::where('user_id',$user->id)->findOrFail($id);

        $request->validate([
            'jenjang'        => 'required|string|max:50',
            'nama_institusi' => 'required|string|max:150',
            'akreditasi'     => 'nullable|string|max:10',
            'tempat'         => 'nullable|string|max:100',
            'tahun_lulus'    => 'nullable|string|max:10',
            'jurusan'        => 'nullable|string|max:150',
            'tahun_masuk'    => 'nullable|string|max:10',
            'dokumen'        => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->except(['dokumen','_token','_method']);

        if ($request->hasFile('dokumen')) {
             if ($pendidikan->dokumen_path && Storage::disk('public')->exists($pendidikan->dokumen_path)) {
                 Storage::disk('public')->delete($pendidikan->dokumen_path);
             }
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/pendidikan','public');
        }

        $pendidikan->update($data);

        return redirect()->route('perawat.pendidikan.index')->with('swal', [
            'icon'=>'success','title'=>'Berhasil','text'=>'Pendidikan diperbarui.'
        ]);
    }

    public function pendidikanDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pendidikan = PerawatPendidikan::where('user_id',$user->id)->findOrFail($id);

        if ($pendidikan->dokumen_path && Storage::disk('public')->exists($pendidikan->dokumen_path)) {
            Storage::disk('public')->delete($pendidikan->dokumen_path);
        }
        $pendidikan->delete();

        return redirect()->route('perawat.pendidikan.index')->with('swal', [
            'icon'=>'success','title'=>'Berhasil','text'=>'Pendidikan dihapus.'
        ]);
    }

    /* ============ PELATIHAN ============ */
    public function pelatihanIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pelatihan = PerawatPelatihan::where('user_id',$user->id)->orderBy('tanggal_mulai','desc')->get();
        return view('perawat.pelatihan.index', compact('user','pelatihan'));
    }

    public function pelatihanCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.pelatihan.create', compact('user'));
    }

    public function pelatihanStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $request->validate([
            'nama_pelatihan' => 'required|string|max:150',
            'penyelenggara'  => 'nullable|string|max:150',
            'tempat'         => 'nullable|string|max:100',
            'durasi'         => 'nullable|string|max:50',
            'tanggal_mulai'  => 'nullable|date',
            'tanggal_selesai'=> 'nullable|date',
            'dokumen'        => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->except(['dokumen','_token']);
        $data['user_id'] = $user->id;

        if ($request->hasFile('dokumen')) {
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/pelatihan','public');
        }

        PerawatPelatihan::create($data);

        return redirect()->route('perawat.pelatihan.index')->with('swal',[
            'icon'=>'success','title'=>'Berhasil','text'=>'Pelatihan ditambahkan.'
        ]);
    }

    public function pelatihanEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pelatihan = PerawatPelatihan::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.pelatihan.edit', compact('user', 'pelatihan'));
    }

    public function pelatihanUpdate(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pelatihan = PerawatPelatihan::where('user_id',$user->id)->findOrFail($id);

        $request->validate([
            'nama_pelatihan' => 'required|string|max:150',
            'penyelenggara'  => 'nullable|string|max:150',
            'tempat'         => 'nullable|string|max:100',
            'durasi'         => 'nullable|string|max:50',
            'tanggal_mulai'  => 'nullable|date',
            'tanggal_selesai'=> 'nullable|date',
            'dokumen'        => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->except(['dokumen','_token','_method']);

        if ($request->hasFile('dokumen')) {
            if ($pelatihan->dokumen_path && Storage::disk('public')->exists($pelatihan->dokumen_path)) {
                Storage::disk('public')->delete($pelatihan->dokumen_path);
            }
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/pelatihan','public');
        }

        $pelatihan->update($data);

        return redirect()->route('perawat.pelatihan.index')->with('swal',[
            'icon'=>'success','title'=>'Berhasil','text'=>'Pelatihan diperbarui.'
        ]);
    }

    public function pelatihanDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pelatihan = PerawatPelatihan::where('user_id',$user->id)->findOrFail($id);

        if ($pelatihan->dokumen_path && Storage::disk('public')->exists($pelatihan->dokumen_path)) {
            Storage::disk('public')->delete($pelatihan->dokumen_path);
        }
        $pelatihan->delete();

        return redirect()->route('perawat.pelatihan.index')->with('swal',[
            'icon'=>'success','title'=>'Berhasil','text'=>'Pelatihan dihapus.'
        ]);
    }

    /* ============ PEKERJAAN ============ */
    public function pekerjaanIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pekerjaan = PerawatPekerjaan::where('user_id', $user->id)->orderBy('tahun_mulai', 'desc')->get();
        return view('perawat.pekerjaan.index', compact('user', 'pekerjaan'));
    }

    public function pekerjaanCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.pekerjaan.create', compact('user'));
    }

    public function pekerjaanStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $request->validate([
            'nama_instansi' => 'required|string|max:150',
            'jabatan'       => 'required|string|max:150',
            'tahun_mulai'   => 'nullable|string|max:4',
            'tahun_selesai' => 'nullable|string|max:4',
            'keterangan'    => 'nullable|string|max:255',
            'dokumen'       => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->except(['dokumen','_token']);
        $data['user_id'] = $user->id;

        if ($request->hasFile('dokumen')) {
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/pekerjaan', 'public');
        }

        PerawatPekerjaan::create($data);

        return redirect()->route('perawat.pekerjaan.index')->with('swal', [
            'icon' => 'success', 'title' => 'Berhasil', 'text' => 'Riwayat pekerjaan ditambahkan.'
        ]);
    }

    public function pekerjaanEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pekerjaan = PerawatPekerjaan::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.pekerjaan.edit', compact('user', 'pekerjaan'));
    }

    public function pekerjaanUpdate(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pekerjaan = PerawatPekerjaan::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'nama_instansi' => 'required|string|max:150',
            'jabatan'       => 'required|string|max:150',
            'tahun_mulai'   => 'nullable|string|max:4',
            'tahun_selesai' => 'nullable|string|max:4',
            'keterangan'    => 'nullable|string|max:255',
            'dokumen'       => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->except(['dokumen','_token','_method']);

        if ($request->hasFile('dokumen')) {
            if ($pekerjaan->dokumen_path && Storage::disk('public')->exists($pekerjaan->dokumen_path)) {
                Storage::disk('public')->delete($pekerjaan->dokumen_path);
            }
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/pekerjaan', 'public');
        }

        $pekerjaan->update($data);

        return redirect()->route('perawat.pekerjaan.index')->with('swal', [
            'icon' => 'success', 'title' => 'Berhasil', 'text' => 'Riwayat pekerjaan diperbarui.'
        ]);
    }

    public function pekerjaanDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pekerjaan = PerawatPekerjaan::where('user_id',$user->id)->findOrFail($id);

        if ($pekerjaan->dokumen_path && Storage::disk('public')->exists($pekerjaan->dokumen_path)) {
            Storage::disk('public')->delete($pekerjaan->dokumen_path);
        }
        $pekerjaan->delete();

        return redirect()->route('perawat.pekerjaan.index')->with('swal',[
            'icon'=>'success','title'=>'Berhasil','text'=>'Riwayat pekerjaan dihapus.'
        ]);
    }

    /* ============ KELUARGA ============ */
    public function keluargaIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $keluarga = PerawatKeluarga::where('user_id', $user->id)->get();
        return view('perawat.keluarga.index', compact('user', 'keluarga'));
    }

    public function keluargaCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.keluarga.create', compact('user'));
    }

    public function keluargaStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $request->validate([
            'hubungan'      => 'required|string|max:50',
            'nama'          => 'required|string|max:150',
            'tanggal_lahir' => 'nullable|date',
            'pekerjaan'     => 'nullable|string|max:150',
        ]);

        $data = $request->only('hubungan', 'nama', 'tanggal_lahir', 'pekerjaan');
        $data['user_id'] = $user->id;

        PerawatKeluarga::create($data);

        return redirect()->route('perawat.keluarga.index')->with('swal', [
            'icon' => 'success', 'title' => 'Berhasil', 'text' => 'Data keluarga ditambahkan.'
        ]);
    }

    public function keluargaEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $keluarga = PerawatKeluarga::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.keluarga.edit', compact('user', 'keluarga'));
    }

    public function keluargaUpdate(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $keluarga = PerawatKeluarga::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'hubungan'      => 'required|string|max:50',
            'nama'          => 'required|string|max:150',
            'tanggal_lahir' => 'nullable|date',
            'pekerjaan'     => 'nullable|string|max:150',
        ]);

        $keluarga->update($request->only('hubungan', 'nama', 'tanggal_lahir', 'pekerjaan'));

        return redirect()->route('perawat.keluarga.index')->with('swal', [
            'icon' => 'success', 'title' => 'Berhasil', 'text' => 'Data keluarga diperbarui.'
        ]);
    }

    public function keluargaDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $keluarga = PerawatKeluarga::where('user_id',$user->id)->findOrFail($id);
        $keluarga->delete();

        return redirect()->route('perawat.keluarga.index')->with('swal',[
            'icon'=>'success','title'=>'Berhasil','text'=>'Data keluarga dihapus.'
        ]);
    }

    /* ============ ORGANISASI ============ */
    public function organisasiIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $organisasi = PerawatOrganisasi::where('user_id', $user->id)->get();
        return view('perawat.organisasi.index', compact('user', 'organisasi'));
    }

    public function organisasiCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.organisasi.create', compact('user'));
    }

    public function organisasiStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $request->validate([
            'nama_organisasi' => 'required|string|max:150',
            'jabatan'         => 'required|string|max:150',
            'tempat'          => 'nullable|string|max:100',
            'tahun_mulai'     => 'nullable|date',
            'tahun_selesai'   => 'nullable|date',
            'pemimpin'        => 'nullable|string|max:150',
            'dokumen'         => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->except(['dokumen','_token']);
        $data['user_id'] = $user->id;

        if ($request->hasFile('dokumen')) {
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/organisasi', 'public');
        }

        PerawatOrganisasi::create($data);

        return redirect()->route('perawat.organisasi.index')->with('swal', [
            'icon' => 'success', 'title' => 'Berhasil', 'text' => 'Data organisasi ditambahkan.'
        ]);
    }

    public function organisasiEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $organisasi = PerawatOrganisasi::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.organisasi.edit', compact('user', 'organisasi'));
    }

    public function organisasiUpdate(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $organisasi = PerawatOrganisasi::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'nama_organisasi' => 'required|string|max:150',
            'jabatan'         => 'required|string|max:150',
            'tempat'          => 'nullable|string|max:100',
            'tahun_mulai'     => 'nullable|date',
            'tahun_selesai'   => 'nullable|date',
            'pemimpin'        => 'nullable|string|max:150',
            'dokumen'         => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->except(['dokumen','_token','_method']);

        if ($request->hasFile('dokumen')) {
            if ($organisasi->dokumen_path && Storage::disk('public')->exists($organisasi->dokumen_path)) {
                Storage::disk('public')->delete($organisasi->dokumen_path);
            }
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/organisasi', 'public');
        }

        $organisasi->update($data);

        return redirect()->route('perawat.organisasi.index')->with('swal', [
            'icon' => 'success', 'title' => 'Berhasil', 'text' => 'Data organisasi diperbarui.'
        ]);
    }

    public function organisasiDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $organisasi = PerawatOrganisasi::where('user_id', $user->id)->findOrFail($id);

        if ($organisasi->dokumen_path && Storage::disk('public')->exists($organisasi->dokumen_path)) {
            Storage::disk('public')->delete($organisasi->dokumen_path);
        }
        $organisasi->delete();

        return redirect()->route('perawat.organisasi.index')->with('swal', [
            'icon' => 'success', 'title' => 'Berhasil', 'text' => 'Data organisasi dihapus.'
        ]);
    }

  /* ============ TANDA JASA ============ */
    public function tandajasaIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $tandajasa = PerawatTandaJasa::where('user_id',$user->id)->get();
        return view('perawat.tandajasa.index', compact('user','tandajasa'));
    }

    public function tandajasaCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.tandajasa.create', compact('user'));
    }

    public function tandajasaStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $request->validate([
            'nama_penghargaan' => 'required|string|max:150',
            'instansi_pemberi' => 'nullable|string|max:150',
            'tahun'            => 'nullable|string|max:10',
            'nomor_sk'         => 'nullable|string|max:100',
            'tanggal_sk'       => 'nullable|date',
            'dokumen'          => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->except(['dokumen','_token']);
        $data['user_id'] = $user->id;

        if ($request->hasFile('dokumen')) {
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/tandajasa','public');
        }

        PerawatTandaJasa::create($data);

        return redirect()->route('perawat.tandajasa.index')->with('swal',[
            'icon'=>'success','title'=>'Berhasil','text'=>'Tanda jasa ditambahkan.'
        ]);
    }

    public function tandajasaEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $tandajasa = PerawatTandaJasa::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.tandajasa.edit', compact('user', 'tandajasa'));
    }

    public function tandajasaUpdate(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $tj = PerawatTandaJasa::where('user_id',$user->id)->findOrFail($id);

        $request->validate([
            'nama_penghargaan' => 'required|string|max:150',
            'instansi_pemberi' => 'nullable|string|max:150',
            'tahun'            => 'nullable|string|max:10',
            'nomor_sk'         => 'nullable|string|max:100',
            'tanggal_sk'       => 'nullable|date',
            'dokumen'          => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->except(['dokumen','_token','_method']);

        if ($request->hasFile('dokumen')) {
            if ($tj->dokumen_path && Storage::disk('public')->exists($tj->dokumen_path)) {
                Storage::disk('public')->delete($tj->dokumen_path);
            }
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/tandajasa','public');
        }

        $tj->update($data);

        return redirect()->route('perawat.tandajasa.index')->with('swal',[
            'icon'=>'success','title'=>'Berhasil','text'=>'Tanda jasa diperbarui.'
        ]);
    }

    public function tandajasaDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $tj = PerawatTandaJasa::where('user_id',$user->id)->findOrFail($id);

        if ($tj->dokumen_path && Storage::disk('public')->exists($tj->dokumen_path)) {
            Storage::disk('public')->delete($tj->dokumen_path);
        }
        $tj->delete();

        return redirect()->route('perawat.tandajasa.index')->with('swal',[
            'icon'=>'success','title'=>'Berhasil','text'=>'Tanda jasa dihapus.'
        ]);
    }

    /* ==========================================================
       BAGIAN MANAJEMEN DOKUMEN (LISENSI, STR, SIP, TAMBAHAN)
       ========================================================== */

    /* ------------ 1. LISENSI (Tanpa Field Jenis) ------------ */
    public function lisensiIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        // Order by expired date descending agar yang paling baru/lama terlihat
        $data = PerawatLisensi::where('user_id', $user->id)->orderBy('tgl_expired', 'desc')->get();
        return view('perawat.dokumen.lisensi.index', compact('user', 'data'));
    }

    public function lisensiCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.dokumen.lisensi.create', compact('user'));
    }

   public function lisensiStore(Request $request)
{
    $user = $this->currentPerawat();
    if (!$user) return redirect('/');

    $request->validate([
        'nama'        => 'required|string|max:100', // Validasi Baru
        'lembaga'     => 'required|string|max:100', // Validasi Baru
        'nomor'       => 'required|string|max:100',
        'tgl_terbit'  => 'required|date',
        'tgl_expired' => 'required|date',
        'dokumen'     => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);

    $data = $request->except(['dokumen', '_token']);
    $data['user_id'] = $user->id;

    if ($request->hasFile('dokumen')) {
        $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/lisensi', 'public');
    }

    PerawatLisensi::create($data);

    return redirect()->route('perawat.lisensi.index')->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'Lisensi berhasil disimpan.']);
}


    public function lisensiEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatLisensi::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.dokumen.lisensi.edit', compact('user', 'data'));
    }

    public function lisensiUpdate(Request $request, $id)
{
    $user = $this->currentPerawat();
    if (!$user) return redirect('/');
    $lisensi = PerawatLisensi::where('user_id', $user->id)->findOrFail($id);

    $request->validate([
        'nama'        => 'required|string|max:100', // Validasi Baru
        'lembaga'     => 'required|string|max:100', // Validasi Baru
        'nomor'       => 'required|string|max:100',
        'tgl_terbit'  => 'required|date',
        'tgl_expired' => 'required|date',
        'dokumen'     => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);

    $data = $request->except(['dokumen', '_token', '_method']);

    if ($request->hasFile('dokumen')) {
        if ($lisensi->file_path && Storage::disk('public')->exists($lisensi->file_path)) {
            Storage::disk('public')->delete($lisensi->file_path);
        }
        $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/lisensi', 'public');
    }

    $lisensi->update($data);
    return redirect()->route('perawat.lisensi.index')->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'Lisensi diperbarui.']);
}

    public function lisensiDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatLisensi::where('user_id', $user->id)->findOrFail($id);

        if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
            Storage::disk('public')->delete($data->file_path);
        }
        $data->delete();
        return redirect()->route('perawat.lisensi.index')->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'Lisensi dihapus.']);
    }

    /* ------------ 2. STR (Surat Tanda Registrasi) ------------ */
    public function strIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatStr::where('user_id', $user->id)->orderBy('tgl_expired', 'desc')->get();
        return view('perawat.dokumen.str.index', compact('user', 'data'));
    }

    public function strCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.dokumen.str.create', compact('user'));
    }

   public function strStore(Request $request)
{
    $user = $this->currentPerawat();
    if (!$user) return redirect('/');

    $request->validate([
        'nama'        => 'required|string|max:100', // Validasi Baru
        'lembaga'     => 'required|string|max:100', // Validasi Baru
        'nomor'       => 'required|string|max:100',
        'tgl_terbit'  => 'required|date',
        'tgl_expired' => 'required|date',
        'dokumen'     => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);

    $data = $request->except(['dokumen', '_token']);
    $data['user_id'] = $user->id;

    if ($request->hasFile('dokumen')) {
        $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/str', 'public');
    }

    PerawatStr::create($data);
    return redirect()->route('perawat.str.index')->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'STR berhasil disimpan.']);
}

    public function strEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatStr::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.dokumen.str.edit', compact('user', 'data'));
    }

   public function strUpdate(Request $request, $id)
{
    $user = $this->currentPerawat();
    if (!$user) return redirect('/');
    $str = PerawatStr::where('user_id', $user->id)->findOrFail($id);

    $request->validate([
        'nama'        => 'required|string|max:100', // Validasi Baru
        'lembaga'     => 'required|string|max:100', // Validasi Baru
        'nomor'       => 'required|string|max:100',
        'tgl_terbit'  => 'required|date',
        'tgl_expired' => 'required|date',
        'dokumen'     => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);

    $data = $request->except(['dokumen', '_token', '_method']);

    if ($request->hasFile('dokumen')) {
        if ($str->file_path && Storage::disk('public')->exists($str->file_path)) {
            Storage::disk('public')->delete($str->file_path);
        }
        $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/str', 'public');
    }

    $str->update($data);
    return redirect()->route('perawat.str.index')->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'STR diperbarui.']);
}

    public function strDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatStr::where('user_id', $user->id)->findOrFail($id);

        if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
            Storage::disk('public')->delete($data->file_path);
        }
        $data->delete();
        return redirect()->route('perawat.str.index')->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'STR dihapus.']);
    }

    /* ------------ 3. SIP (Surat Izin Praktik) ------------ */
    public function sipIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatSip::where('user_id', $user->id)->orderBy('tgl_expired', 'desc')->get();
        return view('perawat.dokumen.sip.index', compact('user', 'data'));
    }

    public function sipCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.dokumen.sip.create', compact('user'));
    }

    public function sipStore(Request $request)
{
    $user = $this->currentPerawat();
    if (!$user) return redirect('/');

    $request->validate([
        'nama'        => 'required|string|max:100', // Validasi Baru
        'lembaga'     => 'required|string|max:100', // Validasi Baru
        'nomor'       => 'required|string|max:100',
        'tgl_terbit'  => 'required|date',
        'tgl_expired' => 'required|date',
        'dokumen'     => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);

    $data = $request->except(['dokumen', '_token']);
    $data['user_id'] = $user->id;

    if ($request->hasFile('dokumen')) {
        $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/sip', 'public');
    }

    PerawatSip::create($data);
    return redirect()->route('perawat.sip.index')->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'SIP berhasil disimpan.']);
}
    public function sipEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatSip::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.dokumen.sip.edit', compact('user', 'data'));
    }

  public function sipUpdate(Request $request, $id)
{
    $user = $this->currentPerawat();
    if (!$user) return redirect('/');
    $sip = PerawatSip::where('user_id', $user->id)->findOrFail($id);

    $request->validate([
        'nama'        => 'required|string|max:100', // Validasi Baru
        'lembaga'     => 'required|string|max:100', // Validasi Baru
        'nomor'       => 'required|string|max:100',
        'tgl_terbit'  => 'required|date',
        'tgl_expired' => 'required|date',
        'dokumen'     => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);

    $data = $request->except(['dokumen', '_token', '_method']);

    if ($request->hasFile('dokumen')) {
        if ($sip->file_path && Storage::disk('public')->exists($sip->file_path)) {
            Storage::disk('public')->delete($sip->file_path);
        }
        $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/sip', 'public');
    }

    $sip->update($data);
    return redirect()->route('perawat.sip.index')->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'SIP diperbarui.']);
}

    public function sipDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatSip::where('user_id', $user->id)->findOrFail($id);

        if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
            Storage::disk('public')->delete($data->file_path);
        }
        $data->delete();
        return redirect()->route('perawat.sip.index')->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'SIP dihapus.']);
    }

    /* ------------ 4. DATA TAMBAHAN (Ada Field Jenis) ------------ */
    public function tambahanIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatDataTambahan::where('user_id', $user->id)->orderBy('tgl_expired', 'desc')->get();
        return view('perawat.dokumen.tambahan.index', compact('user', 'data'));
    }

    public function tambahanCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.dokumen.tambahan.create', compact('user'));
    }

   public function tambahanStore(Request $request)
{
    $user = $this->currentPerawat();
    if (!$user) return redirect('/');

    $request->validate([
        'jenis'       => 'required|string|max:100',
        'nama'        => 'required|string|max:100',
        'lembaga'     => 'required|string|max:100',
        'nomor'       => 'required|string|max:100',
        'tgl_terbit'  => 'required|date',
        'tgl_expired' => 'required|date',
        'dokumen'     => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);

    $data = $request->except(['dokumen', '_token']);
    $data['user_id'] = $user->id;

    if ($request->hasFile('dokumen')) {
        $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/tambahan', 'public');
    }

    PerawatDataTambahan::create($data);
    return redirect()->route('perawat.tambahan.index')->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'Dokumen berhasil disimpan.']);
}

    public function tambahanEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatDataTambahan::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.dokumen.tambahan.edit', compact('user', 'data'));
    }

    public function tambahanUpdate(Request $request, $id)
{
    $user = $this->currentPerawat();
    if (!$user) return redirect('/');
    $tambahan = PerawatDataTambahan::where('user_id', $user->id)->findOrFail($id);

    $request->validate([
        'jenis'       => 'required|string|max:100',
        'nama'        => 'required|string|max:100',
        'lembaga'     => 'required|string|max:100',
        'nomor'       => 'required|string|max:100',
        'tgl_terbit'  => 'required|date',
        'tgl_expired' => 'required|date',
        'dokumen'     => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);

    $data = $request->except(['dokumen', '_token', '_method']);

    if ($request->hasFile('dokumen')) {
        if ($tambahan->file_path && Storage::disk('public')->exists($tambahan->file_path)) {
            Storage::disk('public')->delete($tambahan->file_path);
        }
        $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/tambahan', 'public');
    }

    $tambahan->update($data);
    return redirect()->route('perawat.tambahan.index')->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'Dokumen diperbarui.']);
}

    public function tambahanDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatDataTambahan::where('user_id', $user->id)->findOrFail($id);

        if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
            Storage::disk('public')->delete($data->file_path);
        }
        $data->delete();
        return redirect()->route('perawat.tambahan.index')->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'Dokumen tambahan dihapus.']);
    }

}
