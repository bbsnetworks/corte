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
  <title>Corte BBS | Total</title>

  <link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="../css/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/generales.css">
  <link rel="stylesheet" href="../css/total.css">
  <link rel="stylesheet" href="../css/navbar.css">

  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" />
</head>

<body class="min-h-screen bg-[#071322] text-white overflow-x-hidden">

  <div class="flex min-h-screen">

    <?php include_once '../includes/sidebar.php'; ?>

    <main class="flex-1 p-4 sm:p-6 lg:p-8 w-full">

      <!-- Encabezado principal -->
      <section class="mb-8 rounded-[28px] border border-cyan-400/20 bg-[#061b3b]/80 shadow-2xl shadow-blue-950/40 overflow-hidden">
        <div class="relative p-6 sm:p-8">

          <div class="absolute -top-24 -right-24 w-72 h-72 bg-cyan-500/10 rounded-full blur-3xl"></div>
          <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-blue-600/10 rounded-full blur-3xl"></div>

          <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

            <div class="flex items-start gap-4">
              <div class="w-14 h-14 rounded-2xl bg-cyan-400/10 border border-cyan-300/20 flex items-center justify-center shadow-lg shadow-cyan-950/40">
                <i class="fa-solid fa-chart-pie text-cyan-300 text-2xl"></i>
              </div>

              <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">
                  Corte BBS Networks
                </h1>
                <p class="mt-2 text-sm sm:text-base text-cyan-100/80">
                  Consulta ingresos, gastos, movimientos bancarios y distribución de ganancias.
                </p>
              </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
              <button id="btnExportExcel" type="button"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-500/15 border border-emerald-300/20 px-6 py-3 font-bold text-emerald-200 hover:bg-emerald-500/25 transition">
                <i class="fas fa-file-excel"></i>
                Exportar a Excel
              </button>
            </div>

          </div>
        </div>
      </section>

      <!-- Filtros -->
      <section class="mb-8 rounded-[28px] border border-blue-400/20 bg-[#06152d]/90 shadow-2xl shadow-black/30 p-4 sm:p-6 lg:p-7">

        <div class="mb-6 rounded-2xl border border-cyan-400/20 bg-[#062a52]/70 px-5 py-4">
          <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fa-solid fa-filter text-cyan-300"></i>
            Filtros del corte
          </h2>
          <p class="mt-1 text-sm text-cyan-100/75">
            Filtra los movimientos por fecha, cuenta, tipo o búsqueda general.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

          <!-- Fecha -->
          <div>
            <label for="fecha" class="block text-sm font-semibold mb-2">
              Fecha
            </label>

            <div class="relative">
              <i class="fa-solid fa-calendar-days absolute left-4 top-1/2 -translate-y-1/2 text-cyan-200/50"></i>
              <input type="month" name="fecha" id="fecha" value=""
                class="w-full rounded-2xl bg-[#101b31] border border-white/15 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 outline-none pl-11 pr-4 py-3 text-white placeholder:text-blue-200/60 transition">
            </div>
          </div>

          <!-- Cuenta -->
          <div>
            <label for="cuenta" class="block text-sm font-semibold mb-2">
              A nombre de
            </label>

            <div class="relative">
              <i class="fa-solid fa-user-tie absolute left-4 top-1/2 -translate-y-1/2 text-cyan-200/50"></i>
              <select id="cuenta"
                class="w-full rounded-2xl bg-[#101b31] border border-white/15 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 outline-none pl-11 pr-10 py-3 text-white transition appearance-none">
                <option value="todos" selected>Todos</option>
                <option value="NOC1">NOC1</option>
                <option value="NOC2">NOC2</option>
              </select>
              <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-cyan-200/50 pointer-events-none"></i>
            </div>
          </div>

          <!-- Usuario oculto -->
          <div class="hidden">
            <label for="inputState" class="block text-sm font-semibold mb-2">
              Usuario
            </label>

            <select id="inputState"
              class="w-full rounded-2xl bg-[#101b31] border border-white/15 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 outline-none px-4 py-3 text-white transition">
              <option selected>Todos</option>
              <?php
              include '../php/conexion.php';
              if ($conexion->connect_error) {
                die("Conexión fallida: " . $conexion->connect_error);
              }

              $sql = "select nombre from users";
              $result = $conexion->query($sql);
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  echo "<option value='" . $row['nombre'] . "'>" . $row['nombre'] . "</option>";
                }
              }
              ?>
            </select>
          </div>

          <!-- Tipo -->
          <div>
            <label for="tipo" class="block text-sm font-semibold mb-2">
              Tipo
            </label>

            <div class="relative">
              <i class="fa-solid fa-arrow-right-arrow-left absolute left-4 top-1/2 -translate-y-1/2 text-cyan-200/50"></i>
              <select id="tipo"
                class="w-full rounded-2xl bg-[#101b31] border border-white/15 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 outline-none pl-11 pr-10 py-3 text-white transition appearance-none">
                <option value="todos" selected>Todos</option>
                <option value="gasto">Gasto</option>
                <option value="ingreso">Ingreso</option>
                <option value="ibanco">Ingreso Banco</option>
                <option value="gbanco">Gasto Banco</option>
              </select>
              <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-cyan-200/50 pointer-events-none"></i>
            </div>
          </div>

          <!-- Buscar -->
          <div>
            <label for="buscar" class="block text-sm font-semibold mb-2">
              Buscar
            </label>

            <div class="relative">
              <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-cyan-200/50"></i>
              <input type="text" id="buscar" placeholder="Buscar..."
                class="w-full rounded-2xl bg-[#101b31] border border-white/15 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 outline-none pl-11 pr-4 py-3 text-white placeholder:text-blue-200/60 transition">
            </div>
          </div>

        </div>

      </section>

      <!-- Total general -->
      <section class="mb-8 rounded-[28px] border border-cyan-400/20 bg-gradient-to-br from-[#062a52]/90 to-[#06152d]/95 shadow-2xl shadow-black/30 p-6">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-cyan-400/10 border border-cyan-300/20 flex items-center justify-center">
              <i class="fa-solid fa-sack-dollar text-cyan-300 text-2xl"></i>
            </div>

            <div>
              <p class="text-sm text-cyan-100/65 font-semibold uppercase tracking-[0.18em]">
                Resultado del corte filtrado
              </p>
              <h2 class="text-2xl font-black text-white">
                Total general
              </h2>
            </div>
          </div>

          <div class="rounded-3xl border border-cyan-300/20 bg-[#071322]/80 px-6 py-4 text-right">
            <p class="text-sm text-cyan-100/60">
              Total
            </p>
            <span class="block text-3xl sm:text-4xl font-black text-cyan-200" id="total-corte"></span>
          </div>

        </div>

      </section>

      <!-- Tabla -->
      <section class="mb-8 rounded-[28px] border border-blue-400/20 bg-[#06152d]/90 shadow-2xl shadow-black/30 p-4 sm:p-6 lg:p-7">

        <div class="mb-6 rounded-2xl border border-cyan-400/20 bg-[#062a52]/70 px-5 py-4">
          <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fa-solid fa-table-list text-cyan-300"></i>
            Lista de movimientos
          </h2>
          <p class="mt-1 text-sm text-cyan-100/75">
            Detalle de gastos e ingresos encontrados con los filtros seleccionados.
          </p>
        </div>

        <div class="overflow-hidden rounded-[24px] border border-cyan-400/20 bg-[#071322]/90 shadow-2xl shadow-black/30">

          <div class="overflow-x-auto">
            <table class="w-full min-w-[1050px] border-separate border-spacing-0">
              <thead class="bg-[#071b36]/95">
                <tr class="border-b border-cyan-400/20">
                  <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-[0.18em] text-cyan-100/70">ID</th>
                  <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-[0.18em] text-cyan-100/70">Nombre</th>
                  <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-[0.18em] text-cyan-100/70">Costo</th>
                  <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-[0.18em] text-cyan-100/70">Descripción</th>
                  <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-[0.18em] text-cyan-100/70">Fecha</th>
                  <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-[0.18em] text-cyan-100/70">A nombre de</th>
                  <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-[0.18em] text-cyan-100/70">Tipo</th>
                  <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-[0.18em] text-cyan-100/70">Usuario</th>
                </tr>
              </thead>

              <tbody id="cuerpo-table" class="divide-y divide-cyan-400/10"></tbody>
            </table>
          </div>

        </div>

        <div id="pagination" class="flex flex-wrap justify-center mt-6 gap-2"></div>

      </section>

      <!-- Cards resumen -->
      <section class="rounded-[28px] border border-blue-400/20 bg-[#06152d]/90 shadow-2xl shadow-black/30 p-4 sm:p-6 lg:p-7 respuesta" id="respuesta">

        <div class="mb-6 rounded-2xl border border-cyan-400/20 bg-[#062a52]/70 px-5 py-4">
          <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fa-solid fa-wallet text-cyan-300"></i>
            Resumen del corte
          </h2>
          <p class="mt-1 text-sm text-cyan-100/75">
            Totales separados por NOC, efectivo, banco, diferencias y distribución de ganancias.
          </p>
        </div>

        <div class="grid grid-cols-1 2xl:grid-cols-2 gap-6">

          <!-- NOC1 -->
          <article class="rounded-[28px] border border-cyan-400/20 bg-gradient-to-br from-[#071b36] to-[#06152d] shadow-2xl shadow-black/30 overflow-hidden">

            <div class="px-6 py-5 border-b border-cyan-400/15 bg-cyan-400/5">
              <div class="flex items-center justify-between gap-4">
                <div>
                  <p class="text-sm text-cyan-100/60 font-semibold uppercase tracking-[0.18em] mb-4">
                    Cuenta principal
                  </p>
                  <h3 class="text-2xl font-black text-white txt-name">
                    <span>NOC1</span>
                  </h3>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-cyan-400/10 border border-cyan-300/20 flex items-center justify-center">
                  <i class="fa-solid fa-network-wired text-cyan-300 text-2xl"></i>
                </div>
              </div>
            </div>

            <div class="p-6 space-y-6">

              <!-- Dos columnas -->
              <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 datos">

                <!-- Sr Ester -->
                <div class="rounded-3xl border border-white/10 bg-[#071322]/70 p-5 noc-col">

                  <div class="mb-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-300/20 flex items-center justify-center">
                      <i class="fa-solid fa-user text-emerald-300"></i>
                    </div>
                    <div>
                      <p class="text-sm text-cyan-100/50">Resumen</p>
                      <h4 class="font-black text-white">Sr. Ester</h4>
                    </div>
                  </div>

                  <div class="space-y-4">

                    <div class="rounded-2xl bg-emerald-500/5 border border-emerald-300/15 p-4">
                      <p class="text-sm text-emerald-100/60 label-e">Total de Ingresos Sr. Ester</p>
                      <span class="block mt-1 text-2xl font-black text-emerald-300 monto-e" id="ingreso-e"></span>
                    </div>

                    <div class="rounded-2xl bg-red-500/5 border border-red-300/15 p-4">
                      <p class="text-sm text-red-100/60 label-e">Total de Gastos Sr. Ester</p>
                      <span class="block mt-1 text-2xl font-black text-red-300 monto-e" id="gastos-e"></span>
                    </div>

                    <div class="rounded-2xl bg-cyan-500/5 border border-cyan-300/15 p-4">
                      <p class="text-sm text-cyan-100/60 txt-total-e">Diferencia</p>
                      <span class="block mt-1 text-2xl font-black text-cyan-200 total-e" id="pago-e"></span>
                    </div>

                  </div>

                </div>

                <!-- BBS NOC1 -->
                <div class="rounded-3xl border border-white/10 bg-[#071322]/70 p-5 noc-col">

                  <div class="mb-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-300/20 flex items-center justify-center">
                      <i class="fa-solid fa-building text-blue-300"></i>
                    </div>
                    <div>
                      <p class="text-sm text-cyan-100/50">Resumen</p>
                      <h4 class="font-black text-white">BBS Networks</h4>
                    </div>
                  </div>

                  <div class="space-y-4">

                    <div class="rounded-2xl bg-emerald-500/5 border border-emerald-300/15 p-4">
                      <p class="text-sm text-emerald-100/60 label-e">Total de Ingresos BBS</p>
                      <span class="block mt-1 text-2xl font-black text-emerald-300 monto-e" id="ingreso-b"></span>
                    </div>

                    <div class="rounded-2xl bg-red-500/5 border border-red-300/15 p-4">
                      <p class="text-sm text-red-100/60 label-e">Total de Gastos BBS</p>
                      <span class="block mt-1 text-2xl font-black text-red-300 monto-e" id="gastos-b"></span>
                    </div>

                    <div class="h-px bg-cyan-400/15 noc-subdivider"></div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <div class="rounded-2xl bg-cyan-500/5 border border-cyan-300/15 p-4">
                        <p class="text-sm text-cyan-100/60 label-e">Ingreso banco</p>
                        <span class="block mt-1 text-xl font-black text-cyan-200 monto-e" id="ingreso-banco-noc1"></span>
                      </div>

                      <div class="rounded-2xl bg-orange-500/5 border border-orange-300/15 p-4">
                        <p class="text-sm text-orange-100/60 label-e">Gasto banco</p>
                        <span class="block mt-1 text-xl font-black text-orange-300 monto-e" id="gasto-banco-noc1"></span>
                      </div>
                    </div>

                    <div class="rounded-2xl bg-cyan-500/5 border border-cyan-300/15 p-4">
                      <p class="text-sm text-cyan-100/60 txt-total-e">Diferencia</p>
                      <span class="block mt-1 text-2xl font-black text-cyan-200 total-e" id="pago-b"></span>
                    </div>

                  </div>

                </div>

              </div>

              <!-- Ganancias -->
              <div class="rounded-[24px] border border-emerald-300/20 bg-emerald-500/5 p-5">

                <div class="mb-5 flex items-center justify-between gap-4">
                  <div>
                    <p class="text-sm text-emerald-100/60 txt-ganancias">Total de Ganancias</p>
                    <span class="block mt-1 text-3xl font-black text-emerald-300 total-ganancias" id="total-noc1"></span>
                  </div>

                  <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-300/20 flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-emerald-300 text-xl"></i>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                  <div class="rounded-2xl border border-white/10 bg-[#071322]/70 p-4">
                    <p class="text-sm text-cyan-100/60 label-ganancia">Ganancia Sr Ester 20%</p>
                    <span class="block mt-1 text-xl font-black text-white qty-ganancia" id="qty-e"></span>
                  </div>

                  <div class="rounded-2xl border border-white/10 bg-[#071322]/70 p-4">
                    <p class="text-sm text-cyan-100/60 label-ganancia">Ganancia BBS Networks 80%</p>
                    <span class="block mt-1 text-xl font-black text-white qty-ganancia" id="qty-b"></span>
                  </div>

                  <div class="rounded-2xl border border-cyan-300/20 bg-cyan-500/10 p-4">
                    <p class="text-sm text-cyan-100/60 txt-ganancias">Total a Entregar</p>
                    <span class="block mt-1 text-xl font-black text-cyan-200 total-ganancias" id="entrega"></span>
                  </div>

                </div>

              </div>

            </div>

          </article>

          <!-- NOC2 -->
          <article class="rounded-[28px] border border-blue-400/20 bg-gradient-to-br from-[#071b36] to-[#06152d] shadow-2xl shadow-black/30 overflow-hidden">

            <div class="px-6 py-5 border-b border-blue-400/15 bg-blue-400/5">
              <div class="flex items-center justify-between gap-4">
                <div>
                  <p class="text-sm text-cyan-100/60 font-semibold uppercase tracking-[0.18em] mb-4">
                    Cuenta secundaria
                  </p>
                  <h3 class="text-2xl font-black text-white txt-name">
                    <span>NOC2</span>
                  </h3>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-blue-400/10 border border-blue-300/20 flex items-center justify-center">
                  <i class="fa-solid fa-server text-blue-300 text-2xl"></i>
                </div>
              </div>
            </div>

            <div class="p-6 datos">

              <div class="rounded-3xl border border-white/10 bg-[#071322]/70 p-5">

                <div class="mb-4 flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-300/20 flex items-center justify-center">
                    <i class="fa-solid fa-building text-blue-300"></i>
                  </div>
                  <div>
                    <p class="text-sm text-cyan-100/50">Resumen</p>
                    <h4 class="font-black text-white">BBS Networks</h4>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                  <div class="rounded-2xl bg-emerald-500/5 border border-emerald-300/15 p-4">
                    <p class="text-sm text-emerald-100/60 label-e-bbs">Total de Ingresos BBS</p>
                    <span class="block mt-1 text-2xl font-black text-emerald-300 monto-e-bbs" id="ingreso-b-bbs"></span>
                  </div>

                  <div class="rounded-2xl bg-red-500/5 border border-red-300/15 p-4">
                    <p class="text-sm text-red-100/60 label-e-bbs">Total de Gastos BBS</p>
                    <span class="block mt-1 text-2xl font-black text-red-300 monto-e-bbs" id="gastos-b-bbs"></span>
                  </div>

                  <div class="rounded-2xl bg-cyan-500/5 border border-cyan-300/15 p-4">
                    <p class="text-sm text-cyan-100/60 label-e-bbs">Ingreso banco</p>
                    <span class="block mt-1 text-2xl font-black text-cyan-200 monto-e-bbs" id="ingreso-banco-noc2"></span>
                  </div>

                  <div class="rounded-2xl bg-orange-500/5 border border-orange-300/15 p-4">
                    <p class="text-sm text-orange-100/60 label-e-bbs">Gasto banco</p>
                    <span class="block mt-1 text-2xl font-black text-orange-300 monto-e-bbs" id="gasto-banco-noc2"></span>
                  </div>

                </div>

                <div class="mt-5 rounded-2xl bg-blue-500/5 border border-blue-300/15 p-5">
                  <p class="text-sm text-blue-100/60 txt-total-e-bbs">Diferencia</p>
                  <span class="block mt-1 text-3xl font-black text-blue-200 total-e-bbs" id="pago-b-bbs"></span>
                </div>

              </div>

            </div>

          </article>

        </div>

      </section>

    </main>

  </div>

  <script src="../js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../js/jquery-3.7.1.min.js"></script>
  <script src="../js/moment.min.js"></script>
  <script src="../js/sweetalert2@11.js"></script>

  <!-- Si total.js todavía usa DataTables, déjalo por ahora.
       Cuando revisemos total.js, podemos quitarlo si ya no se usa. -->
  <script src="../js/dataTables.min.js"></script>

  <script src="../js/bootstrapval.js"></script>
  <script src="../js/booststraptoogletips.js"></script>
  <script src="../js/total.js"></script>
  <script src="../js/sidebar.js"></script>

  <script src="../js/dataTables.buttons.min.js"></script>
  <script src="../js/jszip.min.js"></script>
  <script src="../js/pdfmake.min.js"></script>
  <script src="../js/vfs_fonts.js"></script>
  <script src="../js/buttons.html5.min.js"></script>
  <script src="../js/buttons.print.min.js"></script>
</body>

</html>