<?php

namespace App\models;

use App\config\ConectDB;
use PDO;

class reporteModel extends ConectDB
{
    private PDO $conex;

    public function __construct()
    {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    public static function getColumnDefinitions(string $reportType): array
    {
        $columns = [
            'pedidos' => [
                'codigo_pedido' => 'N.º pedido',
                'fecha_pedido' => 'Fecha',
                'nombre_cliente' => 'Cliente',
                'cedula_cliente' => 'Cédula del cliente',
                'nombre_usuario' => 'Atendido por',
                'subtotal' => 'Subtotal',
                'monto_iva' => 'Monto IVA',
                'porcentaje_iva' => '% IVA',
                'monto_total' => 'Monto general',
                'tasa_cambio' => 'Precio del dólar (Bs)',
                'metodo_pago' => 'Método de pago',
                'estado' => 'Estado',
            ],
            'inventario' => [
                'codigo_producto' => 'Código',
                'nombre_producto' => 'Producto',
                'nombre_categoria' => 'Categoría',
                'descripcion' => 'Descripción',
                'stock_actual' => 'Stock actual',
                'stock_minimo' => 'Stock mínimo',
                'precio' => 'Precio',
                'valor_stock' => 'Valor a precio de venta',
                'estado' => 'Estado',
            ],
            'faltantes' => [
                'codigo_producto' => 'Código',
                'nombre_producto' => 'Producto',
                'nombre_categoria' => 'Categoría',
                'stock_actual' => 'Stock actual',
                'stock_minimo' => 'Stock mínimo',
                'precio' => 'Precio',
            ],
            'compras' => [
                'codigo_compra' => 'N.º compra',
                'fecha_compra' => 'Fecha',
                'numero_factura_proveedor' => 'Factura',
                'nombre_proveedor' => 'Proveedor',
                'nombre_usuario' => 'Registrada por',
                'monto_total' => 'Monto general',
                'tasa_cambio' => 'Precio del dólar (Bs)',
                'estado' => 'Estado',
            ],
        ];

        if (!isset($columns[$reportType])) {
            throw new \InvalidArgumentException('Tipo de reporte no válido.');
        }

        return $columns[$reportType];
    }

    public function getReportRows(string $reportType, array $filters = []): array
    {
        switch ($reportType) {
            case 'pedidos':
                return $this->getPedidos($filters);
            case 'inventario':
                return $this->getInventario();
            case 'faltantes':
                return $this->getFaltantes();
            case 'compras':
                return $this->getCompras($filters);
            default:
                throw new \InvalidArgumentException('Tipo de reporte no válido.');
        }
    }

    private function getPedidos(array $filters): array
    {
        $where = [];
        $params = [];
        $this->addDateFilter($where, $params, 'p.fecha_pedido', $filters);

        $orderBy = 'p.fecha_pedido DESC, p.codigo_pedido DESC';
        $limit = '';
        if (!empty($filters['largest'])) {
            $orderBy = 'p.monto_total DESC, p.fecha_pedido DESC, p.codigo_pedido DESC';
            $top = (int) ($filters['top'] ?? 10);
            $limit = ' LIMIT ' . (in_array($top, [5, 10, 20, 50], true) ? $top : 10);
        }

        $sql = "SELECT p.codigo_pedido, p.fecha_pedido, p.cedula_cliente, c.nombre AS nombre_cliente,
                       u.nombre_usuario, p.subtotal, p.monto_iva, p.porcentaje_iva, p.monto_total,
                       p.tasa_cambio, p.estado,
                       COALESCE(
                           (SELECT GROUP_CONCAT(DISTINCT mp.nombre_metodo SEPARATOR ', ')
                            FROM pagos pg
                            LEFT JOIN detalle_pago dpg ON dpg.codigo_pago = pg.codigo_pago
                            LEFT JOIN metodo_pago mp ON mp.codigo_metodo = dpg.codigo_metodo
                            WHERE pg.codigo_pedido = p.codigo_pedido AND pg.estado = 1),
                           CASE WHEN EXISTS (
                               SELECT 1 FROM cashea_credito cc WHERE cc.id_pedido = p.codigo_pedido
                           ) THEN 'CASHEA' ELSE '—' END
                       ) AS metodo_pago
                FROM pedido p
                LEFT JOIN cliente c ON c.cedula_cliente = p.cedula_cliente
                LEFT JOIN usuario u ON u.cedula_usuario = p.cedula_usuario"
            . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
            . " ORDER BY {$orderBy}{$limit}";

        return $this->fetchRows($sql, $params);
    }

    private function getInventario(): array
    {
        $sql = "SELECT p.codigo_producto, p.nombre_producto, c.nombre_categoria, p.descripcion,
                       p.stock_actual, p.stock_minimo, p.precio,
                       ROUND(p.stock_actual * p.precio, 2) AS valor_stock, p.estado
                FROM producto_insumo p
                LEFT JOIN categoria c ON c.codigo_categoria = p.codigo_categoria
                WHERE p.estado = 1
                ORDER BY p.nombre_producto ASC";

        return $this->fetchRows($sql);
    }

    private function getFaltantes(): array
    {
        $sql = "SELECT p.codigo_producto, p.nombre_producto, c.nombre_categoria,
                       p.stock_actual, p.stock_minimo, p.precio
                FROM producto_insumo p
                LEFT JOIN categoria c ON c.codigo_categoria = p.codigo_categoria
                WHERE p.estado = 1 AND (p.stock_actual = 0 OR p.stock_actual < p.stock_minimo)
                ORDER BY p.stock_actual ASC, p.nombre_producto ASC";

        return $this->fetchRows($sql);
    }

    private function getCompras(array $filters): array
    {
        $where = ['c.estado = 1'];
        $params = [];
        $this->addDateFilter($where, $params, 'c.fecha_compra', $filters);

        $sql = "SELECT c.codigo_compra, c.fecha_compra, c.numero_factura_proveedor,
                       p.razon_social AS nombre_proveedor, u.nombre_usuario, c.monto_total,
                       COALESCE(
                           c.tasa_cambio,
                           (SELECT t.monto_bolivares
                            FROM tasa_cambio t
                            WHERE t.fecha < DATE_ADD(c.fecha_compra, INTERVAL 1 DAY)
                            ORDER BY t.fecha DESC, t.codigo_tasa DESC
                            LIMIT 1)
                       ) AS tasa_cambio,
                       c.estado
                FROM compra c
                INNER JOIN proveedor p ON p.codigo_proveedor = c.codigo_proveedor
                INNER JOIN usuario u ON u.cedula_usuario = c.cedula_usuario
                WHERE " . implode(' AND ', $where) . "
                ORDER BY c.fecha_compra DESC, c.codigo_compra DESC";

        return $this->fetchRows($sql, $params);
    }

    private function addDateFilter(array &$where, array &$params, string $column, array $filters): void
    {
        if (empty($filters['start']) || empty($filters['end'])) {
            return;
        }

        $endExclusive = (new \DateTimeImmutable($filters['end']))->modify('+1 day')->format('Y-m-d');
        $where[] = "{$column} >= ? AND {$column} < ?";
        $params[] = $filters['start'];
        $params[] = $endExclusive;
    }

    private function fetchRows(string $sql, array $params = []): array
    {
        $statement = $this->conex->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
