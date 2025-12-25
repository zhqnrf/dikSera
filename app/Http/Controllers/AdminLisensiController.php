<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerawatLisensi;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AdminLisensiController extends Controller
{
    public function lisensiCreate(Request $request, $metode = null)
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

        // Pilih view sesuai metode
        if ($metode === 'interview_only') {
            return view('admin.lisensi_interview_only.create', compact('users'));
        } else {
            return view('admin.lisensi_pg_interview.create', compact('users'));
        }
    }

    public function lisensiStore(Request $request, $metode = null)
    {
        $request->validate([
            'user_ids'            => 'required|array',
            'user_ids.*'          => 'exists:users,id',
            'nama'                => 'required|string|max:100',
            'lembaga'             => 'required|string|max:100',
            'bidang'              => 'required|string|max:100',
            'kfk'   => 'required|array',
            'kfk.*' => 'in:Pra PK,Pra BK,PK 1,PK 1.5,PK 2,PK 2.5,PK 3,PK 3.5,PK 4,PK 4.5,PK 5,BK 1,BK 1.5,BK 2,BK 2.5,BK 3,BK 3.5,BK 4,BK 4.5,BK 5',
            'tgl_mulai'           => 'required|date',
            'tgl_diselenggarakan' => 'required|date',
            'tgl_terbit'          => 'required|date',
            'tgl_expired'         => 'required|date',
        ]);

        $commonData = $request->except(['_token', 'user_ids']);
        $commonData['kfk'] = json_encode($request->kfk);
        // Set metode_perpanjangan sesuai route
        $commonData['metode_perpanjangan'] = $metode ?? 'pg_interview';

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
        // Redirect ke index sesuai metode
        $route = $metode === 'interview_only' ? 'admin.lisensi_interview_only.index' : 'admin.lisensi_pg_interview.index';
        return redirect()->route($route)->with('swal', [
            'icon'  => 'success',
            'title' => 'Berhasil',
            'text'  => "Lisensi berhasil dibuat untuk $count perawat dengan nomor urut otomatis."
        ]);
    }

    public function lisensiIndex(Request $request, $metode = null)
    {
        $query = PerawatLisensi::with('user');
        // Filter by metode_perpanjangan jika ada
        if ($metode) {
            $query->where('metode_perpanjangan', $metode);
        }
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
        if ($request->filled('status')) {
            if ($request->status == 'aktif') {
                $query->whereDate('tgl_expired', '>=', now());
            } elseif ($request->status == 'expired') {
                $query->whereDate('tgl_expired', '<', now());
            }
        }
        $data = $query->latest()->paginate(10);
        // Pilih view sesuai metode
        if ($metode === 'interview_only') {
            return view('admin.lisensi_interview_only.index', compact('data'));
        } else {
            return view('admin.lisensi_pg_interview.index', compact('data'));
        }
    }

    public function lisensiEdit($id, $metode = null)
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
        // Pilih view sesuai metode
        if ($metode === 'interview_only') {
            return view('admin.lisensi_interview_only.edit', compact('data', 'users'));
        } else {
            return view('admin.lisensi_pg_interview.edit', compact('data', 'users'));
        }
    }

    public function lisensiUpdate(Request $request, $id, $metode = null)
    {
        $lisensi = PerawatLisensi::findOrFail($id);
        $request->validate([
            'user_id'             => 'required|exists:users,id',
            'nama'                => 'required|string|max:100',
            'lembaga'             => 'required|string|max:100',
            'nomor'               => 'required|string|max:100',
            'tgl_terbit'          => 'required|date',
            'tgl_expired'         => 'required|date',
            'dokumen'             => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'bidang'              => 'required|string|max:100',
            'kfk'   => 'required|array',
            'kfk.*' => 'in:Pra PK,Pra BK,PK 1,PK 1.5,PK 2,PK 2.5,PK 3,PK 3.5,PK 4,PK 4.5,PK 5,BK 1,BK 1.5,BK 2,BK 2.5,BK 3,BK 3.5,BK 4,BK 4.5,BK 5',
            'tgl_mulai'           => 'required|date',
            'tgl_diselenggarakan' => 'required|date',
        ]);
        $data = $request->except(['dokumen', '_token', '_method']);
        $data['user_id'] = $request->user_id;
        $data['kfk'] = json_encode($request->kfk);
        $data['metode_perpanjangan'] = $metode ?? 'pg_interview';
        if ($request->hasFile('dokumen')) {
            if ($lisensi->file_path && Storage::disk('public')->exists($lisensi->file_path)) {
                Storage::disk('public')->delete($lisensi->file_path);
            }
            $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/lisensi', 'public');
        }
        $lisensi->update($data);
        $route = $metode === 'interview_only' ? 'admin.lisensi_interview_only.index' : 'admin.lisensi_pg_interview.index';
        return redirect()->route($route)->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'Lisensi diperbarui.']);
    }

    public function lisensiDestroy($id, $metode = null)
    {
        $data = PerawatLisensi::findOrFail($id);
        if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
            Storage::disk('public')->delete($data->file_path);
        }
        $data->delete();
        $route = $metode === 'interview_only' ? 'admin.lisensi_interview_only.index' : 'admin.lisensi_pg_interview.index';
        return redirect()->route($route)->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'Lisensi dihapus.']);
    }
}
