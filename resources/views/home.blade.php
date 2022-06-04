@extends('layouts.app')

@section('content')
@if (Auth::user()->type == "admin")
<admin></admin>
@else
<main>
    <routines></routines>
    <activities></activities>
    <aside><leaderboard></leaderboard></aside>
</main>
@endif
@endsection
