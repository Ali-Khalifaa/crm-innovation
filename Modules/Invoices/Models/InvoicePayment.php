<?php

namespace Modules\Invoices\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Traits\BelongsToTenant;
use Modules\CrmAuth\Models\User;

class InvoicePayment extends Model
{
    use BelongsToTenant;

    protected $table = 'invoice_payments';

    protected $fillable = [
        'tenant_id', 'invoice_id', 'amount', 'payment_date', 'method', 'reference', 'notes', 'created_by',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
