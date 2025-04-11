<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                
                // Jika role 'krw' dan punya id_employee, ubah home ke 'employee.show' (bukan URL langsung)
                if ($user->role === 'krw' && $user->id_employee) {
                    $homeRoute = ['name' => 'employee.show', 'params' => ['employee' => $user->id_employee]];
                } else {
                    $homeRoute = ['name' => 'homeindex', 'params' => []];
                }
    
                // Ambil menu dari config
                $navbar = config('navbar.menu');
    
                // Ubah route home
                $navbar[0]['route'] = $homeRoute;
    
                // Kirim navbar ke semua view
                $view->with('navbarMenu', $navbar);
            }
        });
    }
}
