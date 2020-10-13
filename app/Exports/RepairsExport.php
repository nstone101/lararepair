<?php
namespace App\Exports;

use App\Repair;
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

class RepairsExport implements FromCollection, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithEvents {
    use Exportable;

    public $rows = 1;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $repairs = Repair::with('customer', 'status', 'assigned_user', 'created_by', 'updated_by')->get()->toArray();

        $data = [];
        foreach ($repairs as $repair) {
        	$data[] = [
        		'id'=> $repair['code'],
        		'name'=> @$repair['customer']['name'],
        		'serial_number' => $repair['serial_number'],
        		'defect' => $repair['defect'],
        		'model' => $repair['model'],
        		'created_at' => $repair['created_at'],
        		'status' => @$repair['status']['label'],
        		'assigned_to' => @$repair['assigned_user']['first_name'] . ' ' . $repair['assigned_user']['last_name'],
        		'created_by' => @$repair['created_by']['first_name'] . ' ' . $repair['created_by']['last_name'],
        		'updated_by' => @$repair['updated_by']['first_name'] . ' ' . $repair['updated_by']['last_name'],
        		'grand_total' => $repair['grand_total'],
        		'paid' => $repair['paid'],
        	];
            $this->rows++;
        }

        return collect($data);
    }

    public function columnFormats(): array
    {
        return [
            'K' => NumberFormat::FORMAT_NUMBER_00,
            'L' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function registerEvents(): array
    {

        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:L'.$this->rows; // All headers

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FFFF0000'],
                        ],
                    ],
                ];

                $header = 'A1:L1';
                $event->sheet->getDelegate()->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('94ce58');
                $style = array(
                    'font' => array('bold' => true,),
                    'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,),
                );
                $event->sheet->getDelegate()->getStyle($header)->applyFromArray($style);
                

                $header = 'A2:L'.$this->rows;
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
  
  	public function headings(): array
    {

        return [
			__('repair.code'),
			__('customer.name'),
			__('repair.serial_number'),
			__('repair.defect'),
			__('repair.model'),
			__('repair.created_at'),
			__('repair.status'),
			__('repair.assigned_to'),
			__('repair.created_by'),
			__('repair.updated_by'),
			__('repair.grand_total'),
			__('repair.paid'),
        ];
    }
   
}

