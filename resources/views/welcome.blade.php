{{-- This file is unused. The home route points to home.blade.php --}}
@extends('layouts.app')
@section('title', 'Welcome')
@section('content')
    <script>window.location = "{{ route('home') }}";</script>
@endsection
