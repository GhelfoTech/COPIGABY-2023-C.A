<?php

    if (session_status() === PHP_SESSION_NONE) session_start();

    // Verificamos que el usuario esté logueado antes de mostrar el dashboard
    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    // Inicializamos la variable $stats con valores por defecto para evitar el warning.
    // En el futuro, puedes crear un método en tu modelo para obtener estas cifras reales.
    $stats = [
        'productos_activos' => 0,
        'servicios_activos' => 0,
        'pedidos_mes'       => 0,
        'ventas_mes'        => 0
    ];

    // Cargamos la vista del dashboard
    include 'app/views/dashboard/index.php';

?>