@extends('errors.layout')

@section('code', '404')
@section('title_text', 'Halaman Tidak Ditemukan')
@section('icon', 'fi fi-rr-compass')
@section('heading', 'Halaman Tidak Ditemukan')
@section('message', 'Waduh! Halaman yang Anda cari tidak dapat ditemukan atau telah dipindahkan. Silakan periksa kembali alamat URL Anda.')

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
