<?php

namespace App\services;

class ReportDocumentExporter
{
    public static function toPdf(
        string $title,
        array $columns,
        array $rows,
        array $metadata = [],
        ?string $logoPath = null
    ): string {
        $columnCount = max(1, count($columns));
        $landscape = $columnCount > 5;
        $pageWidth = $landscape ? 842 : 612;
        $pageHeight = $landscape ? 595 : 792;
        $margin = 32;
        $tableWidth = $pageWidth - 2 * $margin;
        $columnWidth = $tableWidth / $columnCount;
        $logo = $logoPath !== null ? self::readJpeg($logoPath) : null;
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '',
        ];
        $nextObjectId = 3;
        $imageId = null;
        if ($logo !== null) {
            $imageId = $nextObjectId++;
            $objects[$imageId] = '<< /Type /XObject /Subtype /Image /Width ' . $logo['width']
                . ' /Height ' . $logo['height'] . ' /ColorSpace /DeviceRGB /BitsPerComponent 8'
                . ' /Filter /DCTDecode /Length ' . strlen($logo['data']) . " >>\nstream\n"
                . $logo['data'] . "\nendstream";
        }
        $regularFontId = $nextObjectId++;
        $boldFontId = $nextObjectId++;
        $objects[$regularFontId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objects[$boldFontId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';

        $startPage = static function () use (
            &$nextObjectId,
            $pageWidth,
            $pageHeight,
            $margin,
            $tableWidth,
            $columnCount,
            $columnWidth,
            $title,
            $columns,
            $metadata,
            $logo,
            $imageId
        ): array {
            $pageId = $nextObjectId++;
            $contentId = $nextObjectId++;
            $content = self::pdfRectangle(0, $pageHeight - 108, $pageWidth, 108, [0.07, 0.16, 0.32]);
            $content .= self::pdfRectangle(0, $pageHeight - 112, $pageWidth, 4, [0.18, 0.45, 0.78]);

            $textX = $margin;
            if ($logo !== null && $imageId !== null) {
                $logoDiameter = 72;
                $scale = $logoDiameter / $logo['circleDiameter'];
                $imageWidth = $logo['width'] * $scale;
                $imageHeight = $logo['height'] * $scale;
                $logoBottom = $pageHeight - 90;
                $imageX = $margin - $logo['cropLeft'] * $scale;
                $imageY = $logoBottom - (
                    $logo['height'] - $logo['cropTop'] - $logo['circleDiameter']
                ) * $scale;
                $content .= self::pdfCircleClip($margin + $logoDiameter / 2, $logoBottom + $logoDiameter / 2, $logoDiameter / 2);
                $content .= 'q ' . self::pdfNumber($imageWidth) . ' 0 0 ' . self::pdfNumber($imageHeight) . ' '
                    . self::pdfNumber($imageX) . ' ' . self::pdfNumber($imageY) . " cm /Im1 Do Q\n";
                $content .= "Q\n";
                $textX += $logoDiameter + 18;
            }

            $content .= self::pdfTextCommand('Reporte', $textX, $pageHeight - 39, 10, [0.68, 0.82, 1], true);
            $content .= self::pdfTextCommand('Sistema COPIGABY 2024 C.A.', $textX, $pageHeight - 62, 18, [1, 1, 1], true);
            $content .= self::pdfTextCommand($title, $textX, $pageHeight - 85, 11, [0.85, 0.91, 1], false);
            $content .= self::pdfTextCommand(
                'Generado: ' . date('d/m/Y H:i'),
                $pageWidth - $margin - 112,
                $pageHeight - 38,
                7,
                [0.85, 0.91, 1],
                false
            );

            $metadataLines = [];
            foreach ($metadata as $label => $value) {
                $metadataLines[] = $label . ': ' . $value;
            }
            if ($metadataLines) {
                $metadataText = self::wrapPdfText(
                    implode('  |  ', $metadataLines),
                    max(50, (int) floor($tableWidth / 4.2))
                );
                $metadataBottom = $pageHeight - 158;
                $metadataHeight = 12 + count($metadataText) * 11;
                $content .= self::pdfRectangle(
                    $margin,
                    $metadataBottom,
                    $tableWidth,
                    $metadataHeight,
                    [0.91, 0.95, 0.99]
                );
                $content .= self::pdfRectangle(
                    $margin,
                    $metadataBottom,
                    $tableWidth,
                    $metadataHeight,
                    null,
                    [0.72, 0.80, 0.89]
                );
                foreach ($metadataText as $lineIndex => $line) {
                    $content .= self::pdfTextCommand(
                        $line,
                        $margin + 8,
                        $metadataBottom + 6 + (count($metadataText) - $lineIndex - 1) * 11,
                        8,
                        [0.17, 0.25, 0.38],
                        false,
                        true
                    );
                }
                $headerY = $metadataBottom - 42;
            } else {
                $headerY = $pageHeight - 190;
            }
            $headerHeight = 30;
            $labels = array_values($columns);
            for ($index = 0; $index < $columnCount; $index++) {
                $x = $margin + $index * $columnWidth;
                $content .= self::pdfRectangle($x, $headerY, $columnWidth, $headerHeight, [0.10, 0.27, 0.52]);
                $content .= self::pdfRectangle($x, $headerY, $columnWidth, $headerHeight, null, [0.35, 0.52, 0.72]);
                $labelLines = self::wrapPdfText(
                    (string) ($labels[$index] ?? ''),
                    max(8, (int) floor(($columnWidth - 10) / 4.2))
                );
                $content .= self::pdfTextCommand(
                    implode(' ', $labelLines),
                    $x + 5,
                    $headerY + 11,
                    7,
                    [1, 1, 1],
                    true,
                    true
                );
            }

            return [
                'pageId' => $pageId,
                'contentId' => $contentId,
                'content' => $content,
                'y' => $headerY,
            ];
        };

        $pageContents = [];
        $page = $startPage();
        $dataRows = $rows ?: [['__empty' => 'No hay registros para los filtros seleccionados.']];
        foreach ($dataRows as $rowIndex => $row) {
            $cells = [];
            $maxLines = 1;
            for ($columnIndex = 0; $columnIndex < $columnCount; $columnIndex++) {
                if (!$rows) {
                    $value = $columnIndex === 0 ? $row['__empty'] : '';
                } else {
                    $key = array_keys($columns)[$columnIndex];
                    $value = (string) ($row[$key] ?? '');
                }
                $lines = self::wrapPdfText($value, max(8, (int) floor(($columnWidth - 12) / 4.0)));
                $cells[] = $lines;
                $maxLines = max($maxLines, count($lines));
            }

            $rowHeight = max(28, $maxLines * 10 + 12);
            if ($page['y'] - $rowHeight < 38) {
                $pageContents[] = $page;
                $page = $startPage();
            }

            $bottom = $page['y'] - $rowHeight;
            $fill = $rowIndex % 2 === 0 ? [0.92, 0.96, 1] : [1, 1, 1];
            for ($columnIndex = 0; $columnIndex < $columnCount; $columnIndex++) {
                $x = $margin + $columnIndex * $columnWidth;
                $page['content'] .= self::pdfRectangle($x, $bottom, $columnWidth, $rowHeight, $fill);
                $page['content'] .= self::pdfRectangle($x, $bottom, $columnWidth, $rowHeight, null, [0.72, 0.80, 0.89]);
                foreach ($cells[$columnIndex] as $lineIndex => $line) {
                    $page['content'] .= self::pdfTextCommand(
                        $line,
                        $x + 5,
                        $page['y'] - 14 - $lineIndex * 10,
                        7,
                        [0.12, 0.19, 0.30],
                        false,
                        true
                    );
                }
            }
            $page['y'] = $bottom;
        }
        $pageContents[] = $page;

        foreach ($pageContents as $pageData) {
            $resources = '<< /Font << /F1 ' . $regularFontId . ' 0 R /F2 ' . $boldFontId . ' 0 R >>';
            if ($imageId !== null) {
                $resources .= ' /XObject << /Im1 ' . $imageId . ' 0 R >>';
            }
            $resources .= ' >>';
            $objects[$pageData['pageId']] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 '
                . $pageWidth . ' ' . $pageHeight . '] /Resources ' . $resources
                . ' /Contents ' . $pageData['contentId'] . ' 0 R >>';
            $objects[$pageData['contentId']] = '<< /Length ' . strlen($pageData['content']) . " >>\nstream\n"
                . $pageData['content'] . "\nendstream";
        }

        $pageReferences = array_map(
            static fn(array $pageData): string => $pageData['pageId'] . ' 0 R',
            $pageContents
        );
        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $pageReferences) . '] /Count ' . count($pageContents) . ' >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0];
        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        for ($id = 1; $id <= count($objects); $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id]);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    public static function toDocx(
        string $title,
        array $columns,
        array $rows,
        array $metadata = [],
        ?string $logoPath = null
    ): string {
        $logo = $logoPath !== null ? self::readJpeg($logoPath) : null;
        $body = '';
        if ($logo !== null) {
            $logoWidth = 360000;
            $leftCrop = (int) round($logo['cropLeft'] / $logo['width'] * 100000);
            $topCrop = (int) round($logo['cropTop'] / $logo['height'] * 100000);
            $rightCrop = (int) round(($logo['width'] - $logo['cropLeft'] - $logo['circleDiameter']) / $logo['width'] * 100000);
            $bottomCrop = (int) round(($logo['height'] - $logo['cropTop'] - $logo['circleDiameter']) / $logo['height'] * 100000);
            $body .= '<w:p><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:drawing><wp:inline distT="0" distB="0" distL="0" distR="0">'
                . '<wp:extent cx="' . $logoWidth . '" cy="360000"/>'
                . '<wp:docPr id="1" name="Logo COPIGABY"/>'
                . '<wp:cNvGraphicFramePr><a:graphicFrameLocks xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" noChangeAspect="1"/></wp:cNvGraphicFramePr>'
                . '<a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">'
                . '<a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">'
                . '<pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">'
                . '<pic:nvPicPr><pic:cNvPr id="0" name="Logo COPIGABY"/><pic:cNvPicPr/></pic:nvPicPr>'
                . '<pic:blipFill><a:blip r:embed="rIdLogo"/><a:srcRect l="' . $leftCrop . '" t="' . $topCrop
                . '" r="' . $rightCrop . '" b="' . $bottomCrop . '"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill>'
                . '<pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="' . $logoWidth . '" cy="360000"/></a:xfrm>'
                . '<a:prstGeom prst="ellipse"><a:avLst/></a:prstGeom></pic:spPr></pic:pic>'
                . '</a:graphicData></a:graphic></wp:inline></w:drawing></w:r></w:p>';
        }
        $body .= '<w:p><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:b/><w:color w:val="17365D"/><w:sz w:val="30"/></w:rPr><w:t>Reporte - Sistema COPIGABY 2024 C.A.</w:t></w:r></w:p>';
        $body .= '<w:p><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:b/><w:color w:val="2878C8"/><w:sz w:val="24"/></w:rPr><w:t>' . self::xml($title) . '</w:t></w:r></w:p>';
        $body .= '<w:p><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:color w:val="64748B"/><w:sz w:val="18"/></w:rPr><w:t>Generado: ' . self::xml(date('d/m/Y H:i:s')) . '</w:t></w:r></w:p>';
        foreach ($metadata as $label => $value) {
            $body .= '<w:p><w:r><w:rPr><w:color w:val="17365D"/></w:rPr><w:t>' . self::xml($label . ': ' . $value) . '</w:t></w:r></w:p>';
        }

        $landscape = count($columns) > 6;
        $tableWidth = $landscape ? 14400 : 10800;
        $columnWidth = max(1, intdiv($tableWidth, max(1, count($columns))));
        $body .= '<w:tbl><w:tblPr><w:tblW w:w="' . $tableWidth . '" w:type="dxa"/><w:tblBorders>'
            . '<w:top w:val="single" w:sz="6" w:space="0" w:color="2878C8"/>'
            . '<w:left w:val="single" w:sz="6" w:space="0" w:color="2878C8"/>'
            . '<w:bottom w:val="single" w:sz="6" w:space="0" w:color="2878C8"/>'
            . '<w:right w:val="single" w:sz="6" w:space="0" w:color="2878C8"/>'
            . '<w:insideH w:val="single" w:sz="4" w:space="0" w:color="B7CBE3"/>'
            . '<w:insideV w:val="single" w:sz="4" w:space="0" w:color="B7CBE3"/>'
            . '</w:tblBorders></w:tblPr><w:tblGrid>';
        foreach ($columns as $_label) {
            $body .= '<w:gridCol w:w="' . $columnWidth . '"/>';
        }
        $body .= '</w:tblGrid>';

        $tableRows = [$columns];
        foreach ($rows as $row) {
            $tableRows[] = array_map(
                static fn(string $key): string => (string) ($row[$key] ?? ''),
                array_keys($columns)
            );
        }
        if (!$rows) {
            $emptyRow = array_fill(0, count($columns), '');
            $emptyRow[0] = 'No hay registros para los filtros seleccionados.';
            $tableRows[] = $emptyRow;
        }

        foreach ($tableRows as $rowIndex => $tableRow) {
            $body .= '<w:tr>';
            if ($rowIndex === 0) {
                $body .= '<w:trPr><w:tblHeader/></w:trPr>';
            }
            foreach ($tableRow as $cell) {
                $fill = $rowIndex === 0 ? '17365D' : ($rowIndex % 2 === 1 ? 'EAF2FB' : 'FFFFFF');
                $body .= '<w:tc><w:tcPr><w:tcW w:w="' . $columnWidth . '" w:type="dxa"/><w:shd w:fill="' . $fill . '"/></w:tcPr><w:p><w:r>';
                if ($rowIndex === 0) {
                    $body .= '<w:rPr><w:b/><w:color w:val="FFFFFF"/></w:rPr>';
                } else {
                    $body .= '<w:rPr><w:color w:val="17365D"/></w:rPr>';
                }
                $body .= '<w:t xml:space="preserve">' . self::xml($cell) . '</w:t></w:r></w:p></w:tc>';
            }
            $body .= '</w:tr>';
        }

        $pageSettings = $landscape
            ? '<w:pgSz w:w="15840" w:h="12240" w:orient="landscape"/>'
            : '<w:pgSz w:w="12240" w:h="15840"/>';
        $body .= '</w:tbl><w:sectPr>' . $pageSettings . '<w:pgMar w:top="720" w:right="720" w:bottom="720" w:left="720"/></w:sectPr>';

        $document = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"'
            . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"'
            . ' xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing">'
            . '<w:body>' . $body . '</w:body></w:document>';
        $entries = [
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                . '<Default Extension="xml" ContentType="application/xml"/>'
                . ($logo !== null ? '<Default Extension="jpeg" ContentType="image/jpeg"/>' : '')
                . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
                . '</Types>',
            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
                . '</Relationships>',
            'word/document.xml' => $document,
        ];
        if ($logo !== null) {
            $entries['word/_rels/document.xml.rels'] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rIdLogo" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/logo.jpeg"/>'
                . '</Relationships>';
            $entries['word/media/logo.jpeg'] = $logo['data'];
        }

        return self::createZip($entries);
    }

