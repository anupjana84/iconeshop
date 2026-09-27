<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enquary;

class EnquaryController extends Controller
{
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'msg' => 'required',
            'agree' => 'required'
        ]);

        // Save data
        Enquary::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'loc' => $request->loc,
            'msg' => $request->msg
        ]);

        return back()->with('success', 'Your enquiry submitted successfully!');
    }
}