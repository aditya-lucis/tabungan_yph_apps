<?php

namespace App\Providers;

use App\Models\EmailConfiguration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class CustomMailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('email_configurations')) {
            $emailConfig = EmailConfiguration::first();

            if ($emailConfig) {
                Config::set('mail.mailers.smtp', [
                    'transport' => $emailConfig->driver,
                    'host' => $emailConfig->host,
                    'port' => $emailConfig->port,
                    'encryption' => $emailConfig->encryption,
                    'username' => $emailConfig->username,
                    'password' => $emailConfig->password,
                    'timeout' => null,
                    'auth_mode' => null,
                ]);

                Config::set('mail.default', 'smtp');
                Config::set('mail.from.address', $emailConfig->username);
                Config::set('mail.from.name', 'Tabungan Pendidikan Yayasan Persada Hati');
            }
        }
    }
}