    private static function startPdfText(float $x, float $y, int $size, array $color, bool $bold): string
    {
        $font = $bold ? '/F2' : '/F1';
        return self::pdfColor($color, false) . "BT\n{$font} {$size} Tf\n"
            . self::pdfNumber($x) . ' ' . self::pdfNumber($y) . " Td\n";
    }

    private static function pdfTextCommand(
        string $text,
        float $x,
        float $y,
        int $size,
        array $color,
        bool $bold,
        bool $textIsEncoded = false
    ): string {
        return self::startPdfText($x, $y, $size, $color, $bold)
            . '(' . self::escapePdfString($textIsEncoded ? $text : self::pdfText($text)) . ") Tj\nET\n";
    }

    private static function pdfRectangle(
        float $x,
        float $y,
        float $width,
        float $height,
        ?array $fill,
        ?array $stroke = null
    ): string {
        $content = '';
        if ($fill !== null) {
            $content .= self::pdfColor($fill, false);
        }
        if ($stroke !== null) {
            $content .= self::pdfColor($stroke, true) . "0.5 w\n";
        }
        $content .= self::pdfNumber($x) . ' ' . self::pdfNumber($y) . ' '
            . self::pdfNumber($width) . ' ' . self::pdfNumber($height) . " re\n";
        $content .= $fill !== null && $stroke !== null ? "B\n" : ($fill !== null ? "f\n" : "S\n");

        return $content;
    }

