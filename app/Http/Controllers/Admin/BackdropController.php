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
    /** #####################################
           * BACKDROP LIST AND COLOR CRUD
    */ ######################################
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

        return redirect()->back()->with('success', 'Backdrop added successfully');
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
    $fileName = $backdrop->image; // Default to existing image

    // Unlink the existing file if it exists and a new file is uploaded
    if ($request->hasFile('image')) {
        if ($backdrop->image) {
            $existingFilePath = public_path('uploads/backdrop/' . $directory . '/' . $backdrop->image);
            if (file_exists($existingFilePath)) {
                unlink($existingFilePath);
            }
        }

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
        return redirect()->back()->with('success', 'Backdrop updated successfully.');
    }

    return redirect()->back()->with('error', 'Failed to update backdrop.');
}

    public function destroyBackdrop(BackdropColor $backdrop): RedirectResponse
    {
        if ($backdrop->delete()) {
            return redirect()->back()->with('success', 'Backdrop deleted successfully.');
        }
        return redirect()->back()->with('error', 'Failed to delete this backdrop.');
    }


    /** #####################################
             * BACKDROP TYPE CRUD
    */ ######################################

    public function indexType(): Response
    {

        $backdroptypes = DB::table('backdroptypes')->paginate(10);
        
        return Inertia::render('Admin/BackdropTypeList', [
            'backdroptypes' => $backdroptypes
        ]);
    }

    public function storeType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:backdroptypes,name,'
        ]);

        BackdropType::create($request->all());

        return redirect()->back()->with('success', 'Backdrop type added successfully.');
    }

    public function updateBackdropType(Request $request, BackdropType $backdroptype)
    {
        $request->validate([
            'name' => 'required|string|unique:backdroptypes,name,'
        ]);

        $backdroptype->fill($request->all());

        if ($backdroptype->save()) {
            return redirect()->back()->with('success', 'Backdrop type updated successfully.');
        }
    }

    public function destroyBackdropType(BackdropType $backdroptype): RedirectResponse
    {
        if ($backdroptype->delete()) {
            return redirect()->back()->with('success', 'Backdrop type deleted successfully.');
        }
        return redirect()->back()->with('error', 'Failed to delete this backdrop type.');
    }
}
