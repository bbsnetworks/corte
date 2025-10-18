<?php
include 'conexion.php';
// Verificar la conexión
if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}
session_start();
// Obtener el próximo ID para la imagen
$sql = "SELECT MAX(id) AS mayor FROM gastos";
$result = $conexion->query($sql);
$titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
$costo = isset($_POST['costo']) ? $_POST['costo'] : 0;
$descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : 0;
$nombrede = isset($_POST['nombrede']) ? $_POST['nombrede'] : '';
$id_user = isset($_SESSION['iduser']) ? $_SESSION['iduser'] : 0;
$ibanco = isset($_POST['banco']) ? $_POST['banco'] : 0;

$mayor = 1;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $mayor = intval($row['mayor']) + 1;
    }
}
if ($ibanco == 0) {
// Manejar la subida del archivo si existe
if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    $targetDir = "../evidencia/";
    $fileType = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
    $allowTypes = array('jpg', 'jpeg', 'png', 'gif');

    if (in_array($fileType, $allowTypes)) {
        $fileName = $mayor . '.' . $fileType;
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
            $sql2 = "INSERT INTO gastos (titulo, costo, descripcion, fecha, evidencia, tipo, nombrede, iduser)
                     VALUES ('$titulo', $costo, '$descripcion', '$fecha', '$targetFilePath', $tipo, '$nombrede', $id_user)";
            if ($conexion->query($sql2) === TRUE) {
                echo "ok";
            } else {
                echo "Error al insertar en la base de datos: " . $conexion->error;
            }
        } else {
            echo "Error al subir el archivo.";
        }
    } else {
        echo "Tipo de archivo no permitido.";
    }
} else {
    // Si no se envió un archivo, insertar sin evidencia
    $sql2 = "INSERT INTO gastos (titulo, costo, descripcion, fecha, tipo, nombrede, iduser)
             VALUES ('$titulo', $costo, '$descripcion', '$fecha', $tipo, '$nombrede', $id_user)";
    if ($conexion->query($sql2) === TRUE) {
        echo "ok";
    } else {
        echo "Error al insertar en la base de datos: " . $conexion->error;
    }
}
}else{ //se hace un segundo ingreso a base de datos haciendo un gasto para el apartado de ingreso banco
   // Manejar la subida del archivo si existe
if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    $targetDir = "../evidencia/";
    $fileType = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
    $allowTypes = array('jpg', 'jpeg', 'png', 'gif');

    if (in_array($fileType, $allowTypes)) {
        $fileName = $mayor . '.' . $fileType;
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
            $sql2 = "INSERT INTO gastos (titulo, costo, descripcion, fecha, evidencia, tipo, nombrede, iduser)
                     VALUES ('$titulo', $costo, '$descripcion', '$fecha', '$targetFilePath', $tipo, '$nombrede', $id_user)";
            $sql3 = "INSERT INTO gastos (titulo, costo, descripcion, fecha, evidencia, tipo, nombrede, iduser)
                     VALUES ('Ingreso a banco', $costo, '$descripcion', '$fecha', '$targetFilePath', 1, '$nombrede', $id_user)";         
            if ($conexion->query($sql2) === TRUE && $conexion->query($sql3) === TRUE) {
                echo "ok";
            } else {
                echo "Error al insertar en la base de datos: " . $conexion->error;
            }
        } else {
            echo "Error al subir el archivo.";
        }
    } else {
        echo "Tipo de archivo no permitido.";
    }
} else {
    // Si no se envió un archivo, insertar sin evidencia
    $sql2 = "INSERT INTO gastos (titulo, costo, descripcion, fecha, tipo, nombrede, iduser)
             VALUES ('$titulo', $costo, '$descripcion', '$fecha', $tipo, '$nombrede', $id_user)";
    $sql3 = "INSERT INTO gastos (titulo, costo, descripcion, fecha, tipo, nombrede, iduser)
             VALUES ('Ingreso a banco', $costo, '$descripcion', '$fecha', 1, '$nombrede', $id_user)";         
    if ($conexion->query($sql2) === TRUE && $conexion->query($sql3) === TRUE) {
        echo "ok";
    } else {
        echo "Error al insertar en la base de datos: " . $conexion->error;
    }
} 
}
$conexion->close();

?>
