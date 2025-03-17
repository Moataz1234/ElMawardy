<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExcelExportService
{
    protected $spreadsheet;
    protected $sheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
    }

    public function exportAddRequests($requests, $date = null)
    {
        if ($date) {
            $requests = $requests->whereDate('created_at', $date);
        }

        // Set headers with all columns from your example
        $headers = [
            'Serial Number', 'Shop Name', 'Shop ID', 'Kind', 'Model',
            'Talab (YES/NO)', 'Gold Color', 'Stones', 'Metal Type', 
            'Metal Purity', 'Quantity', 'Weight', 'Rest Since', 'Source',
            'To Print', 'Stars', 'Semi or no', 'Average of Stones', 'Net Weight'
        ];

        // Apply headers
        foreach ($headers as $index => $header) {
            $column = chr(65 + $index); // Convert number to letter (A, B, C, etc.)
            $this->sheet->setCellValue($column . '1', $header);
        }

        // Style headers with green background except Stones column
        $headerRange = 'A1:S1'; // Updated to include all columns
        $this->sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '90EE90'], // Light green
            ],
        ]);

        // Style Stones column (H1) with salmon color
        $this->sheet->getStyle('H1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFA07A'], // Salmon color
            ],
        ]);

        // Add data
        $row = 2;
        foreach ($requests as $request) {
            // Calculate to_print value
            $toPrint = empty($request->source) ? '' : substr($request->source, 0, 1);
            
            // Calculate net_weight
            $netWeight = $request->weight - ($request->modelCategory ? $request->modelCategory->average_of_stones : 0);

            $this->sheet->setCellValue('A' . $row, $request->serial_number);
            $this->sheet->setCellValue('B' . $row, $request->shop_name);
            $this->sheet->setCellValue('C' . $row, $request->shop_id);
            $this->sheet->setCellValue('D' . $row, $request->kind);
            $this->sheet->setCellValue('E' . $row, $request->model);
            $this->sheet->setCellValue('F' . $row, $request->talab ?? 'No');
            $this->sheet->setCellValue('G' . $row, $request->gold_color);
            $this->sheet->setCellValue('H' . $row, $request->stones);
            $this->sheet->setCellValue('I' . $row, $request->metal_type);
            $this->sheet->setCellValue('J' . $row, $request->metal_purity);
            $this->sheet->setCellValue('K' . $row, $request->quantity);
            $this->sheet->setCellValue('L' . $row, $request->weight);
            $this->sheet->setCellValue('M' . $row, $request->rest_since);
            $this->sheet->setCellValue('N' . $row, $request->source);
            $this->sheet->setCellValue('O' . $row, $toPrint);
            $this->sheet->setCellValue('P' . $row, $request->stars);
            $this->sheet->setCellValue('Q' . $row, $request->semi_or_no);
            $this->sheet->setCellValue('R' . $row, $request->modelCategory ? $request->modelCategory->average_of_stones : 0);
            $this->sheet->setCellValue('S' . $row, $netWeight);

            $row++;
        }

        // Auto size columns
        foreach (range('A', 'S') as $column) {
            $this->sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create the excel file
        $writer = new Xlsx($this->spreadsheet);
        $filename = 'add_requests_' . date('Y-m-d_His') . '.xlsx';
        $path = storage_path('app/public/' . $filename);
        
        // Save the file
        $writer->save($path);

        return $filename;
    }
} 