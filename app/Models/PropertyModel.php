<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyModel extends Model
{
    protected $table = 'properties';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'seller_id', 'title', 'description', 'price', 
        'location', 'image_path', 'is_archived'
    ];
    
    // Disable automatic timestamp handling
    protected $useTimestamps = false;
    
    // If you want to keep timestamps but handle them manually, set to false above
    // and remove created_at/updated_at from your insert/update arrays

    public function getFilteredProperties($search = '', $location = '', $price_range = '')
    {
        $builder = $this->db->table('properties')
            ->select('properties.*, users.name as seller_name')
            ->join('users', 'users.id = properties.seller_id')
            ->where('properties.is_archived', 0);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('properties.title', $search)
                ->orLike('properties.description', $search)
                ->groupEnd();
        }

        if (!empty($location)) {
            $builder->like('properties.location', $location);
        }

        if (!empty($price_range)) {
            switch ($price_range) {
                case '1':
                    $builder->where('properties.price <', 1000000);
                    break;
                case '2':
                    $builder->where('properties.price >=', 1000000)
                            ->where('properties.price <=', 10000000);
                    break;
                case '3':
                    $builder->where('properties.price >=', 10000000)
                            ->where('properties.price <=', 20000000);
                    break;
                case '4':
                    $builder->where('properties.price >=', 20000000)
                            ->where('properties.price <=', 30000000);
                    break;
                case '5':
                    $builder->where('properties.price >=', 30000000)
                            ->where('properties.price <=', 40000000);
                    break;
                case '6':
                    $builder->where('properties.price >=', 40000000)
                            ->where('properties.price <=', 50000000);
                    break;
                case '7':
                    $builder->where('properties.price >', 50000000);
                    break;
            }
        }

        return $builder->orderBy('properties.id', 'DESC')->get()->getResultArray();
    }
}