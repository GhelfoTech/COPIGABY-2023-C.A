<?php

    use App\models\dashboardModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    // Verificamos que el usuario esté logueado antes de mostrar el dashboard
    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }
    
    $dashboardModel = new dashboardModel();
    $stats = [
        'productos_activos' => $dashboardModel->getTotalProductosActivos(),
        'servicios_activos' => $dashboardModel->getTotalServiciosActivos(),
        'usuarios_activos'  => $dashboardModel->getTotalUsuariosActivos(),
        'ventas_mes'        => $dashboardModel->getVentasMesActual()
    ];

    // Cargamos la vista del dashboard
    include 'app/views/dashboard/viewDashboard.php';

?>