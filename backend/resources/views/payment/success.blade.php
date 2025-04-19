@extends('layouts.app')
@section('title','Payment Successful')

@section('content')
  <div class="text-center my-5">
    <h1 class="display-4 text-success">🎉 Payment Successful!</h1>
    <p class="lead">Topped up <strong>{{ $currency }} {{ number_format($amount,2) }}</strong> successfully.</p>
    <a href="{{ config('app.frontend_url') }}/dashboard" class="btn btn-primary btn-lg mt-4">
      Go to Dashboard
    </a>
  </div>
@endsection
