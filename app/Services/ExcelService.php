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
    public function __construct($inputFileName)
    {
        $this->file = $inputFileName;
        if (file_exists($inputFileName)) {
            $this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
        } else {
            $this->spreadsheet = new Spreadsheet();
        }
    }

    public function render()
    {
        return $this->spreadsheet->getActiveSheet();
        /*foreach ($this->cells as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($this->file.'.xlsx');*/
    }


}
