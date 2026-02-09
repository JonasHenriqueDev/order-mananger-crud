<?php

namespace App\Http\Controllers;

use App\Http\Resources\PhoneResource;
use App\Models\Phone;
use App\Models\User;
use Illuminate\Http\Request;

class PhoneController extends Controller
{
    /**
     * Display a listing of all phones.
     */
    public function index()
    {
        return PhoneResource::collection(Phone::with('user')->paginate(10));
    }

    /**
     * Store a newly created phone for a user.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'country_code' => ['required', 'string', 'max:5'],
            'number' => ['required', 'string'],
            'type' => ['required', 'in:mobile,home,work'],
            'is_primary' => ['boolean'],
        ]);

        $phone = Phone::create($data);

        return new PhoneResource($phone->load('user'));
    }

    /**
     * Display the specified phone.
     */
    public function show(Phone $phone)
    {
        return new PhoneResource($phone->load('user'));
    }

    /**
     * Update the specified phone.
     */
    public function update(Request $request, Phone $phone)
    {
        $data = $request->validate([
            'country_code' => ['sometimes', 'string', 'max:5'],
            'number' => ['sometimes', 'string'],
            'type' => ['sometimes', 'in:mobile,home,work'],
            'is_primary' => ['sometimes', 'boolean'],
        ]);

        $phone->update($data);

        return new PhoneResource($phone->load('user'));
    }

    /**
     * Remove the specified phone.
     */
    public function destroy(Phone $phone)
    {
        $phone->delete();

        return response()->json(['message' => 'Phone deleted successfully']);
    }

    /**
     * Get phones for a specific user.
     */
    public function userPhones(User $user)
    {
        return PhoneResource::collection($user->phones()->paginate(10));
    }
}
