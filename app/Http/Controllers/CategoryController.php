<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::latest()->get();
        return view('backend.category.index',compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'body' => 'nullable|string',
            'featured' => 'nullable|boolean',
            'status' => 'nullable|boolean',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'other' => 'nullable',
        ]);

        $data['featured'] = $request->featured ?? 0;
        $data['status'] = $request->status ?? 0;
        $data['body'] = $request->body ?? '';
        $data['name'] = $request->title;

        if($request->hasFile('image'))
        {
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/images/category/'),$imageName);
            $data['image'] = $imageName;
        }

        Category::create($data);
        return redirect()->route('category.index')->withSuccess('Category has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $categories = Category::where('parent_id',null)->orderby('title','asc')->get();
        return view('backend.category.show',compact('category','categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('backend.category.edit',compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['required', Rule::unique('categories')->ignore($category)],
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'body' => 'nullable|string',
            'featured' => 'nullable|boolean',
            'status' => 'nullable|boolean',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'other' => 'nullable',
        ]);

        $data['featured'] = $request->featured ?? 0;
        $data['status'] = $request->status ?? 0;
        $data['body'] = $request->body ?? '';

        if($request->delete_image)
        {
            $destination = public_path('uploads/images/category/'.$category->image);
            if(\File::exists($destination))
            {
                \File::delete($destination);
            }

            $data['image'] =  '';

        }

        if($request->hasFile('image')){
            // delete old image
            $destination = public_path('uploads/images/category/'.$category->image);
            if(\File::exists($destination))
            {
                \File::delete($destination);
            }

            //add new image
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/images/category/'),$imageName);
            $data['image'] = $imageName;

        }
        $category->update($data);
        return redirect()->route('category.index')->with('success', 'Category has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if($category->services->count())
       {
            return back()->withErrors('Category cannot be deleted as it is linked to services.');
       }

        $destination = public_path('uploads/images/category/'.$category->image);
        if(\File::exists($destination))
        {
            \File::delete($destination);
        }
        $category->delete();
        return redirect()->back()->with('success', 'Category has been deleted successfully.');
    }
}
