@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_edit.css') }}">
@endsection

@section('content')
<h2 class="profile-edit-title">プロフィール設定</h2>
<form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-edit-form" novalidate>
    @csrf
    <div class="profile-edit-image-row">
        <div class="profile-edit-image-circle" id="profile-image-circle">
            @if(!empty($user->profile_image))
                <img id="preview-img"
                     src="{{ asset('storage/profile/'.$user->profile_image) }}"
                     alt="プロフィール画像">
            @endif
        </div>
        <label class="profile-edit-image-btn">
            画像を選択する
            <input id="profile_image_input" type="file" name="profile_image" accept="image/*" style="display:none;">
        </label>
    </div>
    <div class="profile-edit-fields">
        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" required>
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="zipcode">郵便番号</label>
            <input type="text" name="zipcode" value="{{ old('zipcode', $user->address->postal_code ?? '') }}">
            @error('zipcode')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" value="{{ old('address', $user->address->address ?? '') }}">
            @error('address')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" value="{{ old('building', $user->address->building ?? '') }}">
        </div>
        <button type="submit" class="profile-edit-submit-btn">更新する</button>
    </div>
</form>

<script>
document.getElementById('profile_image_input').addEventListener('change', function(event) {
    const input = event.target;
    const circle = document.getElementById('profile-image-circle');

    if (input.files && input.files[0]) {
        // 既存のimgタグがあれば再利用、なければ新しく作る
        let img = circle.querySelector('img');
        if (!img) {
            img = document.createElement('img');
            img.id = 'preview-img';
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '50%';
            circle.appendChild(img);
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
});
</script>
@endsection
