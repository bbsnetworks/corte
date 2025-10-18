<?php
include("conexion.php");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
//Consultas NOC1
//consultas Ester

//ingresoE
$sql = "select sum(costo) as ingresoE from gastos where iduser=9 and month(fecha)=$_POST[mes] and year(fecha)=$_POST[year] and (tipo=2 || tipo=3) and nombrede=1";

$result = $conexion->query($sql);

if ($result->num_rows > 0) {
  // Output data of each row
  $ingresoE = 0;
  while ($row = $result->fetch_assoc()) {
    //echo "ID: " . $row["nombre"];
    $ingresoE = doubleval($row['ingresoE']);
  }
}

//gastoE
$sql2 = "select sum(costo) as gastoE from gastos where iduser=9 and month(fecha)=$_POST[mes] and year(fecha)=$_POST[year] and (tipo=1 || tipo=4) and nombrede=1";

$result = $conexion->query($sql2);

if ($result->num_rows > 0) {
  // Output data of each row
  $gastoE = 0;
  while ($row = $result->fetch_assoc()) {
    //echo "ID: " . $row["nombre"];
    $gastoE = doubleval($row['gastoE']);
  }
}

//Consultas BBS Networks
//Ingreso BBS
$sql3 = "select sum(costo) as ingresoB from gastos where iduser not like 9 and month(fecha)=$_POST[mes] and year(fecha)=$_POST[year] and (tipo=2 || tipo=3) and nombrede=1";

$result = $conexion->query($sql3);

if ($result->num_rows > 0) {
  // Output data of each row
  $ingresoB = 0;
  while ($row = $result->fetch_assoc()) {
    //echo "ID: " . $row["nombre"];
    $ingresoB = doubleval($row['ingresoB']);
  }
}

//gasto BBS
$sql4 = "select sum(costo) as ingresoB from gastos where iduser not like 9 and month(fecha)=$_POST[mes] and year(fecha)=$_POST[year] and (tipo=1 || tipo=4) and nombrede=1";

$result = $conexion->query($sql4);

if ($result->num_rows > 0) {
  // Output data of each row
  $gastoB = 0;
  while ($row = $result->fetch_assoc()) {
    //echo "ID: " . $row["nombre"];
    $gastoB = doubleval($row['ingresoB']);
  }
}

//consultas NOC2



//Consultas BBS Networks
//Ingreso BBS
$sql5 = "select sum(costo) as ingresoB from gastos where iduser not like 9 and month(fecha)=$_POST[mes] and year(fecha)=$_POST[year] and (tipo=2 || tipo=3) and nombrede=2";

$result = $conexion->query($sql5);

if ($result->num_rows > 0) {
  // Output data of each row
  $ingresoBBS2 = 0;
  while ($row = $result->fetch_assoc()) {
    //echo "ID: " . $row["nombre"];
    $ingresoBBS2 = doubleval($row['ingresoB']);
  }
}

//gasto BBS
$sql6 = "select sum(costo) as ingresoB from gastos where iduser not like 9 and month(fecha)=$_POST[mes] and year(fecha)=$_POST[year] and (tipo=1 || tipo=4) and nombrede=2";

$result = $conexion->query($sql6);

if ($result->num_rows > 0) {
  // Output data of each row
  $gastoBBS2 = 0;
  while ($row = $result->fetch_assoc()) {
    //echo "ID: " . $row["nombre"];
    $gastoBBS2 = doubleval($row['ingresoB']);
  }
}
$conexion->close();

// Retornar los datos como JSON
$response = array(
    
    'ingresoE' => $ingresoE,
    'gastoE' => $gastoE,
    'ingresoB' => $ingresoB,
    'gastoB' => $gastoB,
    'ingresoBBS2' => $ingresoBBS2,
    'gastoBBS2' => $gastoBBS2
);

echo json_encode($response);
?>