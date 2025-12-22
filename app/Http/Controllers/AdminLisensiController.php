<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerawatLisensi;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AdminLisensiController extends Controller
{
    public function lisensiCreate()
    {
        $users = User::where('role', 'perawat')
            ->leftJoin('perawat_pekerjaans', 'users.id', '=', 'perawat_pekerjaans.user_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'perawat_pekerjaans.unit_kerja'
            )
            ->orderBy('users.name', 'asc')
            ->get();

        return view('admin.lisensi.create', compact('users'));
    }

    public function lisensiStore(Request $request)
    {
        $request->validate([
            'user_ids'            => 'required|array',
            'user_ids.*'          => 'exists:users,id',
            'metode_perpanjangan' => 'required|in:pg_only,pg_interview,interview_only',
            'nama'                => 'required|string|max:100',
            'lembaga'             => 'required|string|max:100',
            'bidang'              => 'required|string|max:100',

            // --- UBAH DISINI: Validasi Array KFK ---
            'kfk'   => 'required|array',
            'kfk.*' => 'in:Pra PK,Pra BK,PK 1,PK 1.5,PK 2,PK 2.5,PK 3,PK 3.5,PK 4,PK 4.5,PK 5,BK 1,BK 1.5,BK 2,BK 2.5,BK 3,BK 3.5,BK 4,BK 4.5,BK 5',

            'tgl_mulai'           => 'required|date',
            'tgl_diselenggarakan' => 'required|date',
            'tgl_terbit'          => 'required|date',
            'tgl_expired'         => 'required|date',
        ]);

        $commonData = $request->except(['_token', 'user_ids']);

        // --- UBAH DISINI: Convert Array ke JSON String sebelum disimpan ---
        // Karena input kfk[] berupa array, kita harus ubah jadi string JSON agar bisa masuk DB
        $commonData['kfk'] = json_encode($request->kfk);

        $lastId = PerawatLisensi::max('id') ?? 0;
        $count = 0;

        foreach ($request->user_ids as $index => $userId) {
            $urutan = $lastId + 1 + $count;
            $nomorOtomatis = strtoupper($request->nama) . '-' . date('Y') . '-' . sprintf('%04d', $urutan);

            $data = array_merge($commonData, [
                'user_id' => $userId,
                'nomor'   => $nomorOtomatis
            ]);

            PerawatLisensi::create($data);
            $count++;
        }

        return redirect()->route('admin.lisensi.index')->with('swal', [
            'icon'  => 'success',
            'title' => 'Berhasil',
            'text'  => "Lisensi berhasil dibuat untuk $count perawat dengan nomor urut otomatis."
        ]);
    }

    public function lisensiIndex(Request $request)
{
    // 1. Mulai Query dan Eager Load User (biar ringan)
    $query = PerawatLisensi::with('user');

    // 2. Logika Search (Nama Lisensi, Nomor, Bidang, atau Nama Perawat)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('nomor', 'like', "%{$search}%")
              ->orWhere('bidang', 'like', "%{$search}%")
              ->orWhereHas('user', function($u) use ($search) {
                  $u->where('name', 'like', "%{$search}%");
              });
        });
    }

    // 3. Logika Filter Status (Aktif / Expired)
    if ($request->filled('status')) {
        if ($request->status == 'aktif') {
            $query->whereDate('tgl_expired', '>=', now());
        } elseif ($request->status == 'expired') {
            $query->whereDate('tgl_expired', '<', now());
        }
    }

    // 4. Urutkan dan Paginate (Append query string agar filter tidak hilang saat ganti halaman)
    $data = $query->latest()->paginate(10);

    return view('admin.lisensi.index', compact('data'));
}

    public function lisensiEdit($id)
{
    $data = PerawatLisensi::findOrFail($id);

    $users = User::where('role', 'perawat')
        ->leftJoin('perawat_pekerjaans', 'users.id', '=', 'perawat_pekerjaans.user_id')
        ->select(
            'users.id',
            'users.name',
            'perawat_pekerjaans.unit_kerja'
        )
        ->orderBy('users.name', 'asc')
        ->get();

    return view('admin.lisensi.edit', compact('data', 'users'));
}

    public function lisensiUpdate(Request $request, $id)
    {
        $lisensi = PerawatLisensi::findOrFail($id);

        $request->validate([
            'user_id'             => 'required|exists:users,id',
            'nama'                => 'required|string|max:100',
            'lembaga'             => 'required|string|max:100',
            'nomor'               => 'required|string|max:100',
            'metode_perpanjangan' => 'required|in:pg_only,pg_interview,interview_only',
            'tgl_terbit'          => 'required|date',
            'tgl_expired'         => 'required|date',
            'dokumen'             => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'bidang'              => 'required|string|max:100',

            // --- UBAH DISINI: Validasi Array KFK Update ---
            'kfk'   => 'required|array',
            'kfk.*' => 'in:Pra PK,Pra BK,PK 1,PK 1.5,PK 2,PK 2.5,PK 3,PK 3.5,PK 4,PK 4.5,PK 5,BK 1,BK 1.5,BK 2,BK 2.5,BK 3,BK 3.5,BK 4,BK 4.5,BK 5',

            'tgl_mulai'           => 'required|date',
            'tgl_diselenggarakan' => 'required|date',
        ]);

        $data = $request->except(['dokumen', '_token', '_method']);
        $data['user_id'] = $request->user_id;

        // --- UBAH DISINI: Convert Array ke JSON String ---
        $data['kfk'] = json_encode($request->kfk);

        if ($request->hasFile('dokumen')) {
            if ($lisensi->file_path && Storage::disk('public')->exists($lisensi->file_path)) {
                Storage::disk('public')->delete($lisensi->file_path);
            }
            $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/lisensi', 'public');
        }

        $lisensi->update($data);
        return redirect()->route('admin.lisensi.index')->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'Lisensi diperbarui.']);
    }

    public function lisensiDestroy($id)
    {
        $data = PerawatLisensi::findOrFail($id);

        if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
            Storage::disk('public')->delete($data->file_path);
        }
        $data->delete();
        return redirect()->route('admin.lisensi.index')->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'Lisensi dihapus.']);
    }
}
