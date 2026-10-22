<?php
    namespace App\config;

    use PDO;
    use PDOException; 

    abstract class ConectDB {

        // Atributos de la clase
        private $conex;

        public function __construct() {
            $this->getConnection();
        }

        // metodo para conectar a la base de datos
        protected function getConnection(): PDO {
            if ($this->conex !== null) {
                return $this->conex;
            }
            try {
                $this->conex = new PDO("mysql:host=localhost;dbname=db_copigaby1;charset=utf8", "root", "");
                $this->conex->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die('ERROR DE CONEXIÓN: No se ha podido conectar con la base de datos. ' . $e->getMessage());
            }

            // Retornar la conexión establecida
            return $this->conex;
        }
    }

?>