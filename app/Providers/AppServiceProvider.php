<?php

namespace App\Providers;

use App\Policies\CustomerPolicy;
use App\Policies\GenericPolicy;
use App\Policies\ProductPolicy;
use App\Policies\RolePolicy;
use App\Policies\SalePurchasePolicy;
use App\Policies\ShopPolicy;
use App\Policies\StockAdjustmentReasonPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('create_shop', [ShopPolicy::class, 'create']);
        Gate::define('update_shop', [ShopPolicy::class, 'edit']);
        Gate::define('delete_shop', [ShopPolicy::class, 'delete']);

        Gate::define('create_user', [UserPolicy::class, 'create']); // this gate is used for GET/POST request when the user is first time created
        Gate::define('update_user', [UserPolicy::class, 'update']); // this gate is used for GET/POST request when the user is edited
        Gate::define('delete_user', [UserPolicy::class, 'delete']);
        Gate::define('list_users', [UserPolicy::class, 'showUsers']);
        Gate::define('list_owner_users', [UserPolicy::class, 'showOwnerUsers']);
        Gate::define('request_and_logged_in_user_equal', [UserPolicy::class, 'requestAndLoggedInUserEqual']);

        Gate::define('create_role', [RolePolicy::class, 'create']);
        Gate::define('update_role', [RolePolicy::class, 'update']);

        Gate::define('make_sale_or_purchase', [SalePurchasePolicy::class, 'makeSaleOrPurchase']);

        Gate::define('create_customer', [CustomerPolicy::class, 'create']);
        Gate::define('update_customer', [CustomerPolicy::class, 'update']);
        Gate::define('delete_customer', [CustomerPolicy::class, 'delete']);
        Gate::define('list_customers', [CustomerPolicy::class, 'showCustomers']);

        Gate::define('create_supplier', [SupplierPolicy::class, 'create']);
        Gate::define('update_supplier', [SupplierPolicy::class, 'update']);
        Gate::define('delete_supplier', [SupplierPolicy::class, 'delete']);
        Gate::define('list_suppliers', [SupplierPolicy::class, 'showSuppliers']);

        Gate::define('user_has_access_to_shop', [GenericPolicy::class, 'userHasAccessToShop']);

        Gate::define('create_stock_adjustment_reason', [StockAdjustmentReasonPolicy::class, 'create']);
        Gate::define('update_stock_adjustment_reason', [StockAdjustmentReasonPolicy::class, 'update']);
        Gate::define('delete_stock_adjustment_reason', [StockAdjustmentReasonPolicy::class, 'delete']);
        Gate::define('list_stock_adjustment_reasons', [StockAdjustmentReasonPolicy::class, 'showStockAdjustmentReasons']);

    }
}
