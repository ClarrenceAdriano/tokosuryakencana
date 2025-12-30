<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * 1. INDEX: Menampilkan daftar alamat
     * Mengambil semua alamat milik user yang sedang login, diurutkan dari yang terbaru.
     */
    public function index(Request $request)
    {
        // Ambil data alamat dari relasi user() -> addresses()
        $addresses = $request->user()->addresses()->latest()->get();

        return view('addresses.address', compact('addresses'));
    }

    /**
     * 2. CREATE: Menampilkan form tambah alamat
     */
    public function create()
    {
        return view('addresses.createAddress');
    }

    /**
     * 3. STORE: Menyimpan alamat baru ke database
     */
    public function store(Request $request)
    {
        // A. Validasi Input
        // Memastikan data yang dikirim sesuai format yang diinginkan
        $validated = $request->validate([
            'name'            => 'required|string|max:255',     // Label Alamat (misal: Rumah, Kantor)
            'recipient_name'  => 'required|string|max:255',     // Nama Penerima
            'phone_number'    => 'required|digits_between:10,15', // No HP (angka 10-15 digit)
            'address'         => 'required|string',             // Alamat Lengkap
            'city'            => 'required|string|max:255',     // Kota
            'postal_code'     => 'required|numeric',            // Kode Pos
        ]);

        // B. Assign User ID
        // Menambahkan ID user yang sedang login ke data yang akan disimpan
        $validated['user_id'] = $request->user()->id;

        // C. Simpan ke Database
        Address::create($validated);

        // D. Redirect kembali ke halaman list dengan pesan sukses
        return redirect()->route('addresses.index')
            ->with('success', 'Alamat berhasil ditambahkan.');
    }

    /**
     * 4. EDIT: Menampilkan form edit alamat
     */
    public function edit(Address $address, Request $request)
    {
        // Authorization Check:
        // Pastikan alamat yang mau diedit benar-benar milik user yang login
        if ($address->user_id !== $request->user()->id) {
            abort(403, 'Anda tidak memiliki akses ke alamat ini.');
        }

        return view('addresses.editAddress', compact('address'));
    }

    /**
     * 5. UPDATE: Memperbarui data alamat yang sudah ada
     */
    public function update(Request $request, Address $address)
    {
        // Authorization Check
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        // Validasi ulang input edit
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'recipient_name'  => 'required|string|max:255',
            'phone_number'    => 'required|digits_between:10,15',
            'address'         => 'required|string',
            'city'            => 'required|string|max:255',
            'postal_code'     => 'required|numeric',
        ]);

        // Update data di database
        $address->update($validated);

        return redirect()->route('addresses.index')
            ->with('success', 'Alamat berhasil diperbarui.');
    }

    /**
     * 6. DESTROY: Menghapus alamat
     */
    public function destroy(Request $request, Address $address)
    {
        // Authorization Check
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        // Hapus data
        $address->delete();

        return redirect()->route('addresses.index')
            ->with('success', 'Alamat berhasil dihapus.');
    }

    /**
     * 7. SET DEFAULT: Mengatur alamat utama
     * Logika: Reset semua alamat jadi false, lalu set alamat yang dipilih jadi true.
     */
    public function setDefault(Request $request, Address $address)
    {
        // Authorization Check
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        // Langkah 1: Ubah 'is_default' milik SEMUA alamat user ini menjadi FALSE
        $request->user()->addresses()->update(['is_default' => false]);

        // Langkah 2: Ubah 'is_default' alamat yang DIPILIH menjadi TRUE
        $address->update(['is_default' => true]);

        return redirect()->route('addresses.index')
            ->with('success', 'Alamat utama berhasil diubah.');
    }
}