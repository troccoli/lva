Click here to reset your password: <a href="{{ $link = route('passwordReset', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> {{ $link }} </a>
