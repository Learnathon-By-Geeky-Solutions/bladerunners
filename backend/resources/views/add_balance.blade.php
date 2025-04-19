@extends('layouts.app')
@section('title','Add Balance')

@section('content')
  <h2>Add Balance</h2>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <form action="{{ route('balance.process') }}" method="POST">
    @csrf
    <input type="hidden" name="user_id" value="{{ $user_id }}">

    <div class="form-group">
      <label for="amount">Amount (BDT)</label>
      <input type="number" name="amount" id="amount"
             class="form-control @error('amount') is-invalid @enderror"
             min="1" step="0.01" value="{{ old('amount') }}" required>
      @error('amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
    </div>

    <div class="form-group">
      <label>Payment Method</label><br>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="payment_method"
               id="ssl" value="sslcommerz" checked>
        <label class="form-check-label" for="ssl">SSLCOMMERZ</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="payment_method"
               id="stripe" value="stripe">
        <label class="form-check-label" for="stripe">Stripe</label>
      </div>
      @error('payment_method')<div class="text-danger">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-primary">Continue to Payment</button>
  </form>
@endsection
