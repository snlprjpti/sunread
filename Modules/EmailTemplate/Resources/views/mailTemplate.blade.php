<style>
    body{
        @isset($details["style"]) {{$details["style"]}} @endisset
    }
</style>
{!! $details["body"] !!}
