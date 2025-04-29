<?php

namespace App\Exports;

use App\Models\Mitra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MitrasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search;
    protected $groupId;

    public function __construct($search = null, $groupId = null)
    {
        $this->search = $search;
        $this->groupId = $groupId;
    }

    public function collection()
    {
        $query = Mitra::with(['mitraGroup', 'banks', 'user']);

        // Apply search filter if provided
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone1', 'like', '%' . $this->search . '%');
            });
        }

        // Apply group filter if provided
        if ($this->groupId) {
            $query->where('mitra_group_id', $this->groupId);
        }

        return $query->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'Code',
            'Name',
            'Group',
            'Phone',
            'Email',
            'Address',
            'Payment Terms',
            'Due Terms',
            'NPWP',
            'Status',
            'Created Date'
        ];
    }

    public function map($mitra): array
    {
        return [
            $mitra->code,
            $mitra->name,
            $mitra->mitraGroup ? $mitra->mitraGroup->name : 'Not Assigned',
            $mitra->phone1,
            $mitra->email ?? '-',
            $mitra->address_office_indo ?? '-',
            $mitra->syarat_bayar > 0 ? $mitra->syarat_bayar . ' days' : 'Cash',
            $mitra->batas_tempo > 0 ? $mitra->batas_tempo . ' days' : 'N/A',
            $mitra->npwp ?? '-',
            $mitra->status ? 'Active' : 'Inactive',
            $mitra->created_date ? $mitra->created_date->format('d M Y') : 
                ($mitra->created_at ? $mitra->created_at->format('d M Y') : '-')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}