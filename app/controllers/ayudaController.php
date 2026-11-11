<?php

    if (session_status() === PHP_SESSION_NONE) session_start();

    require_once __DIR__ . '/../../helpers/auth.php';
    checkAuth();

    if (($_GET['descargar'] ?? '') !== 'pdf') {
        header('Location: ?url=dashboard');
        exit;
    }

    $rutaManual = __DIR__ . '/../../manual/Manual_Usuario_CopiGaby2023.pdf';
    if (!is_file($rutaManual)) {
        http_response_code(404);
        exit('El manual de usuario no está disponible.');
    }

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="Manual_Usuario_CopiGaby2023.pdf"');
    header('Content-Length: ' . filesize($rutaManual));
    header('Cache-Control: private, must-revalidate');
    readfile($rutaManual);
    exit;

?>
