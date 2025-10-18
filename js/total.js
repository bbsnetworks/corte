// ========= Estado =========
let currentPage = 1;
const rowsPerPage = 10;
let ultimaRespuesta = { data: [], total_pages: 1, total_rows: 0 };
// ========= Carga inicial =========
let date = moment().format("YYYY-MM");
document.getElementById("fecha").value = date;
let mes = moment().format("MM");
let year = moment().format("YYYY");

cargarTabla(mes, year, "todos", "todos", "todos", currentPage);

// Mantengo tu listaGastos tal cual:
listaGastos(mes, year);

// ========= Utilidad: debounce =========
function debounce(fn, delay = 400) {
  let t;
  return (...args) => {
    clearTimeout(t);
    t = setTimeout(() => fn(...args), delay);
  };
}
// ========= Lectura de filtros auxiliares =========
function obtenerTipoSeleccionado() {
  const v = $("#tipo").val();
  if (v === "todos") return "todos";
  if (v === "gasto") return "1";
  if (v === "ingreso") return "2";
  if (v === "ibanco") return "3";
  if (v === "gbanco") return "4";
  return "todos";
}
function obtenerCuentaSeleccionada() {
  const v = $("#cuenta").val();
  if (v === "todos") return "todos";
  if (v === "NOC1") return "1";
  if (v === "NOC2") return "2";
  return "todos";
}
// ========= Core: cargarTabla con backend search/paginación =========
function cargarTabla(mes, year, nombrede, usuario, tipo, page = 1) {
  const search = ($("#buscar").val() || "").trim();

  const formData = new FormData();
  formData.append("mes", mes);
  formData.append("year", year);
  formData.append("nombrede", nombrede);
  formData.append("usuario", usuario);
  formData.append("tipo", tipo);
  formData.append("search", search);
  formData.append("page", page);
  formData.append("per_page", rowsPerPage);

  $.ajax({
    url: "../php/cargarTotal.php",
    data: formData,
    processData: false,
    contentType: false,
    type: "POST",
    dataType: "json",
    success: function (response) {
      ultimaRespuesta = response;

      // Totales (sobre la página actual; si quieres totales del período completo tendrás que calcularlos con otra query)
      const total = (response.ingreso || 0) - (response.gastos || 0);
      $("#total-corte")
        .text(`$${total.toFixed(2)}`)
        .css("color", total > 0 ? "green" : "red");

      renderTable(response.data || []);
      renderPagination(response.page || 1, response.total_pages || 1);
    }
  });
}
function listaGastos(mes, year) {
  var formData = new FormData();
  formData.append("mes", mes);
  formData.append("year", year);

  $.ajax({
    url: "../php/ganancias.php",
    data: formData,
    processData: false,
    contentType: false,
    type: "POST",
    dataType: "json",
    success: function (response) {
      //console.log(response);
      // Insertar el HTML de la tabla
      //$("#cuerpo-table").html(response.tableData);

      // Acceder a las variables gastos e ingreso
      var ingresoE = response.ingresoE;
      var gastoE = response.gastoE;
      var ingresoB = response.ingresoB;
      var gastoB = response.gastoB;
      var totalE = ingresoE - gastoE;  
      var totalB = ingresoB - gastoB;
      var ganancias = totalE + totalB;
      var qtyE = ganancias*0.20;
      var qtyB = ganancias*0.80;
      var entrega;
      if(totalB==0){
        entrega=0;
      }else{
        entrega=qtyB-totalB+22500;
      }
      //ingresos NOC1
      $("#ingreso-e").text("$" + response.ingresoE.toFixed(2));
      $("#gastos-e").text("$" + response.gastoE.toFixed(2));
      $("#pago-e").text("$" + totalE.toFixed(2));

      $("#ingreso-b").text("$" + response.ingresoB.toFixed(2));
      $("#gastos-b").text("$" + response.gastoB.toFixed(2));
      $("#pago-b").text("$" + totalB.toFixed(2));

      $("#total-noc1").text("$" + ganancias.toFixed(2));
      
      $("#qty-e").text("$" + qtyE.toFixed(2));
      $("#qty-b").text("$" + qtyB.toFixed(2));

      $("#entrega").text("$" + entrega.toFixed(2));

      //ingresos NOC2
     
      var ingresoBBS2 = response.ingresoBBS2;
      var gastoBBS2 = response.gastoBBS2; 
      var totalBBS2 = ingresoBBS2 - gastoBBS2;


      //var ganancias = totalE + totalB;
    
      //var entrega = qtyB-totalB+22500;

      $("#ingreso-b-bbs").text("$" + response.ingresoBBS2.toFixed(2));
      $("#gastos-b-bbs").text("$" + response.gastoBBS2.toFixed(2));
      $("#pago-b-bbs").text("$" + totalBBS2.toFixed(2));

      $("#total-noc2").text("$" + totalBBS2.toFixed(2));
      
    //   $("#qty-e").text("$" + qtyE.toFixed(2));
    //   $("#qty-b").text("$" + qtyB.toFixed(2));

      $("#entrega").text("$" + entrega.toFixed(2));
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log("Error:", textStatus, errorThrown);
    },
  });
}

