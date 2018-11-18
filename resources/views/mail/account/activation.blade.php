@component('mail::message')
Welcome to Polibet

Thank you for registering an account with us.
We would love you to click on the button below to verify your account and start placing bets!!

@component('mail::button', ['url' => $url, 'color' =>'green'])
Verify Account
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent
