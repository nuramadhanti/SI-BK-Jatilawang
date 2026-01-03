# Error Handling & Logging Documentation

## 📋 Overview

Comprehensive error handling and audit logging system untuk SI-BK-Jatilawang dengan:
- Custom exception handler dengan proper HTTP responses
- Audit logging untuk tracking sensitive operations
- Request/response logging dengan performance metrics
- User-friendly error pages
- Automatic log rotation dan cleanup

---

## 🏗️ Komponen Utama

### 1. **Exception Handler** (`app/Exceptions/Handler.php`)

#### Features:
- ✅ Centralized exception handling
- ✅ Automatic logging untuk semua exceptions
- ✅ Custom rendering untuk specific exception types
- ✅ Sensitive data redaction (password, token, etc)
- ✅ User-friendly error messages

#### Exception Types Handled:

```php
ModelNotFoundException        → 404 with "Data tidak ditemukan"
NotFoundHttpException         → 404 with "Halaman tidak ditemukan"
AuthorizationException        → 403 with "Anda tidak memiliki akses"
AuthenticationException       → 401 with "Silakan login terlebih dahulu"
ValidationException           → 422 with validation errors
```

#### Logged Information:
```
- Exception class & message
- File & line number
- Stack trace
- User ID & name
- Request method & URL
- Client IP & User Agent
- Sanitized input data
```

---

### 2. **Audit Logging Trait** (`app/Traits/AuditLogging.php`)

#### Usage in Controllers:

```php
use App\Traits\AuditLogging;

class PermohonanKonselingController extends Controller
{
    use AuditLogging;

    public function approve(Request $request, $id)
    {
        // ... business logic ...
        
        // Log approval action
        $this->auditLog(
            'approve',
            'PermohonanKonseling',
            $id,
            ['status' => 'disetujui'],
            'success',
            'Approved by guru BK'
        );
    }

    public function reject(Request $request, $id)
    {
        $this->auditApproval('reject', $id, $request->alasan_penolakan, 'success');
    }
}
```

#### Audit Log Entry Example:

```json
{
    "timestamp": "2026-01-03 14:30:45",
    "action": "approve",
    "model": "PermohonanKonseling",
    "record_id": "123",
    "user_id": "5",
    "user_name": "Ibu Siti (BK)",
    "user_role": "guru",
    "user_ip": "192.168.1.100",
    "user_agent": "Mozilla/5.0...",
    "changes": {
        "status": "disetujui"
    },
    "status": "success",
    "reason": "Approved by guru BK",
    "url": "/permohonan-konseling/approve/123",
    "method": "POST"
}
```

#### Available Methods:

```php
// General audit log
$this->auditLog($action, $model, $id, $changes, $status, $reason);

// Approval/Rejection shortcut
$this->auditApproval($action, $permohonanId, $reason, $status);

// Log modifications (compare before/after)
$this->auditModification($model, $id, $oldData, $newData, $status);

// Log deletion
$this->auditDeletion($model, $id, $reason);
```

---

### 3. **Request/Response Logging Middleware** (`app/Http/Middleware/LogRequests.php`)

#### Features:
- ✅ Log every incoming request (except static assets)
- ✅ Measure response time (milliseconds)
- ✅ Detect slow requests (> 1 second)
- ✅ Track user & IP for security
- ✅ Skip logging for assets (CSS, JS, images)

#### Logged Data:
```
- HTTP method & path
- Response status code
- Duration in milliseconds
- User ID & name
- Client IP & User Agent
```

#### Example Log Entry:

```
[2026-01-03 14:30:45] local.INFO: Response Sent
{
    "method": "POST",
    "path": "permohonan-konseling",
    "status_code": 201,
    "duration_ms": 125.50,
    "user_id": 15
}

[2026-01-03 14:35:20] local.WARNING: Slow Request Detected
{
    "method": "GET",
    "path": "laporan",
    "duration_ms": 1250.75,
    "user_id": 5
}
```

---

### 4. **Error Pages**

#### 404 Not Found (`resources/views/errors/404.blade.php`)
- ✅ User-friendly message
- ✅ Back button & home button
- ✅ Bootstrap styling

#### 401 Unauthenticated (`resources/views/errors/401.blade.php`)
- ✅ Login prompt
- ✅ Dashboard link

#### 403 Forbidden (`resources/views/errors/403.blade.php`)
- ✅ Unauthorized access message
- ✅ Admin contact suggestion

#### 500 Server Error (`resources/views/errors/500.blade.php`)
- ✅ Apology message
- ✅ Error ID for support reference

---

### 5. **Log Management**

#### Log Files Location:
```
storage/logs/
├── laravel.log              # General application logs (14 days)
├── audit.log                # Audit trail (90 days)
├── errors.log               # Error logs only (30 days)
└── [daily versions]         # e.g., laravel-2026-01-03.log
```

#### Log Channels (config/logging.php):

```php
'daily'    → Daily rotation, 14 days retention
'audit'    → Audit logs, 90 days retention
'errors'   → Error logs only, 30 days retention
```

#### Cleanup Command:

```bash
# Run manually
php artisan logs:cleanup

# Schedule in kernel.php
$schedule->command('logs:cleanup')
         ->daily()
         ->at('02:00');  // Run at 2 AM every day
```

---

## 🚀 Integration Steps

### 1. Register Middleware (app/Http/Kernel.php)

```php
protected $middlewareGroups = [
    'web' => [
        // ... other middleware ...
        \App\Http\Middleware\LogRequests::class,
    ],
];
```

### 2. Update Controllers to Use Audit Logging

```php
use App\Traits\AuditLogging;

class KriteriaController extends Controller
{
    use AuditLogging;

    public function destroy(Kriteria $kriteria)
    {
        $criteriaId = $kriteria->id;
        $kriteria->delete();
        
        $this->auditDeletion('Kriteria', $criteriaId, 'Deleted by guru BK');
        
        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria berhasil dihapus.');
    }
}
```

---

## 📊 Monitoring & Debugging

### View Recent Errors:

```bash
# Last 100 lines of error log
tail -100 storage/logs/errors.log

# Real-time monitoring
tail -f storage/logs/laravel.log

# Search for specific user's actions
grep "user_id\": 5" storage/logs/audit.log
```

### Check Performance:

```bash
# Find slow requests
grep "Slow Request" storage/logs/laravel.log

# Calculate average response times
grep "Response Sent" storage/logs/laravel.log | \
    grep "duration_ms" | \
    awk -F': ' '{sum+=$2; count++} END {print "Average: " sum/count " ms"}'
```

---

## 🔒 Security Best Practices

### 1. **Sensitive Data Protection**
- ✅ Passwords automatically redacted
- ✅ API tokens masked
- ✅ Credit cards sanitized
- ✅ Custom fields can be added to redaction list

### 2. **Audit Trail Immutability**
- ✅ Store in separate channel
- ✅ Long retention (90 days)
- ✅ Use for compliance & forensics

### 3. **Access Control**
- ✅ Only authorized users can perform actions
- ✅ Every action tracked with user info
- ✅ IP logging for suspicious access detection

---

## 🐛 Debugging Tips

### 1. **Find Errors by User**
```bash
grep "user_id\": 15" storage/logs/errors.log
```

### 2. **Find Errors by Time Range**
```bash
grep "2026-01-03 14:" storage/logs/errors.log
```

### 3. **Find Specific Operations**
```bash
grep "\"action\": \"approve\"" storage/logs/audit.log
```

### 4. **Monitor Performance**
```bash
# Check which endpoints are slowest
grep "Response Sent" storage/logs/laravel.log | \
    sort -t: -k12 -nr | head -20
```

---

## 📈 Log Retention Policy

| Log Type | Retention | Rotation | Purpose |
|----------|-----------|----------|---------|
| laravel.log | 14 days | Daily | General application logs |
| audit.log | 90 days | Daily | Compliance & audit trail |
| errors.log | 30 days | Daily | Error tracking & debugging |

---

## 🔧 Configuration

### environment variables (.env):

```env
LOG_CHANNEL=stack
LOG_LEVEL=debug
LOG_STACK=daily
LOG_DAILY_DAYS=14
LOG_AUDIT_DAYS=90
LOG_ERROR_DAYS=30
```

---

## 💡 Future Enhancements

- [ ] Dashboard to view logs in UI
- [ ] Email alerts for critical errors
- [ ] Real-time error notifications
- [ ] Export audit logs to compliance report
- [ ] Search & filter UI for logs
- [ ] Integration with error tracking services (Sentry)
- [ ] Performance metrics dashboard

---

**Status:** ✅ IMPLEMENTED & TESTED
**Last Updated:** 3 January 2026
