<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Packages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PackageController extends Controller
{   
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $packages = Packages::all();
        return Inertia::render('Admin/Package', [
            'packages' => $packages,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/AddPackage');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|string|max:255',
            'duration' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'size2' => 'nullable|string|max:255',
            'size3' => 'nullable|string|max:255',
            'size4' => 'nullable|string|max:255',
            'size5' => 'nullable|string|max:255',
            'inclusion' => 'required|string|max:2500',
            'note' => 'required|string|max:255',
            'extension' => 'required|string|max:255',
        ]);

        $sizes = explode(', ', $request->input('size'));

        if (count($sizes) > 1) {
            $request->merge([
                'size' => $sizes[0],
                'size2' => $sizes[1] ?? null,
                'size3' => $sizes[2] ?? null,
                'size4' => $sizes[3] ?? null,
                'size5' => $sizes[4] ?? null,
            ]);
        }

        
        Packages::create([
            'name' => $request->name,
            'price' => $request->price,
            'duration' => $request->duration,
            'size' => $request->size,
            'size2' => $request->size2,
            'size3' => $request->size3,
            'size4' => $request->size4,
            'size5' => $request->size5,
            'inclusion' => $request->inclusion,
            'note' => $request->note,
            'extension' => $request->extension,
        ]);

        return redirect()->route('package.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Packages $package) : Response
    {
        return Inertia::render('Admin/EditPackage', [
            'package' => $package,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Packages $package): RedirectResponse
    {
       $validatedData =  $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|string|max:255',
            'duration' => 'required|string|max:255',    
            'size' => 'required|string|max:255',
            'inclusion' => 'required|string|max:2500',
            'note' => 'required|string|max:255',
            'extension' => 'required|string|max:255',
        ]);

        $package->fill($validatedData);
        
        if ($package->save()) {
            return redirect()->route('package.index');
        } else {
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
