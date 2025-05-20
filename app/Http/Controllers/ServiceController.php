<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\Category;

use Illuminate\Validation\Rule;
use File;


use Redirect;
use Auth;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::latest()->get();

        return view('backend.service.index',compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::whereStatus(1)->get();
        return view('backend.service.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'       => 'required',
            'title'             => 'required|string|max:200',
            'slug'              => 'required|unique:services,slug',
            'duration'          => 'required|integer|min:1',
            'image'             => 'nullable|image|mimes:jpg,png,jpeg,gif,svg,webp|max:2048',
            'excerpt'           => 'nullable',
            'body'              => 'nullable',
            'meta_title'        => 'nullable',
            'meta_description'  => 'nullable',
            'meta_keywords'     => 'nullable',
            'price'             => 'required|numeric|min:0', // Validation for price field
            'sale_price'        => 'nullable|numeric|min:0', // Validation for price field
            'featured'          => 'nullable',
            'status'            => 'nullable',
            'other'             => 'nullable',
        ]);

        $data['featured'] = $request->featured ?? 0;
        $data['status'] = $request->status ?? 0;
        $data['excerpt'] = $request->excerpt ?? '';

        if($request->file('image'))
        {
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/images/service/'),$imageName);
            $data['image'] = $imageName;
        }

        Service::create($data);
        return redirect()->route('service.index')->with('success','Service has been added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        $categories = Category::whereStatus(1)->get();
        return view('backend.service.edit',compact('service','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'category_id'       => 'required',
            'title'             => 'required|string|max:200',
            'slug'              => ['required', Rule::unique('services')->ignore($service->id)],
            'image'             => 'nullable|image|mimes:jpg,png,jpeg,gif,svg,webp|max:2048',
            'excerpt'           => 'nullable',
            'body'              => 'nullable',
            'meta_title'        => 'nullable',
            'meta_description'  => 'nullable',
            'meta_keywords'     => 'nullable',
            'price'             => 'required|numeric|min:0', // Validation for price field
            'sale_price'        => 'nullable|numeric|min:0', // Validation for price field
            'featured'          => 'nullable',
            'status'            => 'nullable',
            'other'             => 'nullable',
        ]);

        $data['featured'] = $request->featured ?? 0;
        $data['status'] = $request->status ?? 0;
        $data['excerpt'] = $request->excerpt ?? '';

        if($request->file('image'))
        {
            $destination = public_path('uploads/images/service/').$service->image;
            if(File::exists($destination))
            {
                File::delete($destination);
            }

            //create unique name of image
            $imageName = time().'.'.$request->image->getClientOriginalExtension();

            //move image to path you wish -- it auto generate folder
            $request->image->move(public_path('uploads/images/service/'), $imageName);
            $data['image'] = $imageName;
        }

        $service->update($data);
        return redirect()->route('service.index')->withSuccess('Service has been updated successfully.');

    }



    public function destroy(Service $service)
    {

        $service->delete();
        return back()->withSuccess('Service Succesfully moved to trash!');
    }

    public function trashView(Request $request)
    {
        $services = Service::onlyTrashed()->latest()->get();
        return view('backend.service.trash',compact('services'));
    }

    // restore data
    public function restore($id)
    {
        $data = Service::withTrashed()->find($id);
        if(!is_null($data)){
            $data->restore();
        }
        return redirect()->back()->with("success", "Data Restored Succesfully");
    }

    public function force_delete(Request $request, $id)
    {
        $service = Service::withTrashed()->find($id);

         // Check if the category has any services
         if ($service->appointments->count() > 0) {
            return redirect()->back()->withErrors('Cannot deleted permanently, service with existing bookings.');
        }

        if (!is_null($service)) {

            // Remove image
            $destination = public_path('uploads/images/service/') . $service->image;
            if (\File::exists($destination)) {
                \File::delete($destination);
            }

            $service->forceDelete();
        }

        return redirect()->back()->with("success", "Data Deleted Permanently!!");
    }



}
