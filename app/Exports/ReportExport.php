<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements WithMultipleSheets
{
    protected $data;
    protected $title;

    public function __construct(array $data, string $title)
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Summary sheet
        if (isset($this->data['summary'])) {
            $sheets[] = new SummarySheet($this->data['summary'], 'Summary');
        }

        // Data sheets
        if (isset($this->data['clients'])) {
            $sheets[] = new DataSheet($this->data['clients'], 'Clients', [
                'ID', 'Name', 'Email', 'Phone', 'Status', 'Created', 'Invoices', 'Documents', 'Total Amount'
            ]);
        }

        if (isset($this->data['invoices'])) {
            $sheets[] = new DataSheet($this->data['invoices'], 'Invoices', [
                'ID', 'Invoice Number', 'Client', 'Amount', 'Status', 'Created', 'Due Date'
            ]);
        }

        if (isset($this->data['documents'])) {
            $sheets[] = new DataSheet($this->data['documents'], 'Documents', [
                'ID', 'Name', 'Type', 'Client', 'Status', 'Uploaded', 'File Size'
            ]);
        }

        if (isset($this->data['messages'])) {
            $sheets[] = new DataSheet($this->data['messages'], 'Messages', [
                'ID', 'Client', 'Subject', 'Sent At', 'Read'
            ]);
        }

        return $sheets;
    }
}

class SummarySheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $title;

    public function __construct(array $data, string $title)
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->data as $key => $value) {
            $rows[] = [
                ucwords(str_replace('_', ' ', $key)),
                is_numeric($value) ? number_format($value, 2) : $value
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function title(): string
    {
        return $this->title;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class DataSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $title;
    protected $headings;

    public function __construct(array $data, string $title, array $headings)
    {
        $this->data = $data;
        $this->title = $title;
        $this->headings = $headings;
    }

    public function array(): array
    {
        return array_map(function ($item) {
            return array_values($item);
        }, $this->data);
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}