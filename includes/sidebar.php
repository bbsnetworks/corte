<!-- sidebar.php -->
<?php
$base = '/corte'; 
$tipoUsuario = $_SESSION['tipo'] ?? '';
?>
<!-- Botón abrir menú -->
  <button id="btn-sidebar" onclick="toggleSidebar()" class="fixed top-4 left-4 z-50 text-white bg-gray-800 p-2 rounded hover:bg-gray-700">
    <i class="fas fa-bars"></i>
  </button>
<div id="sidebar-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden" onclick="closeSidebar()"></div>

<div id="sidebar" class="fixed top-0 left-0 w-64 h-full bg-gray-900 p-4 z-40 transform -translate-x-full transition-transform duration-300">
  <div class="flex flex-col justify-between h-full">
    <div>
      <div class="text-white text-xl font-bold flex items-center gap-2 mb-8">
        <img src="<?= $base ?>/img/logo.png" class="w-full" alt="">
      </div>
      <nav class="space-y-4">
        <a href="<?= $base ?>/gastos/index.php" onclick="closeSidebar()" class="flex items-center gap-2 text-white hover:text-blue-400">
          <i class="fa-solid fa-plus"></i> <span>Agregar Gasto/Ingreso</span>
        </a>
        <?php  if($tipoUsuario=='root'){ ?>
        <a href="<?= $base ?>/table/index.php" class="flex items-center gap-2 text-white hover:text-blue-400">
          <i class="fa-solid fa-list"></i> <span>Lista de Gastos/Ingresos</span>
        </a>
        <a href="<?= $base ?>/total/index.php" class="flex items-center gap-2 text-white hover:text-blue-400">
          <i class="fa-solid fa-chart-simple"></i><span>Total</span>
        </a>
        <?php } ?>
        <a href="<?= $base ?>/../menu/index.php" class="flex items-center gap-2 text-red-400 hover:text-red-600">
          <i class="fas fa-sign-out-alt"></i> <span>Salir a Menu</span>
        </a>
      </nav>
    </div>
  </div>
</div>




