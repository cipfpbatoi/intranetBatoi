<?php

namespace Intranet\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Servei ExcelService.
 */
class ExcelService
{
    /** @var mixed */
    protected $file;
    /** @var mixed */
    protected $spreadsheet;
    /** @var mixed */
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

    public function render(...$colums)
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $row = 1;
        foreach ($colums as $colum) {
            $col = 1;
            foreach ($colum as $cell) {
                $sheet->setCellValueByColumnAndRow($row, $col, $cell);
                $col++;
            }
            $row++;
        }
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($this->file);
    }



}
