<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class ShopPolicy extends GenericPolicy
{
    use HandlesAuthorization;

    public function create(): bool
    {
        return $this->user_is_not_super_admin;
    }

    public function edit(): bool
    {
        return $this->user_is_not_super_admin && $this->userHasAccessToShop(auth()->user(),request()->route('shop'));
    }

    public function delete(): bool
    {
        return $this->user_is_not_super_admin && $this->userHasAccessToShop(auth()->user(),request()->route('shop'));
    }


}
