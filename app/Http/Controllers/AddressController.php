<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->latest()->get();

        return view('addresses.address', compact('addresses'));
    }

    public function create()
    {
        return view('addresses.createAddress');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'recipient_name'  => 'required|string|max:255',
            'phone_number'    => 'required|digits_between:10,15', 
            'address'         => 'required|string',            
            'city'            => 'required|string|max:255',     
            'postal_code'     => 'required|numeric',
        ]);

        $validated['user_id'] = $request->user()->id;

        Address::create($validated);

        return redirect()->route('addresses.index')
            ->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function edit(Address $address, Request $request)
    {
        if ($address->user_id != $request->user()->id) {
            abort(403, 'Anda tidak memiliki akses ke alamat ini.');
        }

        return view('addresses.editAddress', compact('address'));
    }

    public function update(Request $request, Address $address)
    {
        if ($address->user_id != $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'recipient_name'  => 'required|string|max:255',
            'phone_number'    => 'required|digits_between:10,15',
            'address'         => 'required|string',
            'city'            => 'required|string|max:255',
            'postal_code'     => 'required|numeric',
        ]);

        $address->update($validated);

        return redirect()->route('addresses.index')
            ->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroy(Request $request, Address $address)
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $address->delete();

        return redirect()->route('addresses.index')
            ->with('success', 'Alamat berhasil dihapus.');
    }

    public function setDefault(Request $request, Address $address)
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->user()->addresses()->update(['is_default' => false]);

        $address->update(['is_default' => true]);

        return redirect()->route('addresses.index')
            ->with('success', 'Alamat utama berhasil diubah.');
    }
}