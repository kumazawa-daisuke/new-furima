@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address_edit.css') }}">
@endsection

@section('content')
<div class="address-edit-container">
    <h2 class="address-edit-title">住所の変更</h2>
    <form action="{{ route('address.update', $item->id) }}" method="POST" class="address-edit-form">
        @csrf
        <div class="form-group">
            <label for="postal_code" class="address-edit-label">郵便番号</label>
            <input type="text" id="postal_code" name="postal_code" class="address-edit-input" value="{{ old('postal_code', $address->postal_code ?? '') }}">
            @error('postal_code')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="address" class="address-edit-label">住所</label>
            <input type="text" id="address" name="address" class="address-edit-input" value="{{ old('address', $address->address ?? '') }}">
            @error('address')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="building" class="address-edit-label">建物名</label>
            <input type="text" id="building" name="building" class="address-edit-input" value="{{ old('building', $address->building ?? '') }}">
            @error('building')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="address-edit-btn">更新する</button>
    </form>
</div>
@endsection
