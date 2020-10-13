<?php

namespace App\Exports;

use App\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class CustomersExport implements FromCollection, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithEvents {

    use Exportable;

    public $rows = 1;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $customers = Company::onlyCustomers()->get()->toArray();

        $data = [];
        foreach ($customers as $customer) {
            $data[] = [
                'name' => $customer['name'],
                'company' => $customer['company'],
                'tax_number' => $customer['tax_number'],
                'identity' => $customer['identity'],
                'address' => $customer['address'],
                'city' => $customer['city'],
                'state' => $customer['state'],
                'postal_code' => $customer['postal_code'],
                'country' => $customer['country'],
                'phone' => $customer['phone'],
                'email' => $customer['email'],
            ];
            $this->rows++;
        }

        return collect($data);
    }

    public function registerEvents(): array
    {

        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:K'.$this->rows; // All headers

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FFFF0000'],
                        ],
                    ],
                ];

                $header = 'A1:K1';
                $event->sheet->getDelegate()->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('94ce58');
                $style = array(
                    'font' => array('bold' => true,),
                    'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,),
                );
                $event->sheet->getDelegate()->getStyle($header)->applyFromArray($style);
                

                $header = 'A2:K'.$this->rows;
                $event->sheet->getDelegate()->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('fdbf2d');
                $style = array(
                    'font' => array('bold' => true,),
                    'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_GENERAL,),
                );
                $event->sheet->getDelegate()->getStyle($header)->applyFromArray($style);



                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
            },
        ];
    }
  
    public function columnFormats(): array
    {
        return [
        ];
    }

    public function headings(): array
    {

        return [
            __('customer.name'),
            __('customer.company'),
            __('customer.tax_number'),
            __('customer.identity'),
            __('customer.address'),
            __('customer.city'),
            __('customer.state'),
            __('customer.postal_code'),
            __('customer.country'),
            __('customer.phone'),
            __('customer.email'),
        ];
    }
}
