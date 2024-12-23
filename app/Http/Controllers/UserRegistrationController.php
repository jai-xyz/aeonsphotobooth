<?php

namespace App\Http\Controllers;

use App\Models\Packages;
use App\Models\Registration;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Mail\AdminNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class UserRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $userId = Auth::id();

        $events = DB::table('registrations')
            ->join('barangays', 'registrations.barangay', '=', 'barangays.id')
            ->join('cities', 'registrations.city', '=', 'cities.id')
            ->join('provinces', 'registrations.province', '=', 'provinces.id')
            ->join('regions', 'registrations.region', '=', 'regions.id')
            ->where('registrations.user_id', $userId)
            ->orderBy('registrations.created_at', 'desc')
            ->select(
                'registrations.*',
                'barangays.name as barangay_name',
                'cities.name as city_name',
                'provinces.name as province_name',
                'regions.name as region_name'
            )
            ->get();


        $events = $events->map(function ($event) {
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
        $pkg = $packageId ? Packages::with('options')->find($packageId) : (object) [];

        $getAllPackages = Packages::with('options')->get();

        //                                                       Accepted
        $getEvents = DB::table('registrations')->where('status', 'Pending')->get();

        $backdropTypes = DB::table('backdroptypes')->get();

        $backdropColors = DB::table('backdropcolors')
            ->join('backdroptypes', 'backdropcolors.backdroptype_id', '=', 'backdroptypes.id')
            ->select('backdropcolors.*', 'backdroptypes.name as backdroptype_name')
            ->get();

        return Inertia::render('User/Registration', [
            'pkg' => $pkg,
            'getAllPackages' => $getAllPackages,
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

        $validatedData = $request->validate(
            [
                'event' => 'required|string|max:255',
                'region' => 'required|max:255',
                'province' => 'required|max:255',
                'city' => 'required|max:255',
                'barangay' => 'required|max:255',
                'street' => 'required|string|max:255',
                'zipcode' => 'required|string|max:255',
                'contactperson' => 'required|string|max:255',
                'contactno' => 'required|string|max:100',
                'email' => 'required|email|max:255',
                'date' => 'required|date_format:Y-m-d|after_or_equal:today',
                'hour' => 'required|string',
                'minute' => 'required|string',
                'ampm' => 'required|string|in:AM,PM',
                'packageid' => 'required|exists:packages,id',
                'packagename' => 'required|string|max:255',
                'alias' => 'required|string|max:255', 
                'packagesize' => 'required|string|max:255',
                'backdroptype' => 'required|string|max:255',
                'backdropcolor' => 'required|string|max:255',
                'number_of_shots' => 'required|integer',
                'price' => 'required|string|max:255',
                'extension' => 'required|string|max:255',
                'theme' => 'required|string|max:255',
                'suggestion' => 'nullable|string|max:255',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ],
            [
                'date.after_or_equal' => 'Date must be a future date.',
            ]
        );

      // Convert hour, minute, and ampm to a 24-hour format time string
    $hour = (int) $validatedData['hour'];
    $minute = (int) $validatedData['minute'];
    $ampm = $validatedData['ampm'];

    if ($ampm === 'PM' && $hour != 12) {
        $hour += 12;
    } elseif ($ampm === 'AM' && $hour == 12) {
        $hour = 0;
    }
        // $hour = 5;
        // $minute = 7;
        $time = sprintf('%02d:%02d', $hour, $minute);
        // Output: 05:07

        // Handle file storage
        $imagePaths = [];
        if ($request->hasFile('images')) {
            $imageBaseName = strtolower(preg_replace('/\s+/', '_', $validatedData['contactperson']));
            foreach ($request->file('images') as $index => $file) {
                $fileName = $imageBaseName . '_' . ($index + 1) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/uploads/events', $fileName);
                $imagePaths[] = Storage::url($path);
            }
        }
        // dd($request->all());

        try {
            $userRegistration = Registration::create([
                'user_id' => $request->user()->id,
                'event' => $validatedData['event'],
                'street' => $validatedData['street'],
                'barangay' => $validatedData['barangay'],
                'city' => $validatedData['city'],
                'province' => $validatedData['province'],
                'region' => $validatedData['region'],
                'zipcode' => $validatedData['zipcode'],
                'contactperson' => $validatedData['contactperson'],
                'contactno' => $validatedData['contactno'],
                'email' => $validatedData['email'],
                'date' => $validatedData['date'],
                'time' => $time,
                'packageid' => $validatedData['packageid'],
                'packagename' => $validatedData['packagename'],
                'alias' => $validatedData['alias'],
                'packagesize' => $validatedData['packagesize'],
                'backdroptype' => $validatedData['backdroptype'],
                'backdropcolor' => $validatedData['backdropcolor'],
                'number_of_shots' => $validatedData['number_of_shots'],
                'price' => $validatedData['price'],
                'extension' => $validatedData['extension'],
                'theme' => $validatedData['theme'],
                'suggestion' => $validatedData['suggestion'],
                'images' => json_encode($imagePaths),
            ]);

            // Log the email details
            Log::info('Sending email to: rhyaaaaa01072001@gmail.com');
            Log::info('Email content: ', ['userRegistration' => $userRegistration]);

            // Queue the email to be sent asynchronously
            Mail::to('rhyaaaaa01072001@gmail.com')->queue(new AdminNotification($userRegistration));

            Session::put('registration_success', true);
            
            return redirect()->route('user.event.confirmation');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error processing registration: ' . $e->getMessage());
    
            return redirect()->back()->with(['error' => 'An error occurred while processing your registration. Please try again.'], 500);
        }
    }

    public function indexConfirmation()
    {

        // Check if the registration was successful
        if (!Session::has('registration_success')) {
            return redirect()->route('home');
        }

        // // Clear the session variable after checking
        Session::forget('registration_success');

        return Inertia::render('User/Confirmation');
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
