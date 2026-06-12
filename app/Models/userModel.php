<?php

namespace App\models;

use App\config\ConectDB;
use PDO;
use PDOException;

class userModel extends ConectDB {

    /**
     * Obtiene la lista de usuarios.
     */
    public function getAllUsers() {
        try {
            $query = "SELECT u.cedula_usuario, u.telefono, u.nombre_usuario, u.estado, u.codigo_rol, r.nombre_rol 
                      FROM usuario u 
                      INNER JOIN rol r ON u.codigo_rol = r.codigo_rol";
            $stmt = $this->getConnection()->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Registra un nuevo usuario en el sistema.
     * @param int $rol Por defecto se asigna el rol con código 2 (usualmente Operador/Vendedor).
     */
    public function addUser($cedula, $nombre_usuario, $telefono, $password, $rol = 2) {
        try {
            $query = "INSERT INTO usuario (cedula_usuario, telefono, nombre_usuario, codigo_rol, password, estado) 
                      VALUES (:cedula, :telefono, :nombre, :rol, :pass, 1)";
            $stmt = $this->getConnection()->prepare($query);
            $stmt->bindParam(':cedula', $cedula);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':nombre', trim($nombre_usuario)); // Limpiamos espacios
            $stmt->bindParam(':rol', $rol);
            $stmt->bindParam(':pass', $password);
            
            if ($stmt->execute()) {
                return ["status" => "success", "message" => "Usuario registrado con éxito."];
            }
            return ["status" => "error", "message" => "No se pudo completar el registro."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Actualiza un usuario existente.
     */
    public function updateUser($id, $cedula, $nombre, $telefono, $rol, $estado) {
        try {
            $query = "UPDATE usuario SET cedula_usuario = :cedula, nombre_usuario = :nombre, 
                      telefono = :telefono, codigo_rol = :rol, estado = :estado 
                      WHERE cedula_usuario = :id";
            $stmt = $this->getConnection()->prepare($query);
            $stmt->bindParam(':cedula', $cedula);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':rol', $rol);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                return ["status" => "success", "message" => "Usuario actualizado."];
            }
            return ["status" => "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Eliminación lógica (desactivación).
     */
    public function deleteUser($id) {
        try {
            $query = "UPDATE usuario SET estado = 0 WHERE cedula_usuario = :id";
            $stmt = $this->getConnection()->prepare($query);
            $stmt->bindParam(':id', $id);
            return ["status" => $stmt->execute() ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Verifica las credenciales de acceso de un usuario.
     * Retorna los datos del usuario si es válido y está activo, de lo contrario retorna false.
     */
    public function login($username, $password) {
        try {
            // Limpiamos entradas para evitar espacios accidentales
            $username = trim((string)$username);
            $password = trim((string)$password);

            $query = "SELECT cedula_usuario, nombre_usuario, password, codigo_rol, estado 
                      FROM usuario 
                      WHERE nombre_usuario = :user";
            
            $stmt = $this->getConnection()->prepare($query);
            $stmt->bindParam(':user', $username);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && (int)$user['estado'] === 1) { // Verifica existencia y estado activo
                // Comprueba si la contraseña coincide (hashed o texto plano)
                if ($password === $user['password']) {
                    // Si coincide, elimina la contraseña del array por seguridad y retorna el usuario
                    unset($user['password']);
                    return $user;
                }
            }
            return false;
        } catch (PDOException $e) {
            // Log PDO exceptions for server-side debugging
            error_log("PDOException during login for user '{$username}': " . $e->getMessage());
            return false;
        }
    }
}