    private static function pdfColor(array $color, bool $stroke): string
    {
        return implode(' ', array_map([self::class, 'pdfNumber'], $color)) . ($stroke ? " RG\n" : " rg\n");
    }

    private static function pdfNumber(float $number): string
    {
        return rtrim(rtrim(number_format($number, 3, '.', ''), '0'), '.');
    }

    private static function wrapPdfText(string $text, int $width): array
    {
        $text = self::pdfText(str_replace(["\r", "\n", "\t"], ' ', $text));
        return explode("\n", wordwrap($text, max(1, $width), "\n", true));
    }

    private static function readJpeg(string $path): array
    {
        $image = @getimagesize($path);
        $data = @file_get_contents($path);
        if (
            $image === false
            || ($image['mime'] ?? '') !== 'image/jpeg'
            || (int) ($image['channels'] ?? 0) !== 3
            || $data === false
        ) {
            throw new \RuntimeException('No se pudo cargar el logo JPEG del reporte.');
        }

        return [
            'data' => $data,
            'width' => (int) $image[0],
            'height' => (int) $image[1],
            'circleDiameter' => min((int) round($image[0] * 0.66), (int) round($image[1] * 0.305)),
            'cropLeft' => ((int) $image[0] - min((int) round($image[0] * 0.66), (int) round($image[1] * 0.305))) / 2,
            'cropTop' => ((int) $image[1] - min((int) round($image[0] * 0.66), (int) round($image[1] * 0.305))) / 2,
        ];
    }

