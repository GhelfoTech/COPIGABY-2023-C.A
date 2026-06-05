<?php

    use App\models\productoModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    // Verificamos que el usuario esté logueado
    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new productoModel();

    if (isset($_GET['type'])) {

        // Vista de lista
        if ($_GET['type'] == 'list') {
            $productos = $object->getAllProducts();
            $categorias = $object->getCategories();
            $ivas = $object->getIvas();
            $medidas = $object->getMedidas();
            include 'app/views/productos/index.php';
        } 

        // Procesar Registro
        elseif ($_GET['type'] == 'register') {
            if (isset($_POST['nombre_producto'])) {
                $object->addProduct($_POST);
                header("Location: ?url=producto");
                exit();
            }
        }

        // Procesar Actualización
        elseif ($_GET['type'] == 'update') {
            if (isset($_POST['idproducto'])) {
                $_POST['estado'] = isset($_POST['estado']) ? 1 : 0;
                $object->updateProduct($_POST['idproducto'], $_POST);
                header("Location: ?url=producto");
                exit();
            }
        }

        // Acciones AJAX y Vista Principal
        elseif ($_GET['type'] == 'main') {
            if(isset($_POST["deleteProduct"])) {
                $result = $object->deleteProduct($_POST["idproducto"]); 
                echo json_encode($result);
                die();
            }
            $productos = $object->getAllProducts();
            $categorias = $object->getCategories();
            $ivas = $object->getIvas();
            $medidas = $object->getMedidas();
            include 'app/views/productos/index.php';
        }

    } else {
        // Acción por defecto
        $productos = $object->getAllProducts();
        $categorias = $object->getCategories();
        $ivas = $object->getIvas();
        $medidas = $object->getMedidas();
        include 'app/views/productos/index.php';
    }