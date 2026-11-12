<?php

    use App\models\categoriaModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new categoriaModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'quick_register') {
            header('Content-Type: application/json; charset=utf-8');
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
                exit();
            }

            $nombre = trim((string) ($_POST['nombre_categoria'] ?? ''));
            $result = $object->addCategory($nombre);
            if (($result['status'] ?? '') === 'success') {
                $result['categoria'] = [
                    'codigo_categoria' => $result['codigo_categoria'],
                    'nombre_categoria' => $nombre,
                ];
            } else {
                http_response_code(422);
            }
            echo json_encode($result);
            exit();
        }

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_categoria'])) {
                $result = $object->addCategory(trim($_POST['nombre_categoria']));
                copigabyAjaxRespond($result, 'Categoría registrada correctamente.', 'No se pudo registrar la categoría.');
                $_SESSION['flash_message'] = $result;
                header("Location: ?url=categoria");
                exit();
            }
            header("Location: ?url=categoria");
            exit();
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idcategoria'], $_POST['nombre_categoria'])) {
                $estado = isset($_POST['estado']) ? 1 : 0;
                $result = $object->updateCategory(
                    (int) $_POST['idcategoria'],
                    trim($_POST['nombre_categoria']),
                    $estado
                );
                copigabyAjaxRespond($result, 'Categoría actualizada correctamente.', 'No se pudo actualizar la categoría.');
                $_SESSION['flash_message'] = $result;
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