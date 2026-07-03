@extends('errors.layout')

@section('code', '419')
@section('title_text', 'Sesi Kedaluwarsa')
@section('icon', 'fi fi-rr-clock-three')
@section('heading', 'Sesi Anda Telah Berakhir')
@section('message', 'Oops! Sesi Anda telah kedaluwarsa karena tidak ada aktivitas dalam waktu lama. Silakan segarkan halaman dan coba lagi.')

@section('actions')
    <button onclick="window.location.reload()" class="btn btn-primary">Segarkan Halaman</button>
    @auth
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Dashboard</a>
        @else
            <a href="{{ route('siswa.dashboard') }}" class="btn btn-secondary">Dashboard</a>
        @endif
    @else
        <a href="{{ route('home') }}" class="btn btn-secondary">Beranda</a>
    @endauth
@endsection
