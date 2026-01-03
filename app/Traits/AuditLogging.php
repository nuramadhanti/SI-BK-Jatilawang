<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait AuditLogging
{
    /**
     * Log sensitive operations for audit trail
     * 
     * @param string $action Action name (create, update, delete, approve, reject)
     * @param string $model Model name
     * @param int|string $id Record ID
     * @param array $changes What was changed
     * @param string $status Status (success, failed)
     * @param string $reason Reason for action
     */
    public function auditLog($action, $model, $id, $changes = [], $status = 'success', $reason = '')
    {
        $user = auth()->user();
        
        Log::channel('audit')->info('Audit Log', [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'action' => $action,
            'model' => $model,
            'record_id' => $id,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'user_role' => $user?->role,
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'changes' => $changes,
            'status' => $status,
            'reason' => $reason,
            'url' => request()->url(),
            'method' => request()->method(),
        ]);
    }

    /**
     * Log approval/rejection actions
     */
    public function auditApproval($action, $permohonanId, $reason = '', $status = 'success')
    {
        $this->auditLog(
            $action,
            'PermohonanKonseling',
            $permohonanId,
            ['status' => $action === 'approve' ? 'disetujui' : 'ditolak'],
            $status,
            $reason
        );
    }

    /**
     * Log data modifications
     */
    public function auditModification($model, $id, $oldData, $newData, $status = 'success')
    {
        $changes = [];
        foreach ($newData as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] != $value) {
                $changes[$key] = [
                    'old' => $oldData[$key],
                    'new' => $value
                ];
            }
        }

        if (!empty($changes)) {
            $this->auditLog('update', $model, $id, $changes, $status);
        }
    }

    /**
     * Log deletion
     */
    public function auditDeletion($model, $id, $reason = '')
    {
        $this->auditLog('delete', $model, $id, [], 'success', $reason);
    }
}
