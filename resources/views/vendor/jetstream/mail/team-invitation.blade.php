@component('mail::message')
@if($type == 'group')
{{ __('You have been invited to join the :team team!', ['team' => $invitation->team->name]) }}
@else
{{ __('You have been invited to join a private chat with :user!', ['user' => $user->name]) }}
@endif
{{ __('If you already have an account, you may accept this invitation by clicking the button below:') }}

{{ __('You may accept this invitation by clicking the button below:') }}

@component('mail::button', ['url' => $acceptUrl])
{{ __('Accept Invitation') }}
@endcomponent

{{ __('If you did not expect to receive an invitation to this team, you may discard this email.') }}
@endcomponent
