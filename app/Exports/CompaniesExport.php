<?php

namespace App\Exports;

use App\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


use PhpOffice\PhpSpreadsheet\Shared\Date;

class CompaniesExport implements FromCollection, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Company::orderBy('name_comercial', 'asc')->get();
    }

    public function map($company): array
    {
        return [
            $company->name_comercial,
            $company->name,
            $company->cif,
            $company->address,
            $company->cp,
            $company->city,
            $company->province,
            $company->phone,
            $company->phone2,
            $company->web,
            $company->blocked ? 'Si' : 'No',
            $company->payed ? 'Si' : 'No',
            $company->type == 0 ? 'Básico' : 'Profesional',
            $company->bank_count,
        ];
    }

    public function headings(): array
    {
        return [
            'Nombre comercial',
            'Nombre',
            'CIF',
            'Dirección',
            'Código Postal',
            'Población',
            'Provincia',
            'Teléfono',
            'Teléfono',
            'Web',
            'Bloqueado',
            'Pagado',
            'Tipo',
            'Cuenta bancaria',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
            'M' => NumberFormat::FORMAT_TEXT,
            'N' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $cellRange = 'A1:N1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }

}
