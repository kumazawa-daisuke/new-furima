<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use App\Models\Item;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    // 住所編集フォーム表示
    public function edit($item_id)
    {
        $user = Auth::user();
        $address = $user->address;

        if ($item_id == 0) {
            $item = null;
        } else {
            $item = Item::findOrFail($item_id);
        }

        return view('address_edit', [
            'item'    => $item,
            'address' => $address,
            'item_id' => $item_id // ★この行を追加
        ]);
    }

    // 住所更新処理
    public function update(AddressRequest $request, $item_id)
    {
        $user = Auth::user();

        $data = $request->validated();

        $address = $user->address ?? new Address();
        $address->user_id     = $user->id;
        $address->postal_code = $request->input('postal_code');
        $address->address     = $request->input('address');
        $address->building    = $request->input('building');
        $address->save();
        
        if ($item_id == 0) {
            return redirect()->route('items.index')->with('success', '住所を登録しました');
        } else {
            return redirect()->route('purchase.form', $item_id)->with('success', '住所を更新しました');
        }
    }
}
