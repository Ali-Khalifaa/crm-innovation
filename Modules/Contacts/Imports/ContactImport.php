<?php

namespace Modules\Contacts\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Modules\Contacts\Models\Contact;
use Illuminate\Support\Str;

class ContactImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    private int $tenantId;
    private int $userId;
    private int $importedCount = 0;
    private array $errors = [];

    public function __construct(int $tenantId, int $userId)
    {
        $this->tenantId = $tenantId;
        $this->userId   = $userId;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $firstName = trim($row['first_name'] ?? $row['الاسم_الاول'] ?? '');
            if (empty($firstName)) {
                $this->errors[] = "Row " . ($index + 2) . ": first_name is required";
                continue;
            }

            // Check duplicate email within tenant
            $email = trim($row['email'] ?? $row['البريد_الالكتروني'] ?? '');
            if ($email && Contact::where('tenant_id', $this->tenantId)->where('email', $email)->exists()) {
                $this->errors[] = "Row " . ($index + 2) . ": email {$email} already exists";
                continue;
            }

            $status = in_array(strtolower($row['status'] ?? ''), ['lead', 'customer', 'inactive'])
                ? strtolower($row['status'])
                : 'lead';

            $leadSource = trim($row['lead_source'] ?? $row['مصدر_العميل'] ?? '');
            $allowedSources = ['website', 'referral', 'social_media', 'email_campaign', 'cold_call', 'event', 'other'];
            if (!in_array($leadSource, $allowedSources)) {
                $leadSource = null;
            }

            Contact::create([
                'tenant_id'   => $this->tenantId,
                'first_name'  => $firstName,
                'last_name'   => trim($row['last_name'] ?? $row['الاسم_الاخير'] ?? ''),
                'email'       => $email ?: null,
                'phone'       => trim($row['phone'] ?? $row['الهاتف'] ?? '') ?: null,
                'company'     => trim($row['company'] ?? $row['الشركة'] ?? '') ?: null,
                'address'     => trim($row['address'] ?? $row['العنوان'] ?? '') ?: null,
                'notes'       => trim($row['notes'] ?? $row['ملاحظات'] ?? '') ?: null,
                'status'      => $status,
                'lead_source' => $leadSource,
                'created_by'  => $this->userId,
            ]);

            $this->importedCount++;
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getErrors(): array
    {
        return array_merge($this->errors, array_map(fn($e) => $e->getMessage(), $this->failures()));
    }
}