    private static function pdfCircleClip(float $centerX, float $centerY, float $radius): string
    {
        $control = $radius * 0.55228475;
        $left = $centerX - $radius;
        $right = $centerX + $radius;
        $bottom = $centerY - $radius;
        $top = $centerY + $radius;

        return "q\n"
            . self::pdfNumber($centerX) . ' ' . self::pdfNumber($top) . " m\n"
            . self::pdfNumber($centerX + $control) . ' ' . self::pdfNumber($top) . ' '
            . self::pdfNumber($right) . ' ' . self::pdfNumber($centerY + $control) . ' '
            . self::pdfNumber($right) . ' ' . self::pdfNumber($centerY) . " c\n"
            . self::pdfNumber($right) . ' ' . self::pdfNumber($centerY - $control) . ' '
            . self::pdfNumber($centerX + $control) . ' ' . self::pdfNumber($bottom) . ' '
            . self::pdfNumber($centerX) . ' ' . self::pdfNumber($bottom) . " c\n"
            . self::pdfNumber($centerX - $control) . ' ' . self::pdfNumber($bottom) . ' '
            . self::pdfNumber($left) . ' ' . self::pdfNumber($centerY - $control) . ' '
            . self::pdfNumber($left) . ' ' . self::pdfNumber($centerY) . " c\n"
            . self::pdfNumber($left) . ' ' . self::pdfNumber($centerY + $control) . ' '
            . self::pdfNumber($centerX - $control) . ' ' . self::pdfNumber($top) . ' '
            . self::pdfNumber($centerX) . ' ' . self::pdfNumber($top) . " c\nW n\n";
    }

