<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UsersController extends Controller
{
    public function index(Request $request): View
    {
        $archived = $request->boolean('archived');
        $users = $archived ? User::onlyTrashed() : User::query();

        return view('admin.users.index', [
            'users' => $users->orderBy('is_verified')->orderBy('name')->paginate(20)->withQueryString(),
            'archived' => $archived,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', ['roles' => ['staff', 'admin']]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['staff', 'admin'])],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        User::create($data);

        return to_route('admin.users.index')->with('status', 'Account created.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', ['account' => $user, 'roles' => User::ROLES]);
    }

    public function verify(User $user): RedirectResponse
    {
        if ($user->role !== 'user') {
            return back()->withErrors(['verification' => 'Only resident accounts can be verified.']);
        }

        if ($user->is_verified) {
            return to_route('admin.users.edit', $user)->with('status', 'This account is already verified.');
        }

        DB::transaction(function () use ($user): void {
            $account = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            if ($account->is_verified) {
                return;
            }

            $resident = $account->resident_id ? Resident::find($account->resident_id) : null;

            if (! $resident) {
                $matchingResidents = Resident::query()
                    ->whereRaw('LOWER(first_name) = ?', [mb_strtolower($account->first_name)])
                    ->whereRaw('LOWER(last_name) = ?', [mb_strtolower($account->last_name)])
                    ->whereDate('birth_date', $account->birth_date)
                    ->limit(2)
                    ->get();

                if ($matchingResidents->count() > 1) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'verification' => 'More than one resident record matches this name and birth date. Review the existing records before verifying.',
                    ]);
                }

                $resident = $matchingResidents->first();
            }

            $resident ??= Resident::create([
                'first_name' => $account->first_name,
                'middle_name' => $account->middle_name,
                'last_name' => $account->last_name,
                'suffix' => $account->suffix,
                'birth_date' => $account->birth_date,
                'gender' => $account->gender,
                'civil_status' => $account->civil_status,
                'purok' => $account->purok,
                'address' => $account->address,
                'phone' => $account->phone,
                'is_active' => true,
            ]);

            if ($resident->exists) {
                $resident->update([
                    'first_name' => $account->first_name,
                    'middle_name' => $account->middle_name,
                    'last_name' => $account->last_name,
                    'suffix' => $account->suffix,
                    'birth_date' => $account->birth_date,
                    'gender' => $account->gender,
                    'civil_status' => $account->civil_status,
                    'purok' => $account->purok,
                    'address' => $account->address,
                    'phone' => $account->phone,
                ]);
            }

            $account->resident_id = $resident->id;
            $account->is_verified = true;
            $account->verified_at = now();
            $account->save();
        });

        return to_route('admin.users.edit', $user)->with('status', 'Resident account verified and linked to a resident record.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(User::ROLES)],
            'password' => ['nullable', 'string', 'min:12', 'confirmed'],
        ]);

        if ($user->is($request->user()) && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot remove your own administrator role.'])->withInput();
        }

        if ($user->role === 'admin' && $data['role'] !== 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->withErrors(['role' => 'Promote another administrator before changing the only admin account.'])->withInput();
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return to_route('admin.users.index')->with('status', 'Account updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['account' => 'You cannot delete the account you are currently using.']);
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->withErrors(['account' => 'Promote another administrator before deleting the only admin account.']);
        }

        $user->delete();

        return to_route('admin.users.index')->with('status', 'Account archived. The resident can no longer sign in, and their service history is kept.');
    }

    public function restore(int $userId): RedirectResponse
    {
        User::onlyTrashed()->findOrFail($userId)->restore();

        return to_route('admin.users.index', ['archived' => 1])->with('status', 'Account restored. The resident can sign in again.');
    }
}
