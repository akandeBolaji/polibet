@component('mail::message')
This is just to acknowledge that we are aware of the issues you encoutered while trying to fund your polibet account.
All issues have been resolved and you can choose to fund your account by clicking the fund account on your dashboard.
You have the choice of predicting the outcome of the coming elections and win big. Minimum stake is now 100 naira

@component('mail::button', ['url' => 'https://polibet.ng/dashboard', 'color' =>'green'])
Fund Account
@endcomponent

@component('mail::panel')
If you're having trouble clicking the "Fund Account" button directly, copy and paste the URL below into your web browser.
@endcomponent

@component('mail::panel')
{{ $url }}
@endcomponent


Regards,<br>
{{ config('app.name') }}
@endcomponent
