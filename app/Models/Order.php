<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['notification_type', 'consignment_id', 'invoice', 'status', 'cod_amount', 'delivery_charge', 'tracking_message', 'updated_at'];

    protected $table = 'orders';
}
