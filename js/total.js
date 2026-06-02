// =====================
// Estado global
// =====================
let currentPage = 1;
const rowsPerPage = 10;
let ultimaRespuesta = { data: [], total_pages: 1, total_rows: 0 };

// =====================
// Inicialización
// =====================
let date = moment().format("YYYY-MM");
document.getElementById("fecha").value = date;

let mes = moment().format("MM");
let year = moment().format("YYYY");

$(document).ready(function () {
  cargarTabla(mes, year, "todos", "todos", "todos", currentPage);
  listaGastos(mes, year);
});

// =====================
// Utilidades
// =====================
function debounce(fn, delay = 400) {
  let t;

  return (...args) => {
    clearTimeout(t);
    t = setTimeout(() => fn(...args), delay);
  };
}

function escapeHtml(value) {
  return String(value ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function formatMoney(value) {
  const number = Number(value) || 0;

  return number.toLocaleString("es-MX", {
    style: "currency",
    currency: "MXN"
  });
}

function setMoneyText(selector, value) {
  $(selector).text(formatMoney(value));
}

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

function obtenerFechaSeleccionada() {
  const valorFecha = $("#fecha").val();

  if (!valorFecha) {
    return {
      yearFecha: moment().format("YYYY"),
      mesFecha: moment().format("MM")
    };
  }

  const [yearFecha, mesFecha] = valorFecha.split("-");

  return {
    yearFecha,
    mesFecha
  };
}

function recargarDatos(resetPage = true, recargarCards = false) {
  const { yearFecha, mesFecha } = obtenerFechaSeleccionada();
  const valorTipo = obtenerTipoSeleccionado();
  const valorCuenta = obtenerCuentaSeleccionada();

  if (resetPage) {
    currentPage = 1;
  }

  if (recargarCards) {
    listaGastos(mesFecha, yearFecha);
  }

  cargarTabla(
    mesFecha,
    yearFecha,
    valorCuenta,
    "todos",
    valorTipo,
    currentPage
  );
}

function getTipoBadge(tipo) {
  const label = String(tipo ?? "").trim();
  const tipoLower = label.toLowerCase();

  if (tipoLower.includes("gasto banco")) {
    return `
      <span class="inline-flex items-center gap-2 rounded-full border border-orange-300/20 bg-orange-500/10 px-3 py-1 text-xs font-bold text-orange-300">
        <i class="fa-solid fa-building-columns"></i>
        ${escapeHtml(label)}
      </span>
    `;
  }

  if (tipoLower.includes("ingreso banco")) {
    return `
      <span class="inline-flex items-center gap-2 rounded-full border border-cyan-300/20 bg-cyan-500/10 px-3 py-1 text-xs font-bold text-cyan-200">
        <i class="fa-solid fa-building-columns"></i>
        ${escapeHtml(label)}
      </span>
    `;
  }

  if (tipoLower.includes("gasto")) {
    return `
      <span class="inline-flex items-center gap-2 rounded-full border border-red-300/20 bg-red-500/10 px-3 py-1 text-xs font-bold text-red-300">
        <i class="fa-solid fa-arrow-trend-down"></i>
        ${escapeHtml(label)}
      </span>
    `;
  }

  if (tipoLower.includes("ingreso")) {
    return `
      <span class="inline-flex items-center gap-2 rounded-full border border-emerald-300/20 bg-emerald-500/10 px-3 py-1 text-xs font-bold text-emerald-300">
        <i class="fa-solid fa-arrow-trend-up"></i>
        ${escapeHtml(label)}
      </span>
    `;
  }

  return `
    <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-bold text-cyan-100/70">
      ${escapeHtml(label || "-")}
    </span>
  `;
}

function getCostoClass(tipo) {
  const tipoLower = String(tipo ?? "").toLowerCase();

  if (tipoLower.includes("gasto")) {
    return "text-red-300";
  }

  return "text-emerald-300";
}

function getCostoSign(tipo) {
  const tipoLower = String(tipo ?? "").toLowerCase();

  if (tipoLower.includes("gasto")) {
    return "-";
  }

  return "+";
}

function getCuentaBadge(nombrede) {
  const label = String(nombrede ?? "").trim();

  return `
    <span class="inline-flex items-center gap-2 rounded-full border border-blue-300/20 bg-blue-500/10 px-3 py-1 text-xs font-bold text-blue-200">
      <i class="fa-solid fa-network-wired"></i>
      ${escapeHtml(label || "-")}
    </span>
  `;
}

function pintarTotalGeneral(total) {
  const totalClass = total >= 0 ? "text-emerald-300" : "text-red-300";
  const icon = total >= 0 ? "fa-arrow-trend-up" : "fa-arrow-trend-down";

  $("#total-corte")
    .removeClass("text-cyan-200 text-emerald-300 text-red-300")
    .addClass(totalClass)
    .html(`
      <span class="inline-flex items-center gap-3">
        <i class="fa-solid ${icon} text-2xl"></i>
        ${formatMoney(total)}
      </span>
    `);
}

// =====================
// Cargar tabla principal
// =====================
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

  $("#cuerpo-table").html(`
    <tr>
      <td colspan="8" class="px-5 py-12 text-center text-cyan-100/70">
        <i class="fa-solid fa-spinner fa-spin mr-2 text-cyan-300"></i>
        Cargando movimientos...
      </td>
    </tr>
  `);

  $.ajax({
    url: "../php/cargarTotal.php",
    data: formData,
    processData: false,
    contentType: false,
    type: "POST",
    dataType: "json",
    success: function (response) {
      ultimaRespuesta = response;

      const sumIngresos = Number(response.sum_ingresos ?? 0);
      const sumGastos = Number(response.sum_gastos ?? 0);
      const total = sumIngresos - sumGastos;

      pintarTotalGeneral(total);

      renderTable(response.data || []);
      renderPagination(response.page || 1, response.total_pages || 1);
    },
    error: function (xhr) {
      console.error("Error al cargar total:", xhr.responseText);

      $("#cuerpo-table").html(`
        <tr>
          <td colspan="8" class="px-5 py-12 text-center">
            <div class="mx-auto mb-3 w-14 h-14 rounded-2xl bg-red-500/10 border border-red-400/20 flex items-center justify-center">
              <i class="fa-solid fa-triangle-exclamation text-red-300 text-xl"></i>
            </div>
            <p class="font-bold text-white">No se pudo cargar la información</p>
            <p class="mt-1 text-sm text-red-100/60">Revisa cargarTotal.php o la consola del navegador.</p>
          </td>
        </tr>
      `);

      $("#pagination").html("");
      pintarTotalGeneral(0);
    }
  });
}

// =====================
// Cargar cards de ganancias
// =====================
function listaGastos(mes, year) {
  const formData = new FormData();
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
      const ingresoE = Number(response.ingresoE || 0);
      const gastoE = Number(response.gastoE || 0);
      const ingresoB = Number(response.ingresoB || 0);
      const gastoB = Number(response.gastoB || 0);

      const totalE = ingresoE - gastoE;
      const totalB = ingresoB - gastoB;
      const ganancias = totalE + totalB;

      const qtyE = ganancias * 0.2;
      const qtyB = ganancias * 0.8;

      let entrega = 0;

      if (totalB !== 0) {
        entrega = qtyB - totalB + 22500;
      }

      setMoneyText("#ingreso-e", ingresoE);
      setMoneyText("#gastos-e", gastoE);
      setMoneyText("#pago-e", totalE);

      setMoneyText("#ingreso-b", ingresoB);
      setMoneyText("#gastos-b", gastoB);
      setMoneyText("#pago-b", totalB);

      setMoneyText("#total-noc1", ganancias);
      setMoneyText("#qty-e", qtyE);
      setMoneyText("#qty-b", qtyB);
      setMoneyText("#entrega", entrega);

      const ingresoBBS2 = Number(response.ingresoBBS2 || 0);
      const gastoBBS2 = Number(response.gastoBBS2 || 0);
      const totalBBS2 = ingresoBBS2 - gastoBBS2;

      setMoneyText("#ingreso-b-bbs", ingresoBBS2);
      setMoneyText("#gastos-b-bbs", gastoBBS2);
      setMoneyText("#pago-b-bbs", totalBBS2);

      if ($("#total-noc2").length) {
        setMoneyText("#total-noc2", totalBBS2);
      }

      setMoneyText("#ingreso-banco-noc1", Number(response.ingresoBancoNOC1 || 0));
      setMoneyText("#gasto-banco-noc1", Number(response.gastoBancoNOC1 || 0));
      setMoneyText("#ingreso-banco-noc2", Number(response.ingresoBancoNOC2 || 0));
      setMoneyText("#gasto-banco-noc2", Number(response.gastoBancoNOC2 || 0));
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log("Error ganancias:", textStatus, errorThrown);
    }
  });
}

