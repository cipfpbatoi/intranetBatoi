<?php

namespace Intranet\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelService
{
    protected $file;
    protected $spreadsheet;
    protected $cells;

    /**
     * @param $file
     * @param $spreadsheet
     * @param $cells
     */
    public function __construct($file,  $cells)
    {
        $this->file = $file;
        $this->spreadsheet = new Spreadsheet();
        $this->cells = $cells;
    }

    public static function read($inputFileName)
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
        dd($spreadsheet);
    }

    public function render()
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        foreach ($this->cells as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($this->file.'.xlsx');
    }


}
