<?php

namespace App\traits;

/**
 * Trait UsuariosTrait
 * Validaciones del módulo de Usuarios.
 */
trait UsuariosTrait
{
    use ValidacionesTrait;

    /**
     * Obtiene el código del rol administrador activo.
     */
    protected function getAdminRoleCodigo(): ?int {
        $stmt = $this->conex->prepare(
            "SELECT codigo_rol FROM rol WHERE LOWER(TRIM(nombre_rol)) = 'admin' AND estado = 1 LIMIT 1"
        );
        $stmt->execute();
        $codigo = $stmt->fetchColumn();
        return $codigo !== false ? (int) $codigo : null;
    }

    /**
     * Obtiene la cédula del único administrador del sistema (null si no hay).
     */
    protected function getCedulaAdmin() {
        $stmt = $this->conex->prepare(
            "SELECT u.cedula_usuario
             FROM usuario u
             INNER JOIN rol r ON r.codigo_rol = u.codigo_rol
             WHERE LOWER(TRIM(r.nombre_rol)) = 'admin'
             LIMIT 1"
        );
        $stmt->execute();
        $cedula = $stmt->fetchColumn();
        return $cedula !== false ? (string) $cedula : null;
    }

    /**
     * Indica si la cédula corresponde al usuario administrador.
     */
    protected function esUsuarioAdmin($cedula): bool {
        $cedula = (string) $cedula;
        return $cedula !== '' && $cedula === $this->getCedulaAdmin();
    }

    /**
     * Valida que solo exista un usuario con rol administrador.
     * Se llama en addUser y updateUser.
     */
    protected function validarRolAdminUnico($rol, $cedulaUsuario, $cedulaActual = null)
    {
        $rol = (int) $rol;
        $rolAdminCodigo = $this->getAdminRoleCodigo();
        $admin = $this->getCedulaAdmin();

        // Caso 1: se intenta asignar rol admin a un usuario que no es el admin actual
        if ($rolAdminCodigo !== null && $rol === $rolAdminCodigo) {
            $esElAdminActual = ($admin !== null && $admin === (string) $cedulaUsuario)
                || ($cedulaActual !== null && $admin !== null && $admin === (string) $cedulaActual);

            if (!$esElAdminActual) {
                return ['status' => 'error', 'message' => 'Solo se permite un usuario con rol de administrador en el sistema'];
            }
            return true;
        }

        // Caso 2: se intenta cambiar el rol del admin actual o cambiarle el rol a otro usuario que ya es admin
        if ($admin !== null && $cedulaActual !== null && (string) $cedulaActual === $admin && $rol !== $rolAdminCodigo) {
            return ['status' => 'error', 'message' => 'No se puede cambiar el rol del usuario administrador'];
        }

        return true;
    }

    /**
     * Valida que el usuario administrador no pueda ser eliminado (desactivado).
     */
    protected function validarAdminNoEliminable($cedulaUsuario)
    {
        if ($this->esUsuarioAdmin($cedulaUsuario)) {
            return ['status' => 'error', 'message' => 'No se puede eliminar ni desactivar al usuario administrador'];
        }
        return true;
    }

    /**
    /**
     * Valida los datos de un usuario.
     */
    protected function validarUsuario(array $datos)
    {
        // Campos obligatorios
        if (!$this->camposObligatoriosCompletos($datos, ['cedula_usuario', 'telefono', 'nombre_usuario', 'codigo_rol', 'password', 'estado'])) {
            return ['status' => 'error', 'message' => 'Cédula, teléfono, nombre de usuario, rol, contraseña y estado son obligatorios'];
        }

        // Cédula: solo números y única en la base de datos
        if (!$this->soloDigitos($datos['cedula_usuario'])) {
            return ['status' => 'error', 'message' => 'La cédula del usuario solo debe contener números'];
        }

        $idUsuario = $datos['cedula_usuario_actual'] ?? null;
        if ($this->cedulaUsuarioDuplicada(trim((string) $datos['cedula_usuario']), $idUsuario)) {
            return ['status' => 'error', 'message' => 'La cédula ya está registrada en la base de datos'];
        }

        // Teléfono: solo números
        if (!$this->telefonoNacionalValido($datos['telefono'])) {
            return ['status' => 'error', 'message' => 'El teléfono debe tener 11 dígitos numéricos'];
        }

        // Nombre de usuario: unicidad estricta
        if ($this->nombreUsuarioDuplicado(trim((string) $datos['nombre_usuario']), $idUsuario)) {
            return ['status' => 'error', 'message' => 'El nombre de usuario ya está en uso por otro usuario'];
        }

        // Solo un usuario puede tener rol de administrador
        $validacionRol = $this->validarRolAdminUnico($datos['codigo_rol'], $datos['cedula_usuario'], $idUsuario);
        if ($validacionRol !== true) {
            return $validacionRol;
        }

        // Contraseña: longitud mínima de seguridad
        if (strlen((string) $datos['password']) < 4) {
            return ['status' => 'error', 'message' => 'La contraseña debe tener al menos 4 caracteres'];
        }

        return true;
    }

    /**
     * Verifica si la cédula del usuario ya está registrada (excluyendo un id opcional).
     */
    protected function cedulaUsuarioDuplicada(string $cedula, $idActual = null): bool
    {
        $consulta = 'SELECT COUNT(*) FROM usuario WHERE cedula_usuario = ?';
        $parametros = [$cedula];
        if ($idActual !== null) {
            $consulta .= ' AND cedula_usuario <> ?';
            $parametros[] = $idActual;
        }

        return $this->registroDuplicadoPorConsulta($consulta, $parametros);
    }

    /**
     * Verifica si el nombre de usuario ya está en uso (excluyendo un id opcional).
     */
    protected function nombreUsuarioDuplicado(string $nombreUsuario, $idActual = null): bool
    {
        $consulta = 'SELECT COUNT(*) FROM usuario WHERE nombre_usuario = ?';
        $parametros = [$nombreUsuario];
        if ($idActual !== null) {
            $consulta .= ' AND cedula_usuario <> ?';
            $parametros[] = $idActual;
        }

        return $this->registroDuplicadoPorConsulta($consulta, $parametros);
    }
}
