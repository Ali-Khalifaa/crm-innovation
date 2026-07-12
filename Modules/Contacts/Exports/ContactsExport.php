<?php

namespace Modules\Contacts\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\Contacts\Models\Contact;

class ContactsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private readonly int $tenantId) {}

    public function collection()
    {
        return Contact::withoutGlobalScope('tenant')
            ->where('tenant_id', $this->tenantId)
            ->orderBy('first_name')
            ->get();
    }

    public function headings(): array
    {
        return ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Company', 'Status', 'Created At'];
    }

    public function map($contact): array
    {
        return [
            $contact->id,
            $contact->first_name,
            $contact->last_name,
            $contact->email,
            $contact->phone,
            $contact->company,
            $contact->status,
            $contact->created_at->format('Y-m-d'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
