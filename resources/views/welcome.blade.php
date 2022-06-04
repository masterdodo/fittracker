@extends('layouts.app')

@section('content')
    @guest
    <div class="guest-div">
    <leaderboard></leaderboard>
    </div>
    @else
    <script>
        window.location.href = '{{ route("home") }}';
    </script>
    @endguest
@endsection