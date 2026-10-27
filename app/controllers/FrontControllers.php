<?php

    namespace App\controllers;

    // Helpers de autenticación y permisos por rol
    require_once __DIR__ . '/../../herpers/auth.php';

    class FrontControllers {

        /** Módulos que requieren sesión iniciada (todo excepto el login) */
        private $moduloPublico = 'login';

        /** Módulos accesibles por cualquier usuario autenticado (páginas de inicio) */
        private $modulosBase = ['dashboard'];

        // Definicion de atributos que serán constantes
        private $dir;
        private $controller;
        private $url;

        public function __construct() {
           //ver si funcionan los comit
            // Si existe y no está vacía una request con el nombre de url

            if (isset($_REQUEST["url"])) {

                //se asigna el valor de la request a la variable url
                $this->url = $_REQUEST["url"];

                //directorio donde se encuentran los controladores
                $this->dir = 'app/controllers/';

                //concatenación del nombre del controlador con el nombre de la clase
                $this->controller = 'Controller.php';

                //se ejecuta el método getURL que se encarga de cargar el controlador correspondiente
                $this->getURL();

            } else {

                //si no existe la request se asigna el valor conocido por defecto a la variable url

                echo "Error 404: la url no existe";

                die("<script>location='?url=login'</script>");
            }
        }

        private function getURL() {

            //si existe el controlador en la carpeta de controladores

            if(file_exists($this->dir.$this->url.$this->controller)) {

                // Autenticación y permisos según el rol antes de cargar el controlador
                $this->verificarAcceso();

                //se llama al controlador correspondiente
                require_once($this->dir.$this->url.$this->controller);

            } else {

                echo "<script>location='?url=login'</script>";
            }
        }

        /**
         * Verifica sesión y permisos del módulo/acción solicitado.
         * El login es público; todos los demás módulos requieren sesión
         * y que el rol tenga permiso sobre el módulo y la acción.
         */
        private function verificarAcceso(): void {
            if ($this->url === $this->moduloPublico) {
                return; // el login no requiere sesión
            }

            // El dashboard es la página de inicio: cualquier usuario autenticado puede verlo
            if (in_array($this->url, $this->modulosBase, true)) {
                checkAuth();
                return;
            }

            checkPermission($this->url);
        }

    }

?>