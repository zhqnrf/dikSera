<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ManajemenAkunController extends Controller
{
    /**
     * Menampilkan daftar user (Perawat)
     */
    public function index()
    {
        // Ambil user yang role-nya BUKAN admin (hanya perawat/user biasa)
        // Urutkan dari yang terbaru
        $users = User::where('role', '!=', 'admin')
                     ->orderBy('created_at', 'desc')
                     ->get();

        return view('admin.manajemen_akun.index', compact('users'));
    }

    /**
     * Proses Approve atau Reject
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_akun' => 'required|in:active,rejected,pending'
        ]);

        $user = User::findOrFail($id);
        $user->status_akun = $request->status_akun;
        $user->save();

        $pesan = '';
        if ($request->status_akun == 'active') {
            $pesan = 'Akun berhasil disetujui (Approved).';
        } elseif ($request->status_akun == 'rejected') {
            $pesan = 'Akun berhasil ditolak (Rejected).';
        } else {
            $pesan = 'Status akun diubah menjadi Pending.';
        }

        return redirect()->back()->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => $pesan,
        ]);
    }

    /**
     * Hapus user jika diperlukan (Opsional)
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('swal', [
            'icon' => 'success',
            'title' => 'Dihapus',
            'text' => 'Data akun pengguna berhasil dihapus.',
        ]);
    }
}
