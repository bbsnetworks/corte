<!Doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Corte BBS</title>
  <link href="../css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/dataTables.dataTables.css">
  <link rel="stylesheet" href="../css/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/generales.css">
  <link rel="stylesheet" href="../css/table.css">
  <link rel="stylesheet" href="../css/navbar.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" />

</head>
<?php
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: ../../menu/login/index.php");
  exit();
}

//echo "Bienvenido, " . $_SESSION['username'];
?>
<body class="bg-gradient-to-b from-[#0f0f0f] to-[#1a1a1a] text-white min-h-screen flex">

  <?php include_once '../includes/sidebar.php'; ?>

  <div class="flex-1 p-6 w-full">
    <h1 class="text-2xl font-bold mb-6 text-center">Lista de Gastos / Ingresos</h1>

    <!-- Filtro de Fecha -->
    <div class="mb-4 flex flex-col sm:flex-row gap-4 justify-between items-center">
      <div>
        <label for="fechaa" class="block text-sm font-medium mb-1">Fecha:</label>
        <input type="month" id="fechaa" name="fecha"
          class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 text-white">
      </div>

      <!-- Buscador -->
      <div class="w-full sm:w-1/3">
        <label for="buscar" class="block text-sm font-medium mb-1">Buscar:</label>
        <input type="text" id="buscar" placeholder="Buscar..."
          class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-500 text-white">
      </div>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto bg-gray-900 rounded-xl shadow-lg border border-gray-800">
      <table id="tablaGastos" class="w-full border-collapse">
        <thead class="bg-gray-800">
          <tr>
            <th class="p-3 text-left">ID</th>
            <th class="p-3 text-left">Fecha</th>
            <th class="p-3 text-left">Título</th>
            <th class="p-3 text-left">Costo</th>
            <th class="p-3 text-left">Tipo</th>
            <th class="p-3 text-left">Usuario</th>
            <th class="p-3 text-left">Evidencia</th>
            <th class="p-3 text-left">Acciones</th>
          </tr>
        </thead>
        <tbody id="tablaBody" class="divide-y divide-gray-700">
          <!-- Filas dinámicas -->
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <div id="pagination" class="flex justify-center mt-6 gap-2">
      <!-- Botones de paginación dinámicos -->
    </div>
    <!-- Modal de edición -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarLabel">Editar Gasto / Ingreso</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="modal2">
        <!-- Aquí se cargará el formulario de editarGI.php -->
      </div>
    </div>
  </div>
</div>

  </div>
  <script src="../js/bootstrap.bundle.min.js"
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
    crossorigin="anonymous"></script>
  <script src="../js/jquery-3.7.1.min.js"></script>
  <script src="../js/moment.min.js"></script>
  <script src="../js/sweetalert2@11.js"></script>
  <script src="../js/dataTables.min.js"></script>
  <script src="../js/bootstrapval.js"></script>
  <script src="../js/booststraptoogletips.js"></script>
  <script src="../js/table.js"></script>
  <script src="../js/sidebar.js"></script>

</body>

</html>