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
        $item = Item::findOrFail($item_id);

        return view('address_edit', [
            'item'    => $item,
            'address' => $address,
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

        return redirect()->route('purchase.form', $item_id)->with('success', '住所を更新しました');
    }
}
