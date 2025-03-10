<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

use PhpOffice\PhpSpreadsheet\Shared\Date;

class ClientsExport implements FromCollection, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::where('role', 'user')->orWhere('role', 'admin')->orWhere('role', 'crew')
            ->orderBy('name', 'asc')->get();
    }

    public function map($user): array
    {

        $rol = '';
        switch($user->role){
            case 'user': $rol = 'Usuario'; break;
            case 'admin': $rol = 'Administrador de negocio'; break;
            case 'crew': $rol = 'Profesional'; break;
        }

        return [
            $user->name,
            $user->surname,
            $user->dni,
            $user->address,
            $user->cp,
            $user->city,
            $user->username,
            $user->email,
            $user->phone,
            $rol,
            Date::dateTimeToExcel($user->created_at),
        ];
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Apellidos',
            'DNI',
            'Dirección',
            'Código Postal',
            'Población',
            'Nombre de usuario',
            'Correo electrónico',
            'Teléfono',
            'Rol',
            'Fecha de alta'
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
            'K' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $cellRange = 'A1:K1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }

}
