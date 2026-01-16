<?php

namespace App\Actions\Traits;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait HasUser
{
    protected ?User $user = null;

    public function user(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    protected function resolveUser(): User
    {
        $user = $this->user ?? Auth::user();

        if (! $user) {
            throw new \RuntimeException('This action requires an authenticated user.');
        }

        return $user;
    }

    protected function resolveUserRole(RoleEnum $role): User
    {
        $user = $this->resolveUser();

        if ($user->role !== $role) {
            throw new \RuntimeException("This action requires a user with the role: {$role->value}.");
        }

        return $user;
    }
}
