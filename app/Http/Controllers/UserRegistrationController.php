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

class UserRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
{  
        $userId = Auth::id();

        $events = DB::table('registrations')->where('user_id', $userId)->get();

        $events = $events->map(function ($event){
            $event->user = DB::table('users')->where('id', $event->user_id)->first();   
            $event->date = (new DateTime($event->date))->format('m-d-Y');
            $event->time = (new DateTime($event->time))->format('g:i A');
            return $event;
        }); 

        return Inertia::render('User/List', ['events' => $events]);            
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create($packageId = null): Response
    {    
        $pkg = $packageId ? DB::table('packages')->where('id', $packageId)->first() : (object) [];
        

        $getEvents = DB::table('registrations')->get(); 
    
        $backdropTypes = DB::table('backdroptypes')->get();
        
        $backdropColors = DB::table('backdropcolors')
            ->join('backdroptypes', 'backdropcolors.backdroptype_id', '=', 'backdroptypes.id')
            ->select('backdropcolors.*', 'backdroptypes.name as backdroptype_name')
            ->get();
    
        return Inertia::render('User/Registration', [
            'pkg' => $pkg,
            'getEvents' => $getEvents,
            'backdropTypes' => $backdropTypes,
            'backdropColors' => $backdropColors
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {

         $request->validate([
            'event' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contactperson' => 'required|string|max:255',
            'contactno' => 'required|string|max:100',
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'hour' => 'required|string',
            'minute' => 'required|string',
            'ampm' => 'required|string|in:AM,PM',
            'packageid' => 'required|exists:packages,id',
            'packagename' => 'required|string|max:255',
            'packagesize' => 'required|string|max:255',
            'backdroptype' => 'required|string|max:255',
            'backdropcolor' => 'required|string|max:255',
            'suggestion' => 'required|string|max:255',
        ],
        [
            'date.after_or_equal' => 'Date must be a future date.',
        ]
    );

            
         // Convert hour, minute, and ampm to a 24-hour format time string
        $hour = $request->hour;
        $minute = $request->minute;
        $ampm = $request->ampm;

        if ($ampm === 'PM' && $hour != 12) {
            $hour += 12;
        } elseif ($ampm === 'AM' && $hour == 12) {
            $hour = 0;
        }

        // $hour = 5;
        // $minute = 7;
        $time = sprintf('%02d:%02d', $hour, $minute);
        // Output: 05:07

        $userId = Auth::id();

        Registration::create([
            'user_id' => $userId,
            'event' => $request->event,
            'address' => $request->address,
            'contactperson' => $request->contactperson,
            'contactno' => $request->contactno,
            'date' => $request->date,
            'time' => $time,
            'packageid' => $request->packageid,
            'packagename' => $request->packagename,
            'packagesize' => $request->packagesize,
            'backdroptype' => $request->backdroptype,
            'backdropcolor' => $request->backdropcolor,
            'suggestion' => $request->suggestion,
        ]);

        return redirect()->route('user.event.index');
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
