<?php
include("conexion.php");

if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
    }
            
            
             $query = "UPDATE gastos SET
              titulo = '$_POST[titulo]',
              costo = $_POST[costo],
              descripcion = '$_POST[descripcion]',
              fecha = '$_POST[fecha]',
              tipo = $_POST[tipo],
              nombrede = '$_POST[nombrede]'
         WHERE id = $_POST[id]";

if ($conexion->query($query) === TRUE) {
    // Si la inserción es exitosa, procede con el flujo
    $response = array("status" => "success", "message" => "Registro actualizado correctamente.");
    
} else {
    // Si hay un error, muestra el mensaje de error
    $response = array("status" => "error", "message" => "Error al actualizar el registro: " . mysqli_error($conexion));
}



    $conexion->close();

    echo json_encode($response);
         
?>