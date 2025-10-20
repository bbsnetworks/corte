<!Doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Corte BBS</title>
  <link href="css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="css/bootstrap-icons.min.css">
  <link rel="stylesheet" href="css/generales.css">
  <link rel="stylesheet" href="css/index.css">
  <link rel="stylesheet" href="css/navbar.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" />


</head>
<?php
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: ../menu/login/index.php");
  exit();
}

//echo "Bienvenido, " . $_SESSION['username'];
?>
<body class="bg-gradient-to-b from-[#0f0f0f] to-[#1a1a1a] text-white min-h-screen flex">
  <div class="container mx-auto p-6">
    <!-- Sidebar -->
    <?php include_once 'includes/sidebar.php'; ?>

    <!-- Contenido principal -->
    <div class="flex flex-col items-center gap-6 mt-10">

      <!-- Botón Generar Gasto/Pago -->
      <button id="gp"
        class="px-8 py-4 text-lg font-semibold rounded-2xl bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-400 hover:to-blue-600 transition-all shadow-lg flex items-center gap-3">
        <i class="fas fa-plus-circle text-xl"></i>
        Generar Gasto/Pago
      </button>

      <!-- Botón Tabla de Gastos/Pagos -->
      <button id="tp"
        class="px-8 py-4 text-lg font-semibold rounded-2xl bg-gradient-to-r from-green-500 to-green-700 hover:from-green-400 hover:to-green-600 transition-all shadow-lg flex items-center gap-3">
        <i class="fas fa-table text-xl"></i>
        Tabla de Gastos/Pagos
      </button>

    </div>
  </div>

  <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://momentjs.com/downloads/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/bootstrapval.js"></script>
  <script src="js/booststraptoogletips.js"></script>
  <script src="js/index.js"></script>

</body>

</html>