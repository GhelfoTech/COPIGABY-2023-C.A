<?php

    use App\models\userModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    $object = new userModel();
    $error = ""; // Inicializamos la variable de error para la vista

    if (isset($_GET['type'])) {

        // Se verifica si el tipo de vista es 'list' y se llama al método correspondiente
        if ($_GET['type'] == 'list') {
            $result = $object->getAllUsers();
            include 'app/views/user/listView.php';
        } 

        // Se verifica si el tipo de vista es 'register' y se llama al método correspondiente
        elseif ($_GET['type'] == 'register') {
            // Se ajustan los campos según la tabla 'usuario' del SQL proporcionado
            if (isset($_POST['cedula']) && isset($_POST['nombre_usuario']) && isset($_POST['telefono']) && isset($_POST['password'])) {
                $rol = $_POST['codigo_rol'] ?? 2; // Rol por defecto si no se envía
                $result = $object->addUser($_POST['cedula'], $_POST['nombre_usuario'], $_POST['telefono'], $_POST['password'], $rol);
            }
            include 'app/views/user/registerView.php';
        }

        elseif ($_GET['type'] == 'main') {
            if(isset($_POST["getUsers"])) {
                $result = $object->getAllUsers();
                echo json_encode($result);
                die();
            }
            if(isset($_POST["deleteUser"])) {
                // idUser mapeado a codigo_usuario
                $result = $object->deleteUser($_POST["idUser"]); 
                echo json_encode($result);
                die();
            }
            include 'app/views/user/userView.php';
        }
        
        else {
            echo "Error: Tipo de vista no válido.";
        }

    } else {
        // Lógica de Autenticación (Login)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioInput = trim($_POST['username'] ?? ''); // Limpiamos espacios
            $passwordInput = trim($_POST['password'] ?? '');

            if (!empty($usuarioInput) && !empty($passwordInput)) {
                $user = $object->login($usuarioInput, $passwordInput);
                
                if ($user) {
                    // Guardamos los datos esenciales en la sesión
                    $_SESSION['user_id']  = (string)$user['cedula_usuario'];
                    $_SESSION['username'] = $user['nombre_usuario']; // nombre_usuario
                    $_SESSION['rol']      = $user['codigo_rol'];
                    
                    header("Location: ?url=dashboard");
                    exit();
                } else {
                    $error = "Usuario o contraseña incorrectos.";
                }
            } else {
                $error = "Por favor, llene todos los campos.";
            }
        }
        $empresa = $object->getEmpresaRIF();
        $rifEmpresa = $empresa['rif'] ?? 'J-504149357';
        $nombreEmpresa = $empresa['nombre_empresa'] ?? 'CopiGaby';
        include 'app/views/login/viewLogin.php';
    }