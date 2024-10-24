<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use DateTime;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : Response
    {
        $registrationQuery = Registration::query();

        $this->applySearch($registrationQuery, $request->search);

        $events = $registrationQuery->orderBy('created_at', 'desc')->paginate(10);

        $events->getCollection()->transform(function ($event) {
            $event->user = DB::table('users')->where('id', $event->user_id)->first();   
            $event->date = (new DateTime($event->date))->format('m-d-Y');
            $event->time = (new DateTime($event->time))->format('g:i A');
            return $event;
        }); 

        return Inertia::render('Admin/Registration', [
            'events' => $events,
            'search' => $request->search ?? '',
        ]);
    }

    protected function applySearch($query, $search)
    {
        return $query->when($search, function($query, $search){
            $query->where('event', 'LIKE', '%'.$search.'%')
            ->orWhere('contactperson', 'LIKE', '%'.$search.'%')
            ->orWhere('address', 'LIKE', '%'.$search.'%')
            ->orWhere('date', 'LIKE', '%'.$search.'%')
            ->orWhere('time', 'LIKE', '%'.$search.'%');
        });
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
    public function update(Request $request, Registration $event) : RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Pending,Accepted,Declined'],
            'user_id' => ['required', 'integer'], 
        ]);

        $event->status = $validated['status'];
        $event->user_id = $validated['user_id'];

        $event->save();
    return Redirect::route('event.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
