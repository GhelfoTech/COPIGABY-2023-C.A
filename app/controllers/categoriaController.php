<?php

    use App\models\categoriaModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    // Verificamos que el usuario esté logueado
    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new categoriaModel();
    $error = ""; // Inicializamos la variable de error para la vista

    if (isset($_GET['type'])) {

        // Se verifica si el tipo de vista es 'list' y se llama al método correspondiente
        if ($_GET['type'] == 'list') {
            $categorias = $object->getAllCategories();
            include 'app/views/categorias/viewCategoria.php';
        } 

        // Se verifica si el tipo de vista es 'register' y se llama al método correspondiente
        elseif ($_GET['type'] == 'register') {
            if (isset($_POST['nombre_categoria'])) {
                $result = $object->addCategory($_POST['nombre_categoria']);
            }
            include 'app/views/categorias/registerView.php';
        }

        elseif ($_GET['type'] == 'main') {
            if(isset($_POST["getCategories"])) {
                $result = $object->getAllCategories();
                echo json_encode($result);
                die();
            }
            if(isset($_POST["deleteCategory"])) {
                $result = $object->deleteCategory($_POST["idcategoria"]); 
                echo json_encode($result);
                die();
            }
            $categorias = $object->getAllCategories();
            include 'app/views/categorias/viewCategoria.php';
        }
    } else {
        // Acción por defecto cuando se accede sin parámetro 'type'
        $categorias = $object->getAllCategories();
        include 'app/views/categorias/viewCategoria.php';
    }