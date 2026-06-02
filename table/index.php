<?php
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: ../../menu/login/index.php");
  exit();
}
?>

<!Doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Corte BBS | Lista de Gastos</title>

  <!-- Bootstrap solo para modal -->
  <link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

  <!-- Iconos -->
  <link rel="stylesheet" href="../css/bootstrap-icons.min.css">
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" />

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Tus estilos -->
  <link rel="stylesheet" href="../css/generales.css">
  <link rel="stylesheet" href="../css/table.css">
  <link rel="stylesheet" href="../css/navbar.css">
</head>

<body class="min-h-screen bg-[#071322] text-white overflow-x-hidden">

  <div class="flex min-h-screen">

    <?php include_once '../includes/sidebar.php'; ?>

    <main class="flex-1 p-4 sm:p-6 lg:p-8 w-full">

      <!-- Encabezado principal -->
      <section class="mb-8 rounded-[28px] border border-cyan-400/20 bg-[#061b3b]/80 shadow-2xl shadow-blue-950/40 overflow-hidden">
        <div class="relative p-6 sm:p-8">

          <!-- Brillos decorativos -->
          <div class="absolute -top-24 -right-24 w-72 h-72 bg-cyan-500/10 rounded-full blur-3xl"></div>
          <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-blue-600/10 rounded-full blur-3xl"></div>

          <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

            <div class="flex items-start gap-4">
              <div class="w-14 h-14 rounded-2xl bg-cyan-400/10 border border-cyan-300/20 flex items-center justify-center shadow-lg shadow-cyan-950/40">
                <i class="fa-solid fa-table-list text-cyan-300 text-2xl"></i>
              </div>

              <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">
                  Lista de Gastos / Ingresos
                </h1>
                <p class="mt-2 text-sm sm:text-base text-cyan-100/80">
                  Consulta, filtra y administra los movimientos registrados en el corte.
                </p>
              </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
              <button type="button"
                onclick="limpiarFiltrosTabla()"
                class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-5 py-3 font-semibold text-white hover:bg-white/10 transition">
                <i class="fa-solid fa-filter-circle-xmark"></i>
                Limpiar filtros
              </button>

              <button type="button"
                onclick="cargarGastos && cargarGastos()"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 px-6 py-3 font-bold text-white shadow-lg shadow-blue-900/40 hover:from-cyan-400 hover:to-blue-500 transition">
                <i class="fa-solid fa-rotate"></i>
                Actualizar
              </button>
            </div>

          </div>
        </div>
      </section>

      <!-- Card principal -->
      <section class="rounded-[28px] border border-blue-400/20 bg-[#06152d]/90 shadow-2xl shadow-black/30 p-4 sm:p-6 lg:p-7">

        <!-- Título sección -->
        <div class="mb-6 rounded-2xl border border-cyan-400/20 bg-[#062a52]/70 px-5 py-4">
          <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fa-solid fa-magnifying-glass-chart text-cyan-300"></i>
            Filtros de búsqueda
          </h2>
          <p class="mt-1 text-sm text-cyan-100/75">
            Filtra los registros por mes o busca por título, usuario, tipo o descripción.
          </p>
        </div>

        <!-- Filtros -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-7">

          <!-- Fecha -->
          <div>
            <label for="fechaa" class="block text-sm font-semibold mb-2">
              Fecha
            </label>

            <div class="relative">
              <i class="fa-solid fa-calendar-days absolute left-4 top-1/2 -translate-y-1/2 text-cyan-200/50"></i>
              <input type="month" id="fechaa" name="fecha"
                class="w-full rounded-2xl bg-[#101b31] border border-white/15 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 outline-none pl-11 pr-4 py-3 text-white placeholder:text-blue-200/60 transition">
            </div>
          </div>

          <!-- Buscador -->
          <div class="lg:col-span-2">
            <label for="buscar" class="block text-sm font-semibold mb-2">
              Buscar
            </label>

            <div class="relative">
              <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-cyan-200/50"></i>
              <input type="text" id="buscar" placeholder="Buscar por título, usuario, tipo o descripción..."
                class="w-full rounded-2xl bg-[#101b31] border border-white/15 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 outline-none pl-11 pr-4 py-3 text-white placeholder:text-blue-200/60 transition">
            </div>
          </div>

        </div>

        <!-- Resumen / estado -->
        <div class="mb-5 grid grid-cols-1 md:grid-cols-3 gap-4">

          <div class="rounded-2xl border border-cyan-400/15 bg-[#071b36]/80 p-4">
            <p class="text-sm text-cyan-100/60">Registros cargados</p>
            <h3 id="totalRegistros" class="mt-1 text-2xl font-bold text-white">0</h3>
          </div>

          <div class="rounded-2xl border border-red-400/15 bg-red-500/5 p-4">
            <p class="text-sm text-red-100/60">Gastos</p>
            <h3 id="totalGastos" class="mt-1 text-2xl font-bold text-red-300">$0.00</h3>
          </div>

          <div class="rounded-2xl border border-emerald-400/15 bg-emerald-500/5 p-4">
            <p class="text-sm text-emerald-100/60">Ingresos</p>
            <h3 id="totalIngresos" class="mt-1 text-2xl font-bold text-emerald-300">$0.00</h3>
          </div>

        </div>

        <!-- Tabla -->
        <div class="overflow-hidden rounded-[24px] border border-cyan-400/15 bg-[#071b36]/80 shadow-xl shadow-black/20">

          <div class="overflow-x-auto">
            <table id="tablaGastos" class="w-full min-w-[980px] border-collapse">

              <thead class="bg-[#071b36]/95">
                <tr class="border-b border-cyan-400/20">
                  <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-[0.18em] text-cyan-100/70">ID</th>
                  <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-[0.18em] text-cyan-100/70">Fecha</th>
                  <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-[0.18em] text-cyan-100/70">Título</th>
                  <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-[0.18em] text-cyan-100/70">Costo</th>
                  <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-[0.18em] text-cyan-100/70">Tipo</th>
                  <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-[0.18em] text-cyan-100/70">Usuario</th>
                  <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-[0.18em] text-cyan-100/70">Evidencia</th>
                  <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-[0.18em] text-cyan-100/70">Acciones</th>
                </tr>
              </thead>

              <tbody id="tablaBody" class="divide-y divide-white/10">
                <!-- Filas dinámicas desde table.js -->
              </tbody>

            </table>
          </div>

          <!-- Estado vacío -->
          <div id="tablaVacia" class="hidden px-6 py-14 text-center">
            <div class="mx-auto mb-4 w-16 h-16 rounded-2xl bg-cyan-400/10 border border-cyan-300/20 flex items-center justify-center">
              <i class="fa-solid fa-folder-open text-cyan-300 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-white">No hay registros para mostrar</h3>
            <p class="mt-1 text-sm text-cyan-100/60">
              Intenta cambiar el mes seleccionado o modificar la búsqueda.
            </p>
          </div>

        </div>

        <!-- Paginación -->
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

          <p id="paginationInfo" class="text-sm text-cyan-100/60">
            Mostrando registros...
          </p>

          <div id="pagination" class="flex flex-wrap justify-center sm:justify-end gap-2">
            <!-- Botones de paginación dinámicos desde table.js -->
          </div>

        </div>

      </section>

      <!-- Modal de edición -->
      <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

          <div class="modal-content border border-cyan-400/20 bg-[#06152d] text-white rounded-[24px] overflow-hidden shadow-2xl shadow-black/50">

            <div class="modal-header border-b border-cyan-400/15 bg-[#062a52]/80 px-5 py-4">
              <div>
                <h5 class="modal-title font-bold text-xl flex items-center gap-2" id="modalEditarLabel">
                  <i class="fa-solid fa-pen-to-square text-cyan-300"></i>
                  Editar Gasto / Ingreso
                </h5>
                <p class="mt-1 text-sm text-cyan-100/60">
                  Modifica la información del movimiento seleccionado.
                </p>
              </div>

              <button type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal"
                aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-5" id="modal2">
              <!-- Aquí se cargará el formulario de editarGI.php -->
            </div>

          </div>
        </div>
      </div>

    </main>

  </div>

  <!-- JS -->
  <script src="../js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../js/jquery-3.7.1.min.js"></script>
  <script src="../js/moment.min.js"></script>
  <script src="../js/sweetalert2@11.js"></script>

  <!-- Ya no usamos DataTables -->
  <!-- <script src="../js/dataTables.min.js"></script> -->

  <script src="../js/bootstrapval.js"></script>
  <script src="../js/booststraptoogletips.js"></script>
  <script src="../js/table.js"></script>
  <script src="../js/sidebar.js"></script>

  <script>
    function limpiarFiltrosTabla() {
      const fecha = document.getElementById('fechaa');
      const buscar = document.getElementById('buscar');

      if (fecha) {
        fecha.value = moment().format('YYYY-MM');
      }

      if (buscar) {
        buscar.value = '';
      }

      if (typeof cargarGastos === 'function') {
        cargarGastos();
      }
    }
  </script>

</body>

</html>