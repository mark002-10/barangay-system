<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('settings.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $rules = [
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:12', 'confirmed'],
        ];

        if ($user->hasRole('user')) {
            $rules += [
                'first_name' => ['required', 'string', 'max:100', 'regex:/^[\pL]+(?:[ \'-][\pL]+)*$/u'],
                'middle_name' => ['required', 'string', 'max:100', 'regex:/^[\pL]+(?:[ \'-][\pL]+)*$/u'],
                'last_name' => ['required', 'string', 'max:100', 'regex:/^[\pL]+(?:[ \'-][\pL]+)*$/u'],
                'suffix' => ['required', 'string', 'max:20'],
                'birth_date' => ['required', 'date', 'before:today'],
                'gender' => ['required', Rule::in(\App\Models\Resident::GENDERS)],
                'civil_status' => ['required', Rule::in(\App\Models\Resident::CIVIL_STATUSES)],
                'purok' => ['required', 'string', 'max:100'],
                'address' => ['required', 'string', 'max:2000'],
                'phone' => ['required', 'digits_between:7,15'],
            ];
        } else {
            $rules['name'] = ['required', 'string', 'max:255'];
        }

        $data = $request->validate($rules, [
            'first_name.regex' => 'Use letters only; spaces, apostrophes, and hyphens are allowed.',
            'middle_name.regex' => 'Use letters only; spaces, apostrophes, and hyphens are allowed.',
            'last_name.regex' => 'Use letters only; spaces, apostrophes, and hyphens are allowed.',
            'phone.digits_between' => 'Enter 7 to 15 digits only, without spaces or symbols.',
        ]);

        $residentFields = ['first_name', 'middle_name', 'last_name', 'suffix', 'birth_date', 'gender', 'civil_status', 'purok', 'address', 'phone'];
        $identityFields = ['first_name', 'middle_name', 'last_name', 'suffix', 'birth_date'];
        $profile = $user->hasRole('user') ? collect($data)->only($residentFields)->all() : [];
        $identityChanged = $profile && $user->is_verified && collect($identityFields)->contains(
            function (string $field) use ($user, $profile): bool {
                $savedValue = $field === 'birth_date' ? $user->birth_date?->format('Y-m-d') : (string) $user->{$field};
                $newValue = $field === 'birth_date' ? substr($profile[$field], 0, 10) : (string) $profile[$field];

                return $savedValue !== $newValue;
            }
        );

        DB::transaction(function () use ($user, $data, $profile, $residentFields, $identityChanged): void {
            if ($profile) {
                $user->fill($profile);
                $user->name = collect([$profile['first_name'], $profile['middle_name'], $profile['last_name'], $profile['suffix']])->filter()->join(' ');
            } else {
                $user->name = $data['name'];
            }

            $user->email = strtolower($data['email']);
            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            if ($identityChanged) {
                $user->is_verified = false;
                $user->verified_at = null;
            }

            $user->save();

            if ($profile && ! $identityChanged && $user->is_verified && $user->resident_id) {
                $user->resident()->update(collect($profile)->only($residentFields)->all());
            }
        });

        if ($identityChanged) {
            return back()->with('status', 'Your details have been saved. Changes to your name or birth date are waiting for the barangay office to review.');
        }

        return back()->with('status', $profile
            ? 'Your account and resident details have been updated.'
            : 'Your account details have been updated.');
    }
}