$("#fecha").on("change", function () {
  // Obtener el valor del input
  var valorFecha = $("#fecha").val();
  var valorTipo;
  var valorCuenta="";
  //obtencion tipo
  if($("#tipo").val()=="todos"){
    valorTipo="todos";
    }else
  if($("#tipo").val()=="gasto"){
    valorTipo="1";
  }else if($("#tipo").val()=="ingreso"){
    valorTipo="2";
  }else if($("#tipo").val()=="ibanco"){
    valorTipo="3";
  }else if($("#tipo").val()=="gbanco"){
    valorTipo="4";
  }

  //obtencion cuenta
  if($("#cuenta").val()=="todos"){
    valorCuenta="todos";
    }else
  if($("#cuenta").val()=="NOC1"){
    valorCuenta="1";
  }else if($("#cuenta").val()=="NOC2"){
    valorCuenta="2";
  }
  // Separar el valor en año y mes
  var yearFecha = valorFecha.split("-")[0]; // Obtener el año
  var mesFecha = valorFecha.split("-")[1]; // Obtener el mes

  //console.log(valorTipo);

  // Llamada a la función listaGastos con los valores obtenidos
  listaGastos(mesFecha, yearFecha);
  cargarTabla(mesFecha, yearFecha, valorCuenta, "todos", valorTipo);
});
$("#cuenta").on("change", function () {
   // Obtener el valor del input
   var valorFecha = $("#fecha").val();
   var valorTipo;
   var valorCuenta="";
   //obtencion tipo
   if($("#tipo").val()=="todos"){
     valorTipo="todos";
     }else
   if($("#tipo").val()=="gasto"){
     valorTipo="1";
   }else if($("#tipo").val()=="ingreso"){
     valorTipo="2";
   }else if($("#tipo").val()=="ibanco"){
     valorTipo="3";
   }else if($("#tipo").val()=="gbanco"){
     valorTipo="4";
   }
 
   //obtencion cuenta
   if($("#cuenta").val()=="todos"){
     valorCuenta="todos";
     }else
   if($("#cuenta").val()=="NOC1"){
     valorCuenta="1";
   }else if($("#cuenta").val()=="NOC2"){
     valorCuenta="2";
   }
   // Separar el valor en año y mes
   var yearFecha = valorFecha.split("-")[0]; // Obtener el año
   var mesFecha = valorFecha.split("-")[1]; // Obtener el mes
 
   //console.log(valorTipo);
 
   // Llamada a la función listaGastos con los valores obtenidos
   listaGastos(mesFecha, yearFecha);
   cargarTabla(mesFecha, yearFecha, valorCuenta, "todos", valorTipo); 
  
});
$("#tipo").on("change", function () {
  // Obtener el valor del input
  var valorFecha = $("#fecha").val();
  var valorTipo;
  var valorCuenta="";
  //obtencion tipo
  if($("#tipo").val()=="todos"){
    valorTipo="todos";
    }else
  if($("#tipo").val()=="gasto"){
    valorTipo="1";
  }else if($("#tipo").val()=="ingreso"){
    valorTipo="2";
  }else if($("#tipo").val()=="ibanco"){
    valorTipo="3";
  }else if($("#tipo").val()=="gbanco"){
    valorTipo="4";
  }

  //obtencion cuenta
  if($("#cuenta").val()=="todos"){
    valorCuenta="todos";
    }else
  if($("#cuenta").val()=="NOC1"){
    valorCuenta="1";
  }else if($("#cuenta").val()=="NOC2"){
    valorCuenta="2";
  }
  // Separar el valor en año y mes
  var yearFecha = valorFecha.split("-")[0]; // Obtener el año
  var mesFecha = valorFecha.split("-")[1]; // Obtener el mes

  //console.log(valorTipo);

  // Llamada a la función listaGastos con los valores obtenidos
  listaGastos(mesFecha, yearFecha);
  cargarTabla(mesFecha, yearFecha, valorCuenta, "todos", valorTipo);
  
});

