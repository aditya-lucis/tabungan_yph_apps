<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class CheckEmployeeAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->role === 'krw') {
            $routeName = $request->route()->getName();

            // 🔒 blok akses home
            if ($routeName === 'homeindex') {
                return redirect()->route('employee.show', ['encrypted' => Crypt::encryptString($user->id_employee)])
                                 ->with('error', 'Unauthorized access.');
            }

            // 🔒 blok akses employee.index
            if ($routeName === 'employee.index') {
                return redirect()->route('employee.show', ['encrypted' => Crypt::encryptString($user->id_employee)])
                                 ->with('error', 'Unauthorized access.');
            }

            // 🔒 cek detail employee atau pengajuan
            $encrypted = $request->route('employee') ?? $request->route('id');

            if ($encrypted) {
                try {
                    $requestedId = Crypt::decryptString($encrypted);
                } catch (\Exception $e) {
                    return redirect()->route('employee.show', ['encrypted' => Crypt::encryptString($user->id_employee)])
                                     ->with('error', 'Invalid ID.');
                }

                // jika ID tidak sesuai
                if ((int)$requestedId !== (int)$user->id_employee) {
                    return redirect()->route('employee.show', ['encrypted' => Crypt::encryptString($user->id_employee)])
                                     ->with('error', 'Unauthorized access.');
                }

                // ganti parameter route menjadi ID asli supaya controller bisa pakai
                if ($request->route('employee')) {
                    $request->route()->setParameter('employee', $requestedId);
                }
                if ($request->route('id')) {
                    $request->route()->setParameter('id', $requestedId);
                }
            }
        }

        return $next($request);
    }
}
