<?php
namespace App\Models;
use CodeIgniter\Model;

class OfferModel extends Model
{
    protected $table = 'offers';
    protected $allowedFields = ['property_id','buyer_id','amount','status'];
}