// ========= Render de tabla =========
function renderTable(rows) {
  let html = "";
  (rows || []).forEach(row => {
    html += `
      <tr class="border-b border-gray-700">
        <td class="p-3">${row.id}</td>
        <td class="p-3">${row.nombre}</td>
        <td class="p-3">$${Number(row.costo).toFixed(2)}</td>
        <td class="p-3">${row.descripcion ?? ""}</td>
        <td class="p-3">${row.fecha}</td>
        <td class="p-3">${row.nombrede}</td>
        <td class="p-3">${row.tipo}</td>
        <td class="p-3">${row.usuario}</td>
      </tr>
    `;
  });
  $("#cuerpo-table").html(html);
}

// ========= Paginación =========
function renderPagination(page, totalPages) {
  currentPage = page;
  let html = "";

  // Prev
  html += `<button onclick="changePage(${Math.max(1, page - 1)})"
            class="px-3 py-1 rounded me-1 ${page === 1 ? 'bg-gray-800 text-gray-500 cursor-not-allowed' : 'bg-gray-700 text-gray-200 hover:bg-gray-600'}"
            ${page === 1 ? 'disabled' : ''}>«</button>`;

  // Números (compacto)
  const maxButtons = 7;
  let start = Math.max(1, page - 3);
  let end = Math.min(totalPages, start + maxButtons - 1);
  if (end - start + 1 < maxButtons) start = Math.max(1, end - maxButtons + 1);

  for (let i = start; i <= end; i++) {
    html += `<button onclick="changePage(${i})"
              class="px-3 py-1 rounded me-1 ${i === page ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'}">
              ${i}
            </button>`;
  }

  // Next
  html += `<button onclick="changePage(${Math.min(totalPages, page + 1)})"
            class="px-3 py-1 rounded ${page === totalPages ? 'bg-gray-800 text-gray-500 cursor-not-allowed' : 'bg-gray-700 text-gray-200 hover:bg-gray-600'}"
            ${page === totalPages ? 'disabled' : ''}>»</button>`;

  $("#pagination").html(html);
}

function changePage(page) {
  const valorFecha = $("#fecha").val();
  const yearFecha = valorFecha.split("-")[0];
  const mesFecha = valorFecha.split("-")[1];

  const valorTipo = obtenerTipoSeleccionado();
  const valorCuenta = obtenerCuentaSeleccionada();

  cargarTabla(mesFecha, yearFecha, valorCuenta, "todos", valorTipo, page);
}
// ========= Eventos de filtros =========
$("#fecha").on("change", function () {
  const valorFecha = $("#fecha").val();
  const yearFecha = valorFecha.split("-")[0];
  const mesFecha = valorFecha.split("-")[1];

  const valorTipo = obtenerTipoSeleccionado();
  const valorCuenta = obtenerCuentaSeleccionada();

  listaGastos(mesFecha, yearFecha);
  currentPage = 1;
  cargarTabla(mesFecha, yearFecha, valorCuenta, "todos", valorTipo, currentPage);
});

$("#cuenta").on("change", function () {
  const valorFecha = $("#fecha").val();
  const yearFecha = valorFecha.split("-")[0];
  const mesFecha = valorFecha.split("-")[1];

  const valorTipo = obtenerTipoSeleccionado();
  const valorCuenta = obtenerCuentaSeleccionada();

  currentPage = 1;
  cargarTabla(mesFecha, yearFecha, valorCuenta, "todos", valorTipo, currentPage);
});

