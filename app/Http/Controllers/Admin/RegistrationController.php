<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserAcceptNotification;
use App\Mail\UserDeclineNotification;
use App\Mail\CancelEventNotification;
use App\Mail\PaymentMail;
use DateTime;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Packages;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexPending(Request $request): Response
    {
        $registrationQuery = Registration::query()
            ->where('status', 'Pending')
            ->where('payment_status', 'Pending');

        $this->applySearch($registrationQuery, $request->search);

        $events = $registrationQuery->orderBy('created_at', 'desc')->paginate(10);

        $events->getCollection()->transform(function ($event) {
            $event->user = DB::table('users')->where('id', $event->user_id)->first();
            $event->date = (new DateTime($event->date))->format('m-d-Y');
            $event->time = (new DateTime($event->time))->format('g:i A');
            return $event;
        });

        return Inertia::render('Admin/PendingRegistration', [
            'events' => $events,
            'search' => $request->search ?? '',
        ]);
    }

    protected function applySearch($query, $search)
    {
        return $query->when($search, function ($query, $search) {
            $query->where('event', 'LIKE', '%' . $search . '%');
        });
    }


    public function indexAccepted(Request $request): Response
    {
        $registrationQuery = Registration::query()
            ->where('status', 'Accept')
            ->where('payment_status', 'Pending');

        $this->applySearch($registrationQuery, $request->search);

        $events = $registrationQuery->orderBy('created_at', 'desc')->paginate(10);

        $events->getCollection()->transform(function ($event) {
            $event->user = DB::table('users')->where('id', $event->user_id)->first();
            $event->date = (new DateTime($event->date))->format('m-d-Y');
            $event->time = (new DateTime($event->time))->format('g:i A');
            return $event;
        });

        return Inertia::render('Admin/AcceptedRegistration', [
            'events' => $events,
            'search' => $request->search ?? '',
        ]);
    }

    public function indexArchive(Request $request): Response
    {
        $registrationQuery = Registration::query()
            ->whereIn('status', ['Cancel', 'Complete', 'Decline']);

        $this->applySearch($registrationQuery, $request->search);

        $events = $registrationQuery->orderBy('updated_at', 'desc')->paginate(10);

        $events->getCollection()->transform(function ($event) {
            $event->user = DB::table('users')->where('id', $event->user_id)->first();
            $event->date = (new DateTime($event->date))->format('m-d-Y');
            $event->time = (new DateTime($event->time))->format('g:i A');
            return $event;
        });

        return Inertia::render('Admin/ArchiveRegistration', [
            'events' => $events,
            'search' => $request->search ?? '',
        ]);
    }


    public function index(Request $request): Response
    {
        $this->updateExpiredEvents();

        $registrationQuery = Registration::query()
            ->where('status', 'Accept')
            ->where('payment_status', 'paid');

        $this->applySearch($registrationQuery, $request->search);

        $events = $registrationQuery->orderBy('created_at', 'desc')->paginate(10);

        $events->getCollection()->transform(function ($event) {
            $event->user = DB::table('users')->where('id', $event->user_id)->first();
            $event->date = (new DateTime($event->date))->format('m-d-Y');
            $event->time = (new DateTime($event->time))->format('g:i A');
            return $event;
        });

        $getEvents = DB::table('registrations')->where('status', 'Accept')->where('payment_status', 'paid')->get();

        return Inertia::render('Admin/Registration', [
            'events' => $events,
            'getEvents' => $getEvents,
            'search' => $request->search ?? '',
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

    public function updateRestoreEvents(Request $request, Registration $event): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Pending,Accept,Decline,Cancel,Complete'],
            'user_id' => ['required', 'integer'],
        ]);

        $event->status = $validated['status'];
        $event->user_id = $validated['user_id'];

        $event->save();

        Mail::to('rhyaaaaa01072001@gmail.com')->queue(new CancelEventNotification($event));
        return redirect()->back()->with('success', 'Event has been restored.');
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

    // CANCEL LISTED EVENTS
    public function cancelListedEvents(Request $request, Registration $event): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Pending,Accept,Decline,Cancel,Complete'],
            'user_id' => ['required', 'integer'],
        ]);

        $event->status = $validated['status'];
        $event->user_id = $validated['user_id'];
        $email = $event->email;

        if ($event->save()) {
            Log::info("Event '{$event->event}' (ID: {$event->id}) status updated to 'Cancel'.");
        } else {
            Log::warning("Failed to update status for event '{$event->event}' (ID: {$event->id}).");
        }

        Mail::to($email)->queue(new CancelEventNotification($event));
        return redirect()->back()->with('success', 'The event was canceled successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Registration $event): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Pending,Accept,Decline,Cancel,Complete'],
            'user_id' => ['required', 'integer'],
        ]);

        $event->status = $validated['status'];
        $event->user_id = $validated['user_id'];
        $email = $event->email;

        $event->save();

        if ($event->status === 'Accept') {

            // PAYMONGO API

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.paymongo.com/v1/links",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    'data' => [
                        'attributes' => [
                            'amount' => 50000,
                            'description' => 'NON-REFUNDABLE DOWN PAYMENT',
                            'remarks' => 'Down payment for the event',
                        ]
                    ]
                ]),
                CURLOPT_HTTPHEADER => [
                    "accept: application/json",
                    "authorization: Basic c2tfdGVzdF8zZnI4VUNkR0Z5VHZVVmF2Tk5RMkF2QmU6",
                    "content-type: application/json"
                ]
            ]);

            $response = curl_exec($curl);
            $decoded = json_decode($response, true);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                $checkout_url = $decoded['data']['attributes']['checkout_url'];
                $reference_number = $decoded['data']['attributes']['reference_number'];


                // Log the checkout URL and reference number
                Log::info("Checkout URL: " . $checkout_url);
                Log::info("Reference Number: " . $reference_number);

                // Store the reference number in the event
                $event->reference_number = $reference_number;
                $event->checkout_url_expiry = now()->addHours(2);
                $event->save();
            }

            Mail::to($email)->queue(new UserAcceptNotification($event, $checkout_url));
        } elseif ($event->status === 'Decline') {
            Mail::to($email)->queue(new UserDeclineNotification($event));
        }

        return redirect()->back()->with('success', 'Event status updated successfully');
    }

    public function checkAndUpdatePaymentStatus()
    {
        Log::info('checkAndUpdatePaymentStatus method called.');

        $events = collect();

        try {
            $registrationQuery = Registration::query()
                ->where('payment_status', 'Pending');

            $events = $registrationQuery->get();
            Log::info('Number of pending events: ' . $events->count());
        } catch (\Exception $e) {
            Log::error('Error fetching pending events: ' . $e->getMessage());
        }

        foreach ($events as $event) {
            $reference_number = $event->reference_number;
            Log::info("Processing event with reference number: {$reference_number}");

            if (empty($reference_number)) {
                Log::error("Event with ID {$event->id} has a blank reference number.");
                continue;
            }

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.paymongo.com/v1/links?reference_number={$reference_number}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "accept: application/json",
                    "authorization: Basic c2tfdGVzdF8zZnI4VUNkR0Z5VHZVVmF2Tk5RMkF2QmU6",
                    "content-type: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $decoded = json_decode($response, true);
            $err = curl_error($curl);

            curl_close($curl);

            Log::info('API Response:', ['response' => $decoded]);

            if ($err) {
                Log::error("cURL Error #: " . $err);
                continue;
            }

            if (isset($decoded['data'][0]['attributes']['status'])) {
                $status = $decoded['data'][0]['attributes']['status'];
                Log::info("Payment status for reference number {$reference_number}: {$status}");

                if ($status === 'paid') {
                    $event->payment_status = 'paid';
                    $event->save();
                    Log::info("Payment status updated to 'paid' for reference number {$reference_number}");

                    // Send email notification to user
                    if (Mail::to('rhyaaaaa01072001@gmail.com')->send(new PaymentMail($event))) {
                        Log::info("Email sent to admin");
                    } else {
                        Log::error("Email not sent to admin");
                    }
                }
            } else {
                Log::error("API response does not contain the expected 'data' key.", ['response' => $decoded]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
