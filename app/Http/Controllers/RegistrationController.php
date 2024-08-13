<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {  
        $userId = Auth::id();
        
        $packages = 

        $events = DB::table('registrations')->where('user_id', $userId)->get();

        $events = $events->map(function ($event){
            $event->user = DB::table('users')->where('id', $event->user_id)->first();   
            $event->date = (new DateTime($event->date))->format('m-d-Y');
            $event->time = (new DateTime($event->time))->format('g:i A');
            return $event;
        }); 

        return Inertia::render('Event/List', ['events' => $events]);
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
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'address' => 'required|string|max:255',
            'backdroptype' => 'required|string|max:255',
            'backdropcolor' => 'required|string|max:255',
            'suggestion' => 'required|string|max:255',
        ],
        [
            'date.after_or_equal' => 'Date must be a future date.',
        ]
    );
            
         $userId = Auth::id();

        Registration::create([
            'user_id' => $userId,
            'event' => $request->event,
            'date' => $request->date,
            'time' => $request->time,
            'address' => $request->address,
            'backdroptype' => $request->backdroptype,
            'backdropcolor' => $request->backdropcolor,
            'suggestion' => $request->suggestion,
        ]);

        return redirect()->route('event.index');
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
