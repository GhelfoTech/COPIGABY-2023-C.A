<?php

use App\models\reporteModel;
use App\services\ReportDocumentExporter;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$reportTypes = [
    'pedidos' => 'Pedido',
    'inventario' => 'Stock',
    'faltantes' => 'Lista de faltantes',
    'compras' => 'Compra',
];
$reportType = (string) ($_GET['report'] ?? 'pedidos');
if (!isset($reportTypes[$reportType])) {
    http_response_code(400);
    exit('Tipo de reporte no válido.');
}
$reportModule = [
    'pedidos' => 'pedido',
    'inventario' => 'producto',
    'faltantes' => 'producto',
    'compras' => 'compra',
][$reportType];
if (!puedeVerModulo($reportModule)) {
    http_response_code(403);
    exit('No tienes permisos para consultar este reporte.');
}

$columns = reporteModel::getColumnDefinitions($reportType);
$filters = ['period' => (string) ($_GET['period'] ?? 'all')];
$dateError = null;
$periodLabels = [
    'all' => 'Todos los registros',
    'week' => 'Últimos 7 días',
    'days15' => 'Últimos 15 días',
    'month' => 'Último mes',
    'custom' => 'Período personalizado',
];

if (in_array($reportType, ['pedidos', 'compras'], true)) {
    if ($filters['period'] === 'week') {
        $filters['start'] = (new DateTimeImmutable('today'))->modify('-6 days')->format('Y-m-d');
        $filters['end'] = (new DateTimeImmutable('today'))->format('Y-m-d');
    } elseif ($filters['period'] === 'days15') {
        $filters['start'] = (new DateTimeImmutable('today'))->modify('-14 days')->format('Y-m-d');
        $filters['end'] = (new DateTimeImmutable('today'))->format('Y-m-d');
    } elseif ($filters['period'] === 'month') {
        $filters['start'] = (new DateTimeImmutable('today'))->modify('-1 month')->format('Y-m-d');
        $filters['end'] = (new DateTimeImmutable('today'))->format('Y-m-d');
    } elseif ($filters['period'] === 'custom') {
        $start = (string) ($_GET['start_date'] ?? '');
        $end = (string) ($_GET['end_date'] ?? '');
        $validDate = static function (string $date): bool {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return false;
            }
            [$year, $month, $day] = array_map('intval', explode('-', $date));
            return checkdate($month, $day, $year);
        };
        if (!$validDate($start) || !$validDate($end) || $start > $end) {
            $dateError = 'Indica un rango de fechas válido; la fecha inicial debe ser anterior o igual a la final.';
        } else {
            $filters['start'] = $start;
            $filters['end'] = $end;
        }
    } elseif ($filters['period'] !== 'all') {
        $dateError = 'El período seleccionado no es válido.';
        $filters['period'] = 'all';
    }
}

$filters['largest'] = $reportType === 'pedidos' && isset($_GET['largest']) && $_GET['largest'] === '1';
$top = (int) ($_GET['top'] ?? 10);
$filters['top'] = in_array($top, [5, 10, 20, 50], true) ? $top : 10;

$fieldSelectionSent = isset($_GET['fields_present']);
$requestedFields = $_GET['fields'] ?? null;
$selectedFields = $fieldSelectionSent
    ? (is_array($requestedFields)
        ? array_values(array_intersect(array_keys($columns), array_map('strval', $requestedFields)))
        : [])
    : array_keys($columns);
$visibleColumns = array_intersect_key($columns, array_flip($selectedFields));

$errorMessage = $dateError;
$rows = [];
if (!$errorMessage && !$visibleColumns) {
    $errorMessage = 'Selecciona al menos un campo para mostrar en el reporte.';
}

$exportType = isset($_GET['format']) ? 'export' : ($_GET['type'] ?? '');
$exportFormat = $_GET['format'] ?? '';
if (!$errorMessage && $exportType === 'export' && !in_array($exportFormat, ['pdf', 'docx'], true)) {
    $errorMessage = 'El formato de descarga no es válido.';
}

if (!$errorMessage) {
    try {
        $reportModel = new reporteModel();
        $rows = $reportModel->getReportRows($reportType, $filters);
    } catch (\PDOException $exception) {
        error_log('[reporteController] No se pudo consultar el reporte: ' . $exception->getMessage());
        $errorMessage = 'No se pudo consultar la información del reporte. Verifica la conexión y la configuración de la base de datos.';
    }
}

if (!$errorMessage && $exportType === 'export') {
    $exportRows = array_map(
        static function (array $row) use ($visibleColumns): array {
            $formatted = [];
            foreach ($visibleColumns as $key => $_label) {
                $value = $row[$key] ?? '';
                if (in_array($key, ['subtotal', 'monto_iva', 'monto_total', 'precio', 'valor_stock'], true)) {
                    $value = number_format((float) $value, 2, ',', '.');
                } elseif ($key === 'porcentaje_iva') {
                    $value = number_format((float) $value, 2, ',', '.') . '%';
                } elseif (in_array($key, ['fecha_pedido', 'fecha_compra'], true) && $value !== '') {
                    $value = date('d/m/Y H:i', strtotime((string) $value));
                } elseif ($key === 'estado') {
                    $value = (int) $value === 1 ? 'Activo' : 'Anulado';
                }
                $formatted[$key] = (string) $value;
            }
            return $formatted;
        },
        $rows
    );

    $metadata = [];
    if (isset($filters['start'], $filters['end'])) {
        $metadata['Período'] = date('d/m/Y', strtotime($filters['start'])) . ' al ' . date('d/m/Y', strtotime($filters['end']));
    } elseif (in_array($reportType, ['pedidos', 'compras'], true)) {
        $metadata['Período'] = $periodLabels[$filters['period']] ?? $periodLabels['all'];
    }
    if ($reportType === 'pedidos' && $filters['largest']) {
        $metadata['Orden'] = 'Pedidos de mayor monto; máximo ' . $filters['top'];
    }
    $metadata['Registros'] = number_format(count($exportRows), 0, ',', '.');

    $filename = $reportType . '-' . date('Ymd-His');
    $logoPath = __DIR__ . '/../../assets/img/logo.jpeg';
    if ($exportFormat === 'pdf') {
        $document = ReportDocumentExporter::toPdf($reportTypes[$reportType], $visibleColumns, $exportRows, $metadata, $logoPath);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');
    } else {
        $document = ReportDocumentExporter::toDocx($reportTypes[$reportType], $visibleColumns, $exportRows, $metadata, $logoPath);
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $filename . '.docx"');
    }
    header('Content-Length: ' . strlen($document));
    echo $document;
    exit();
}

$summary = ['count' => count($rows)];
if (in_array($reportType, ['pedidos', 'compras'], true)) {
    $summary['total'] = array_sum(array_map(
        static fn(array $row): float => (float) ($row['monto_total'] ?? 0),
        $rows
    ));
} elseif ($reportType === 'inventario') {
    $summary['stock'] = array_sum(array_map(
        static fn(array $row): int => (int) ($row['stock_actual'] ?? 0),
        $rows
    ));
}

$filterValues = [
    'period' => $filters['period'],
    'start_date' => $_GET['start_date'] ?? '',
    'end_date' => $_GET['end_date'] ?? '',
    'largest' => $filters['largest'],
    'top' => $filters['top'],
];
$reportLabels = $reportTypes;

include 'app/views/reportes/viewReportes.php';
