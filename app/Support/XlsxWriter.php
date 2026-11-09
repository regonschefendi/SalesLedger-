<?php

namespace App\Support;

use RuntimeException;

class XlsxWriter
{
    /**
     * Build a valid .xlsx file binary without any external dependency.
     *
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     * @param  array<int, int|float>  $widths
     * @param  array<int, mixed>|null  $totals
     */
    public static function make(
        string $sheetName,
        string $title,
        array $headers,
        array $rows,
        array $widths = [],
        ?array $totals = null
    ): string {
        $columnCount = max(count($headers), 1);
        $lastColumn = self::columnName($columnCount);

        $sheetData = '';
        $sheetData .= '<row r="1" ht="24" customHeight="1">'
            .self::stringCell('A1', $title, 4)
            .'</row>';

        $headerCells = '';
        foreach ($headers as $index => $header) {
            $headerCells .= self::stringCell(self::columnName($index + 1).'2', (string) $header, 1);
        }
        $sheetData .= '<row r="2" ht="28" customHeight="1">'.$headerCells.'</row>';

        $rowNumber = 3;
        foreach ($rows as $row) {
            $cells = '';
            foreach (array_values($row) as $index => $value) {
                $ref = self::columnName($index + 1).$rowNumber;
                $cells .= self::cell($ref, $value, is_int($value) || is_float($value) ? 2 : 5);
            }
            $sheetData .= '<row r="'.$rowNumber.'">'.$cells.'</row>';
            $rowNumber++;
        }

        $lastRow = $rowNumber - 1;

        if ($totals !== null && count($totals) > 0) {
            $cells = '';
            foreach (array_values($totals) as $index => $value) {
                if ($value === null) {
                    continue;
                }
                $ref = self::columnName($index + 1).$rowNumber;
                $cells .= self::cell($ref, $value, 3);
            }
            $sheetData .= '<row r="'.$rowNumber.'" ht="22" customHeight="1">'.$cells.'</row>';
            $lastRow = $rowNumber;
        }

        $cols = '';
        for ($i = 1; $i <= $columnCount; $i++) {
            $width = $widths[$i - 1] ?? 18;
            $cols .= '<col min="'.$i.'" max="'.$i.'" width="'.$width.'" customWidth="1"/>';
        }

        $mergeRef = 'A1:'.$lastColumn.'1';
        $autoFilterRef = 'A2:'.$lastColumn.max($lastRow, 2);

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<dimension ref="A1:'.$lastColumn.$lastRow.'"/>'
            .'<sheetViews><sheetView workbookViewId="0">'
            .'<pane ySplit="2" topLeftCell="A3" activePane="bottomLeft" state="frozen"/>'
            .'</sheetView></sheetViews>'
            .'<sheetFormatPr defaultRowHeight="18"/>'
            .'<cols>'.$cols.'</cols>'
            .'<sheetData>'.$sheetData.'</sheetData>'
            .'<autoFilter ref="'.$autoFilterRef.'"/>'
            .'<mergeCells count="1"><mergeCell ref="'.$mergeRef.'"/></mergeCells>'
            .'<pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>'
            .'</worksheet>';

        $files = [
            '[Content_Types].xml' => self::contentTypesXml(),
            '_rels/.rels' => self::rootRelsXml(),
            'xl/workbook.xml' => self::workbookXml($sheetName),
            'xl/_rels/workbook.xml.rels' => self::workbookRelsXml(),
            'xl/styles.xml' => self::stylesXml(),
            'xl/worksheets/sheet1.xml' => $sheetXml,
        ];

        return self::zip($files);
    }

    private static function cell(string $ref, mixed $value, int $style): string
    {
        if (is_int($value) || is_float($value)) {
            return '<c r="'.$ref.'" s="'.$style.'"><v>'.$value.'</v></c>';
        }

        return self::stringCell($ref, (string) $value, $style);
    }

    private static function stringCell(string $ref, string $value, int $style): string
    {
        return '<c r="'.$ref.'" s="'.$style.'" t="inlineStr"><is><t xml:space="preserve">'
            .self::escape($value)
            .'</t></is></c>';
    }

    private static function columnName(int $index): string
    {
        $name = '';

        while ($index > 0) {
            $index--;
            $name = chr(65 + ($index % 26)).$name;
            $index = intdiv($index, 26);
        }

        return $name;
    }

    private static function escape(string $value): string
    {
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $value) ?? '';

        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private static function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>';
    }

    private static function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    private static function workbookXml(string $sheetName): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="'.self::escape($sheetName).'" sheetId="1" r:id="rId1"/></sheets>'
            .'</workbook>';
    }

    private static function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>';
    }

    private static function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<numFmts count="1"><numFmt numFmtId="164" formatCode="#,##0"/></numFmts>'
            .'<fonts count="3">'
            .'<font><sz val="11"/><color theme="1"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="14"/><color rgb="FF1F2937"/><name val="Calibri"/><family val="2"/></font>'
            .'</fonts>'
            .'<fills count="3">'
            .'<fill><patternFill patternType="none"/></fill>'
            .'<fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FF2563EB"/><bgColor indexed="64"/></patternFill></fill>'
            .'</fills>'
            .'<borders count="2">'
            .'<border><left/><right/><top/><bottom/><diagonal/></border>'
            .'<border><left style="thin"><color rgb="FFD1D5DB"/></left><right style="thin"><color rgb="FFD1D5DB"/></right><top style="thin"><color rgb="FFD1D5DB"/></top><bottom style="thin"><color rgb="FFD1D5DB"/></bottom><diagonal/></border>'
            .'</borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="6">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            .'<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1"/>'
            .'<xf numFmtId="164" fontId="1" fillId="2" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1"/>'
            .'<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"/>'
            .'</cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'<dxfs count="0"/>'
            .'</styleSheet>';
    }

    /**
     * Minimal ZIP writer (store/uncompressed) so the export does not depend on ext-zip.
     *
     * @param  array<string, string>  $files
     */
    private static function zip(array $files): string
    {
        if (! function_exists('crc32')) {
            throw new RuntimeException('CRC32 is not available.');
        }

        $localData = '';
        $centralData = '';
        $offset = 0;
        $dosTime = self::dosTime();

        foreach ($files as $name => $content) {
            $crc = crc32($content);
            $size = strlen($content);
            $nameLength = strlen($name);

            $localHeader = pack(
                'VvvvvvVVVvv',
                0x04034B50,
                20,
                0x0800,
                0,
                $dosTime[0],
                $dosTime[1],
                $crc,
                $size,
                $size,
                $nameLength,
                0
            );

            $localData .= $localHeader.$name.$content;

            $centralData .= pack(
                'VvvvvvvVVVvvvvvVV',
                0x02014B50,
                20,
                20,
                0x0800,
                0,
                $dosTime[0],
                $dosTime[1],
                $crc,
                $size,
                $size,
                $nameLength,
                0,
                0,
                0,
                0,
                0,
                $offset
            ).$name;

            $offset += strlen($localHeader) + $nameLength + $size;
        }

        $endRecord = pack(
            'VvvvvVVv',
            0x06054B50,
            0,
            0,
            count($files),
            count($files),
            strlen($centralData),
            strlen($localData),
            0
        );

        return $localData.$centralData.$endRecord;
    }

    /**
     * @return array{0: int, 1: int}
     */
    private static function dosTime(): array
    {
        $now = getdate();

        $time = ($now['hours'] << 11) | ($now['minutes'] << 5) | intdiv($now['seconds'], 2);
        $date = (($now['year'] - 1980) << 9) | ($now['mon'] << 5) | $now['mday'];

        return [$time, $date];
    }
}
