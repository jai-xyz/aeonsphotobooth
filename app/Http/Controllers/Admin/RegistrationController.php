<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserAcceptNotification;
use App\Mail\UserDeclineNotification;
use DateTime;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Packages;
use Illuminate\Support\Facades\Mail;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
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

        // Retrieve packages with options
        $packages = Packages::with('options')->get();

        return Inertia::render('Admin/Registration', [
            'events' => $events,
            'search' => $request->search ?? '',
        ]);
    }

    protected function applySearch($query, $search)
    {
        return $query->when($search, function ($query, $search) {
            $query->where('event', 'LIKE', '%' . $search . '%')
                ->orWhere('contactperson', 'LIKE', '%' . $search . '%')
                ->orWhere('address', 'LIKE', '%' . $search . '%')
                ->orWhere('date', 'LIKE', '%' . $search . '%')
                ->orWhere('time', 'LIKE', '%' . $search . '%');
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
    public function show($id)
    {
        $registration = Registration::findOrFail($id);
        return response()->json($registration);
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
    public function update(Request $request, Registration $event): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Pending,Accept,Decline'],
            'user_id' => ['required', 'integer'],
        ]);

        $event->status = $validated['status'];
        $event->user_id = $validated['user_id'];
        $email = $event->email;
        
        $event->save();

        if ($event->status === 'Accept') {
            Mail::to($email)->send(new UserAcceptNotification($event));
        } elseif ($event->status === 'Decline') {
            Mail::to($email)->send(new UserDeclineNotification($event));
        }

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
