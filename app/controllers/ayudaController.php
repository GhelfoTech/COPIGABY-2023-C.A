<?php

    /**
     * Módulo Ayuda — Manual de Usuario
     * Permite visualizar y descargar el manual del sistema CopiGaby 2023.
     * Accesible para cualquier usuario autenticado (módulo base).
     */

    if (session_status() === PHP_SESSION_NONE) session_start();

    require_once __DIR__ . '/../../helpers/auth.php';
    checkAuth();

    // Descarga directa del manual (PDF si existe, de lo contrario el Markdown)
    if (isset($_GET['descargar'])) {

        $archivos = [
            'pdf'  => __DIR__ . '/../../docs/Manual_Usuario_CopiGaby2023.pdf',
            'docx' => __DIR__ . '/../../docs/Manual_Usuario_CopiGaby2023.docx',
            'md'   => __DIR__ . '/../../docs/Manual_Usuario_CopiGaby2023.md',
        ];

        $tipo     = $_GET['descargar'];
        $rutaReal = $archivos[$tipo] ?? null;

        // Fallback: si pide pdf/docx y no existe, entrega el markdown
        if (!$rutaReal || !file_exists($rutaReal)) {
            $tipo     = 'md';
            $rutaReal = $archivos['md'];
        }

        if (file_exists($rutaReal)) {
            $nombres = [
                'pdf'  => 'Manual_Usuario_CopiGaby2023.pdf',
                'docx' => 'Manual_Usuario_CopiGaby2023.docx',
                'md'   => 'Manual_Usuario_CopiGaby2023.md',
            ];

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $nombres[$tipo] . '"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($rutaReal));
            header('Cache-Control: must-revalidate');
            readfile($rutaReal);
            exit;
        }

        $_SESSION['flash_message'] = [
            'status'  => 'error',
            'message' => 'El manual de usuario no está disponible en este momento.',
        ];
        header('Location: ?url=ayuda');
        exit;
    }

    // Disponibilidad de formatos para mostrar los botones correctos en la vista
    $raizDocs = __DIR__ . '/../../docs/';
    $manual = [
        'pdf'  => file_exists($raizDocs . 'Manual_Usuario_CopiGaby2023.pdf'),
        'docx' => file_exists($raizDocs . 'Manual_Usuario_CopiGaby2023.docx'),
        'md'   => file_exists($raizDocs . 'Manual_Usuario_CopiGaby2023.md'),
    ];

    include 'app/views/ayuda/viewAyuda.php';

?>
