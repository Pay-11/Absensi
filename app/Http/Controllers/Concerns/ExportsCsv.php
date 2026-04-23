<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Response;

trait ExportsCsv
{
    /**
     * Generate file HTML yang dibuka Excel dengan tabel + warna header + border.
     *
     * @param array  $headers     Baris header
     * @param array  $rows        Data rows
     * @param string $filename    Nama file (tanpa ekstensi)
     * @param string $headerColor Hex warna header (tanpa #)
     * @param string $stripeColor Hex warna baris selang-seling (tanpa #)
     */
    protected function csvResponse(
        array  $headers,
        array  $rows,
        string $filename,
        string $headerColor = '1a7431',
        string $stripeColor = 'e6f4ea'
    ): Response {

        $th = '';
        foreach ($headers as $h) {
            $th .= '<th>' . htmlspecialchars((string)$h) . '</th>';
        }

        $tbody = '';
        foreach ($rows as $i => $row) {
            $bg    = ($i % 2 === 0) ? '#ffffff' : "#{$stripeColor}";
            $tbody .= "<tr style=\"background:{$bg}\">";
            foreach ($row as $cell) {
                $tbody .= '<td>' . htmlspecialchars((string)$cell) . '</td>';
            }
            $tbody .= '</tr>';
        }

        $html = <<<HTML
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="UTF-8">
<!--[if gte mso 9]>
<xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>
<x:Name>{$filename}</x:Name>
<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml>
<![endif]-->
<style>
  body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
  table { border-collapse: collapse; width: 100%; }
  th {
    background-color: #{$headerColor};
    color: #ffffff;
    font-weight: bold;
    font-size: 11pt;
    text-align: center;
    padding: 6px 10px;
    border: 1px solid #888888;
  }
  td {
    padding: 5px 10px;
    border: 1px solid #cccccc;
    font-size: 10pt;
  }
  tr:first-child th { border-top: 2px solid #555555; }
</style>
</head>
<body>
<table>
  <thead><tr>{$th}</tr></thead>
  <tbody>{$tbody}</tbody>
</table>
</body>
</html>
HTML;

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.xls"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }
}
