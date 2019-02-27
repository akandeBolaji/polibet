@component('mail::message')

Thank you for verifying you own this email address.
We would love you to click on the button below to finish the verification process!!

@component('mail::button', ['url' => $url, 'color' =>'green'])
Verify Account
@endcomponent

@component('mail::panel')
If you're having trouble clicking the "Verify Account" button directly, copy and paste the URL below into your web browser.
@endcomponent

@component('mail::panel')
{{ $url }}
@endcomponent


Regards,<br>
{{ config('app.name') }}
@endcomponent