// =====================
// Render de tabla
// =====================
function renderTable(rows) {
  let html = "";

  if (!rows || rows.length === 0) {
    $("#cuerpo-table").html(`
      <tr>
        <td colspan="8" class="px-5 py-14 text-center">
          <div class="mx-auto mb-4 w-16 h-16 rounded-2xl bg-cyan-400/10 border border-cyan-300/20 flex items-center justify-center">
            <i class="fa-solid fa-folder-open text-cyan-300 text-2xl"></i>
          </div>
          <p class="font-bold text-white text-lg">No hay movimientos para mostrar</p>
          <p class="mt-1 text-sm text-cyan-100/60">Intenta cambiar la fecha, cuenta, tipo o búsqueda.</p>
        </td>
      </tr>
    `);
    return;
  }

  rows.forEach((row) => {
    const id = escapeHtml(row.id);
    const nombre = escapeHtml(row.nombre || "-");
    const descripcion = escapeHtml(row.descripcion || "");
    const fecha = escapeHtml(row.fecha || "-");
    const nombrede = escapeHtml(row.nombrede || "-");
    const usuario = escapeHtml(row.usuario || "-");
    const tipo = row.tipo || "-";
    const costo = Number(row.costo || 0);

    html += `
      <tr class="group border-b border-cyan-400/10 bg-[#071322]/40 hover:bg-cyan-400/[0.06] transition">
        
        <td class="px-5 py-4 align-middle">
          <span class="inline-flex items-center justify-center min-w-11 rounded-full border border-cyan-300/30 bg-cyan-400/10 px-3 py-1.5 text-sm font-black text-cyan-100 shadow-sm shadow-cyan-950/30">
            #${id}
          </span>
        </td>

        <td class="px-5 py-4 align-middle">
          <p class="font-bold text-white max-w-[240px] truncate" title="${nombre}">
            ${nombre}
          </p>
          <p class="mt-1 text-xs text-cyan-100/45">
            Movimiento registrado
          </p>
        </td>

        <td class="px-5 py-4 align-middle whitespace-nowrap">
          <span class="font-extrabold ${getCostoClass(tipo)}">
            ${getCostoSign(tipo)} ${formatMoney(costo)}
          </span>
        </td>

        <td class="px-5 py-4 align-middle">
          <p class="max-w-[340px] truncate text-sm text-cyan-100/75" title="${descripcion}">
            ${descripcion || "Sin descripción"}
          </p>
        </td>

        <td class="px-5 py-4 align-middle text-sm text-cyan-100/80 whitespace-nowrap">
          <i class="fa-solid fa-calendar-day mr-2 text-cyan-300/60"></i>
          ${fecha}
        </td>

        <td class="px-5 py-4 align-middle whitespace-nowrap">
          ${getCuentaBadge(nombrede)}
        </td>

        <td class="px-5 py-4 align-middle whitespace-nowrap">
          ${getTipoBadge(tipo)}
        </td>

        <td class="px-5 py-4 align-middle text-sm text-cyan-100/80 whitespace-nowrap">
          <i class="fa-solid fa-user mr-2 text-cyan-300/60"></i>
          ${usuario}
        </td>

      </tr>
    `;
  });

  $("#cuerpo-table").html(html);
}

