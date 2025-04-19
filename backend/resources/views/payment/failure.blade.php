@extends('layouts.app')
@section('title','Payment Failed')

@section('content')
  <div class="text-center my-5">
    <h1 class="display-4 text-danger">❌ Payment Failed</h1>
    <p class="lead">We couldn’t process your payment. Please try again.</p>
    <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Back</a>
  </div>
@endsection
