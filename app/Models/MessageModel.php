<?php
namespace App\Models;
use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table = 'messages';
    protected $allowedFields = ['sender_id','receiver_id','property_id','message','created_at'];
}
