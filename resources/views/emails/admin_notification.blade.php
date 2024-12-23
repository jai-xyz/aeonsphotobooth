<x-slot:header>
    <x-mail::header :url="config('app.url')">
        {{ config('app.name') }}
    </x-mail::header>
</x-slot:header>

 @component('mail::message')
Hello Admin,

A new registration has been received for your photo booth service.

## Event Details:
- Title of the Event: **{{ $userRegistration->event }}**
- Event Date: **{{ $userRegistration->date . ' ' . $userRegistration->time }}**
- Contact Person: **{{ $userRegistration->contactperson }}**
- Contact No.: **{{ $userRegistration->contactno }}**
- Email: **{{ $userRegistration->email }}**

Please review the registration details and connect with the client to finalize preparations.

To view the complete event registration details, please visit the following link: [{{ $adminUrl }}]({{ $adminUrl }})

Thank you!

@endcomponent

<x-slot:footer>
    <x-mail::footer>
        © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
    </x-mail::footer>
</x-slot:footer>