<?php

namespace App\models;

use App\config\ConectDB;
use PDO;
use PDOException;

class dashboardModel extends ConectDB {
    private $conex;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    /**
     * Obtiene el número total de productos activos.
     */
    public function getTotalProductosActivos() {
        try {
            $stmt = $this->conex->prepare("SELECT COUNT(*) FROM producto_insumo WHERE estado = 1");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Obtiene el número total de servicios activos.
     */
    public function getTotalServiciosActivos() {
        try {
            $stmt = $this->conex->prepare("SELECT COUNT(*) FROM servicio WHERE estado = 1");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Obtiene el número total de clientes registrados.
     */
    public function getTotalClientesActivos() {
        try {
            $stmt = $this->conex->prepare("SELECT COUNT(*) FROM cliente");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Obtiene el número total de usuarios activos del sistema.
     */
    public function getTotalUsuariosActivos() {
        try {
            $stmt = $this->conex->prepare("SELECT COUNT(*) FROM usuario WHERE estado = 1");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Obtiene el número de pedidos activos en el mes actual.
     */
    public function getPedidosMesActual() {
        try {
            $stmt = $this->conex->prepare("SELECT COUNT(*) FROM pedido WHERE estado = 1 AND MONTH(fecha_pedido) = MONTH(CURRENT_DATE()) AND YEAR(fecha_pedido) = YEAR(CURRENT_DATE())");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Calcula el monto total de ventas del mes actual basado en los subtotales de los detalles.
     */
    public function getVentasMesActual() {
        try {
            $query = "SELECT COALESCE(SUM(dp.subtotal), 0) 
                      FROM pedido p 
                      INNER JOIN detalle_pedido dp ON p.codigo_pedido = dp.codigo_pedido 
                      WHERE p.estado = 1 AND MONTH(p.fecha_pedido) = MONTH(CURRENT_DATE()) AND YEAR(p.fecha_pedido) = YEAR(CURRENT_DATE())";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0.00;
        }
    }
}