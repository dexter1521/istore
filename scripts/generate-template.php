<?php
// Genera un archivo .xlsx en formato XML (Excel 2003 Spreadsheet XML) a partir de resources/templates/import-template.csv
$csvPath = __DIR__ . '/../src/src/resources/templates/import-template.csv';
$outPath = __DIR__ . '/../src/src/resources/templates/import-template.xlsx';
if (!file_exists($csvPath)) {
    echo "Archivo CSV de plantilla no encontrado: $csvPath\n";
    exit(1);
}
$lines = file($csvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$rows = array_map('str_getcsv', $lines);
$xml = "<?xml version=\"1.0\"?>\n";
$xml .= "<?mso-application progid=\"Excel.Sheet\"?>\n";
$xml .= "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"\n";
$xml .= " xmlns:o=\"urn:schemas-microsoft-com:office:office\"\n";
$xml .= " xmlns:x=\"urn:schemas-microsoft-com:office:excel\"\n";
$xml .= " xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"\n";
$xml .= " xmlns:html=\"http://www.w3.org/TR/REC-html40\">\n";
$xml .= "  <Worksheet ss:Name=\"Template\">\n";
$xml .= "    <Table>\n";
foreach ($rows as $r) {
    $xml .= "      <Row>\n";
    foreach ($r as $cell) {
        $type = is_numeric($cell) ? 'Number' : 'String';
        $escaped = htmlspecialchars($cell, ENT_QUOTES | ENT_XML1);
        $xml .= "        <Cell><Data ss:Type=\"$type\">$escaped</Data></Cell>\n";
    }
    $xml .= "      </Row>\n";
}
$xml .= "    </Table>\n";
$xml .= "  </Worksheet>\n";
$xml .= "</Workbook>\n";
file_put_contents($outPath, $xml);
echo "Generado: $outPath\n";
