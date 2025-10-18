<?php
session_start();

include 'conexion.php';
// Verificar conexión
if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = sha1($_POST['password']); // Encriptamos la contraseña con SHA1

    // Consulta SQL para obtener el iduser del usuario que coincide con el nombre y la contraseña
    $sql = "SELECT iduser,tipo FROM users WHERE nombre = '$username' AND password = '$password'";
    $result = $conexion->query($sql);

    if ($result->num_rows > 0) {
        // Usuario y contraseña correctos
        $row = $result->fetch_assoc();  // Obtener el resultado de la consulta
        $iduser = $row['iduser'];  // Almacenar el valor de iduser
        // echo $row['iduser'];
        // die();

        // Almacenar en la sesión
        $_SESSION['username'] = $username;
        $_SESSION['iduser'] = $iduser;
        $_SESSION['tipo'] = $row['tipo'];  
        // echo $_SESSION['username'];
        // echo $_SESSION['iduser'];
        // die();
        // Guardamos el iduser en la sesión
        header("Location: ../gastos/index.php"); // Redirigir a una página de bienvenida o dashboard
    } else {
        // Usuario o contraseña incorrectos
        header("Location: ../php/incorrecto.php");
    }
}

$conexion->close();
?>
