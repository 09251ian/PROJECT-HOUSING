<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'actor_user_id',
        'actor_role',
        'activity_type',
        'entity_type',
        'entity_id',
        'metadata',
        'ip_address',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // ========== ADD THIS METHOD ==========
    /**
     * Log an activity to audit log
     */
    public function logActivity($activityType, $entityType = null, $entityId = null, $metadata = null)
    {
        $session = session();
        $user = $session->get('user');
        
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'unknown';
        
        $data = [
            'actor_user_id' => $user['id'] ?? 0,
            'actor_role'    => $user['role'] ?? 'guest',
            'activity_type' => $activityType,
            'entity_type'   => $entityType,
            'entity_id'     => $entityId,
            'metadata'      => $metadata ? json_encode($metadata) : null,
            'ip_address'    => $ipAddress,
            'created_at'    => date('Y-m-d H:i:s')
        ];
        
        $result = $this->insert($data);
        
        // ========== ADD THIS BLOCK ==========
        if ($result) {
            $data['id'] = $this->getInsertID();
            $this->sendWebSocketNotification($data);
        }
        // ========== END ADD ==========
        
        return $result;
    }

    private function sendWebSocketNotification($auditData)
    {
        try {
            $client = \Config\Services::curlrequest();
            $client->post('http://localhost:3000/new-audit-log', [
                'json' => $auditData,
                'timeout' => 2
            ]);
        } catch (\Exception $e) {
            log_message('error', 'WebSocket audit notification failed: ' . $e->getMessage());
        }
    }
}