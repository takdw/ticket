@component('mail::message')
# Hello,

Thank you for purchasing your tickets. You can find your order information by following the link below.

@component('mail::button', ['url' => config('app.url') . '/orders/' . $order->confirmation_number])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
