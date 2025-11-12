// =====================
// Inicialización de fecha
// =====================
let date = moment().format("YYYY-MM");
document.getElementById("fechaa").value = date;
let mes = moment().format("MM");
let year = moment().format("YYYY");

// =====================
// Eventos
// =====================
$("#fechaa").on("change", function () {
    let valorFecha = $("#fechaa").val();
    let yearFecha = valorFecha.split("-")[0];
    let mesFecha = valorFecha.split("-")[1];
    cargarTabla(mesFecha, yearFecha);
});

$("#buscar").on("input", function () {
    let valorFecha = $("#fechaa").val();
    let yearFecha = valorFecha.split("-")[0];
    let mesFecha = valorFecha.split("-")[1];
    let search = $("#buscar").val().trim();
    cargarTabla(mesFecha, yearFecha, search);
});


// =====================
// Variables para tabla
// =====================
let dataRegistros = [];
let currentPage = 1;
const rowsPerPage = 10;

// =====================
// Cargar tabla desde PHP
// =====================
function cargarTabla(mes, year, search = "") {
    let formData = new FormData();
    formData.append("mes", mes);
    formData.append("year", year);
    formData.append("search", search);

    $.ajax({
        url: '../php/cargarTabla.php',
        data: formData,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (response) {
            try {
                dataRegistros = response;
                currentPage = 1;
                renderTable();
            } catch (e) {
                console.error("Error al procesar datos:", e);
                $('#tablaBody').html('<tr><td colspan="6" class="p-3 text-center">Error al cargar datos</td></tr>');
            }
        },
        error: function () {
            $('#tablaBody').html('<tr><td colspan="6" class="p-3 text-center">Error al cargar la tabla</td></tr>');
        }
    });
}


// =====================
// Renderizado tabla
// =====================
function renderTable() {
  const q = ($("#buscar").val() || "").toLowerCase();

  const inc = (v) => String(v ?? "").toLowerCase().includes(q);

  const filteredData = dataRegistros.filter(row =>
    inc(row.id) ||
    inc(row.titulo) ||
    inc(row.tipo) ||
    inc(row.usuario) ||
    inc(row.costo) ||    // <-- ahora busca por costo
    inc(row.fecha)       // (opcional) útil si quieres también por fecha
  );

  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  const dataToShow = filteredData.slice(start, end);

  let html = "";
  dataToShow.forEach(row => {
    html += `
      <tr class="border-b border-gray-700">
        <td class="p-3">${row.id}</td>
        <td class="p-3">${row.fecha}</td>
        <td class="p-3">${row.titulo || '-'}</td>
        <td class="p-3">$${row.costo}</td>
        <td class="p-3">${row.tipo}</td>
        <td class="p-3">${row.usuario}</td>
        <td class="p-3 text-center">
          ${row.evidencia
            ? `<i class="fas fa-image text-blue-400 hover:text-blue-300 cursor-pointer text-lg"
                 onclick="verImagen('${row.evidencia}')"></i>`
            : `<span class="text-gray-500">—</span>`
          }
        </td>
        <td class="p-3">
          <button onclick="editGI(${row.id})" class="text-blue-400 hover:text-blue-300 mr-2">
            <i class="fas fa-edit"></i>
          </button>
          <button onclick="deleteGI(${row.id})" class="text-red-400 hover:text-red-300">
            <i class="fas fa-trash"></i>
          </button>
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

    for (let i = 1; i <= totalPages; i++) {
        html += `
            <button onclick="changePage(${i})"
                class="px-3 py-1 rounded ${i === currentPage ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'}">
                ${i}
            </button>
        `;
    }

    $("#pagination").html(html);
}

function changePage(page) {
    currentPage = page;
    renderTable();
}

// =====================
// Editar, actualizar, eliminar (sin cambios)
// =====================
function editGI(id) {
    $.ajax({
        url: "../php/editarGI.php",
        type: "POST",
        data: { id: id },
        success: function (response) {
            $('#modal2').html(response);

            // Ajustar color del costo según tipo
            if ($("#tipo").val() == "1" || $("#tipo").val() == "4") {
                $("#costo").removeClass("verde").addClass("rojo");
            } else {
                $("#costo").removeClass("rojo").addClass("verde");
            }

            // Mostrar modal
            var modal = new bootstrap.Modal(document.getElementById('modalEditar'));
            modal.show();
        },
        error: function () {
            Swal.fire("Error", "No se pudo cargar el formulario de edición.", "error");
        }
    });
}


function updateGI() {
    let id = $('#id').val();
    let titulo = $('#titulo').val();
    let costo = $('#costo').val();
    let descripcion = $('#descripcion').val();
    let fecha = $('#fecha').val();
    let tipo = $('#tipo').val();
    let nombrede = $('#nombrede').val();
    let iduser = $('#id-user').val();

    let formData2 = new FormData();
    formData2.append('id', id);
    formData2.append('titulo', titulo);
    formData2.append('costo', costo);
    formData2.append('descripcion', descripcion);
    formData2.append('fecha', fecha);
    formData2.append('tipo', tipo);
    formData2.append('nombrede', nombrede);
    formData2.append('iduser', iduser);

    $.ajax('../php/updateGI.php', {
        method: 'POST',
        data: formData2,
        processData: false,
        contentType: false,
        success: function (data) {
            let jsonResponse = JSON.parse(data);
            if (jsonResponse.status === "success") {
                $('#modalEditar').modal('hide');
                let valorFecha = $("#fechaa").val();
                cargarTabla(valorFecha.split("-")[1], valorFecha.split("-")[0]);
                Swal.fire('Éxito', jsonResponse.message, 'success');
            } else {
                Swal.fire('Error', jsonResponse.message, 'error');
            }
        }
    });
}

function deleteGI(id) {
    Swal.fire({
        title: "Estas seguro de eliminar el registro?",
        showDenyButton: true,
        confirmButtonText: "Si",
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData();
            formData.append('id', id);
            $.ajax('../php/deleteGI.php', {
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    let jsonResponse = JSON.parse(data);
                    if (jsonResponse.status === "success") {
                        let valorFecha = $("#fechaa").val();
                        cargarTabla(valorFecha.split("-")[1], valorFecha.split("-")[0]);
                        Swal.fire('Éxito', jsonResponse.message, 'success');
                    } else {
                        Swal.fire('Error', jsonResponse.message, 'error');
                    }
                }
            });
        }
    });
}

function verImagen(ruta) {
    Swal.fire({
        text: 'Haz clic en la imagen para cerrar',
        imageUrl: ruta,
        imageAlt: 'Evidencia',
        showConfirmButton: false,
        allowOutsideClick: true,
        imageWidth: 400,
        imageHeight: 600
    });
}

// =====================
// Cargar al inicio
// =====================
cargarTabla(mes, year);
