<?php

    use App\models\dashboardModel;
    use App\models\tasaCambioModel;
    use App\models\monedaModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $dashboardModel = new dashboardModel();
    $tasaCambioModel = new tasaCambioModel();
    $monedaModel = new monedaModel();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_tasa'])) {
        $codigoMoneda = (int) ($_POST['codigo_moneda'] ?? 0);
        $resultado = $monedaModel->actualizarTasaMoneda(
            $codigoMoneda,
            (float) ($_POST['monto_bolivares'] ?? 0)
        );
        $_SESSION['tasa_flash'] = $resultado;
        header('Location: ?url=dashboard');
        exit();
    }

    $empresa = (new \App\models\userModel())->getEmpresaRIF();
    $stats = [
        'productos_activos' => $dashboardModel->getTotalProductosActivos(),
        'servicios_activos' => $dashboardModel->getTotalServiciosActivos(),
        'usuarios_activos'  => $dashboardModel->getTotalUsuariosActivos(),
        'ventas_mes'        => $dashboardModel->getVentasMesActual()
    ];
    $tasaActual = $tasaCambioModel->getTasaActual();
    $monedas = $monedaModel->getAllMonedas();
    $monedaActual = $monedaModel->getMonedaActiva() ?: ($monedas[0] ?? null);
    $historialTasas = array_slice($tasaCambioModel->getAllTasas(), 0, 5);
    $nombreEmpresa = $empresa['nombre_empresa'] ?? 'CopiGaby';
    $rifEmpresa = $empresa['rif'] ?? 'J-504149357';

    include 'app/views/dashboard/viewDashboard.php';

?>
