<?php

namespace App\Repository;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class ShopRepository extends GenericRepository
{

    public function getLoggedInUserShopsForTable()
    {
        return $this->getPaginatedData(function () {

            $search_keyword = request()->query('search_keyword');

            return DB::table('shops as s')
                ->join('shop_user as su', 's.id', '=', 'su.shop_id')
                ->join('users as u', 's.owner_id', '=', 'u.id')
                ->whereNull('s.deleted_at')
                // only super admin can see all shops otherwise any other user can see shops assigned to him/her.
                ->when(!isSuperAdmin(), function ($query_when) {

                    $shop_ids = $this->getLoggedInUserShops()->pluck('id')->toArray();

                    return $query_when->whereIn('su.shop_id', $shop_ids);

                })
                ->when($search_keyword, function ($query_when) use ($search_keyword) {
                    return $query_when->where(function ($query) use ($search_keyword) {
                        $query->where('s.name', 'LIKE', "%$search_keyword%")
                            ->orWhere('s.city', 'LIKE', "%$search_keyword%")
                            ->orWhere('u.name', 'LIKE', "%$search_keyword%")
                            ->orWhere('u.email', 'LIKE', "%$search_keyword%");
                    });
                })
                ->select(['s.id', 's.name', 's.address', 's.deleted_at', 's.print_on_sale', 's.print_on_purchase',
                    's.printer_name', 's.operating_system', 's.city', 'u.name as owner', 'u.email'])
                ->groupBy(['s.id']);

        }, 'id');
    }


    public function getLoggedInUserShops()
    {
        return $this->getDataFromSessionOrDB('user_shops', function () {

            $shops = User::with(['shops' => function ($query) {

                $query->whereNull('deleted_at');

            }, 'shops.timeZone:id,value'])->select(['id'])->findOrFail(auth()->id())->shops;

            return $shops->except(['is_master', 'created_at', 'updated_at', 'deleted_at', 'deleted_by']);

        });

    }

}
