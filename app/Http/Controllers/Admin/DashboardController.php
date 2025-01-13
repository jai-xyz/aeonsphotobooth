<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use DateTime;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // SALES BY LOCATION
        $eventsalelocation = Registration::query()
            ->select('province', DB::raw('SUM(price) as total_price'))
            ->where('status', 'Complete')
            ->where('payment_status', 'paid')
            ->groupBy('province')
            ->orderBy('total_price', 'desc')
            ->get();

        // EVENT STATUS PENDING - COUNT
        $eventstatuspending = Registration::query()
            ->where('status', 'Pending')
            ->where('payment_status', 'Pending')
            ->count();

        // EVENT STATUS ACCEPTED - COUNT
        $eventstatusaccepted = Registration::query()
            ->where('status', 'Accept')
            ->where('payment_status', 'Pending')
            ->count();

        // EVENT STATUS LISTED - COUNT
        $eventstatuslisted = Registration::query()
            ->where('status', 'Accept')
            ->where('payment_status', 'paid')
            ->count();

        // EVENT STATUS LISTED - COUNT
        $eventstatuscomplete = Registration::query()
            ->where('status', 'Complete')
            ->where('payment_status', 'paid')
            ->count();

        // USERS - COUNT
        $usercount = User::query()
            ->where('usertype', 'user')
            ->count();


        // CALENDAR
        $this->updateExpiredEvents();

        // $registrationQuery = Registration::query()
        //     ->where('status', 'Accept')
        //     ->where('payment_status', 'paid');

        // $events = $registrationQuery->orderBy('created_at', 'desc')->get();

        // $events->getCollection()->transform(function ($event) {
        //     $event->user = DB::table('users')->where('id', $event->user_id)->first();
        //     $event->date = (new DateTime($event->date))->format('m-d-Y');
        //     $event->time = (new DateTime($event->time))->format('g:i A');
        //     return $event;
        // });

        $getEvents = DB::table('registrations')->where('status', 'Accept')->where('payment_status', 'paid')->get();


        return Inertia::render('Admin/Dashboard', [
            'eventsalelocation' => $eventsalelocation,
            'eventstatuspending' => $eventstatuspending,
            'eventstatusaccepted' => $eventstatusaccepted,
            'eventstatuslisted' => $eventstatuslisted,
            'eventstatuscomplete' => $eventstatuscomplete,
            'usercount' => $usercount,

            // VUECALENDAR
            'getEvents' => $getEvents,
        ]);
    }

    // TO AUTOMATICALLY UPDATE EXPIRED EVENTS TO COMPLETE STATUS
    public function updateExpiredEvents()
    {
        // Get the current date
        $currentDate = now()->format('Y-m-d');

        // Find events that are not on the current date, have a status of 'Accept', and payment_status of 'paid'
        $events = Registration::where('status', 'Accept')
            ->where('payment_status', 'paid')
            ->where('date', '<', $currentDate)
            ->get();

        foreach ($events as $event) {
            $originalStatus = $event->status;
            $event->status = 'Complete';
            $event->save();

            if ($event->wasChanged('status')) {
                Log::info("Event '{$event->event}' (ID: {$event->id}) status updated from '{$originalStatus}' to 'Complete'.");
            } else {
                Log::warning("Failed to update status for event '{$event->event}' (ID: {$event->id}).");
            }
        }

        Log::info('Expired events update process completed.');
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
