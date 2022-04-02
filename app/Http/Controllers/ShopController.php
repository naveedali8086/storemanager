<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShopStoreUpdateRequest;
use App\Models\Shop;
use App\Repository\ShopRepository;
use App\Repository\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ShopController extends Controller
{
    public function create()
    {
        Gate::authorize('create_shop');

        return view('layouts.vue', ['component' => "<shop/>"]);
    }


    public function edit($shop)
    {
        Gate::authorize('update_shop');

        $shop = Shop::query()->findOrFail($shop);

        return view('layouts.vue', ['component' => "<shop :shop='$shop'/>"]);
    }


    public function store(ShopStoreUpdateRequest $request, UserRepository $user_repository): JsonResponse
    {
        Gate::authorize('create_shop');

        $shop_data = array_merge($request->all(), ['owner_id' => auth()->user()->owner_id]);

        // getting all the users of single owner who have 'Shop Admin' role
        $shop_admins = $user_repository->getShopAdminsOfOwner($shop_data['owner_id'])->pluck('id')->toArray();

        DB::transaction(function () use ($shop_data, &$shop_admins) {

            $shop = Shop::query()->create($shop_data);

            // adding logged-in user's id into '$admin_users' ONLY if it does not already exist due to following two reasons:
            // 1. The logged-in user does have a permission to create shop but he/she is not actually a 'Shop Admin'
            // 2. To avoid 'Integrity Constraint Violation' error due to duplicate user_ids in '$admin_users'
            if (!in_array(auth()->id(), $shop_admins)) {
                $shop_admins[] = auth()->id();
            }

            // assigning newly created shop to all 'Shop Admin' users of single owner +  currently logged-in user
            $shop->users()->attach($shop_admins);

        });

        $this->has_err = false;

        $this->data['shops'] = $this->getLoggedInUserShops();

        return $this->sendResponse();
    }


    public function update(ShopStoreUpdateRequest $request, $shop): JsonResponse
    {
        Gate::authorize('update_shop');

        if (Shop::query()->where('id', '=', $shop)->update($request->all())) {
            $this->has_err = false;
        }

        $this->data['shops'] = $this->getLoggedInUserShops();

        return $this->sendResponse();
    }


    public function index(ShopRepository $repository)
    {
        if (\request()->ajax()) {

            $this->data = $repository->getLoggedInUserShopsForTable();

            $this->has_err = false;

            return $this->sendResponse();

        } else {

            if (isSuperAdmin()) {
                // as per the application's flow, super admin should not be able to create or update any shop
                $edit_allowed = false;
                $delete_allowed = false;
            } else {
                $edit_allowed = userHasPermission('update_shop');
                $delete_allowed = userHasPermission('delete_shop');
            }

            return view('layouts.vue', ['component' => "<shops-list :edit_allowed='$edit_allowed' :delete_allowed='$delete_allowed'>"]);
        }
    }

    public function destroy($shop): JsonResponse
    {
        Gate::authorize('delete_shop');

        if (Shop::query()->where('id', '=', $shop)->update(['deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => auth()->id()])) {
            $this->has_err = false;
        }

        $this->data['shops'] = $this->getLoggedInUserShops();

        return $this->sendResponse();
    }

    private function getLoggedInUserShops()
    {
        // putting logged-in user's shops in session whenever a new shop is created or existing shop is updated, if request is from web
        if (request()->session()) {
            // Note: using session()->now() is important here otherwise session()->remove()
            // and session()->forget() are not deleting session data instantly
            session()->now('user_shops', '');
            return (App::make(ShopRepository::class))->getLoggedInUserShops();
        } else {
            return [];
        }
    }

}
