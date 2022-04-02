<?php

namespace App\Policies;

use App\Models\User;
use App\Repository\RoleRepository;
use App\Repository\ShopRepository;
use App\Repository\UserRepository;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\App;

class GenericPolicy
{
    use HandlesAuthorization;

    protected $user_is_not_super_admin;


//    /**
//     * Perform pre-authorization checks.
//     *
//     * @param User $user
//     * @param string $ability
//     * @return void|bool
//     */
//    public function before(User $user, $ability)
//    {
//        if (isSuperAdmin()) {
//            return true;
//        }
//    }


    public function __construct()
    {
        $this->user_is_not_super_admin = !isSuperAdmin();
    }


    public function userHasAccessToShop(User $user, $shop_id): bool
    {
        $shop_repository = App::make(ShopRepository::class);

        $user_shops = $shop_repository->getLoggedInUserShops();

        // checking if the shop_id in request exists in '$user_shops' collection
        return (bool)$user_shops->firstWhere('id', $shop_id);
    }


    protected function userHasAccessToRole($role_id): bool
    {
        $role_repository = App::make(RoleRepository::class);

        $user_roles = $role_repository->getUserChildrenRoles();

        return (bool)$user_roles->firstWhere('id', $role_id);
    }

    protected function loggedInUserHasAccessToOtherUser($other_user_id): bool
    {
        $user_can_access_other_user = false;

        $user_repository = App::make(UserRepository::class);

        // getting all the shops that logged-in user has access to and other user has access to
        $users_with_shops = $user_repository->getUsersWithShops([auth()->id(), $other_user_id]);

        if ($users_with_shops->count() === 1) { // user is editing himself/herself

            $user_can_access_other_user = true;

        } else if ($users_with_shops->count() === 2) { // i.e. logged-in user's shops + 2nd user's shops

            $logged_in_user_shop_ids = $users_with_shops->firstWhere('id', auth()->id())->shops->pluck('id')->toArray();

            $second_user_shop_ids = $users_with_shops->firstWhere('id', $other_user_id)->shops->pluck('id')->toArray();

            foreach ($logged_in_user_shop_ids as $logged_in_user_shop_id) {

                foreach ($second_user_shop_ids as $second_user_shop_id) {

                    // making sure that the logged-in has access to any one of the same shop as the second user has
                    if ($logged_in_user_shop_id === $second_user_shop_id) {
                        $user_can_access_other_user = true;
                        break;
                    }

                }

            }

        }

        return $user_can_access_other_user;
    }

}
