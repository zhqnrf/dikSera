<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PerawatProfile;
use App\Models\PerawatPendidikan;
use App\Models\PerawatPelatihan;
use App\Models\PerawatPekerjaan;
use App\Models\PerawatKeluarga;
use App\Models\PerawatOrganisasi;
use App\Models\PerawatTandaJasa;

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
        if (!$user) {
            return redirect('/');
        }

        $profile     = PerawatProfile::where('user_id', $user->id)->first();
        $pendidikan  = PerawatPendidikan::where('user_id', $user->id)->get();
        $pelatihan   = PerawatPelatihan::where('user_id', $user->id)->get();
        $pekerjaan   = PerawatPekerjaan::where('user_id', $user->id)->get();
        $keluarga    = PerawatKeluarga::where('user_id', $user->id)->get();
        $organisasi  = PerawatOrganisasi::where('user_id', $user->id)->get();
        $tandajasa   = PerawatTandaJasa::where('user_id', $user->id)->get();

        return view('perawat.drh.index', compact(
            'user','profile',
            'pendidikan','pelatihan','pekerjaan',
            'keluarga','organisasi','tandajasa'
        ));
    }

    /* ============ IDENTITAS ============ */
    public function editIdentitas()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

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

        $request->validate([
            'nama_lengkap'      => 'required|string|max:150',
            'nik'               => 'nullable|string|max:30',
            'tempat_lahir'      => 'nullable|string|max:100',
            'tanggal_lahir'     => 'nullable|date',
            'jenis_kelamin'     => 'nullable|string|max:20',
            'agama'             => 'nullable|string|max30',
            'status_perkawinan' => 'nullable|string|max:50',
            'no_hp'             => 'nullable|string|max:30',
            'alamat'            => 'nullable|string|max:255',
            'kota'              => 'nullable|string|max:100',
            'tinggi_badan'      => 'nullable|integer',
            'berat_badan'       => 'nullable|integer',
            'golongan_darah'    => 'nullable|string|max:5',
            'rambut'            => 'nullable|string|max:50',
            'bentuk_muka'       => 'nullable|string|max:50',
            'warna_kulit'       => 'nullable|string|max:50',
            'ciri_khas'         => 'nullable|string|max:100',
            'cacat_tubuh'       => 'nullable|string|max:50',
            'jabatan'           => 'nullable|string|max:100',
            'pangkat'           => 'nullable|string|max:50',
            'golongan'          => 'nullable|string|max:50',
            'nip'               => 'nullable|string|max:50',
            'nirp'              => 'nullable|string|max:50',
            'foto_3x4'          => 'nullable|image|max:2048',
        ]);

        $profile = PerawatProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['nama_lengkap' => $user->name]
        );

        $data = $request->except(['foto_3x4','_token']);
        if ($request->hasFile('foto_3x4')) {
            $data['foto_3x4'] = $request->file('foto_3x4')->store('perawat/foto','public');
        }

        $profile->update($data);

        return redirect()->route('perawat.drh')->with('swal', [
            'icon'  => 'success',
            'title' => 'Identitas diperbarui',
            'text'  => 'Data identitas berhasil disimpan.',
        ]);
    }

    /* ============ PENDIDIKAN ============ */
    public function pendidikanIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $pendidikan = PerawatPendidikan::where('user_id',$user->id)->orderBy('tahun_lulus','desc')->get();
        return view('perawat.pendidikan.index', compact('user','pendidikan'));
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
            'dokumen'        => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
            'jurusan'        => 'nullable|string|max:150', 
            'tahun_masuk'    => 'nullable|string|max:10', 
        ]);

        $data = $request->only('jenjang','nama_institusi','akreditasi','tempat','tahun_lulus','jurusan','tahun_masuk');
        $data['user_id'] = $user->id;

        if ($request->hasFile('dokumen')) {
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/pendidikan','public');
        }

        PerawatPendidikan::create($data);

        return redirect()->route('perawat.pendidikan.index')->with('swal', [
            'icon'=>'success','title'=>'Berhasil','text'=>'Pendidikan ditambahkan.'
        ]);
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
            'dokumen'        => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
            'jurusan'        => 'nullable|string|max:150', 
            'tahun_masuk'    => 'nullable|string|max:10', 
        ]);

        $data = $request->only('jenjang','nama_institusi','akreditasi','tempat','tahun_lulus','jurusan','tahun_masuk');
        if ($request->hasFile('dokumen')) {
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

        $data = $request->only('nama_pelatihan','penyelenggara','tempat','durasi','tanggal_mulai','tanggal_selesai');
        $data['user_id'] = $user->id;

        if ($request->hasFile('dokumen')) {
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/pelatihan','public');
        }

        PerawatPelatihan::create($data);

        return redirect()->route('perawat.pelatihan.index')->with('swal',[
            'icon'=>'success','title'=>'Berhasil','text'=>'Pelatihan ditambahkan.'
        ]);
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

        $data = $request->only('nama_pelatihan','penyelenggara','tempat','durasi','tanggal_mulai','tanggal_selesai');
        if ($request->hasFile('dokumen')) {
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

        // Urutkan berdasarkan tahun mulai terbaru
        $pekerjaan = PerawatPekerjaan::where('user_id', $user->id)
                     ->orderBy('tahun_mulai', 'desc')
                     ->get();

        return view('perawat.pekerjaan.index', compact('user', 'pekerjaan'));
    }

   public function pekerjaanStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $request->validate([
            'nama_instansi' => 'required|string|max:150',
            'jabatan'       => 'required|string|max:150',
            'tahun_mulai'   => 'nullable|string|max:4', // Sesuai Migration
            'tahun_selesai' => 'nullable|string|max:4', // Sesuai Migration
            'keterangan'    => 'nullable|string|max:255', // Sesuai Migration
            'dokumen'       => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->only('nama_instansi', 'jabatan', 'tahun_mulai', 'tahun_selesai', 'keterangan');
        $data['user_id'] = $user->id;

        if ($request->hasFile('dokumen')) {
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/pekerjaan', 'public');
        }

        PerawatPekerjaan::create($data);

        return redirect()->route('perawat.pekerjaan.index')->with('swal', [
            'icon' => 'success', 'title' => 'Berhasil', 'text' => 'Riwayat pekerjaan ditambahkan.'
        ]);
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

        $data = $request->only('nama_instansi', 'jabatan', 'tahun_mulai', 'tahun_selesai', 'keterangan');

        if ($request->hasFile('dokumen')) {
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

        $organisasi = PerawatOrganisasi::where('user_id',$user->id)->get();
        return view('perawat.organisasi.index', compact('user','organisasi'));
    }

    public function organisasiStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $request->validate([
            'nama_organisasi' => 'required|string|max:150',
            'jabatan'         => 'required|string|max:150',
            'tempat'          => 'nullable|string|max:100',
            'tahun_mulai'   => 'nullable|date',
            'tahun_selesai' => 'nullable|date',
            'pemimpin'        => 'nullable|string|max:150',
            'dokumen'         => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->only('nama_organisasi','jabatan','tempat','tahun_mulai','tahun_selesai','pemimpin');
        $data['user_id'] = $user->id;

        if ($request->hasFile('dokumen')) {
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/organisasi','public');
        }

        PerawatOrganisasi::create($data);

        return redirect()->route('perawat.organisasi.index')->with('swal',[
            'icon'=>'success','title'=>'Berhasil','text'=>'Data organisasi ditambahkan.'
        ]);
    }

    public function organisasiUpdate(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $organisasi = PerawatOrganisasi::where('user_id',$user->id)->findOrFail($id);

        $request->validate([
            'nama_organisasi' => 'required|string|max:150',
            'jabatan'         => 'required|string|max:150',
            'tempat'          => 'nullable|string|max:100',
            'tahun_mulai'   => 'nullable|date',
            'tahun_selesai' => 'nullable|date',
            'pemimpin'        => 'nullable|string|max:150',
            'dokumen'         => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->only('nama_organisasi','jabatan','tempat','tahun_mulai','tahun_selesai','pemimpin');
        if ($request->hasFile('dokumen')) {
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/organisasi','public');
        }

        $organisasi->update($data);

        return redirect()->route('perawat.organisasi.index')->with('swal',[
            'icon'=>'success','title'=>'Berhasil','text'=>'Data organisasi diperbarui.'
        ]);
    }

    public function organisasiDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $organisasi = PerawatOrganisasi::where('user_id',$user->id)->findOrFail($id);
        $organisasi->delete();

        return redirect()->route('perawat.organisasi.index')->with('swal',[
            'icon'=>'success','title'=>'Berhasil','text'=>'Data organisasi dihapus.'
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

        $data = $request->only('nama_penghargaan','instansi_pemberi','tahun','nomor_sk','tanggal_sk');
        $data['user_id'] = $user->id;

        if ($request->hasFile('dokumen')) {
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/tandajasa','public');
        }

        PerawatTandaJasa::create($data);

        return redirect()->route('perawat.tandajasa.index')->with('swal',[
            'icon'=>'success','title'=>'Berhasil','text'=>'Tanda jasa ditambahkan.'
        ]);
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

        $data = $request->only('nama_penghargaan','instansi_pemberi','tahun','nomor_sk','tanggal_sk');
        if ($request->hasFile('dokumen')) {
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
        $tj->delete();

        return redirect()->route('perawat.tandajasa.index')->with('swal',[
            'icon'=>'success','title'=>'Berhasil','text'=>'Tanda jasa dihapus.'
        ]);
    }
}
