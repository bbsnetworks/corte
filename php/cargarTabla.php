<?php
header('Content-Type: application/json; charset=utf-8');
include("conexion.php");
$baseURL = '../'; // o la ruta relativa correcta desde tu HTML principal
if ($conexion->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Conexión fallida: " . $conexion->connect_error
    ]));
}

$mes = intval($_POST['mes'] ?? 0);
$year = intval($_POST['year'] ?? 0);

$search = trim($_POST['search'] ?? '');
$searchSQL = '';

if ($search !== '') {
    $searchEscaped = $conexion->real_escape_string($search);
    $searchSQL = " AND (
        g.id LIKE '%$searchEscaped%' OR
        g.titulo LIKE '%$searchEscaped%' OR
        g.fecha LIKE '%$searchEscaped%' OR
        u.nombre LIKE '%$searchEscaped%' OR
        g.tipo LIKE '%$searchEscaped%'
    )";
}

$sql = "SELECT g.id, g.titulo, g.costo, g.descripcion, g.fecha, g.evidencia, g.tipo, u.nombre AS usuario
        FROM gastos g
        INNER JOIN users u ON u.iduser = g.iduser
        WHERE MONTH(g.fecha) = $mes 
          AND YEAR(g.fecha) = $year
          $searchSQL
        ORDER BY g.id DESC";


$result = $conexion->query($sql);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        // Convertir tipo numérico a texto
        switch ($row["tipo"]) {
            case 1: $tipoTexto = "Gasto"; break;
            case 2: $tipoTexto = "Ingreso"; break;
            case 3: $tipoTexto = "Ingreso Banco"; break;
            case 4: $tipoTexto = "Gasto Banco"; break;
            default: $tipoTexto = "Desconocido";
        }
        
        $evidenciaRuta = '';
if (!empty($row["evidencia"])) {
    // Normalizar para quitar "../" inicial
    $evidenciaRuta = $baseURL . ltrim($row["evidencia"], './');
}

$data[] = [
    "id"        => $row["id"],
    "titulo"    => $row["titulo"],
    "costo"     => $row["costo"],
    "descripcion" => $row["descripcion"],
    "fecha"     => $row["fecha"],
    "evidencia" => $evidenciaRuta,
    "tipo"      => $tipoTexto,
    "usuario"   => $row["usuario"]
];

    }
}

$conexion->close();

// Enviar JSON limpio
echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>
