<?php

namespace App\Models;

use CodeIgniter\Model;

class FavoriteModel extends Model
{
    protected $table = 'favorites';
    protected $primaryKey = 'id';
    protected $allowedFields = ['buyer_id', 'property_id', 'created_at'];

    public function isFavorited(int $buyerId, int $propertyId): bool
    {
        return $this->where('buyer_id', $buyerId)
            ->where('property_id', $propertyId)
            ->countAllResults() > 0;
    }

    public function toggleFavorite(int $buyerId, int $propertyId): bool
    {
        $existing = $this->where('buyer_id', $buyerId)
            ->where('property_id', $propertyId)
            ->first();

        if ($existing) {
            return (bool) $this->where('id', $existing['id'])->delete();
        }

        return (bool) $this->insert([
            'buyer_id' => $buyerId,
            'property_id' => $propertyId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getFavoritesForBuyer(int $buyerId): array
    {
        $builder = $this->db->table('favorites');
        $builder->select('favorites.id AS favorite_id, properties.*, users.name AS seller_name');
        $builder->join('properties', 'properties.id = favorites.property_id');
        $builder->join('users', 'users.id = properties.seller_id');
        $builder->where('favorites.buyer_id', $buyerId);
        $builder->where('properties.is_archived', 0);
        $builder->orderBy('favorites.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }
}

