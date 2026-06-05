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
            $query = "SELECT u.codigo_usuario, u.cedula, u.telefono, u.nombre_usuario, u.estado, r.nombre_rol 
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
     * Obtiene los roles para los selectores.
     */
    public function getRoles() {
        try {
            $stmt = $this->getConnection()->prepare("SELECT * FROM rol");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    /**
     * Registra un nuevo usuario en el sistema.
     * @param int $rol Por defecto se asigna el rol con código 2 (usualmente Operador/Vendedor).
     */
    public function addUser($cedula, $nombre_usuario, $telefono, $password, $rol = 2) {
        try {
            $query = "INSERT INTO usuario (cedula, telefono, nombre_usuario, codigo_rol, password, estado) 
                      VALUES (:cedula, :telefono, :nombre, :rol, :pass, 1)";
            $stmt = $this->getConnection()->prepare($query);
            $stmt->bindParam(':cedula', $cedula);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':nombre', $nombre_usuario);
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
            $query = "UPDATE usuario SET cedula = :cedula, nombre_usuario = :nombre, 
                      telefono = :telefono, codigo_rol = :rol, estado = :estado 
                      WHERE codigo_usuario = :id";
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
            $query = "UPDATE usuario SET estado = 0 WHERE codigo_usuario = :id";
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
            // Buscamos por nombre_usuario, password y estado activo
            $query = "SELECT codigo_usuario, nombre_usuario, codigo_rol 
                      FROM usuario 
                      WHERE nombre_usuario = :user AND password = :pass AND estado = 1";
            $stmt = $this->getConnection()->prepare($query);
            $stmt->bindParam(':user', $username);
            $stmt->bindParam(':pass', $password);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}