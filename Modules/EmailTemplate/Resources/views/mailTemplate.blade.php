@component('mail::layout')
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
        @endcomponent
    @endslot

    @component('mail::message')

        <h3>{{ $details['title'] }}</h3>
        <h3>{{ $details['body'] }}</h3>

        @component('mail::button', ['url' => ''])
            Button Text
        @endcomponent

        Thanks,<br>
        {{ config('app.name') }}
    @endcomponent

    @slot('footer')
        @component('mail::footer')
        @endcomponent
    @endslot
@endcomponent
