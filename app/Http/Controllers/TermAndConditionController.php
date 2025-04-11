<?php

namespace App\Http\Controllers;

use App\Models\TermAndCondition;
use Illuminate\Http\Request;

class TermAndConditionController extends Controller
{
    public function add() {
        $term = TermAndCondition::first();
        return view('master.termandcondition.create', compact('term'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'text'  => 'required|string',
        ]);

        $term = TermAndCondition::first();

        if ($term) {
            $term->update(['text' => $validated['text']]);
        } else {
            $term = TermAndCondition::create(['text' => $validated['text']]);
        }

        return response()->json(['message' => 'Data berhasil disimpan!'], 200);
    }
    
}
