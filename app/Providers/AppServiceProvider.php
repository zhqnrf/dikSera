<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Tambahan
use Illuminate\Support\Facades\Auth; // Tambahan
use App\Models\Form; // Tambahan
use Carbon\Carbon; // Tambahan

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // View Composer: Logika ini akan berjalan di SEMUA file view (*.blade.php)
        // Jadi variabel $ujianActiveCount bisa dipakai di sidebar, header, dashboard, dll.
        View::composer('*', function ($view) {

            $ujianActiveCount = 0;

            // 1. Cek apakah user sedang login
            // 2. Cek apakah role-nya adalah 'perawat'
            if (Auth::check() && Auth::user()->role === 'perawat') {

                $ujianActiveCount = Form::where('status', 'publish')
                    ->where('waktu_selesai', '>', Carbon::now()) // Hanya yang belum kedaluwarsa
                    ->where(function($query) {
                        // Ambil yang targetnya 'semua' ATAU 'khusus' tapi user ini terdaftar
                        $query->where('target_peserta', 'semua')
                              ->orWhereHas('participants', function($q) {
                                  $q->where('users.id', Auth::id());
                              });
                    })
                    ->count();
            }

            // Kirim variabel ke view
            $view->with('ujianActiveCount', $ujianActiveCount);
        });
    }
}
