<x-slot:header>
    <x-mail::header :url="config('app.url')">
        {{ config('app.name') }}
    </x-mail::header>
</x-slot:header>

 @component('mail::message')
Hi there,

Your registration for the event **{{ $event->event }}** has been **cancelled**.

Unfortunately, this decision was made due to unforeseen circumstances beyond our control. 
We deeply regret any inconvenience this may cause and want to assure you that we are doing everything possible to address the situation.

If you have any questions, feel free to respond to this e-mail. 

Best regards,   
Aeon's Photobooth team

@endcomponent

<x-slot:footer>
    <x-mail::footer>
        © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
    </x-mail::footer>
</x-slot:footer>