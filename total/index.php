<!Doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Corte BBS</title>
  <link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="../css/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/generales.css">
  <link rel="stylesheet" href="../css/total.css">
  <link rel="stylesheet" href="../css/navbar.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
  <div class="container-fluid main">
    <?php
    include_once '../includes/sidebar.php';
    ?>
    <div class="row">
      <div class="col-12 centrar txt1">
        <span>Corte BBS Networks</span>
      </div>
      <div class="col-12 row filtros">
        <div class="col-12 col-lg-6">
          <label for="inputCity" class="form-label">Fecha:</label>
          <input type="month" name="fecha" id="fecha" class="form-control" value="">
        </div>
        <div class="col-12 col-lg-6">
          <label for="inputCity" class="form-label">A nombre de:</label>
          <select id="cuenta" class="form-select">
            <option value="todos" selected>Todos</option>
            <option value="NOC1">NOC1</option>
            <option value="NOC2">NOC2</option>
          </select>
        </div>
        <div class="col-12 col-lg-6 d-none">
          <label for="inputCity" class="form-label">Usuario:</label>
          <select id="inputState" class="form-select">
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
        <div class="col-12 col-lg-6 p-2">
          <label for="inputCity" class="form-label">Tipo:</label>
          <select id="tipo" class="form-select">
            <option value="todos" selected>Todos</option>
            <option value="gasto">Gasto</option>
            <option value="ingreso">Ingreso</option>
            <option value="ibanco">Ingreso Banco</option>
            <option value="gbanco">Gasto Banco</option>
          </select>
        </div>
        <div class="col-12 col-lg-6 row centrar p-2">
        <label for="buscar" class="block text-sm font-medium mb-1">Buscar:</label>
        <input type="text" id="buscar" placeholder="Buscar..."
          class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-500 text-white">
        </div>    
        <div class="col-6 row centrar p-4">
          <button id="btnExportExcel" type="button" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500 w-full md:w-2/4">
            <i class="fas fa-file-excel"></i> Exportar a Excel
          </button>
        </div>

      </div>
      <div class="overflow-x-auto flex lg:justify-center">
        
  <table class="w-5/6 border-collapse mt-8">
    <thead class="bg-gray-800 text-white">
      <tr>
        <th class="p-3 text-left">ID</th>
        <th class="p-3 text-left">Nombre</th>
        <th class="p-3 text-left">Costo</th>
        <th class="p-3 text-left">Descripción</th>
        <th class="p-3 text-left">Fecha</th>
        <th class="p-3 text-left">A nombre de</th>
        <th class="p-3 text-left">Tipo</th>
        <th class="p-3 text-left">Usuario</th>
      </tr>
    </thead>
    <tbody id="cuerpo-table" class="divide-y divide-gray-700"></tbody>
  </table>
</div>

<div id="pagination" class="flex justify-center mt-6 gap-2"></div>

<div class="mt-8 ml-8 text-left text-2xl">
  <span class="font-bold">Total: </span>
  <span class="font-bold" id="total-corte"></span>
