<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackdropColor;
use App\Models\BackdropType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class BackdropController extends Controller
{
    /**
     * Display a listing of the resource.   
     */
    public function index() : Response
    {

        $backdrops = DB::table('backdroptypes')
                    ->join('backdropcolors', 'backdroptypes.id', '=', 'backdropcolors.backdroptype_id')
                    ->select('backdropcolors.*', 'backdroptypes.name as backdroptype')
                    ->get();

        // dd($backdrops);

        return Inertia::render('Admin/Backdrop', [
            'backdrops' => $backdrops
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createType()
    {
        return Inertia::render('Admin/AddBackdropType');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeType(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        BackdropType::create($request->all());

        return redirect()->route('backdrop.index');
    }


       /**
     * Show the form for creating a new resource.
     */
    public function createColor()
    {
        $backdropTypes = BackdropType::all();

        return Inertia::render('Admin/AddBackdropColor', [
                            'backdroptypes' => $backdropTypes
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeColor(Request $request)
    {
        $request->validate([
            'backdroptype_id' => 'required|exists:backdroptypes,id',
            'color' => 'required|string'
        ]);

        BackdropColor::create($request->all());

        return redirect()->route('backdrop.index');
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
