<?php

namespace App\traits;

/**
 * Trait PedidosTrait
 * Validaciones del módulo de Pedidos.
 */
trait PedidosTrait
{
    private static $metodosConBanco = [563, 564];

    /**
     * Valida los datos generales de un pedido y sus ítems.
     * Devuelve true si todo es válido, o un array de error.
     */
    protected function validarPedido(array $datos, array $items)
    {
        // Cliente y usuario obligatorios
        $cedulaCliente = $datos['cedula_cliente'] ?? '';
        $cedulaUsuario = $datos['cedula_usuario'] ?? '';

        if (empty($cedulaUsuario) || empty($cedulaCliente)) {
            return ['status' => 'error', 'message' => 'Cliente o usuario no válido para registrar el pedido'];
        }

        // Al menos un producto o servicio en el detalle
        if (empty($items)) {
            return ['status' => 'error', 'message' => 'El pedido debe incluir al menos un ítem'];
        }

        // Cantidades y subtotales mayores a cero
        foreach ($items as $index => $item) {
            $linea      = (int) $index + 1;
            $cantidad   = (float) ($item['cantidad'] ?? 0);
            $precioVenta = (float) ($item['precio_venta'] ?? 0);

            if ($cantidad <= 0) {
                return ['status' => 'error', 'message' => "Cantidad inválida en la línea {$linea}"];
            }
            if ($precioVenta <= 0) {
                return ['status' => 'error', 'message' => "Precio inválido en la línea {$linea}"];
            }
            $subtotal = $cantidad * $precioVenta;
            if ($subtotal <= 0) {
                return ['status' => 'error', 'message' => "Subtotal inválido en la línea {$linea}"];
            }
        }

        // Total mayor a cero
        $montoTotal = (float) ($datos['monto_total'] ?? 0);
        if ($montoTotal <= 0) {
            return ['status' => 'error', 'message' => 'El total del pedido debe ser mayor a cero'];
        }

        // Debe existir una tasa de cambio activa para el cálculo en divisas
        $validacionTasa = $this->validarTasaCambioActiva();
        if ($validacionTasa !== true) {
            return $validacionTasa;
        }

        // Validación del pago asociado
        $pago = func_get_args()[2] ?? null;
        if (is_array($pago)) {
            $validacionPago = $this->validarDatosPago($pago);
            if ($validacionPago !== true) {
                return $validacionPago;
            }
        }

        return true;
    }

    /**
     * Verifica que exista una tasa de cambio activa para el cálculo en divisas.
     */
    protected function validarTasaCambioActiva()
    {
        try {
            $stmt = $this->conex->prepare('SELECT 1 FROM tasa_cambio LIMIT 1');
            $stmt->execute();
            if (!$stmt->fetchColumn()) {
                return ['status' => 'error', 'message' => 'No existe una tasa de cambio activa para el cálculo en divisas'];
            }
            return true;
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'No se pudo verificar la tasa de cambio activa'];
        }
    }

    /**
     * Valida los datos del pago del pedido.
     */
    protected function validarDatosPago(array $pago)
    {
        $codigoMetodo = (int) ($pago['codigo_metodo'] ?? 0);
        if ($codigoMetodo <= 0) {
            return ['status' => 'error', 'message' => 'Debe seleccionar un método de pago'];
        }

        if ($this->metodoRequiereBanco($codigoMetodo)) {
            if (empty($pago['codigo_banco'])) {
                return ['status' => 'error', 'message' => 'Debe seleccionar el banco para este método de pago'];
            }
            if (empty(trim((string) ($pago['referencia'] ?? '')))) {
                return ['status' => 'error', 'message' => 'Debe indicar la referencia del pago'];
            }
        }
        return true;
    }

    /**
     * Indica si el método de pago requiere banco y referencia.
     */
    protected function metodoRequiereBanco(int $codigoMetodo): bool
    {
        return in_array($codigoMetodo, self::$metodosConBanco, true);
    }
}
