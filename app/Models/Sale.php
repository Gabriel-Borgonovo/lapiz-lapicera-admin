<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_name',
        'client_company',
        'total_amount',
        'total_before_adjustments',
        'discount_percent',
        'surcharge_percent'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }
}
