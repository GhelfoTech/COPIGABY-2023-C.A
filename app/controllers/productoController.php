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
                $_POST['porcentaje_ganancia'] = $_POST['porcentaje_ganancia'] ?? 0;
                $_POST['costo'] = $_POST['costo'] ?? 0;
                $resultado = $object->addProduct($_POST);

                if (($_POST['origen'] ?? '') === 'compra') {
                    header('Content-Type: application/json');
                    echo json_encode($resultado);
                    exit();
                }

                setSystemFlashFromResult($resultado, 'Producto registrado correctamente.', 'No se pudo registrar el producto.');
                header("Location: ?url=producto");
                exit();
            }
            header("Location: ?url=producto");
            exit();
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idproducto'])) {
                $_POST['estado'] = isset($_POST['estado']) ? 1 : 0;
                $_POST['porcentaje_ganancia'] = $_POST['porcentaje_ganancia'] ?? 0;
                $_POST['costo'] = $_POST['costo'] ?? 0;
                $resultado = $object->updateProduct((int) $_POST['idproducto'], $_POST);
                setSystemFlashFromResult($resultado, 'Producto actualizado correctamente.', 'No se pudo actualizar el producto.');
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
    include 'app/views/productos/viewProducto.php';
