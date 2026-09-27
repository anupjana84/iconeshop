<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Storage;

class CategoryController extends Controller
{
    // Fetch GST by category ID (AJAX)
    public function getGst($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['status' => 0, 'message' => 'Category not found']);
        }

        return response()->json([
            'status' => 1,
            'gst' => $category->gst
        ]);
    }
    public function categoryList(Request $request)
    {
        $page_title = "Category List";
    
        // Start query
        $query = Category::query();
    
        // 🔍 Search filter (name, hsn_code, gst)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('hsn_code', 'like', "%{$search}%")
                  ->orWhere('gst', 'like', "%{$search}%");
            });
        }
    
        // Get paginated results
        $categories = $query->orderBy('name')->paginate(20);
    
        // Pass data to view
        return view('admin.categories.categoryList', compact('page_title', 'categories'));
    }
    

    public function create()
    {
        $page_title = "Add New Category";
        $data= compact('page_title');
        return view('admin.categories.form', ['category' => new Category()])->with($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'hsn_code' => 'required|string|max:50',
            'gst' => 'required|integer|min:0|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'active' => 'required|boolean',
        ]);

        if ($request->hasFile('image')) {
            $folderPath = 'icon_computer/category_images';
                $localPath = $request->file('image')->store($folderPath, 'public');
                $category = env('APP_URL') . Storage::url($localPath);
        }
        Category::create([
            'name'=>$request->name,
            'hsn_code'=>$request->hsn_code,
            'gst'=>$request->gst,
            'image'=>$category ?? null,
            'active'=>$request->active,
        ]);

        return redirect()->route('category.list')->with('success', 'Category created successfully!');
    }

    public function edit($id)
    {
        $page_title = "Edit Category";
        $category = Category::findOrFail($id);
        return view('admin.categories.form', compact('category','page_title'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'hsn_code' => 'required|string|max:50',
            'gst' => 'required|integer|min:0|max:100',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'active' => 'required|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($category->image) {
                $oldImagePath = str_replace(env('APP_URL') . '/storage/', '', $category->image);
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
                $folderPath = 'icon_computer/category_images';
                $localPath = $request->file('image')->store($folderPath, 'public');
                $category->image = env('APP_URL') . Storage::url($localPath);
            }else{
                $folderPath = 'icon_computer/category_images';
                $localPath = $request->file('image')->store($folderPath, 'public');
                $category->image = env('APP_URL') . Storage::url($localPath);
            }
        }

        $category->name=$request->name;
        $category->hsn_code=$request->hsn_code;
        $category->gst=$request->gst;
        $category->active=$request->active;
        $category->save();

        return redirect()->route('category.list')->with('success', 'Category updated successfully!');
    }
}
