<?php
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: ../../menu/login/index.php");
  exit();
}
?>

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

<body class="min-h-screen bg-[#071322] text-white overflow-x-hidden">

  <div class="flex min-h-screen">

    <?php include_once '../includes/sidebar.php'; ?>

    <main class="flex-1 p-4 sm:p-6 lg:p-8">

      <!-- Encabezado principal -->
      <section
        class="mb-8 rounded-[28px] border border-cyan-400/20 bg-[#061b3b]/80 shadow-2xl shadow-blue-950/40 overflow-hidden">
        <div class="relative p-6 sm:p-8">

          <!-- Brillos decorativos -->
          <div class="absolute -top-24 -right-24 w-72 h-72 bg-cyan-500/10 rounded-full blur-3xl"></div>
          <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-blue-600/10 rounded-full blur-3xl"></div>

          <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

            <div class="flex items-start gap-4">
              <div
                class="w-14 h-14 rounded-2xl bg-cyan-400/10 border border-cyan-300/20 flex items-center justify-center shadow-lg shadow-cyan-950/40">
                <i class="fa-solid fa-receipt text-cyan-300 text-2xl"></i>
              </div>

              <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">
                  Agregar Gasto / Ingreso
                </h1>
                <p class="mt-2 text-sm sm:text-base text-cyan-100/80">
                  Registra movimientos de efectivo, banco, ingresos y gastos del corte.
                </p>
              </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
              <button type="button" onclick="limpiarFormularioGasto()"
                class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-5 py-3 font-semibold text-white hover:bg-white/10 transition">
                <i class="fa-solid fa-rotate-left"></i>
                Limpiar formulario
              </button>

              <button type="submit" form="uploadForm" data-guardar-movimiento
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 px-6 py-3 font-bold text-white shadow-lg shadow-blue-900/40 hover:from-cyan-400 hover:to-blue-500 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fa-solid fa-check"></i>
                <span>Ingresar</span>
              </button>
            </div>

          </div>
        </div>
      </section>

      <!-- Contenedor del formulario -->
      <section
        class="rounded-[28px] border border-blue-400/20 bg-[#06152d]/90 shadow-2xl shadow-black/30 p-4 sm:p-6 lg:p-7">

        <!-- Título de sección -->
        <div class="mb-6 rounded-2xl border border-cyan-400/20 bg-[#062a52]/70 px-5 py-4">
          <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fa-solid fa-file-invoice-dollar text-cyan-300"></i>
            Datos del movimiento
          </h2>
          <p class="mt-1 text-sm text-cyan-100/75">
            Captura la información principal del gasto o ingreso.
          </p>
        </div>

        <form id="uploadForm" enctype="multipart/form-data" class="space-y-7">

          <!-- Grid principal -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <!-- Título -->
            <div class="lg:col-span-2">
              <label for="titulo" class="block text-sm font-semibold mb-2">
                Título <span class="text-cyan-300">*</span>
              </label>
              <div class="relative">
                <i class="fa-solid fa-pen absolute left-4 top-1/2 -translate-y-1/2 text-cyan-200/50"></i>
                <input type="text" id="titulo" name="titulo" required
                  placeholder="Ej. Pago de combustible, compra de material..."
                  class="w-full rounded-2xl bg-[#101b31] border border-white/15 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 outline-none pl-11 pr-4 py-3 text-white placeholder:text-blue-200/60 transition">
              </div>
            </div>

            <!-- Costo -->
            <div>
              <label for="costo" class="block text-sm font-semibold mb-2">
                Costo / Monto <span class="text-cyan-300">*</span>
              </label>
              <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-cyan-200/70 font-bold">$</span>
                <input type="number" id="costo" name="costo" value="1" min="1" pattern="^[0-9]+" required
                  class="w-full rounded-2xl bg-[#101b31] border border-white/15 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 outline-none pl-9 pr-4 py-3 text-white placeholder:text-blue-200/60 transition">
              </div>
            </div>

            <!-- Descripción -->
            <div class="lg:col-span-3">
              <label for="descripcion" class="block text-sm font-semibold mb-2">
                Descripción <span class="text-cyan-300">*</span>
              </label>
              <textarea id="descripcion" name="descripcion" rows="4" required
                placeholder="Agrega una descripción o comentario del movimiento..."
                class="w-full rounded-2xl bg-[#101b31] border border-white/15 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 outline-none px-4 py-3 text-white placeholder:text-blue-200/60 transition resize-none"></textarea>
            </div>

            <!-- Fecha -->
            <div>
              <label for="fecha" class="block text-sm font-semibold mb-2">
                Fecha <span class="text-cyan-300">*</span>
              </label>
              <div class="relative">
                <i class="fa-solid fa-calendar-days absolute left-4 top-1/2 -translate-y-1/2 text-cyan-200/50"></i>
                <input type="date" id="fecha" name="fecha" required
                  class="w-full rounded-2xl bg-[#101b31] border border-white/15 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 outline-none pl-11 pr-4 py-3 text-white transition">
              </div>
            </div>

            <!-- Tipo -->
            <div>
              <label for="tipo" class="block text-sm font-semibold mb-2">
                Tipo de movimiento
              </label>
              <div class="relative">
                <i
                  class="fa-solid fa-arrow-right-arrow-left absolute left-4 top-1/2 -translate-y-1/2 text-cyan-200/50"></i>
                <select id="tipo" name="tipo"
                  class="w-full rounded-2xl bg-[#101b31] border border-white/15 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 outline-none pl-11 pr-4 py-3 text-white transition appearance-none">
                  <option value="1">Gasto</option>
                  <option value="2">Ingreso</option>
                  <option value="3">Banco Ingreso</option>
                  <option value="4">Banco Gasto</option>
                </select>
                <i
                  class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-cyan-200/50 pointer-events-none"></i>
              </div>
            </div>

            <!-- A nombre de -->
            <div>
              <label for="nombrede" class="block text-sm font-semibold mb-2">
                A nombre de
              </label>
              <div class="relative">
                <i class="fa-solid fa-user-tie absolute left-4 top-1/2 -translate-y-1/2 text-cyan-200/50"></i>
                <select id="nombrede" name="nombrede"
                  class="w-full rounded-2xl bg-[#101b31] border border-white/15 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 outline-none pl-11 pr-4 py-3 text-white transition appearance-none">
                  <option value="1">NOC1</option>
                  <?php if ($_SESSION['tipo'] == 'root') { ?>
                    <option value="2">NOC2</option>
                  <?php } ?>
                </select>
                <i
                  class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-cyan-200/50 pointer-events-none"></i>
              </div>
            </div>

          </div>

          <!-- Segunda sección -->
          <div class="rounded-[24px] border border-cyan-400/15 bg-[#071b36]/80 p-5">

            <div class="mb-5">
              <h3 class="text-lg font-bold flex items-center gap-2">
                <i class="fa-solid fa-paperclip text-cyan-300"></i>
                Evidencia y usuario
              </h3>
              <p class="mt-1 text-sm text-cyan-100/70">
                Puedes adjuntar una imagen, comprobante o archivo relacionado al movimiento.
              </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

              <!-- Evidencia -->
              <div>
                <label for="file" class="block text-sm font-semibold mb-2">
                  Evidencia
                </label>

                <div class="space-y-4">

                  <!-- Área para seleccionar archivo -->
                  <label for="file" id="file-drop-area"
                    class="flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-cyan-300/30 bg-[#101b31] px-4 py-8 text-center cursor-pointer hover:bg-[#12213c] transition">

                    <div
                      class="w-12 h-12 rounded-2xl bg-cyan-400/10 border border-cyan-300/20 flex items-center justify-center">
                      <i class="fa-solid fa-cloud-arrow-up text-cyan-300 text-xl"></i>
                    </div>

                    <div>
                      <p class="font-semibold text-white">Seleccionar archivo</p>
                      <p class="text-sm text-cyan-100/60">Comprobante, imagen o evidencia</p>
                    </div>

                    <input type="file" id="file" name="file" class="hidden" accept="image/*,.pdf">
                  </label>

                  <!-- Vista previa del archivo -->
                  <div id="file-preview" class="hidden rounded-2xl border border-cyan-400/20 bg-[#101b31] p-4">

                    <div class="flex flex-col sm:flex-row gap-4">

                      <!-- Preview imagen / icono -->
                      <div id="preview-visual"
                        class="w-full sm:w-32 h-32 rounded-xl border border-white/10 bg-[#071322] flex items-center justify-center overflow-hidden">
                      </div>

                      <!-- Datos del archivo -->
                      <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                          <div class="min-w-0">
                            <p class="text-sm text-cyan-100/60">Archivo seleccionado</p>
                            <h4 id="preview-name" class="font-bold text-white truncate"></h4>
                            <p id="preview-info" class="mt-1 text-sm text-cyan-100/70"></p>
                          </div>

                          <button type="button" id="remove-file"
                            class="shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl bg-red-500/10 border border-red-400/20 text-red-300 hover:bg-red-500/20 transition"
                            title="Eliminar archivo">
                            <i class="fa-solid fa-trash"></i>
                          </button>
                        </div>

                        <p class="mt-3 text-xs text-cyan-100/50">
                          Si seleccionaste el archivo incorrecto, puedes eliminarlo y subir otro.
                        </p>
                      </div>

                    </div>
                  </div>

                </div>
              </div>

              <!-- Usuario -->
              <div>
                <label for="id-user" class="block text-sm font-semibold mb-2">
                  Usuario que registra
                </label>

                <div class="relative">
                  <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-cyan-200/50"></i>
                  <input type="text" id="id-user" name="id-user" value="<?php echo $_SESSION['username'] ?>" disabled
                    class="w-full rounded-2xl bg-[#0d1729] border border-white/10 pl-11 pr-4 py-3 text-blue-100/70 cursor-not-allowed">
                </div>

                <div id="div-banco" class="hidden mt-5 rounded-2xl border border-cyan-400/15 bg-cyan-400/5 px-4 py-4">
                  <label for="banco-check" class="flex items-center gap-3 cursor-pointer">
                    <input id="banco-check" type="checkbox"
                      class="h-5 w-5 rounded border-white/20 bg-[#101b31] text-cyan-500 focus:ring-cyan-400">
                    <span class="text-sm font-medium text-cyan-50">
                      Tomar a cuenta del efectivo
                    </span>
                  </label>
                </div>
              </div>

            </div>
          </div>

          <!-- Botones inferiores -->
          <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2">

            <button type="button" onclick="limpiarFormularioGasto()"
              class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-6 py-3 font-semibold text-white hover:bg-white/10 transition">
              <i class="fa-solid fa-broom"></i>
              Limpiar
            </button>

            <button type="submit" data-guardar-movimiento
              class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 px-8 py-3 font-bold text-white shadow-lg shadow-blue-950/50 hover:from-cyan-400 hover:to-blue-500 transition disabled:opacity-50 disabled:cursor-not-allowed">
              <i class="fa-solid fa-floppy-disk"></i>
              <span>Guardar movimiento</span>
            </button>

          </div>

        </form>

        <div id="message" class="mt-6"></div>

      </section>

    </main>
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