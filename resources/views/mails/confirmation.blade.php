Hi {{ $name }}
<p>Your registration is completed, please click link below to continue.</p>

{{ route('confirmation', $token) }}