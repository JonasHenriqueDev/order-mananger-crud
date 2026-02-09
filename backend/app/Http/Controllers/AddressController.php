<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of all addresses.
     */
    public function index()
    {
        return AddressResource::collection(Address::with('user')->paginate(10));
    }

    /**
     * Store a newly created address for a user.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'street' => ['required', 'string'],
            'number' => ['sometimes', 'string', 'nullable'],
            'complement' => ['sometimes', 'string', 'nullable'],
            'district' => ['sometimes', 'string', 'nullable'],
            'city' => ['required', 'string'],
            'state' => ['required', 'string', 'size:2'],
            'postal_code' => ['required', 'string'],
            'country' => ['required', 'string', 'size:2'],
            'is_primary' => ['boolean'],
        ]);

        $address = Address::create($data);

        return new AddressResource($address->load('user'));
    }

    /**
     * Display the specified address.
     */
    public function show(Address $address)
    {
        return new AddressResource($address->load('user'));
    }

    /**
     * Update the specified address.
     */
    public function update(Request $request, Address $address)
    {
        $data = $request->validate([
            'street' => ['sometimes', 'string'],
            'number' => ['sometimes', 'string', 'nullable'],
            'complement' => ['sometimes', 'string', 'nullable'],
            'district' => ['sometimes', 'string', 'nullable'],
            'city' => ['sometimes', 'string'],
            'state' => ['sometimes', 'string', 'size:2'],
            'postal_code' => ['sometimes', 'string'],
            'country' => ['sometimes', 'string', 'size:2'],
            'is_primary' => ['sometimes', 'boolean'],
        ]);

        $address->update($data);

        return new AddressResource($address->load('user'));
    }

    /**
     * Remove the specified address.
     */
    public function destroy(Address $address)
    {
        $address->delete();

        return response()->json(['message' => 'Address deleted successfully']);
    }

    /**
     * Get addresses for a specific user.
     */
    public function userAddresses(User $user)
    {
        return AddressResource::collection($user->address()->paginate(10));
    }
}
