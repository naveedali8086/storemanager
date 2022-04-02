<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'city', 'owner_id', 'address', 'is_master', 'deleted_at', 'deleted_by', 'print_on_sale',
        'print_on_purchase', 'printer_name', 'operating_system', 'dialing_code', 'contact_number', 'time_zone_id'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'shop_user', 'shop_id', 'user_id', 'id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function timeZone()
    {
        return $this->belongsTo(TimeZone::class, 'time_zone_id', 'id');
    }

}
