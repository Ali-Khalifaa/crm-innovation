<?php

namespace Modules\Invoices\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Contacts\Models\Contact;
use Modules\Core\Traits\BelongsToTenant;

class Invoice extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'invoice_number', 'contact_id',
        'issue_date', 'due_date', 'status',
        'subtotal', 'tax_rate', 'tax_amount', 'discount', 'total',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date'   => 'date',
        'subtotal'   => 'float',
        'tax_rate'   => 'float',
        'tax_amount' => 'float',
        'discount'   => 'float',
        'total'      => 'float',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\Modules\CrmAuth\Models\Tenant::class);
    }
}
