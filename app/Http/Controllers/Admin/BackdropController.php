<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackdropColor;
use App\Models\BackdropType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class BackdropController extends Controller
{
    /**
     * Display a listing of the resource.   
     */
    public function index(Request $request): Response
    {
        // TODO: filter by backdroptype plain - sequins - custom

        $backdropsQuery = DB::table('backdroptypes')
            ->join('backdropcolors', 'backdroptypes.id', '=', 'backdropcolors.backdroptype_id')
            ->select('backdropcolors.*', 'backdroptypes.name as backdroptype');

        $backdrops = $backdropsQuery->paginate(10);
        $backdroptypes = DB::table('backdroptypes')->get();

        return Inertia::render('Admin/BackdropList', [
            'backdrops' => $backdrops,  
            'backdroptypes' => $backdroptypes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeBackdrop(Request $request)
    {
        $request->validate([
            'backdroptype_id' => 'required|exists:backdroptypes,id',
            'color' => 'required|string',
            'image' => 'required|file|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->backdroptype_id == 1) {
            $directory = 'plain';
        } elseif ($request->backdroptype_id == 2) {
            $directory = 'sequins';
        } else {
            $directory = 'custom';
        }

        // small letters and replace space with underscore
        $color = strtolower(preg_replace('/\s+/', '_', $request->color));
        $fileName = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = $color . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/backdrop/' . $directory . '/'), $fileName);
        }

        BackdropColor::create([
            'backdroptype_id' => $request->backdroptype_id,
            'color' => $request->color,
            'image' => $fileName
        ]);

        return redirect()->back();
    }

    public function updateBackdrop(Request $request, BackdropColor $backdrop)
    {
        $request->validate([
            'backdroptype_id' => 'required|exists:backdroptypes,id',
            'color' => 'required|string',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->backdroptype_id == 1) {
            $directory = 'plain';
        } elseif ($request->backdroptype_id == 2) {
            $directory = 'sequins';
        } else {
            $directory = 'custom';
        }

            // small letters and replace space with underscore
        $color = strtolower(preg_replace('/\s+/', '_', $request->color));
        $fileName = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = $color . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/backdrop/' . $directory . '/'), $fileName);
        }

        $backdrop->fill([
            'backdroptype_id' => $request->backdroptype_id,
            'color' => $request->color,
            'image' => $fileName
        ]);

        if ($backdrop->save()) {
            return redirect()->back();
        }
    }

    public function indexType(): Response
    {

        $backdroptypes = DB::table('backdroptypes')->paginate(10);

        return Inertia::render('Admin/BackdropTypeList', [
            'backdroptypes' => $backdroptypes
        ]);
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

        return redirect()->back();
    }

    public function updateBackdropType(Request $request, BackdropType $backdroptype)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $backdroptype->fill($request->all());

        if ($backdroptype->save()) {
            return redirect()->back();
        }
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
   

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
