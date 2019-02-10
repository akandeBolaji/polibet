@component('mail::message')

Dear {{ $user->full_name }}, We deeply regret to inform you of our decision to shutdown Polibet temporarily.

This decision was made based on the numerous issues we are facing with getting licensed presently. Refunds would definitely be made within the week.
We would also like to give you a first hand tip that we would be taking a pivot to become the first peer to peer betting platform after getting licensed. You would be able to create your
own bets, get it approved and invite your friends to play. You can reach us via our facebook page - https://www.facebook.com/Polibet-776451619355667/ or phone +2348052764314 for more enquires



Regards,<br>
{{ config('app.name') }}
@endcomponent
