<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UsersExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public const AVAILABLE_FIELDS = [
        'name'      => 'Nama',
        'email'     => 'Email',
        'role'      => 'Role',
        'is_active' => 'Status',
        'created_at'=> 'Dibuat Pada',
    ];

    public function __construct(private array $fields = [])
    {
        if (empty($this->fields)) {
            $this->fields = array_keys(self::AVAILABLE_FIELDS);
        }
    }

    public function query()
    {
        return User::query()->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return array_map(fn($key) => self::AVAILABLE_FIELDS[$key], $this->fields);
    }

    public function map($user): array
    {
        return array_map(function ($field) use ($user) {
            return match ($field) {
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'is_active'  => $user->is_active ? 'Aktif' : 'Nonaktif',
                'created_at' => $user->created_at?->format('Y-m-d H:i'),
                default      => '',
            };
        }, $this->fields);
    }
}
