<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'role'     => 'required|in:ong_representant,ong_agent',
        ])->validate();

        $role = Role::where('name', $input['role'])->first();

        return User::create([
            'name'     => $input['name'],
            'email'    => $input['email'],
            'password' => $input['password'],
            'role_id'  => $role?->id,
        ]);
    }
}
