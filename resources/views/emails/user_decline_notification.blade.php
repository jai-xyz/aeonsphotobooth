<x-slot:header>
    <x-mail::header :url="config('app.url')">
        {{ config('app.name') }}
    </x-mail::header>
</x-slot:header>

 @component('mail::message')
Hi there,

Unfortunately, your registration for the event **{{ $event->event }}** has been declined.

## Possible Reasons:
 - The event may have reached its maximum capacity.
 - There could be incomplete or incorrect information in your registration.
 - Payment requirements may not have been met.

If you believe this was a mistake or need further assistance, feel free to contact us.

Best,   
Aeon's Photobooth team

@endcomponent

<x-slot:footer>
    <x-mail::footer>
        © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
    </x-mail::footer>
</x-slot:footer>