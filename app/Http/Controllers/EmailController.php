<?php

namespace App\Http\Controllers;

use App\Models\EmailConfiguration;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function index()
    {
        $email = EmailConfiguration::first();

        return view('master.email.index', compact('email'));
    }
    
    public function update(Request $request)
    {
        $validated = $request->validate([
            'driver'     => 'required',
            'host'       => 'required',
            'port'       => 'required',
            'username'   => 'required',
            'password'   => 'required',
            'encryption' => 'required',
        ]);

        $email = EmailConfiguration::first();

        if ($email) {
            $email->update($validated);
        } else {
            EmailConfiguration::create($validated);
        }

        return response()->json(['message' => 'Email configuration saved successfully.']);
    }

}
