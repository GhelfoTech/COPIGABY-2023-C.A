<?php

    use App\models\dashboardModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $dashboardModel = new dashboardModel();
    $empresa = (new \App\models\userModel())->getEmpresaRIF();
    $stats = [
        'productos_activos' => $dashboardModel->getTotalProductosActivos(),
        'servicios_activos' => $dashboardModel->getTotalServiciosActivos(),
        'usuarios_activos'  => $dashboardModel->getTotalUsuariosActivos(),
        'ventas_mes'        => $dashboardModel->getVentasMesActual()
    ];
    $nombreEmpresa = $empresa['nombre_empresa'] ?? 'CopiGaby';
    $rifEmpresa = $empresa['rif'] ?? 'J-504149357';

    include 'app/views/dashboard/viewDashboard.php';

?>
