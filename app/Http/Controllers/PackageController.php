<?php

namespace App\Http\Controllers;

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

        return Inertia::render('Admin/Packages', [
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
            'inclusion' => 'required|string|max:2500',
            'note' => 'required|string|max:255',
            'extension' => 'required|string|max:255',
        ]);

        Packages::create([
            'name' => $request->name,
            'price' => $request->price,
            'duration' => $request->duration,
            'size' => $request->size,
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
            // Redirect to the package index route if the update is successful
            return redirect()->route('package.index')->with('success', 'Package updated successfully.');
        } else {
            // Redirect back with an error message if the update fails
            return redirect()->back()->with('error', 'Failed to update package.');
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
