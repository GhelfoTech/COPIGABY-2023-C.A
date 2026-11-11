<?php

function setSystemFlashFromResult($result, string $successMessage, string $errorMessage): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (is_array($result)) {
        $success = ($result['status'] ?? '') === 'success';
        $message = trim((string) ($result['message'] ?? ''))
            ?: ($success ? $successMessage : $errorMessage);
    } elseif (is_int($result) || is_float($result)) {
        $success = $result > 0;
        $message = $success ? $successMessage : $errorMessage;
    } elseif (is_string($result)) {
        $success = false;
        $message = trim($result) ?: $errorMessage;
    } else {
        $success = (bool) $result;
        $message = $success ? $successMessage : $errorMessage;
    }

    $_SESSION['system_flash'] = [
        'status' => $success ? 'success' : 'error',
        'message' => $message,
    ];
}
