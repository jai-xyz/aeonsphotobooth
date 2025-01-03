<x-slot:header>
    <x-mail::header :url="config('app.url')">
        {{ config('app.name') }}
    </x-mail::header>
</x-slot:header>

 @component('mail::message')
Hi there,

Your registration for the event **{{ $event->event }}** has been **accepted**.

To fully complete your registration, please proceed with the necessary payment. 
Note that your registration will remain incomplete until payment is made.

## Important: A non-refundable downpayment of **P500.00** is required for the event.

Please click the link below to make your payment:
[Make Your Payment Here]({{ $checkout_url }})

If you have any questions, feel free to respond to this e-mail. 

Best,   
Aeon's Photobooth team

@endcomponent

<x-slot:footer>
    <x-mail::footer>
        © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
    </x-mail::footer>
</x-slot:footer>