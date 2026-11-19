<?php
/**
 * helpers/auth.php
 * Autenticación y control de permisos por rol.
 *
 * Los códigos de rol pueden variar entre bases de datos; los permisos se
 * determinan por los nombres canónicos "admin" y "empleado".
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Nombre canónico del rol administrador */
const NOMBRE_ROL_ADMIN = 'admin';

/** Nombre canónico del rol empleado */
const NOMBRE_ROL_EMPLEADO = 'empleado';

/**
 * Módulos permitidos para el rol EMPLEADO y las acciones que puede ejecutar.
 * Acciones válidas: 'add', 'update', 'delete', 'consultar'
 */
const PERMISOS_EMPLEADO = [
    'pedido'    => ['add', 'consultar'],
    'cliente'   => ['add', 'consultar'],
    'cashea'    => ['add', 'consultar'],
    'producto'  => ['add', 'consultar'],
    'categoria' => ['add', 'consultar'],
    'servicio'  => ['add', 'consultar'],
    'reporte'   => ['consultar'],
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

    $userModel = new \App\models\userModel();
    $rol = $userModel->getActiveUserRole($_SESSION['user_id']);
    if ($rol === null) {
        unset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['rol'], $_SESSION['rol_nombre']);
        header('Location: ?url=login&msg=unauthorized');
        exit;
    }

    $_SESSION['rol'] = $rol['codigo_rol'];
    $_SESSION['rol_nombre'] = $rol['nombre_rol'];
}

/**
 * Verifica que el rol del usuario esté dentro de los roles permitidos.
 * El rol admin siempre tiene acceso; los demás deben estar en la lista.
 */
function checkRole(array $roles): void
{
    checkAuth();

    $rol = (int) ($_SESSION['rol'] ?? 0);

    // El administrador siempre tiene acceso
    if (strtolower(trim((string) ($_SESSION['rol_nombre'] ?? ''))) === NOMBRE_ROL_ADMIN) {
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
    $rolNombre = strtolower(trim((string) ($_SESSION['rol_nombre'] ?? '')));

    // Administrador: acceso total
    if ($rolNombre === NOMBRE_ROL_ADMIN) {
        return true;
    }

    if ($rolNombre !== NOMBRE_ROL_EMPLEADO) {
        return false;
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
    return strtolower(trim((string) ($_SESSION['rol_nombre'] ?? ''))) === NOMBRE_ROL_ADMIN;
}

/**
 * Indica si el usuario logueado puede ver el módulo en el menú
 * (para ocultar opciones del sidebar según el rol).
 */
function puedeVerModulo(string $modulo): bool
{
    $rolNombre = strtolower(trim((string) ($_SESSION['rol_nombre'] ?? '')));

    if ($rolNombre === NOMBRE_ROL_ADMIN) {
        return true;
    }

    return $rolNombre === NOMBRE_ROL_EMPLEADO && isset(PERMISOS_EMPLEADO[$modulo]);
}
