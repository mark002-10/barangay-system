<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register', [
            'genders' => Resident::GENDERS,
            'civilStatuses' => Resident::CIVIL_STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[\pL]+(?:[ \'-][\pL]+)*$/u'],
            'middle_name' => ['required', 'string', 'max:100', 'regex:/^[\pL]+(?:[ \'-][\pL]+)*$/u'],
            'last_name' => ['required', 'string', 'max:100', 'regex:/^[\pL]+(?:[ \'-][\pL]+)*$/u'],
            'suffix' => ['required', 'string', 'max:20'],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::in(Resident::GENDERS)],
            'civil_status' => ['required', Rule::in(Resident::CIVIL_STATUSES)],
            'purok' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:2000'],
            'phone' => ['required', 'string', 'digits_between:7,15'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ], [
            'first_name.regex' => 'Use letters only; spaces, apostrophes, and hyphens are allowed.',
            'middle_name.regex' => 'Use letters only; spaces, apostrophes, and hyphens are allowed.',
            'last_name.regex' => 'Use letters only; spaces, apostrophes, and hyphens are allowed.',
            'phone.digits_between' => 'Enter 7 to 15 digits only, without spaces or symbols.',
        ]);

        $fullName = collect([
            $data['first_name'], $data['middle_name'],
            $data['last_name'], $data['suffix'],
        ])->filter()->join(' ');

        $user = User::create([
            'name' => $fullName,
            'email' => strtolower($data['email']),
            'password' => $data['password'],
            'role' => 'user',
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'suffix' => $data['suffix'],
            'birth_date' => $data['birth_date'],
            'gender' => $data['gender'],
            'civil_status' => $data['civil_status'],
            'purok' => $data['purok'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'is_verified' => false,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('verification.pending');
    }
}
