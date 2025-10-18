<?php
header('Content-Type: application/json; charset=utf-8');
include("conexion.php");

if ($conexion->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Conexión fallida: " . $conexion->connect_error
    ]));
}

$mes       = intval($_POST['mes'] ?? 0);
$year      = intval($_POST['year'] ?? 0);
$nombrede  = $_POST['nombrede'] ?? "todos";   // 1=NOC1, 2=NOC2, "todos"
$usuario   = $_POST['usuario'] ?? "todos";    // nombre de users.nombre o "todos"
$tipoFiltro= $_POST['tipo'] ?? "todos";       // 1,2,3,4 o "todos"
$search    = trim($_POST['search'] ?? "");

$page      = max(1, intval($_POST['page'] ?? 1));
$perPage   = max(1, min(100, intval($_POST['per_page'] ?? 10))); // límite sano

// Para exportar todo sin LIMIT (opcional)
$noLimit   = intval($_POST['export'] ?? 0) === 1;

$where = [];
$where[] = "MONTH(g.fecha) = {$mes}";
$where[] = "YEAR(g.fecha) = {$year}";

if ($nombrede !== "todos") {
    $where[] = "g.nombrede = " . intval($nombrede);
}
if ($usuario !== "todos") {
    $where[] = "u.nombre = '" . $conexion->real_escape_string($usuario) . "'";
}
if ($tipoFiltro !== "todos") {
    $where[] = "g.tipo = " . intval($tipoFiltro);
}

if ($search !== "") {
    $s = $conexion->real_escape_string($search);
    // Búsqueda en título, descripción, usuario, fecha, cuenta (NOC1/NOC2) y tipo texto
    $where[] = "(
        g.titulo LIKE '%{$s}%'
        OR g.descripcion LIKE '%{$s}%'
        OR u.nombre LIKE '%{$s}%'
        OR DATE_FORMAT(g.fecha, '%Y-%m-%d') LIKE '%{$s}%'
        OR (CASE g.nombrede WHEN 1 THEN 'NOC1' WHEN 2 THEN 'NOC2' ELSE '-' END) LIKE '%{$s}%'
        OR (CASE g.tipo
              WHEN 1 THEN 'Gasto'
              WHEN 2 THEN 'Ingreso'
              WHEN 3 THEN 'Ingreso Banco'
              WHEN 4 THEN 'Gasto Banco'
            END) LIKE '%{$s}%'
    )";
}

$whereSQL = implode(" AND ", $where);

// Conteo total para paginación
$countSQL = "
  SELECT COUNT(*) AS total
  FROM gastos g
  INNER JOIN users u ON u.iduser = g.iduser
  WHERE $whereSQL
";
$countRes = $conexion->query($countSQL);
$totalRows = 0;
if ($countRes && $countRes->num_rows > 0) {
    $totalRows = intval($countRes->fetch_assoc()['total']);
}

// Paginación
$totalPages = max(1, (int)ceil($totalRows / $perPage));
$offset = ($page - 1) * $perPage;

// Query principal
$limitSQL = $noLimit ? "" : " LIMIT $perPage OFFSET $offset";

$sql = "SELECT g.id, g.titulo, g.costo, g.descripcion, g.fecha, g.nombrede, g.tipo, u.nombre
        FROM gastos g
        INNER JOIN users u ON u.iduser = g.iduser
        WHERE $whereSQL
        ORDER BY g.fecha DESC
        $limitSQL";

$result = $conexion->query($sql);

$data = [];
$gastos = 0.0;
$ingreso = 0.0;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Sumar gastos/ingresos (en totales de la página actual o de todo? → mantenemos de la consulta actual)
        if ($row["tipo"] == 1 || $row["tipo"] == 4) {
            $gastos += floatval($row["costo"]);
        } elseif ($row["tipo"] == 2 || $row["tipo"] == 3) {
            $ingreso += floatval($row["costo"]);
        }

        $nombreCuenta = $row["nombrede"] == 1 ? "NOC1" : ($row["nombrede"] == 2 ? "NOC2" : "-");

        switch (intval($row["tipo"])) {
            case 1: $tipoTexto = "Gasto"; break;
            case 2: $tipoTexto = "Ingreso"; break;
            case 3: $tipoTexto = "Ingreso Banco"; break;
            case 4: $tipoTexto = "Gasto Banco"; break;
            default: $tipoTexto = "Desconocido";
        }

        $data[] = [
            "id"         => $row["id"],
            "nombre"     => $row["titulo"],
            "costo"      => (0 + $row["costo"]),
            "descripcion"=> $row["descripcion"],
            "fecha"      => $row["fecha"],
            "nombrede"   => $nombreCuenta,
            "tipo"       => $tipoTexto,
            "usuario"    => $row["nombre"]
        ];
    }
}

$conexion->close();

echo json_encode([
    "data"         => $data,
    "gastos"       => $gastos,
    "ingreso"      => $ingreso,
    "tipo"         => $tipoFiltro,
    "cuenta"       => $nombrede,
    "page"         => $page,
    "per_page"     => $perPage,
    "total_rows"   => $totalRows,
    "total_pages"  => $totalPages
], JSON_UNESCAPED_UNICODE);
