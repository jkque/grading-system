@component('mail::message')
# Successfull

{{$mail->description}}.

@component('mail::button', ['url' => url('/login')])
Log in
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
