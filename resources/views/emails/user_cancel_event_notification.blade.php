<x-slot:header>
    <x-mail::header :url="config('app.url')">
        {{ config('app.name') }}
    </x-mail::header>
</x-slot:header>

 @component('mail::message')
 Hi Admin,

 This is to inform you that Ms./Mr. {{ $event->contactperson }} has cancelled their registration for the event {{ $event->event }}.
 
 Here are the details of the cancellation:
 
 - Title of the Event: **{{ $event->event }}**
 - Event Date: **{{ $event->date . ' ' . $event->time }}**
 - Contact Person: **{{ $event->contactperson }}**
 - Contact No.: **{{ $event->contactno }}**
 - Email: **{{ $event->email }}**
 - **Date of Cancellation: {{ now()->format('Y-m-d H:i:s') }}**
 
 Please review this cancellation and take any necessary actions.
 
 Thank you!
 

@endcomponent

<x-slot:footer>
    <x-mail::footer>
        © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
    </x-mail::footer>
</x-slot:footer>