$("#tipo").on("change", function () {
  const valorFecha = $("#fecha").val();
  const yearFecha = valorFecha.split("-")[0];
  const mesFecha = valorFecha.split("-")[1];

  const valorTipo = obtenerTipoSeleccionado();
  const valorCuenta = obtenerCuentaSeleccionada();

  currentPage = 1;
  cargarTabla(mesFecha, yearFecha, valorCuenta, "todos", valorTipo, currentPage);
});

// ========= Búsqueda con debounce =========
$("#buscar").on("input", debounce(function () {
  const valorFecha = $("#fecha").val();
  const yearFecha = valorFecha.split("-")[0];
  const mesFecha = valorFecha.split("-")[1];

  const valorTipo = obtenerTipoSeleccionado();
  const valorCuenta = obtenerCuentaSeleccionada();

  currentPage = 1; // reiniciar a página 1 al buscar
  cargarTabla(mesFecha, yearFecha, valorCuenta, "todos", valorTipo, currentPage);
}, 400));

// Asegúrate de tener SheetJS y moment cargados
// <script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/moment.min.js"></script>
// <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

document.getElementById("btnExportExcel").addEventListener("click", exportarExcel);

async function exportarExcel(e) {
  e?.preventDefault?.();

  const valorFecha = $("#fecha").val();
  if (!valorFecha) {
    Swal.fire("Falta fecha", "Selecciona un mes/año primero", "warning");
    return;
  }

  const [yearFecha, mesFecha] = valorFecha.split("-");
  const search = ($("#buscar").val() || "").trim();

  // mismas funciones de mapeo que usas en tu código
  const obtenerTipoSeleccionado = () => {
    const v = $("#tipo").val();
    if (v === "todos") return "todos";
    if (v === "gasto") return "1";
    if (v === "ingreso") return "2";
    if (v === "ibanco") return "3";
    if (v === "gbanco") return "4";
    return "todos";
  };
  const obtenerCuentaSeleccionada = () => {
    const v = $("#cuenta").val();
    if (v === "todos") return "todos";
    if (v === "NOC1") return "1";
    if (v === "NOC2") return "2";
    return "todos";
  };

  const valorTipo = obtenerTipoSeleccionado();
  const valorCuenta = obtenerCuentaSeleccionada();

  // pedir TODO al backend (sin LIMIT)
  const formData = new FormData();
  formData.append("mes", mesFecha);
  formData.append("year", yearFecha);
  formData.append("nombrede", valorCuenta);
  formData.append("usuario", "todos");
  formData.append("tipo", valorTipo);
  formData.append("search", search);
  formData.append("export", 1); // <<< señal para no aplicar LIMIT/OFFSET en PHP

  try {
    const resp = await $.ajax({
      url: "../php/cargarTotal.php",
      data: formData,
      processData: false,
      contentType: false,
      type: "POST",
      dataType: "json"
    });

    const rows = resp?.data || [];
    if (!rows.length) {
      Swal.fire("Sin datos", "No hay datos para exportar con el filtro actual", "warning");
      return;
    }

    // total: ingresos suman, gastos restan (igual que en tu tabla)
    let total = 0;
    rows.forEach(r => {
      const costo = parseFloat(r.costo) || 0;
      const t = (r.tipo || "").toLowerCase();
      if (t.includes("ingreso")) total += costo; else total -= costo;
    });

    // datos legibles para Excel
    const exportData = rows.map(r => ({
      ID: r.id,
      Título: r.nombre,
      Costo: Number(r.costo).toFixed(2),
      Descripción: r.descripcion ?? "",
      Fecha: r.fecha,
      Cuenta: r.nombrede,
      Tipo: r.tipo,
      Usuario: r.usuario
    }));

    // fila TOTAL
    exportData.push({ Título: "TOTAL", Costo: total.toFixed(2) });

    const ws = XLSX.utils.json_to_sheet(exportData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Corte");
    XLSX.writeFile(wb, `Corte_${moment().format("YYYY-MM-DD_HH-mm")}.xlsx`);
  } catch (err) {
    console.error(err);
    Swal.fire("Error", "No se pudo generar el Excel", "error");
  }
}


