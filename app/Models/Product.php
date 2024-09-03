<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'barcode',
        'name',
        'image',
        'category',
        'unit_type',
        'purchase_price',
        'profit_margin',
        'sale_price',
        'stock',
    ];

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function ticketItems()
    {
        return $this->hasMany(TicketItem::class);
    }
}
