<?php

    use App\models\compraModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new compraModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numero_factura_proveedor'])) {
                // Usamos cedula_usuario para que coincida con el modelo y la DB
                $_POST['cedula_usuario'] = $_SESSION['user_id']; 
                $items = json_decode($_POST['items'], true);
                if (!is_array($items)) {
                    $items = [];
                }
                $resultado = $object->addCompra($_POST, $items);

                copigabyAjaxRespond($resultado, 'Compra registrada correctamente.', 'No se pudo registrar la compra.');

                setSystemFlashFromResult($resultado, 'Compra registrada correctamente.', 'No se pudo registrar la compra.');
                header("Location: ?url=compra");
                exit();
            }
            header("Location: ?url=compra");
            exit();
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idcompra'], $_POST['codigo_proveedor'])) {
                $estado = isset($_POST['estado']) ? 1 : 0;
                $resultado = $object->updateCompra((int) $_POST['idcompra'], [
                    'codigo_proveedor'          => (int) $_POST['codigo_proveedor'],
                    'numero_factura_proveedor'  => trim($_POST['numero_factura_proveedor']),
                    'fecha_compra'              => $_POST['fecha_compra'],
                    'monto_total'               => $_POST['monto_total'],
                    'estado'                    => $estado,
                ]);
                copigabyAjaxRespond($resultado, 'Compra actualizada correctamente.', 'No se pudo actualizar la compra.');
                setSystemFlashFromResult($resultado, 'Compra actualizada correctamente.', 'No se pudo actualizar la compra.');
                header("Location: ?url=compra");
                exit();
            }
            header("Location: ?url=compra");
            exit();
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deleteCompra'])) {
                $result = $object->deleteCompra((int) $_POST['idcompra']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            header("Location: ?url=compra");
            exit();
        }

        elseif ($_GET['type'] === 'details') {
            if (isset($_GET['id'])) {
                $header = $object->getCompraById((int)$_GET['id']);
                $items = $object->getItemsByCompra((int)$_GET['id']);
                header('Content-Type: application/json');
                echo json_encode(['header' => $header, 'items' => $items]);
                exit();
            }
        }

        elseif ($_GET['type'] === 'update_details') {
            header('Content-Type: application/json; charset=utf-8');
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
                exit();
            }

            $idCompra = filter_var($_POST['idcompra'] ?? null, FILTER_VALIDATE_INT);
            $items = isset($_POST['items']) && is_string($_POST['items'])
                ? json_decode($_POST['items'], true)
                : null;
            if ($idCompra === false || $idCompra <= 0 || !is_array($items)) {
                http_response_code(422);
                echo json_encode(['status' => 'error', 'message' => 'Los datos de la compra no son válidos']);
                exit();
            }

            $camposCabecera = ['codigo_proveedor', 'numero_factura_proveedor', 'fecha_compra', 'estado'];
            $camposCabeceraEnviados = array_filter(
                $camposCabecera,
                static fn($campo) => array_key_exists($campo, $_POST)
            );
            if ($camposCabeceraEnviados && count($camposCabeceraEnviados) !== count($camposCabecera)) {
                http_response_code(422);
                echo json_encode(['status' => 'error', 'message' => 'Los datos de la compra están incompletos']);
                exit();
            }

            $datosCompra = $camposCabeceraEnviados
                ? array_intersect_key($_POST, array_flip($camposCabecera))
                : null;
            $resultado = $object->updateCompraItems($idCompra, $items, $datosCompra);
            if (($resultado['status'] ?? '') !== 'success') {
                http_response_code(422);
            }
            echo json_encode($resultado);
            exit();
        }

        else {
            header("Location: ?url=compra");
            exit();
        }
    }

    $compras = $object->getAllCompras();
    $proveedores = $object->getProviders();
    $productos = $object->getProducts();
    $categorias = $object->getCategories();
    include 'app/views/compras/viewCompra.php';