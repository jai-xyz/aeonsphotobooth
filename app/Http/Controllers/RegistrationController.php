<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Event/List');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Event/Registration');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {

         $request->validate([
            'event' => 'required|string|max:255',
            'date' => 'required|string|max:255',
            'time' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'backdroptype' => 'required|string|max:255',
            'backdropcolor' => 'required|string|max:255',
            'numofpics' => 'required|string|max:255',
            'suggestion' => 'required|string|max:255',
        ]);

        $userId = auth()->id();
        
        Registration::create([
            'user_id' => $userId,
            'event' => $request->event,
            'date' => $request->date,
            'time' => $request->time,
            'address' => $request->address,
            'backdroptype' => $request->backdroptype,
            'backdropcolor' => $request->backdropcolor,
            'numofpics' => $request->numofpics,
            'suggestion' => $request->suggestion,
        ]);

        return redirect(route('event', [], false));
    }

    /**
     * Display the specified resource.
     */
    public function show(Registration $registration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Registration $registration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Registration $registration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Registration $registration)
    {
        //
    }
}
