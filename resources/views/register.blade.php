@extends('layouts.main')
@section('title','Register')
@section('content')

<form action="{{route('registerUser')}}" method="post" class="p-5" enctype="multipart/form-data">
    @csrf
    <h3 class="text-secondary pb-5">Register</h3>
    <div class="form-group">
        <label for="username">Name</label>
        <input type="text" name="username" class="form-control">
        @error('username')
        <small class="text-danger">{{$message}}</small>
        @enderror
    </div>
    <div class="form-group ">
        <label for="Email">Email</label>
        <input type="text" name="email" class="form-control">
        @error('email')
        <small class="text-danger">{{$message}}</small>
        @enderror
    </div>
    <div class="form-group ">
        <label for="password">Password</label>
        <input type="password" name="password" class="form-control">
        @error('password')
        <small class="text-danger">{{$message}}</small>
        @enderror
    </div>    
    <div class="form-group ">
        <label for="password_confirmation">Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control">
        @error('password_confirmation')
        <small class="text-danger">{{$message}}</small>
        @enderror
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>


@endsection
