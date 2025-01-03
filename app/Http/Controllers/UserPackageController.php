<?php

namespace App\Http\Controllers;

use App\Models\Packages;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($packageId): Response
    {
        $package = Packages::with('options')->find($packageId);
        if (!$package) {
            abort(404, 'Package not found');
        }
    
        $getAllPackages = Packages::with('options')->get();


        return Inertia::render(
            'Packages',
            [
                'packages' => $package,
                'getAllPackages' => $getAllPackages,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