// =====================
// Paginación
// =====================
function renderPagination(page, totalPages) {
  currentPage = page;

  let html = "";

  if (totalPages <= 1) {
    $("#pagination").html("");
    return;
  }

  html += `
    <button type="button"
      onclick="changePage(${Math.max(1, page - 1)})"
      ${page === 1 ? "disabled" : ""}
      class="inline-flex items-center justify-center min-w-10 h-10 rounded-xl border border-white/10 bg-white/5 px-3 text-sm font-bold text-white hover:bg-white/10 disabled:opacity-40 disabled:cursor-not-allowed transition">
      <i class="fa-solid fa-chevron-left"></i>
    </button>
  `;

  const maxButtons = 7;
  let start = Math.max(1, page - 3);
  let end = Math.min(totalPages, start + maxButtons - 1);

  if (end - start + 1 < maxButtons) {
    start = Math.max(1, end - maxButtons + 1);
  }

  if (start > 1) {
    html += `
      <button type="button"
        onclick="changePage(1)"
        class="inline-flex items-center justify-center min-w-10 h-10 rounded-xl border border-white/10 bg-white/5 px-3 text-sm font-bold text-cyan-100 hover:bg-white/10 transition">
        1
      </button>
    `;

    if (start > 2) {
      html += `
        <span class="inline-flex items-center justify-center min-w-10 h-10 text-cyan-100/40">
          ...
        </span>
      `;
    }
  }

  for (let i = start; i <= end; i++) {
    html += `
      <button type="button"
        onclick="changePage(${i})"
        class="inline-flex items-center justify-center min-w-10 h-10 rounded-xl px-3 text-sm font-bold transition
        ${i === page
          ? "bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-lg shadow-blue-950/40"
          : "border border-white/10 bg-white/5 text-cyan-100 hover:bg-white/10"}">
        ${i}
      </button>
    `;
  }

  if (end < totalPages) {
    if (end < totalPages - 1) {
      html += `
        <span class="inline-flex items-center justify-center min-w-10 h-10 text-cyan-100/40">
          ...
        </span>
      `;
    }

    html += `
      <button type="button"
        onclick="changePage(${totalPages})"
        class="inline-flex items-center justify-center min-w-10 h-10 rounded-xl border border-white/10 bg-white/5 px-3 text-sm font-bold text-cyan-100 hover:bg-white/10 transition">
        ${totalPages}
      </button>
    `;
  }

  html += `
    <button type="button"
      onclick="changePage(${Math.min(totalPages, page + 1)})"
      ${page === totalPages ? "disabled" : ""}
      class="inline-flex items-center justify-center min-w-10 h-10 rounded-xl border border-white/10 bg-white/5 px-3 text-sm font-bold text-white hover:bg-white/10 disabled:opacity-40 disabled:cursor-not-allowed transition">
      <i class="fa-solid fa-chevron-right"></i>
    </button>
  `;

  $("#pagination").html(html);
}

