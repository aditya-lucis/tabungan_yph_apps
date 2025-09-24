<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmployeeAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 🔒 cek akses employee/{employee} detail
        $requestedParam = $request->route('id') ?? $request->route('employee');

        if ($user && $user->role === 'krw') {

            $routeName = $request->route()->getName();

            // 🔒 blok akses home
            if ($routeName === 'homeindex') {
                return redirect()->route('employee.show', $user->id_employee)
                    ->with('error', 'Unauthorized access.');
            }

            // 🔒 blok akses employee.index
            if ($routeName === 'employee.index') {
                return redirect()->route('employee.show', $user->id_employee)
                    ->with('error', 'Unauthorized access.');
            }

            if ($requestedParam) {
                $requestedId = is_object($requestedParam) ? $requestedParam->id : $requestedParam;

                if ((int) $requestedId !== (int) $user->id_employee) {
                    return redirect()->route('employee.show', $user->id_employee)
                    ->with('error', 'Unauthorized access.');
                }
            }
        }

        return $next($request);
    }
}