</div>

      <div class="row respuesta" id="respuesta">
        <div class="col-12 centrar txt-lista">
          <span>Lista de Gastos</span>
        </div>
        <div class="row col-lg-6 noc1 centrar">
          <div>
            <div class="col-12 txt-name centrar">
              <span>NOC1</span>
            </div>
            <!-- <div class="col-12 total centrar">
              <span id="total-noc1">$30,885.62</span>
            </div> -->
            <div class="row col-12 datos">
              <div class="col-12 col-md-6">
                <div class="col-12 label-e centrar">
                  <span>Total de Ingresos Sr. Ester</span>
                </div>
                <div class="col-12 monto-e centrar">
                  <span id="ingreso-e"></span>
                </div>
                <div class="col-12 label-e centrar">
                  <span>Total de Gastos Sr. Ester</span>
                </div>
                <div class="col-12 monto-e centrar">
                  <span class="text-danger" id="gastos-e"></span>
                </div>
                <div class="col-12 txt-total-e centrar">
                  <span class="text-success">Diferencia</span>
                </div>
                <div class="col-12 total-e centrar">
                  <span id="pago-e"></span>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="col-12 label-e centrar">
                  <span>Total de Ingresos BBS</span>
                </div>
                <div class="col-12 monto-e centrar">
                  <span id="ingreso-b"></span>
                </div>
                <div class="col-12 label-e centrar">
                  <span>Total de Gastos BBS</span>
                </div>
                <div class="col-12 monto-e centrar">
                  <span class="text-danger" id="gastos-b"></span>
                </div>
                <div class="col-12 txt-total-e centrar">
                  <span class="text-success">Diferencia</span>
                </div>
                <div class="col-12 total-e centrar">
                  <span id="pago-b"></span>
                </div>
              </div>
            </div>
            <div class="col-12 centrar txt-ganancias">
              <span class="text-success" id="">Total de Ganancias</span>
            </div>
            <div class="col-12 centrar total-ganancias">
              <span id="total-noc1"></span>
            </div>
            <div class="col-12 row">
              <div class="col-12 centrar label-ganancia">
                <span>Ganancia Sr Ester 20%</span>
              </div>
              <div class="col-12 centrar qty-ganancia">
                <span id="qty-e"></span>
              </div>
              <div class="col-12 centrar label-ganancia">
                <span>Ganancia BBS Networks 80%</span>
              </div>
              <div class="col-12 centrar qty-ganancia">
                <span id="qty-b"></span>
              </div>
              <div class="col-12 centrar txt-ganancias">
                <span class="text-success" id="">Total a Entregar</span>
              </div>
              <div class="col-12 centrar total-ganancias">
                <span id="entrega"></span>
              </div>
            </div>
          </div>
        </div>
        <div class="row col-lg-6 noc1 centrar">
          <div>
            <div class="col-12 txt-name centrar">
              <span>NOC2</span>
            </div>
            <!-- <div class="col-12 total centrar">
              <span id="total-noc1">$30,885.62</span>
            </div> -->
            <div class="row col-12 datos">
              
              <div class="col-12">
                <div class="col-12 label-e-bbs centrar">
                  <span>Total de Ingresos BBS</span>
                </div>
                <div class="col-12 monto-e-bbs centrar">
                  <span id="ingreso-b-bbs"></span>
                </div>
                <div class="col-12 label-e-bbs centrar">
                  <span>Total de Gastos BBS</span>
                </div>
                <div class="col-12 monto-e-bbs centrar">
                  <span class="text-danger" id="gastos-b-bbs"></span>
                </div>
                <div class="col-12 txt-total-e-bbs centrar">
                  <span class="text-success">Diferencia</span>
                </div>
                <div class="col-12 total-e-bbs centrar">
                  <span id="pago-b-bbs"></span>
                </div>
              </div>
            </div>
            <!-- <div class="col-12 centrar txt-ganancias">
              <span class="text-success" id="">Total de Ganancias</span>
            </div>
            <div class="col-12 centrar total-ganancias">
              <span id="total-noc2"></span>
            </div> -->
            <div class="col-12 row">
              <!-- <div class="col-12 centrar label-ganancia">
                <span>Ganancia Sr Ester 20%</span>
              </div>
              <div class="col-12 centrar qty-ganancia">
                <span id="qty-e"></span>
              </div> -->
              <!-- <div class="col-12 centrar label-ganancia">
                <span>Ganancia BBS Networks</span>
              </div>
              <div class="col-12 centrar qty-ganancia">
                <span id="qty-b-bbs"></span>
              </div>
              <div class="col-12 centrar txt-ganancias">
                <span class="text-success" id="">Total a Entregar</span>
              </div>
              <div class="col-12 centrar total-ganancias">
                <span id="entrega-bbs"></span>
              </div> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->
  <script src="../js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../js/jquery-3.7.1.min.js"></script>
  <script src="../js/moment.min.js"></script>
  <script src="../js/sweetalert2@11.js"></script>
  <script src="../js/dataTables.min.js"></script>
  <script src="../js/bootstrapval.js"></script>
  <script src="../js/booststraptoogletips.js"></script>
  <script src="../js/total.js"></script>
  <script src="../js/sidebar.js"></script>

  <!-- DataTables Buttons extension and other dependencies -->
<script src="../js/dataTables.buttons.min.js"></script>
<script src="../js/jszip.min.js"></script>
<script src="../js/pdfmake.min.js"></script>
<script src="../js/vfs_fonts.js"></script>
<script src="../js/buttons.html5.min.js"></script>
<script src="../js/buttons.print.min.js"></script>


</body>

</html>