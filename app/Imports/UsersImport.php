<?php

namespace App\Imports;

use App\Exports\UsersExport;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements SkipsOnFailure, ToModel, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;

    public int $successCount = 0;

    private function getFieldMap(): array
    {
        $map = [];
        foreach (UsersExport::AVAILABLE_FIELDS as $dbField => $label) {
            $map[Str::slug($label, '_')] = $dbField;
        }

        return $map;
    }

    public function model(array $row)
    {
        $map = $this->getFieldMap();
        $mappedData = [];

        foreach ($row as $header => $value) {
            if (isset($map[$header])) {
                $dbField = $map[$header];
                if ($dbField === 'is_active') {
                    $mappedData['is_active'] = filter_var($value ?? true, FILTER_VALIDATE_BOOLEAN);
                } else {
                    $mappedData[$dbField] = $value;
                }
            }
        }

        if (empty($mappedData['password'])) {
            $mappedData['password'] = Hash::make('password123'); // Default password
        }

        $this->successCount++;

        return new User($mappedData);
    }

    public function rules(): array
    {
        $map = $this->getFieldMap();

        $nameHeader = array_search('name', $map);
        $emailHeader = array_search('email', $map);
        $roleHeader = array_search('role', $map);

        return [
            $nameHeader => ['required', 'string', 'max:255'],
            $emailHeader => ['required', 'email', 'max:255', 'unique:users,email'],
            $roleHeader => ['required', 'in:Administrator,Staff'],
        ];
    }
}
