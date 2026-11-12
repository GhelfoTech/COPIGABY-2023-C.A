<?php
/**
 * Configuración común de peticiones AJAX para CopiGaby.
 * Se carga desde index.php antes de ejecutar cualquier controlador.
 */

if (!function_exists('copigabyIsAjaxRequest')) {
    function copigabyIsAjaxRequest(): bool
    {
        $requestedWith = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));

        return $requestedWith === 'xmlhttprequest'
            || (string) ($_POST['_ajax_request'] ?? '') === '1'
            || (string) ($_POST['ajax'] ?? '') === '1';
    }
}

if (!function_exists('copigabyAjaxRespond')) {
    /**
     * Devuelve una respuesta JSON estandarizada solamente para peticiones AJAX.
     * Para formularios tradicionales no imprime nada y permite continuar con
     * el flujo de flash + redirección que ya tenía el sistema.
     *
     * @param mixed $result Resultado original del modelo.
     */
    function copigabyAjaxRespond($result, string $successMessage, string $errorMessage): void
    {
        if (!copigabyIsAjaxRequest()) {
            return;
        }

        $payload = is_array($result) ? $result : [];

        if (isset($payload['status'])) {
            $success = $payload['status'] === 'success';
        } elseif (array_key_exists('success', $payload)) {
            $success = (bool) $payload['success'];
        } elseif (is_int($result) || is_float($result)) {
            $success = $result > 0;
        } else {
            $success = $result === true;
        }

        $payload['status'] = $success ? 'success' : 'error';

        $message = trim((string) ($payload['message'] ?? ''));
        if ($message === '' && is_string($result) && !$success) {
            $message = trim($result);
        }
        $payload['message'] = $message !== ''
            ? $message
            : ($success ? $successMessage : $errorMessage);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit();
    }
}
