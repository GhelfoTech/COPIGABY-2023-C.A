<?php

    use App\models\categoriaModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new categoriaModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_categoria'])) {
                $object->addCategory(trim($_POST['nombre_categoria']));
                header("Location: ?url=categoria");
                exit();
            }
            header("Location: ?url=categoria");
            exit();
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idcategoria'], $_POST['nombre_categoria'])) {
                $estado = isset($_POST['estado']) ? 1 : 0;
                $object->updateCategory(
                    (int) $_POST['idcategoria'],
                    trim($_POST['nombre_categoria']),
                    $estado
                );
                header("Location: ?url=categoria");
                exit();
            }
            header("Location: ?url=categoria");
            exit();
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['getCategories'])) {
                $result = $object->getAllCategories();
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            if (isset($_POST['deleteCategory'])) {
                $result = $object->deleteCategory((int) $_POST['idcategoria']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            header("Location: ?url=categoria");
            exit();
        }

        elseif ($_GET['type'] === 'list') {
            header("Location: ?url=categoria");
            exit();
        }
    }

    $categorias = $object->getAllCategories();
    include 'app/views/categorias/viewCategoria.php';
