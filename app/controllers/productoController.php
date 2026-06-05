<?php

    use App\models\productoModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new productoModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_producto'])) {
                $object->addProduct($_POST);
                header("Location: ?url=producto");
                exit();
            }
            header("Location: ?url=producto");
            exit();
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idproducto'])) {
                $_POST['estado'] = isset($_POST['estado']) ? 1 : 0;
                $object->updateProduct((int) $_POST['idproducto'], $_POST);
                header("Location: ?url=producto");
                exit();
            }
            header("Location: ?url=producto");
            exit();
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deleteProduct'])) {
                $result = $object->deleteProduct((int) $_POST['idproducto']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            header("Location: ?url=producto");
            exit();
        }

        elseif ($_GET['type'] === 'list') {
            header("Location: ?url=producto");
            exit();
        }
    }

    $productos = $object->getAllProducts();
    $categorias = $object->getCategories();
    $ivas = $object->getIvas();
    $medidas = $object->getMedidas();
    include 'app/views/productos/viewProducto.php';
