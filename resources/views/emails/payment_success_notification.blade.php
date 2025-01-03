<x-slot:header>
    <x-mail::header :url="config('app.url')">
        {{ config('app.name') }}
    </x-mail::header>
</x-slot:header>

 @component('mail::message')
Hello Admin,

We’re pleased to inform you that a payment has been successfully processed for a new registration.

## Event Details:
- Title of the Event: **{{ $event->event }}**
- Event Date: **{{ $event->date . ' ' . $event->time }}**
- Contact Person: **{{ $event->contactperson }}**
- Contact No.: **{{ $event->contactno }}**
- Email: **{{ $event->email }}**

Please review the details and proceed accordingly.

Thank you!

@endcomponent

<x-slot:footer>
    <x-mail::footer>
        © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
    </x-mail::footer>
</x-slot:footer>