    private static function createZip(array $entries): string
    {
        $zip = '';
        $centralDirectory = '';
        $time = getdate();
        $dosTime = ($time['hours'] << 11) | ($time['minutes'] << 5) | (int) ($time['seconds'] / 2);
        $dosDate = (($time['year'] - 1980) << 9) | ($time['mon'] << 5) | $time['mday'];

        foreach ($entries as $name => $contents) {
            $nameLength = strlen($name);
            $size = strlen($contents);
            $crc = crc32($contents);
            $offset = strlen($zip);
            $zip .= pack('VvvvvvVVVvv', 0x04034b50, 20, 0, 0, $dosTime, $dosDate, $crc, $size, $size, $nameLength, 0)
                . $name . $contents;
            $centralDirectory .= pack(
                'VvvvvvvVVVvvvvvVV',
                0x02014b50,
                20,
                20,
                0,
                0,
                $dosTime,
                $dosDate,
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
            ) . $name;
        }

        $centralOffset = strlen($zip);
        $zip .= $centralDirectory;
        $zip .= pack('VvvvvVVv', 0x06054b50, 0, 0, count($entries), count($entries), strlen($centralDirectory), $centralOffset, 0);

        return $zip;
    }

    private static function pdfText(string $text): string
    {
        $text = str_replace(["\r", "\n", "\t"], ' ', $text);
        $converted = iconv('UTF-8', 'Windows-1252//TRANSLIT', $text);
        return $converted === false ? $text : $converted;
    }

    private static function escapePdfString(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private static function xml(string $text): string
    {
        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
