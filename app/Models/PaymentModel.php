<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'offer_id',
        'buyer_id',
        'seller_id',
        'property_id',
        'amount',
        'status',
        'created_at',
    ];
}

