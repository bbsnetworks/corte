<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Corte BBS</title>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.1.4/css/dataTables.dataTables.min.css">

  <!-- FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" />

  <!-- Tus CSS personalizados -->
  <link rel="stylesheet" href="../css/generales.css">
  <link rel="stylesheet" href="../css/gastos.css">
  <link rel="stylesheet" href="../css/navbar.css">

</head>

<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: ../../menu/login/index.php");
  exit();
}
?>

<body class="bg-gradient-to-b from-[#0f0f0f] to-[#1a1a1a] text-white min-h-screen">

  <div class="flex">
    <?php include_once '../includes/sidebar.php'; ?>

    <div class="flex-1 p-6">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold">Agregar Gasto / Ingreso</h1>
      </div>

      <form id="uploadForm" enctype="multipart/form-data" class="max-w-xl mx-auto space-y-5">
        
        <!-- Título -->
        <div>
          <label for="titulo" class="block text-sm font-medium mb-1">Título</label>
          <input type="text" id="titulo" name="titulo" required
            class="w-full rounded-lg bg-gray-800 border border-gray-700 focus:ring-2 focus:ring-blue-500 px-4 py-2 text-white">
        </div>

        <!-- Costo -->
        <div>
          <label for="costo" class="block text-sm font-medium mb-1">Costo</label>
          <input type="number" id="costo" name="costo" value="1" min="1" pattern="^[0-9]+" required
            class="w-full rounded-lg bg-gray-800 border border-gray-700 focus:ring-2 focus:ring-red-500 px-4 py-2 text-white">
        </div>

        <!-- Descripción -->
        <div>
          <label for="descripcion" class="block text-sm font-medium mb-1">Descripción</label>
          <textarea id="descripcion" name="descripcion" rows="3" required
            class="w-full rounded-lg bg-gray-800 border border-gray-700 focus:ring-2 focus:ring-blue-500 px-4 py-2 text-white"></textarea>
        </div>

        <!-- Fecha -->
        <div>
          <label for="fecha" class="block text-sm font-medium mb-1">Fecha</label>
          <input type="date" id="fecha" name="fecha" required
            class="w-full rounded-lg bg-gray-800 border border-gray-700 focus:ring-2 focus:ring-blue-500 px-4 py-2 text-white">
        </div>

        <!-- Evidencia -->
        <div>
          <label for="file" class="block text-sm font-medium mb-1">Evidencia</label>
          <input type="file" id="file" name="file"
            class="w-full rounded-lg bg-gray-800 border border-gray-700 focus:ring-2 focus:ring-blue-500 px-4 py-2 text-white">
        </div>

        <!-- Tipo -->
        <div>
          <label for="tipo" class="block text-sm font-medium mb-1">Tipo</label>
          <select id="tipo" name="tipo"
            class="w-full rounded-lg bg-gray-800 border border-gray-700 focus:ring-2 focus:ring-blue-500 px-4 py-2 text-white">
            <option value="1">Gasto</option>
            <option value="2">Ingreso</option>
            <option value="3">Banco Ingreso</option>
            <option value="4">Banco Gasto</option>
          </select>
        </div>

        <!-- A nombre de -->
        <div>
          <label for="nombrede" class="block text-sm font-medium mb-1">A nombre de:</label>
          <select id="nombrede" name="nombrede"
            class="w-full rounded-lg bg-gray-800 border border-gray-700 focus:ring-2 focus:ring-blue-500 px-4 py-2 text-white">
            <option value="1">NOC1</option>
            <?php if($_SESSION['tipo']=='root'){ ?>
            <option value="2">NOC2</option>
            <?php } ?>
          </select>
        </div>

        <!-- Usuario -->
        <div>
          <input type="text" id="id-user" name="id-user" value="<?php echo $_SESSION['username'] ?>" disabled
            class="w-full rounded-lg bg-gray-800 border border-gray-700 px-4 py-2 text-gray-400">
        </div>

        <!-- Banco switch -->
        <div id="div-banco" class="hidden flex items-center space-x-2">
          <input id="banco-check" type="checkbox"
            class="h-5 w-5 text-blue-600 focus:ring-blue-500 rounded border-gray-300">
          <label for="banco-check" class="text-sm">Tomar a cuenta del efectivo</label>
        </div>

        <!-- Botón -->
        <div class="text-center">
          <button type="button" id="ingresar" onclick="submitForm()"
            class="px-6 py-3 rounded-lg bg-green-600 hover:bg-green-500 transition text-white font-semibold">
            Ingresar
          </button>
        </div>

      </form>

      <div id="message" class="mt-6"></div>
    </div>
  </div>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://momentjs.com/downloads/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.datatables.net/2.1.4/js/dataTables.min.js"></script>
  <script src="../js/gastos.js"></script>
  <script src="../js/sidebar.js"></script>

</body>
</html>
