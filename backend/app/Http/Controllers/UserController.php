<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::with(['phones', 'address'])->paginate(10));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8'],
            'role' => ['required', 'in:user,manager,admin'],
            'active' => ['boolean'],
            'phone' => ['sometimes', 'array'],
            'phone.country_code' => ['string', 'max:5'],
            'phone.number' => ['string'],
            'phone.type' => ['in:mobile,home,work'],
            'address' => ['sometimes', 'array'],
            'address.street' => ['string'],
            'address.number' => ['string', 'nullable'],
            'address.complement' => ['string', 'nullable'],
            'address.district' => ['string', 'nullable'],
            'address.city' => ['string'],
            'address.state' => ['string', 'size:2'],
            'address.postal_code' => ['string'],
            'address.country' => ['string', 'size:2'],
        ]);

        $phoneData = $data['phone'] ?? null;
        $addressData = $data['address'] ?? null;

        unset($data['phone'], $data['address']);

        $data['password'] = Hash::make($data['password']);
        $data['active'] = $data['active'] ?? true;

        $user = User::create($data);

        if ($phoneData) {
            $phoneData['is_primary'] = true;
            $user->phones()->create($phoneData);
        }

        if ($addressData) {
            $addressData['is_primary'] = true;
            $user->address()->create($addressData);
        }

        return new UserResource($user->load(['phones', 'address']));
    }

    public function show(User $user)
    {
        return new UserResource($user->load(['phones', 'address']));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'first_name' => ['sometimes', 'string'],
            'last_name' => ['sometimes', 'string'],
            'email' => ['sometimes', 'email', "unique:users,email,{$user->id}"],
            'password' => ['nullable', 'min:8'],
            'role' => ['sometimes', 'in:user,manager,admin'],
            'active' => ['boolean'],
            'phone' => ['sometimes', 'array'],
            'phone.country_code' => ['string', 'max:5'],
            'phone.number' => ['string'],
            'phone.type' => ['in:mobile,home,work'],
            'address' => ['sometimes', 'array'],
            'address.street' => ['string'],
            'address.number' => ['string', 'nullable'],
            'address.complement' => ['string', 'nullable'],
            'address.district' => ['string', 'nullable'],
            'address.city' => ['string'],
            'address.state' => ['string', 'size:2'],
            'address.postal_code' => ['string'],
            'address.country' => ['string', 'size:2'],
        ]);

        $phoneData = $data['phone'] ?? null;
        $addressData = $data['address'] ?? null;

        unset($data['phone'], $data['address']);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        if ($phoneData) {
            $user->phones()->delete();
            $phoneData['is_primary'] = true;
            $user->phones()->create($phoneData);
        }

        if ($addressData) {
            $user->address()->where('is_primary', true)->update($addressData);
        }

        return new UserResource($user->load(['phones', 'address']));
    }

    public function toggle(User $user)
    {
        $user->update([
            'active' => ! $user->active,
        ]);

        return response()->json([
            'active' => $user->active,
        ]);
    }
}
