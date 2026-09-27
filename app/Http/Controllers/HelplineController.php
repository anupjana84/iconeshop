<?php

namespace App\Http\Controllers;

use App\Models\HelplineNumber;
use Illuminate\Http\Request;

class HelplineController extends Controller
{
    // Show list
    public function index()
    {
        $page_title = 'Helpline Numbers';
        $data = HelplineNumber::all();
        return view('admin.helpline.index', compact('data', 'page_title'));
    }

    // Show create page
    public function create()
    {
        return view('admin.helpline.create');
    }

    // Store new data
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phones' => 'required|array',
            'phones.*' => 'required|string',
        ]);

        HelplineNumber::create([
            'name' => $request->name,
            'phones' => $request->phones,
        ]);

        return redirect()->route('tollfree.index')->with('success', 'Added Successfully!');
    }

    // Show edit page
    public function edit($id)
    {
        $item = HelplineNumber::findOrFail($id);
        return view('admin.helpline.edit', compact('item'));
    }

    // Update record
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'phones' => 'required|array',
            'phones.*' => 'required|string',
        ]);

        $item = HelplineNumber::findOrFail($id);
        $item->update([
            'name' => $request->name,
            'phones' => $request->phones,
        ]);

        return redirect()->route('tollfree.index')->with('success', 'Updated Successfully!');
    }

    // Delete
    public function destroy($id)
    {
        HelplineNumber::destroy($id);
        return redirect()->route('tollfree.index')->with('success', 'Deleted Successfully!');
    }
}
