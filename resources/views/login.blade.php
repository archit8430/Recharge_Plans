@extends('layouts.main')
@section('title','Login')
@section('content')
<form action="{{route('loginUser')}}" method="post" class="p-5">
    @csrf
    <h3 class="text-secondary pb-5">Login</h3>
    @if(Session::has('error'))
    <div class="alert alert-warning show" role="alert">
        <strong></strong> {{Session::get('error')}}
    </div>
    @endif
    <div class="mb-3 ">
        <label for="Email">Email</label>
        <input type="text" name="email" class="form-control">
        @error('email')
        <small class="text-danger">{{$message}}</small>
        @enderror
    </div>
    <div class="mb-3 ">
        <label for="password">Password</label>
        <input type="password" name="password" class="form-control">
        @error('password')
        <small class="text-danger">{{$message}}</small>
        @enderror
    </div>  
    <button type="submit" class="btn btn-primary">Submit</button>
</form>



@endsection
