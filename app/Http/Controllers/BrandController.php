<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function brandCreate()
    {
        $page_title = "Add Brand";
        $url = route('brand.store');
        $data = compact('page_title', 'url');
        return view('admin.brand.brandCreate')->with($data);
    }
    public function brandList(Request $request)
{
    $page_title = "Brand List";
    $url = route('brand.store');
    $search = $request->search;

    $brands = Brand::query()
        ->when($search, function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%");
        })
        ->orderBy('name')
        ->paginate(20)
        ->withQueryString(); // ⭐ THIS FIXES PAGE 2+ ISSUE

    return view('admin.brand.brandList', compact(
        'page_title',
        'brands',
        'url',
        'search'
    ));
}



    public function brandStore(Request $request)
    {

        $request->validate([
            'brand' => 'required|unique:brands,name',
        ]);

        $brand = new Brand();
        $brand->name = $request->brand;
        $brand->save();

        return redirect()->route('brand.list')->with('success', 'Brand added successfully.');
    }

    public function brandEdit($id)
    {
        $brand = Brand::findOrFail($id);
        $page_title = "Edit Brand";
        $url = route('brand.update', $id);
        $data = compact('page_title', 'brand', 'url');
        return view('admin.brand.brandCreate')->with($data);
    }

    public function brandUpdate(Request $request, $id)
    {
        $request->validate([
            'brand' => 'required|unique:brands,name,' . $id,
        ]);

        $brand = Brand::findOrFail($id);
        $brand->name = $request->brand;
        $brand->save();

        return redirect()->route('brand.list')->with('success', 'Brand updated successfully.');
    }

}
