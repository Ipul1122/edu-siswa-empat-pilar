@extends('errors.layout')

@section('code', '500')
@section('title_text', 'Kesalahan Server Internal')
@section('icon', 'fi fi-rr-settings')
@section('heading', 'Kesalahan Server Internal')
@section('message', 'Mohon maaf! Terjadi kesalahan teknis pada server kami. Tim kami sedang berusaha mengatasi masalah ini. Silakan coba kembali beberapa saat lagi.')

@section('actions')
    @auth
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Dashboard</a>
        @else
            <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary">Dashboard</a>
        @endif
    @else
        <a href="{{ route('home') }}" class="btn btn-primary">Kembali Beranda</a>
    @endauth
    <button onclick="window.history.back()" class="btn btn-secondary">Kembali</button>
@endsection
