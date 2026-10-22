<?php

namespace App\traits;

/**
 * Trait ComprasTrait
 * Validaciones del módulo de Compras.
 */
trait ComprasTrait
{
    /**
     * Valida los datos de una compra y sus items.
     */
    protected function validarCompra(array $datos, array $items)
    {
        // Campos obligatorios
        foreach (['codigo_proveedor', 'cedula_usuario', 'fecha_compra', 'monto_total'] as $campo) {
            if (!isset($datos[$campo]) || trim((string) $datos[$campo]) === '') {
                return ['status' => 'error', 'message' => 'Todos los campos obligatorios de la compra deben ser completados'];
            }
        }

        if (!empty($items)) {
            foreach ($items as $index => $item) {
                $linea    = (int) $index + 1;
                $cantidad = (float) ($item['cantidad'] ?? 0);
                $costo    = (float) ($item['costo'] ?? 0);

                // Costo unitario y cantidad estrictamente mayores a cero
                if ($cantidad <= 0) {
                    return ['status' => 'error', 'message' => "La cantidad debe ser mayor a cero en la línea {$linea}"];
                }
                if ($costo <= 0) {
                    return ['status' => 'error', 'message' => "El costo unitario debe ser mayor a cero en la línea {$linea}"];
                }
                $subtotal = $cantidad * $costo;
                if ($subtotal <= 0) {
                    return ['status' => 'error', 'message' => "El subtotal debe ser mayor a cero en la línea {$linea}"];
                }
            }
        }

        // Monto total estrictamente mayor a cero
        if ((float) $datos['monto_total'] <= 0) {
            return ['status' => 'error', 'message' => 'El monto total debe ser mayor a cero'];
        }

        // Número de factura: si se ingresa, no debe duplicarse para el mismo proveedor
        $factura = trim((string) ($datos['numero_factura_proveedor'] ?? ''));
        if ($factura !== '') {
            $idCompra = isset($datos['codigo_compra']) ? (int) $datos['codigo_compra'] : null;
            if ($this->facturaDuplicada($factura, (int) $datos['codigo_proveedor'], $idCompra)) {
                return ['status' => 'error', 'message' => 'El número de factura ya está registrado para este proveedor'];
            }
        }

        return true;
    }

    /**
     * Verifica si el número de factura ya existe para el mismo proveedor.
     */
    protected function facturaDuplicada(string $factura, int $codigoProveedor, ?int $idCompra = null): bool
    {
        try {
            if ($idCompra !== null) {
                $stmt = $this->conex->prepare(
                    'SELECT COUNT(*) FROM compra
                     WHERE numero_factura_proveedor = ? AND codigo_proveedor = ? AND codigo_compra <> ?'
                );
                $stmt->execute([$factura, $codigoProveedor, $idCompra]);
            } else {
                $stmt = $this->conex->prepare(
                    'SELECT COUNT(*) FROM compra
                     WHERE numero_factura_proveedor = ? AND codigo_proveedor = ?'
                );
                $stmt->execute([$factura, $codigoProveedor]);
            }
            return (int) $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }
}
