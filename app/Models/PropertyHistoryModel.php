<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyHistoryModel extends Model
{
    protected $table = 'property_history';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'property_id',
        'property_title',
        'action',
        'changed_by',
        'changed_by_id',
        'changed_by_role',
        'old_values',
        'new_values',
        'created_at'
    ];
    protected $useTimestamps = false;
    
    public function logPropertyAction($propertyId, $propertyTitle, $action, $oldValues = null, $newValues = null)
    {
        $session = session();
        $user = $session->get('user');
        
        $data = [
            'property_id' => $propertyId,
            'property_title' => $propertyTitle,
            'action' => $action,
            'changed_by' => $user['name'] ?? 'System',
            'changed_by_id' => $user['id'] ?? 0,
            'changed_by_role' => $user['role'] ?? 'system',
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->insert($data);
    }
}