@extends('layouts.app')
@section('title', 'Edit Siswa')

@section('content')
@include('admin.siswa.create', ['siswa' => $siswa, 'kelas' => $kelas])
@endsection
