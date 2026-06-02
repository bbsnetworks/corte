function submitForm() {
  let titulo = $("#titulo").val().trim();
  let costo = parseFloat($("#costo").val());
  let descripcion = $("#descripcion").val().trim();
  let fecha = $("#fecha").val();
  let tipo = $("#tipo").val();
  let nombrede = $("#nombrede").val();
  let fileInput = $("#file")[0];

  // VALIDACIONES BÁSICAS
  if (!titulo) {
    Swal.fire("Error", "Debes ingresar un título.", "error");
    return;
  }

  if (isNaN(costo) || costo < 1) {
    Swal.fire(
      "Error",
      "El costo debe ser un número mayor o igual a 1.",
      "error",
    );
    return;
  }

  if (!descripcion) {
    Swal.fire("Error", "Debes ingresar una descripción.", "error");
    return;
  }

  if (!fecha) {
    Swal.fire("Error", "Debes seleccionar una fecha.", "error");
    return;
  }

  if (!tipo) {
    Swal.fire("Error", "Debes seleccionar un tipo.", "error");
    return;
  }

  if (!nombrede) {
    Swal.fire("Error", "Debes seleccionar 'A nombre de'.", "error");
    return;
  }

  // DESHABILITAR BOTÓN
  $("#ingresar").prop("disabled", true);

  // PREPARAR DATOS
  var formElement = document.getElementById("uploadForm");
  var formData = new FormData(formElement);

  formData.append("titulo", titulo);
  formData.append("costo", costo);
  formData.append("descripcion", descripcion);
  formData.append("fecha", fecha);
  formData.append("tipo", tipo);
  formData.append("nombrede", nombrede);
  formData.append("banco", $("#banco-check").is(":checked") ? "1" : "0");

  // Archivo solo si se selecciona
  if (fileInput.files.length > 0) {
    formData.append("file", fileInput.files[0]);
  }

  // ENVIAR POR AJAX
  $.ajax({
    url: "../php/insertGasto.php",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      response = response.trim();
      if (response === "ok") {
        Swal.fire("¡Se ha guardado!", "Haz clic en OK.", "success");
        $("#uploadForm")[0].reset();

        clearFilePreview();

        $("#ingresar").prop("disabled", false);
        $("#div-banco").addClass("hidden");
        $("#banco-check").prop("checked", false);

        let date = moment().format("YYYY-MM-DD");
        document.getElementById("fecha").value = date;
      } else {
        console.log(response);
        Swal.fire("Error", "No se pudo guardar el gasto.", "error");
        $("#ingresar").prop("disabled", false);
      }
    },
    error: function () {
      $("#message").html(
        '<span style="color:red;">Error al enviar los datos.</span>',
      );
      $("#ingresar").prop("disabled", false);
    },
  });
}
function formatFileSize(bytes) {
  if (bytes === 0) return "0 KB";

  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(1024));
  const size = bytes / Math.pow(1024, i);

  return `${size.toFixed(2)} ${sizes[i]}`;
}

function clearFilePreview() {
  const fileInput = document.getElementById("file");

  if (fileInput) {
    fileInput.value = "";
  }

  $("#file-preview").addClass("hidden");
  $("#preview-visual").html("");
  $("#preview-name").text("");
  $("#preview-info").text("");
}

function showFilePreview(file) {
  if (!file) {
    clearFilePreview();
    return;
  }

  const fileName = file.name;
  const fileType = file.type || "Archivo";
  const fileSize = formatFileSize(file.size);

  $("#preview-name").text(fileName);
  $("#preview-info").text(`${fileType} · ${fileSize}`);
  $("#file-preview").removeClass("hidden");

  const previewVisual = $("#preview-visual");
  previewVisual.html("");

  // Si es imagen, mostrar preview real
  if (file.type.startsWith("image/")) {
    const reader = new FileReader();

    reader.onload = function (e) {
      previewVisual.html(`
                <img src="${e.target.result}" 
                     alt="Vista previa" 
                     class="w-full h-full object-cover">
            `);
    };

    reader.readAsDataURL(file);
    return;
  }

  // Si es PDF
  if (file.type === "application/pdf") {
    previewVisual.html(`
            <div class="flex flex-col items-center justify-center text-center px-3">
                <i class="fa-solid fa-file-pdf text-red-300 text-4xl mb-2"></i>
                <span class="text-xs text-cyan-100/60">PDF</span>
            </div>
        `);
    return;
  }

  // Cualquier otro archivo
  previewVisual.html(`
        <div class="flex flex-col items-center justify-center text-center px-3">
            <i class="fa-solid fa-file text-cyan-300 text-4xl mb-2"></i>
            <span class="text-xs text-cyan-100/60">Archivo</span>
        </div>
    `);
}
function limpiarFormularioGasto() {
    $('#uploadForm')[0].reset();

    clearFilePreview();

    $("#div-banco").addClass("hidden");
    $("#banco-check").prop("checked", false);
    $("#costo").removeClass("verde rojo");

    let date = moment().format('YYYY-MM-DD');
    document.getElementById('fecha').value = date;
}
// Cuando el usuario selecciona archivo
$("#file").on("change", function () {
  const file = this.files[0];

  if (!file) {
    clearFilePreview();
    return;
  }

  showFilePreview(file);
});

// Botón eliminar archivo
$("#remove-file").on("click", function () {
  clearFilePreview();
});

$("#tipo").on("change", function () {
  let tipo = $("#tipo").val();

  // Colores en costo según tipo
  if (tipo == "1" || tipo == "4") {
    // Gasto o Banco Gasto
    $("#costo").removeClass("verde").addClass("rojo");
  } else {
    $("#costo").removeClass("rojo").addClass("verde");
  }

  // Mostrar checkbox banco solo en tipos bancarios
  if (tipo == "3" || tipo == "4") {
    // Banco Ingreso o Banco Gasto
    $("#div-banco").removeClass("hidden");
  } else {
    $("#div-banco").addClass("hidden");
    $("#banco-check").prop("checked", false);
  }
});

// Fecha por defecto hoy
let date = moment().format("YYYY-MM-DD");
document.getElementById("fecha").value = date;
