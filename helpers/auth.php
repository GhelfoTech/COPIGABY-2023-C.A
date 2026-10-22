<?php
/**
 * helpers/auth.php
 * Autenticación y control de permisos por rol.
 *
 * Roles del sistema:
 *   1 = admin     -> acceso total al sistema
 *   2 = empleado  -> solo los módulos definidos en PERMISOS_EMPLEADO,
 *                    y dentro de ellos únicamente "añadir" y "consultar"
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Código del rol administrador */
const ROL_ADMIN = 1;

/** Código del rol empleado */
const ROL_EMPLEADO = 2;

/**
 * Módulos permitidos para el rol EMPLEADO y las acciones que puede ejecutar.
 * Acciones válidas: 'add', 'update', 'delete', 'consultar'
 */
const PERMISOS_EMPLEADO = [
    'pedido'    => ['add', 'consultar'],
    'cliente'   => ['add', 'consultar'],
    'producto'  => ['add', 'consultar'],
    'categoria' => ['add', 'consultar'],
    'servicio'  => ['add', 'consultar'],
];

/**
 * Verifica que exista una sesión activa.
 * Si no la hay, redirige al login.
 */
function checkAuth(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: ?url=login&msg=unauthorized');
        exit;
    }
}

/**
 * Verifica que el rol del usuario esté dentro de los roles permitidos.
 * El admin (rol 1) siempre tiene acceso; si no está en la lista, redirige.
 */
function checkRole(array $roles): void
{
    checkAuth();

    $rol = (int) ($_SESSION['rol'] ?? 0);

    // El administrador siempre tiene acceso
    if ($rol === ROL_ADMIN) {
        return;
    }

    if (!in_array($rol, array_map('intval', $roles), true)) {
        header('Location: ?url=dashboard&msg=forbidden');
        exit;
    }
}

/**
 * Obtiene el módulo actual desde la URL (?url=xxx).
 */
function getModuloActual(): string
{
    return strtolower(trim((string) ($_REQUEST['url'] ?? '')));
}

/**
 * Determina la acción solicitada según el "type" del query string y el método HTTP.
 *  - register/update con POST       -> 'add' / 'update'
 *  - type=main con POST + delete    -> 'delete'
 *  - type=main con POST + get       -> 'consultar'
 *  - cualquier GET                  -> 'consultar' (ver la vista)
 */
function getAccionActual(): string
{
    $type = $_GET['type'] ?? '';
    $esPost = ($_SERVER['REQUEST_METHOD'] === 'POST');

    if ($esPost) {
        if (isset($_POST['deleteCategory'], $_POST['deleteUser'], $_POST['deleteProduct'], $_POST['deleteCliente'], $_POST['deleteServicio'], $_POST['deleteMetodo'], $_POST['deleteRol'], $_POST['deleteIva'], $_POST['deleteMoneda'], $_POST['deleteMedida'], $_POST['deleteEmpresa'], $_POST['deleteProveedor'], $_POST['deletePedido'], $_POST['deleteCompra'])) {
            return 'delete';
        }
        if ($type === 'register') {
            return 'add';
        }
        if ($type === 'update') {
            return 'update';
        }
        return 'consultar'; // type=main con getCategories, getUsers, etc.
    }

    return 'consultar'; // GET: ver la vista / listar
}

/**
 * Verifica si el usuario logueado tiene permiso para ejecutar
 * la acción actual sobre el módulo actual.
 * El admin siempre tiene permiso.
 */
function tienePermiso(string $modulo = null, string $accion = null): bool
{
    $rol = (int) ($_SESSION['rol'] ?? 0);

    // Administrador: acceso total
    if ($rol === ROL_ADMIN) {
        return true;
    }

    $modulo = $modulo ?? getModuloActual();
    $accion = $accion ?? getAccionActual();

    // El módulo no está en la lista del empleado -> sin permiso
    if (!isset(PERMISOS_EMPLEADO[$modulo])) {
        return false;
    }

    return in_array($accion, PERMISOS_EMPLEADO[$modulo], true);
}

/**
 * Igual que tienePermiso, pero redirige con mensaje si no tiene acceso.
 */
function checkPermission(string $modulo = null, string $accion = null): void
{
    checkAuth();

    if (!tienePermiso($modulo, $accion)) {
        $_SESSION['flash_message'] = [
            'status'  => 'error',
            'message' => 'No tienes permisos para realizar esta acción.',
        ];
        header('Location: ?url=dashboard&msg=forbidden');
        exit;
    }
}

/**
 * Indica si el usuario logueado es administrador (para usar en las vistas).
 */
function esAdmin(): bool
{
    return (int) ($_SESSION['rol'] ?? 0) === ROL_ADMIN;
}

/**
 * Indica si el usuario logueado puede ver el módulo en el menú
 * (para ocultar opciones del sidebar según el rol).
 */
function puedeVerModulo(string $modulo): bool
{
    $rol = (int) ($_SESSION['rol'] ?? 0);

    if ($rol === ROL_ADMIN) {
        return true;
    }

    return isset(PERMISOS_EMPLEADO[$modulo]);
}