function changePage(page) {
  currentPage = page;
  recargarDatos(false, false);
}

// =====================
// Eventos de filtros
// =====================
$("#fecha").on("change", function () {
  recargarDatos(true, true);
});

$("#cuenta").on("change", function () {
  recargarDatos(true, false);
});

$("#tipo").on("change", function () {
  recargarDatos(true, false);
});

$("#buscar").on(
  "input",
  debounce(function () {
    recargarDatos(true, false);
  }, 400)
);

// =====================
// Exportar Excel
// =====================
document
  .getElementById("btnExportExcel")
  .addEventListener("click", exportarExcel);

async function exportarExcel(e) {
  e?.preventDefault?.();

  const valorFecha = $("#fecha").val();

  if (!valorFecha) {
    Swal.fire("Falta fecha", "Selecciona un mes/año primero", "warning");
    return;
  }

  const [yearFecha, mesFecha] = valorFecha.split("-");
  const search = ($("#buscar").val() || "").trim();

  const valorTipo = obtenerTipoSeleccionado();
  const valorCuenta = obtenerCuentaSeleccionada();

  const formData = new FormData();
  formData.append("mes", mesFecha);
  formData.append("year", yearFecha);
  formData.append("nombrede", valorCuenta);
  formData.append("usuario", "todos");
  formData.append("tipo", valorTipo);
  formData.append("search", search);
  formData.append("export", 1);

  try {
    Swal.fire({
      title: "Generando Excel...",
      text: "Espera un momento.",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

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
      Swal.fire(
        "Sin datos",
        "No hay datos para exportar con el filtro actual",
        "warning"
      );
      return;
    }

    let total = 0;

    rows.forEach((r) => {
      const costo = parseFloat(r.costo) || 0;
      const t = String(r.tipo || "").toLowerCase();

      if (t.includes("ingreso")) {
        total += costo;
      } else {
        total -= costo;
      }
    });

    const exportData = rows.map((r) => ({
      ID: r.id,
      Título: r.nombre,
      Costo: Number(r.costo).toFixed(2),
      Descripción: r.descripcion ?? "",
      Fecha: r.fecha,
      Cuenta: r.nombrede,
      Tipo: r.tipo,
      Usuario: r.usuario
    }));

    exportData.push({
      Título: "TOTAL",
      Costo: total.toFixed(2)
    });

    const ws = XLSX.utils.json_to_sheet(exportData);
    const wb = XLSX.utils.book_new();

    XLSX.utils.book_append_sheet(wb, ws, "Corte");
    XLSX.writeFile(wb, `Corte_${moment().format("YYYY-MM-DD_HH-mm")}.xlsx`);

    Swal.fire("Excel generado", "El archivo se descargó correctamente.", "success");
  } catch (err) {
    console.error(err);
    Swal.fire("Error", "No se pudo generar el Excel", "error");
  }
}