@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items_create.css') }}">
@endsection

@section('content')
<div class="create-container">
    <h2 class="create-title">商品の出品</h2>
    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="create-form">
        @csrf

        <div class="form-group img-area">
            <label class="img-label">商品画像</label>
            <div class="img-flex">
                <img id="preview" class="img-preview">
                <label class="img-upload-btn">
                    画像を選択する
                    <input type="file" name="image" accept="image/*" id="image-input" class="img-file-input">
                </label>
            </div>
            @error('image')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group detail-area">
            <div class="detail-heading">商品の詳細</div>
            <div class="detail-hr"></div>

            <div class="category-area">
                <label class="input-label">カテゴリー</label>
                <div class="category-list">
                    @foreach(['ファッション','家電','インテリア','レディース','メンズ','コスメ','本','ゲーム','スポーツ','キッチン','ハンドメイド','アクセサリー','おもちゃ','ベビー・キッズ'] as $category)
                        <input 
                            type="checkbox" 
                            name="category[]" 
                            id="cat-{{ $loop->index }}" 
                            value="{{ $category }}" 
                            class="category-checkbox" 
                            {{ is_array(old('category')) && in_array($category, old('category', [])) ? 'checked' : '' }}
                        >
                        <label for="cat-{{ $loop->index }}" class="category-tag">{{ $category }}</label>
                    @endforeach
                </div>
                @error('category')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- 商品の状態 -->
            <div class="form-group">
                <label class="input-label">商品の状態</label>
                <div class="select-wrap">
                  <select name="condition" class="form-select" id="item-condition">
                      <option value="">選択してください</option>
                      <option value="良好" {{ old('condition') == '良好' ? 'selected' : '' }}>良好</option>
                      <option value="目立った傷や汚れなし" {{ old('condition') == '目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
                      <option value="やや傷や汚れあり" {{ old('condition') == 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
                      <option value="状態が悪い" {{ old('condition') == '状態が悪い' ? 'selected' : '' }}>状態が悪い</option>
                  </select>
                  <span class="select-arrow"></span>
                </div>
                @error('condition')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="section-heading">
                商品名と説明
                <div class="section-hr"></div>
            </div>

            <!-- 商品名 -->
            <div class="form-group">
                <label class="input-label">商品名</label>
                <input type="text" name="name" class="form-input" value="{{ old('name') }}">
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- ブランド名 -->
            <div class="form-group">
                <label class="input-label">ブランド名</label>
                <input type="text" name="brand" class="form-input" value="{{ old('brand') }}">
                @error('brand')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- 商品の説明 -->
            <div class="form-group">
                <label class="input-label">商品の説明</label>
                <textarea name="description" class="form-textarea">{{ old('description') }}</textarea>
                @error('description')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- 販売価格 -->
            <div class="form-group">
                <label class="input-label">販売価格</label>
                <div class="price-field">
                  <span class="yen">￥</span>
                  <input type="text" name="price" class="form-input price-input" value="{{ old('price') }}">
                </div>
                @error('price')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- 出品するボタン -->
        <button type="submit" class="submit-btn">出品する</button>
    </form>
</div>

<script>
  // カテゴリーチェックUI
document.querySelectorAll('.category-checkbox').forEach((input, idx) => {
  const tag = document.querySelector(`label[for="cat-${idx}"]`);
  const updateActive = () => {
      if (input.checked) {
          tag.classList.add('active');
      } else {
          tag.classList.remove('active');
      }
  };
    input.addEventListener('change', updateActive);
    // 初期状態
    if (input.checked) tag.classList.add('active');
});

// 商品状態ドロップダウンUI
const select = document.getElementById('item-condition');
select && select.addEventListener('change', function() {
  this.classList.toggle('selected', !!this.value);
});
// 初期状態
if(select && select.value) select.classList.add('selected');
</script>
<script>
document.getElementById('image-input').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
});
</script>
@endsection
