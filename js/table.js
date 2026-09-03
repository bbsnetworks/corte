// =====================
// Inicialización de fecha
// =====================
let date = moment().format("YYYY-MM");
document.getElementById("fechaa").value = date;

let mes = moment().format("MM");
let year = moment().format("YYYY");

// =====================
// Variables para tabla
// =====================
let dataRegistros = [];
let currentPage = 1;
const rowsPerPage = 10;

$("#fechaa").on("change", function () {
  const valorFecha = $("#fechaa").val();

  if (!valorFecha) return;

  const [yearFecha, mesFecha] = valorFecha.split("-");

  currentPage = 1;

  cargarTabla(mesFecha, yearFecha);
});

$("#buscar").on("input", function () {
  currentPage = 1;
  renderTable();
});

// =====================
// Helpers visuales
// =====================
function escapeHtml(value) {
  return String(value ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function formatMoney(value) {
  const number = parseFloat(value);

  if (isNaN(number)) {
    return "$0.00";
  }

  return number.toLocaleString("es-MX", {
    style: "currency",
    currency: "MXN",
  });
}

function normalizeTipo(tipo) {
  const value = String(tipo ?? "").trim();

  const tipos = {
    1: "Gasto",
    2: "Ingreso",
    3: "Ingreso Banco",
    4: "Gasto Banco",
  };

  return tipos[value] || "Desconocido";
}

function getTipoBadge(tipo) {
  const value = String(tipo ?? "").trim();
  const label = normalizeTipo(value);

  if (value === "1") {
    return `
            <span class="inline-flex items-center gap-2 rounded-full border border-red-400/20 bg-red-500/10 px-3 py-1 text-xs font-bold text-red-300">
                <i class="fa-solid fa-arrow-trend-down"></i>
                ${label}
            </span>
        `;
  }

  if (value === "2") {
    return `
            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-bold text-emerald-300">
                <i class="fa-solid fa-arrow-trend-up"></i>
                ${label}
            </span>
        `;
  }

  if (value === "3") {
    return `
            <span class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-cyan-500/10 px-3 py-1 text-xs font-bold text-cyan-300">
                <i class="fa-solid fa-building-columns"></i>
                ${label}
            </span>
        `;
  }

  if (value === "4") {
    return `
            <span class="inline-flex items-center gap-2 rounded-full border border-orange-400/20 bg-orange-500/10 px-3 py-1 text-xs font-bold text-orange-300">
                <i class="fa-solid fa-building-columns"></i>
                ${label}
            </span>
        `;
  }

  return `
        <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-bold text-cyan-100">
            ${escapeHtml(label)}
        </span>
    `;
}

function getCostoClass(tipo) {
  const value = String(tipo ?? "").trim();

  if (value === "1" || value === "4") {
    return "text-red-300";
  }

  return "text-emerald-300";
}

function getCostoSign(tipo) {
  const value = String(tipo ?? "").trim();

  if (value === "1" || value === "4") {
    return "-";
  }

  return "+";
}

function isGasto(row) {
  const value = String(row.tipo ?? "").trim();

  return value === "1" || value === "4";
}

function isIngreso(row) {
  const value = String(row.tipo ?? "").trim();

  return value === "2" || value === "3";
}

function getFilteredData() {
  const q = ($("#buscar").val() || "").toLowerCase().trim();

  if (!q) {
    return dataRegistros;
  }

  const inc = (value) =>
    String(value ?? "")
      .toLowerCase()
      .includes(q);

  return dataRegistros.filter(
    (row) =>
      inc(row.id) ||
      inc(row.fecha) ||
      inc(row.titulo) ||
      inc(row.descripcion) ||
      inc(normalizeTipo(row.tipo)) ||
      inc(row.usuario) ||
      inc(row.costo),
  );
}

function updateResumen(filteredData) {
  let totalGastos = 0;
  let totalIngresos = 0;

  filteredData.forEach((row) => {
    const costo = parseFloat(row.costo) || 0;

    if (isGasto(row)) {
      totalGastos += costo;
    }

    if (isIngreso(row)) {
      totalIngresos += costo;
    }
  });

  $("#totalRegistros").text(filteredData.length);
  $("#totalGastos").text(formatMoney(totalGastos));
  $("#totalIngresos").text(formatMoney(totalIngresos));
}

function updateEmptyState(filteredData) {
  if (filteredData.length === 0) {
    $("#tablaVacia").removeClass("hidden");
    $("#tablaGastos").addClass("hidden");
  } else {
    $("#tablaVacia").addClass("hidden");
    $("#tablaGastos").removeClass("hidden");
  }
}

function updatePaginationInfo(totalRows, start, end) {
  if (totalRows === 0) {
    $("#paginationInfo").text("Sin registros para mostrar.");
    return;
  }

  $("#paginationInfo").text(
    `Mostrando ${start + 1} a ${Math.min(end, totalRows)} de ${totalRows} registros.`,
  );
}

function cargarTabla(mes, year) {
  let formData = new FormData();
  formData.append("mes", mes);
  formData.append("year", year);

  $("#tablaBody").html(`
        <tr>
            <td colspan="8" class="px-5 py-10 text-center text-cyan-100/70">
                <i class="fa-solid fa-spinner fa-spin mr-2 text-cyan-300"></i>
                Cargando registros...
            </td>
        </tr>
    `);

  $("#tablaVacia").addClass("hidden");
  $("#tablaGastos").removeClass("hidden");

  $.ajax({
    url: "../php/cargarTabla.php",
    data: formData,
    processData: false,
    contentType: false,
    type: "POST",
    dataType: "json",
    success: function (response) {
      if (Array.isArray(response)) {
        dataRegistros = response;
      } else {
        dataRegistros = [];
        console.warn("La respuesta no es un arreglo:", response);
      }

      currentPage = 1;
      renderTable();
    },
    error: function (xhr) {
      console.error("Error AJAX:", xhr.responseText);

      dataRegistros = [];
      updateResumen([]);
      $("#pagination").html("");
      $("#paginationInfo").text("No se pudo cargar la información.");

      $("#tablaGastos").removeClass("hidden");
      $("#tablaVacia").addClass("hidden");

      $("#tablaBody").html(`
                <tr>
                    <td colspan="8" class="px-5 py-10 text-center">
                        <div class="mx-auto mb-3 w-14 h-14 rounded-2xl bg-red-500/10 border border-red-400/20 flex items-center justify-center">
                            <i class="fa-solid fa-triangle-exclamation text-red-300 text-xl"></i>
                        </div>
                        <p class="font-bold text-white">Error al cargar la tabla</p>
                        <p class="mt-1 text-sm text-red-100/60">Revisa la respuesta de cargarTabla.php en consola.</p>
                    </td>
                </tr>
            `);
    },
  });
}

function cargarGastos() {
  const valorFecha = $("#fechaa").val();

  if (!valorFecha) return;

  const [yearFecha, mesFecha] = valorFecha.split("-");

  currentPage = 1;

  cargarTabla(mesFecha, yearFecha);
}

function renderTable() {
  const filteredData = getFilteredData();

  const totalPages = Math.ceil(filteredData.length / rowsPerPage);

  if (currentPage > totalPages && totalPages > 0) {
    currentPage = totalPages;
  }

  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  const dataToShow = filteredData.slice(start, end);

  updateResumen(filteredData);
  updateEmptyState(filteredData);
  updatePaginationInfo(filteredData.length, start, end);

  if (filteredData.length === 0) {
    $("#tablaBody").html("");
    renderPagination(0);
    return;
  }

  let html = "";

  dataToShow.forEach((row) => {
    const id = escapeHtml(row.id);
    const fecha = escapeHtml(row.fecha || "-");
    const titulo = escapeHtml(row.titulo || "-");
    const descripcion = escapeHtml(row.descripcion || "");
    const usuario = escapeHtml(row.usuario || "-");
    const costo = parseFloat(row.costo) || 0;
    const costoClass = getCostoClass(row.tipo);
    const costoSign = getCostoSign(row.tipo);

    html += `
            <tr class="group border-b border-white/10 hover:bg-cyan-400/5 transition">
                <td class="px-5 py-4 align-middle">
                    <span class="inline-flex items-center justify-center min-w-10 rounded-xl border border-cyan-400/15 bg-cyan-400/10 px-3 py-1 text-sm font-bold text-cyan-200">
                        ${id}
                    </span>
                </td>

                <td class="px-5 py-4 align-middle text-sm text-cyan-100/80 whitespace-nowrap">
                    <i class="fa-solid fa-calendar-day mr-2 text-cyan-300/60"></i>
                    ${fecha}
                </td>

                <td class="px-5 py-4 align-middle">
    <p class="font-bold text-white max-w-[280px] truncate"
       title="${titulo}">
        ${titulo}
    </p>

    ${
      descripcion
        ? `
            <p class="mt-1 text-xs text-cyan-100/50 max-w-[280px] truncate"
               title="${descripcion}">
                ${descripcion}
            </p>
        `
        : `
            <p class="mt-1 text-xs text-cyan-100/30">
                Sin descripción
            </p>
        `
    }
</td>

                <td class="px-5 py-4 align-middle whitespace-nowrap">
                    <span class="font-extrabold ${costoClass}">
                        ${costoSign} ${formatMoney(costo)}
                    </span>
                </td>

                <td class="px-5 py-4 align-middle whitespace-nowrap">
                    ${getTipoBadge(row.tipo)}
                </td>

                <td class="px-5 py-4 align-middle text-sm text-cyan-100/80 whitespace-nowrap">
                    <i class="fa-solid fa-user mr-2 text-cyan-300/60"></i>
                    ${usuario}
                </td>

                <td class="px-5 py-4 align-middle text-center">
                    ${
                      row.evidencia
                        ? `
                            <button type="button"
                                onclick="verImagen('${escapeHtml(row.evidencia)}')"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-cyan-400/20 bg-cyan-500/10 px-3 py-2 text-sm font-semibold text-cyan-200 hover:bg-cyan-500/20 transition">
                                <i class="fa-solid fa-image"></i>
                                Ver
                            </button>
                        `
                        : `
                            <span class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-cyan-100/40">
                                —
                            </span>
                        `
                    }
                </td>

                <td class="px-5 py-4 align-middle">
                    <div class="flex items-center justify-center gap-2">
                        <button type="button"
                            onclick="editGI(${row.id})"
                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-blue-400/20 bg-blue-500/10 text-blue-300 hover:bg-blue-500/20 transition"
                            title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>

                        <button type="button"
                            onclick="deleteGI(${row.id})"
                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-red-400/20 bg-red-500/10 text-red-300 hover:bg-red-500/20 transition"
                            title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
  });

  $("#tablaBody").html(html);
  renderPagination(filteredData.length);
}

// =====================
// Paginación
// =====================
function renderPagination(totalRows) {
  const totalPages = Math.ceil(totalRows / rowsPerPage);
  let html = "";

  if (totalPages <= 1) {
    $("#pagination").html("");
    return;
  }

  html += `
        <button type="button"
            onclick="changePage(${currentPage - 1})"
            ${currentPage === 1 ? "disabled" : ""}
            class="inline-flex items-center justify-center min-w-10 h-10 rounded-xl border border-white/10 bg-white/5 px-3 text-sm font-bold text-white hover:bg-white/10 disabled:opacity-40 disabled:cursor-not-allowed transition">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
    `;

  for (let i = 1; i <= totalPages; i++) {
    if (i === 1 || i === totalPages || Math.abs(i - currentPage) <= 2) {
      html += `
                <button type="button"
                    onclick="changePage(${i})"
                    class="inline-flex items-center justify-center min-w-10 h-10 rounded-xl px-3 text-sm font-bold transition
                    ${
                      i === currentPage
                        ? "bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-lg shadow-blue-950/40"
                        : "border border-white/10 bg-white/5 text-cyan-100 hover:bg-white/10"
                    }">
                    ${i}
                </button>
            `;
    } else if (i === currentPage - 3 || i === currentPage + 3) {
      html += `
                <span class="inline-flex items-center justify-center min-w-10 h-10 text-cyan-100/40">
                    ...
                </span>
            `;
    }
  }

  html += `
        <button type="button"
            onclick="changePage(${currentPage + 1})"
            ${currentPage === totalPages ? "disabled" : ""}
            class="inline-flex items-center justify-center min-w-10 h-10 rounded-xl border border-white/10 bg-white/5 px-3 text-sm font-bold text-white hover:bg-white/10 disabled:opacity-40 disabled:cursor-not-allowed transition">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    `;

  $("#pagination").html(html);
}

function changePage(page) {
  const filteredData = getFilteredData();
  const totalPages = Math.ceil(filteredData.length / rowsPerPage);

  if (page < 1 || page > totalPages) {
    return;
  }

  currentPage = page;
  renderTable();
}

// =====================
// Editar
// =====================
function editGI(id) {
  $.ajax({
    url: "../php/editarGI.php",
    type: "POST",
    data: { id: id },
    success: function (response) {
      $("#modal2").html(response);

      if ($("#tipo").val() == "1" || $("#tipo").val() == "4") {
        $("#costo").removeClass("verde").addClass("rojo");
      } else {
        $("#costo").removeClass("rojo").addClass("verde");
      }

      var modal = new bootstrap.Modal(document.getElementById("modalEditar"));
      modal.show();
    },
    error: function () {
      Swal.fire(
        "Error",
        "No se pudo cargar el formulario de edición.",
        "error",
      );
    },
  });
}

// =====================
// Actualizar
// =====================
function updateGI() {
  let id = $("#id").val();
  let titulo = $("#titulo").val();
  let costo = $("#costo").val();
  let descripcion = $("#descripcion").val();
  let fecha = $("#fecha").val();
  let tipo = $("#tipo").val();
  let nombrede = $("#nombrede").val();
  let iduser = $("#id-user").val();

  let formData2 = new FormData();
  formData2.append("id", id);
  formData2.append("titulo", titulo);
  formData2.append("costo", costo);
  formData2.append("descripcion", descripcion);
  formData2.append("fecha", fecha);
  formData2.append("tipo", tipo);
  formData2.append("nombrede", nombrede);
  formData2.append("iduser", iduser);

  $.ajax("../php/updateGI.php", {
    method: "POST",
    data: formData2,
    processData: false,
    contentType: false,
    success: function (data) {
      let jsonResponse;

      try {
        jsonResponse = JSON.parse(data);
      } catch (e) {
        console.error(data);
        Swal.fire("Error", "La respuesta del servidor no es válida.", "error");
        return;
      }

      if (jsonResponse.status === "success") {
        $("#modalEditar").modal("hide");
        cargarGastos();
        Swal.fire("Éxito", jsonResponse.message, "success");
      } else {
        Swal.fire("Error", jsonResponse.message, "error");
      }
    },
    error: function () {
      Swal.fire("Error", "No se pudo actualizar el registro.", "error");
    },
  });
}

// =====================
// Eliminar
// =====================
function deleteGI(id) {
  Swal.fire({
    title: "¿Estás seguro de eliminar el registro?",
    text: "Esta acción no se puede deshacer.",
    icon: "warning",
    showDenyButton: true,
    confirmButtonText: "Sí, eliminar",
    denyButtonText: "Cancelar",
    confirmButtonColor: "#ef4444",
    denyButtonColor: "#334155",
  }).then((result) => {
    if (result.isConfirmed) {
      let formData = new FormData();
      formData.append("id", id);

      $.ajax("../php/deleteGI.php", {
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
          let jsonResponse;

          try {
            jsonResponse = JSON.parse(data);
          } catch (e) {
            console.error(data);
            Swal.fire(
              "Error",
              "La respuesta del servidor no es válida.",
              "error",
            );
            return;
          }

          if (jsonResponse.status === "success") {
            cargarGastos();
            Swal.fire("Éxito", jsonResponse.message, "success");
          } else {
            Swal.fire("Error", jsonResponse.message, "error");
          }
        },
        error: function () {
          Swal.fire("Error", "No se pudo eliminar el registro.", "error");
        },
      });
    }
  });
}

// =====================
// Ver evidencia
// =====================
function verImagen(ruta) {
  if (!ruta) return;

  const extension = String(ruta).split(".").pop().toLowerCase();

  if (extension === "pdf") {
    Swal.fire({
      title: "Evidencia PDF",
      html: `
                <div class="rounded-2xl border border-cyan-400/20 bg-[#071322] p-4">
                    <i class="fa-solid fa-file-pdf text-red-300 text-5xl mb-3"></i>
                    <p class="text-sm text-cyan-100/70 mb-4">El archivo seleccionado es un PDF.</p>
                    <a href="${ruta}" target="_blank"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 px-5 py-3 font-bold text-white no-underline">
                        <i class="fa-solid fa-up-right-from-square"></i>
                        Abrir PDF
                    </a>
                </div>
            `,
      background: "#06152d",
      color: "#ffffff",
      showConfirmButton: false,
      showCloseButton: true,
    });
    return;
  }

  Swal.fire({
    title: "Evidencia",
    text: "Haz clic fuera de la imagen para cerrar",
    imageUrl: ruta,
    imageAlt: "Evidencia",
    showConfirmButton: false,
    showCloseButton: true,
    allowOutsideClick: true,
    background: "#06152d",
    color: "#ffffff",
    imageWidth: 420,
  });
}

// =====================
// Cargar al inicio
// =====================
cargarTabla(mes, year);
