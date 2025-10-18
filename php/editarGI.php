<?php
include('conexion.php');

$idgasto = $_POST['id'];

if ($conexion->connect_error) {
    die('Conexión fallida: ' . $conexion->connect_error);
}

$sql = 'SELECT * from gastos where id=' . $idgasto;
$result = $conexion->query($sql);




if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
    echo '<form id="uploadForm" enctype="multipart/form-data">  
    <div class="mb-3">
        <input type="text" class="form-control d-none" id="id" name="id" value="'.$row["id"].'" required>
        <label for="titulo" class="form-label">Titúlo</label>
        <input type="text" class="form-control" id="titulo" name="titulo" value="'.$row["titulo"].'" required>
    </div>
    <div class="mb-3">
        <label for="costo" class="form-label">Costo</label>
        <input type="number" class="form-control rojo" id="costo" name="costo" value="'.$row["costo"].'" min="1" pattern="^[0-9]+" required>
    </div>
    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required>'.$row["descripcion"].'</textarea>
    </div>
    <div class="mb-3">
        <label for="fecha" class="form-label">Fecha</label>
        <input type="date" class="form-control" id="fecha" name="fecha" value="'.$row["fecha"].'" required>
    </div>
    <div class="mb-3">
        <label for="tipo" class="form-label">Tipo</label>';
        switch($row["tipo"]){
        case "1":
        echo '<select class="form-select" id="tipo" name="tipo">
            <option value="1" selected>Gasto</option>
            <option value="2">Ingreso</option>
            <option value="3">Ingreso Banco</option>
            <option value="4">Gasto Banco</option>
        </select>';
        break;
        case "2":
            echo '<select class="form-select" id="tipo" name="tipo">
                <option value="1">Gasto</option>
                <option value="2" selected>Ingreso</option>
                <option value="3">Ingreso Banco</option>
                <option value="4">Gasto Banco</option>
            </select>';
            break;
            case "3":
                echo '<select class="form-select" id="tipo" name="tipo">
                    <option value="1">Gasto</option>
                    <option value="2">Ingreso</option>
                    <option value="3" selected>Ingreso Banco</option>
                    <option value="4">Gasto Banco</option>
                </select>';
                break;
                case "4":
                    echo '<select class="form-select" id="tipo" name="tipo">
                        <option value="1">Gasto</option>
                        <option value="2">Ingreso</option>
                        <option value="3">Ingreso Banco</option>
                        <option value="4" selected>Gasto Banco</option>
                    </select>';
                    break;     
        }
    echo '</div>
    <div class="mb-3">
        <label for="nombrede" class="form-label">A nombre de:</label>';
        switch($row["nombrede"]){
            case "1":
      echo '<select class="form-select" id="nombrede" name="nombrede">
            <option value="1" selected>NOC1</option>
            <option value="2">NOC2</option>
        </select>';
        break;
        case "2":
            echo '<select class="form-select" id="nombrede" name="nombrede">
                  <option value="1">NOC1</option>
                  <option value="2" selected>NOC2</option>
              </select>';
              break;  
        }
    echo '</div>
    <div class="mb-3">
        <label for="nombrede" class="form-label">ID Usuario:</label>
        <input type="text" class="form-control" id="id-user" name="id-user" value="'.$row["iduser"].'" disabled>
    </div>
    <div class="mb-3 centrar">
        <button type="button" class="btn btn-warning" onclick="updateGI()">Actualizar</button>
    </div>
</form>';
    }
}
$conexion->close();
?>