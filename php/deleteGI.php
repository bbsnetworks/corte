<?php
include("conexion.php");

if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
    }
            
            
             $query = "DELETE FROM gastos WHERE id=$_POST[id]";

if ($conexion->query($query) === TRUE) {
    // Si la inserción es exitosa, procede con el flujo
    $response = array("status" => "success", "message" => "Registro eliminado correctamente.");
    
} else {
    // Si hay un error, muestra el mensaje de error
    $response = array("status" => "error", "message" => "Error al eliminar el registro: " . mysqli_error($conexion));
}



    $conexion->close();

    echo json_encode($response);
         
?>