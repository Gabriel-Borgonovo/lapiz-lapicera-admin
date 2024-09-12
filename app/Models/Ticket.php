<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'ticket_number',
        'pdf_path',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function ticketItems()
    {
        return $this->hasMany(TicketItem::class);
    }

}
