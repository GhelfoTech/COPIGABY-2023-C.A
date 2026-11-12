<?php

    use App\models\pedidoModel;
    use App\models\medidaModel;
    use App\models\clienteModel;

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: ?url=login');
        exit();
    }

    $object = new pedidoModel();
    $medidaModel = new medidaModel();

    if (isset($_GET['type'])) {

        // Registro rápido de cliente desde el formulario de pedidos (AJAX).
        if ($_GET['type'] === 'cliente') {
            header('Content-Type: application/json; charset=utf-8');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
                exit();
            }

            $datosCliente = [
                'cedula_cliente' => trim((string) ($_POST['cedula_cliente'] ?? '')),
                'nombre'         => trim((string) ($_POST['nombre'] ?? '')),
                'telefono'       => trim((string) ($_POST['telefono'] ?? '')),
                'correo'         => trim((string) ($_POST['correo'] ?? '')),
                'direccion'      => trim((string) ($_POST['direccion'] ?? '')),
            ];

            $clienteModel = new clienteModel();
            $resultadoCliente = $clienteModel->addCliente($datosCliente);

            if (is_array($resultadoCliente) && ($resultadoCliente['status'] ?? '') === 'success') {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Cliente registrado correctamente.',
                    'cliente' => [
                        'cedula_cliente' => $datosCliente['cedula_cliente'],
                        'nombre' => $datosCliente['nombre'],
                    ],
                ]);
                exit();
            }

            echo json_encode([
                'status' => 'error',
                'message' => $resultadoCliente['message'] ?? 'No se pudo registrar el cliente.',
            ]);
            exit();
        }

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ?url=pedido');
                exit();
            }

            $isAjax = copigabyIsAjaxRequest();

            $esCashea = ($_POST['es_cashea'] ?? '') === '1';
            $porcentajeInicialRaw = $_POST['porcentaje_inicial'] ?? '';
            $porcentajeInicial = in_array((string) $porcentajeInicialRaw, ['0', '10', '20', '30', '40', '50', '60'], true)
                ? (int) $porcentajeInicialRaw
                : null;
            $requiereMetodoPago = !$esCashea || ($porcentajeInicial !== null && $porcentajeInicial > 0);

            if (!isset($_POST['codigo_cliente'], $_POST['items']) || ($requiereMetodoPago && empty($_POST['codigo_metodo']))) {
                $payload = [
                    'status'  => 'error',
                    'message' => 'Datos incompletos: cliente, ítems y método de pago de la inicial son obligatorios.',
                ];
                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($payload);
                    exit();
                }
                $_SESSION['pedido_flash'] = $payload;
                header('Location: ?url=pedido');
                exit();
            }

            $items = json_decode((string) $_POST['items'], true);
            if (!is_array($items) || $items === []) {
                $payload = [
                    'status'  => 'error',
                    'message' => 'El pedido debe incluir al menos un ítem válido.',
                ];
                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($payload);
                    exit();
                }
                $_SESSION['pedido_flash'] = $payload;
                header('Location: ?url=pedido');
                exit();
            }

            if (!isset($_POST['codigo_IVA']) || (int) $_POST['codigo_IVA'] <= 0) {
                $payload = [
                    'status'  => 'error',
                    'message' => 'Debe seleccionar un porcentaje de IVA válido.',
                ];
                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($payload);
                    exit();
                }
                $_SESSION['pedido_flash'] = $payload;
                header('Location: ?url=pedido');
                exit();
            }

            $datos = [
                'cedula_cliente'  => trim((string) $_POST['codigo_cliente']),
                'cedula_usuario'  => $_SESSION['user_id'],
                'fecha_pedido'    => $_POST['fecha_pedido'] ?? date('Y-m-d'),
                'codigo_IVA'      => (int) $_POST['codigo_IVA'],
                'subtotal'        => $_POST['subtotal'] ?? null,
                'monto_iva'       => $_POST['monto_iva'] ?? null,
                'porcentaje_iva'  => $_POST['porcentaje_iva'] ?? null,
                'monto_total'     => $_POST['monto_total'] ?? null,
                'tasa_actual'     => $_POST['tasa_actual'] ?? null,
                'es_cashea'       => $esCashea,
                'nivel_cashea'    => (int) ($_POST['nivel_cashea'] ?? 0),
                'porcentaje_inicial' => $porcentajeInicial,
            ];

            $pago = [
                'codigo_metodo' => (int) ($_POST['codigo_metodo'] ?? 0),
                'codigo_banco'  => isset($_POST['codigo_banco']) ? (int) $_POST['codigo_banco'] : null,
                'referencia'    => trim((string) ($_POST['referencia_pago'] ?? '')),
            ];

            $result = $object->addPedido($datos, $items, $pago);

            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($result);
                exit();
            }

            $_SESSION['pedido_flash'] = $result;
            header('Location: ?url=pedido');
            exit();
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_pedido'], $_POST['codigo_cliente'], $_POST['items'])) {
                $isAjax = copigabyIsAjaxRequest();

                $items = json_decode((string) $_POST['items'], true);
                if (!is_array($items) || $items === []) {
                    $payload = ['status' => 'error', 'message' => 'El pedido debe incluir al menos un ítem válido.'];
                    if ($isAjax) {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($payload);
                        exit();
                    }
                    $_SESSION['pedido_flash'] = $payload;
                    header('Location: ?url=pedido');
                    exit();
                }

                $pago = [
                    'codigo_metodo' => (int) ($_POST['codigo_metodo'] ?? 0),
                    'codigo_banco'  => isset($_POST['codigo_banco']) ? (int) $_POST['codigo_banco'] : null,
                    'referencia'    => trim((string) ($_POST['referencia_pago'] ?? '')),
                ];

                if (!isset($_POST['codigo_IVA']) || (int) $_POST['codigo_IVA'] <= 0) {
                    $payload = ['status' => 'error', 'message' => 'Debe seleccionar un porcentaje de IVA válido.'];
                    if ($isAjax) {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($payload);
                        exit();
                    }
                    $_SESSION['pedido_flash'] = $payload;
                    header('Location: ?url=pedido');
                    exit();
                }

                $result = $object->updatePedido((int) $_POST['codigo_pedido'], [
                    'cedula_cliente'  => trim((string) $_POST['codigo_cliente']),
                    'estado'          => isset($_POST['estado']) ? (int) $_POST['estado'] : 1,
                    'codigo_IVA'      => (int) $_POST['codigo_IVA'],
                    'subtotal'        => $_POST['subtotal'] ?? null,
                    'monto_iva'       => $_POST['monto_iva'] ?? null,
                    'porcentaje_iva'  => $_POST['porcentaje_iva'] ?? null,
                    'monto_total'     => $_POST['monto_total'] ?? null,
                    'tasa_actual'     => $_POST['tasa_actual'] ?? null,
                ], $items, $pago);

                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($result);
                    exit();
                }

                $_SESSION['pedido_flash'] = $result;
                header('Location: ?url=pedido');
                exit();
            }
            header('Location: ?url=pedido');
            exit();
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deletePedido'])) {
                $result = $object->deletePedido((int) $_POST['idpedido']);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($result);
                exit();
            }
            header('Location: ?url=pedido');
            exit();
        }

        elseif ($_GET['type'] === 'details') {
            if (isset($_GET['id'])) {
                $header = $object->getPedidoById((int) $_GET['id']);
                $items  = $object->getItemsByPedido((int) $_GET['id']);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['header' => $header, 'items' => $items]);
                exit();
            }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['header' => null, 'items' => []]);
            exit();
        }

        else {
            header('Location: ?url=pedido');
            exit();
        }
    }

    $pedidos      = $object->getAllPedidos();
    $clientes     = $object->getClientesActivos();
    $productos    = $object->getProductosActivos();
    $servicios    = $object->getServiciosActivos();
    $metodos      = $object->getMetodosActivos();
    $bancos       = $object->getBancosActivos();
    $monedaActiva = $object->getMonedaActiva();
    $tasaActual   = $object->getTasaActual();
    $ivas         = $object->getIvasActivos();
    $ivaActivo    = $object->getIvaActivo();
    $medidas      = $medidaModel->getMedidasActivas();
    $pedidoFlash  = $_SESSION['pedido_flash'] ?? null;
    unset($_SESSION['pedido_flash']);

    include 'app/views/pedidos/viewPedido.php';