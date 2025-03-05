<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Password::min(8)->letters()->numbers()->symbols()],
            'accountType'=> 'required|in:admin,teacher,student'
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address cannot be more than 255 characters.',
            'email.unique' => 'This email address is already taken.',
            'password.required' => 'Pole hasło jest wymagane.',
            'password.min' => 'Hasło musi mieć co najmniej :min znaków.',
            'password.letters' => 'Hasło musi zawierać przynajmniej jedną literę.',
            'password.mixed' => 'Hasło musi zawierać małe i duże litery.',
            'password.numbers' => 'Hasło musi zawierać przynajmniej jedną cyfrę.',
            'password.symbols' => 'Hasło musi zawierać przynajmniej jeden znak specjalny.',
            'accountType.required' => 'Please enter your account type.',
            'accountType.in' => 'Please enter a valid account type.'
        ]);
        try {
            User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'accountType' => $request->accountType,
            ]);
            return response()->json(['message' => 'sucess'], 201);
        }
        catch (\Exception $e) {
            return response()->json(['message' => 'error',
                'error' => $e->getMessage()], 500);
        }

    }





}
