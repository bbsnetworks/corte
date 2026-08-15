let envioEnProceso = false;

document.addEventListener("DOMContentLoaded", function () {
  const formulario = document.getElementById("uploadForm");
  const archivoInput = document.getElementById("file");
  const removerArchivo = document.getElementById("remove-file");
  const tipoSelect = document.getElementById("tipo");

  // Capturar el envío del formulario
  if (formulario) {
    formulario.addEventListener("submit", submitForm);
  }

  // Vista previa del archivo
  if (archivoInput) {
    archivoInput.addEventListener("change", function () {
      const archivo = this.files[0];

      if (!archivo) {
        clearFilePreview();
        return;
      }

      showFilePreview(archivo);
    });
  }

  // Botón para quitar el archivo
  if (removerArchivo) {
    removerArchivo.addEventListener("click", function (event) {
      event.preventDefault();
      clearFilePreview();
    });
  }

  // Cambios según el tipo de movimiento
  if (tipoSelect) {
    tipoSelect.addEventListener("change", actualizarTipoMovimiento);
  }

  // Fecha actual por defecto
  establecerFechaActual();
});

async function submitForm(event) {
  // Evitar el envío tradicional del formulario
  if (event) {
    event.preventDefault();
  }

  // Evitar doble envío aunque se llame dos veces a la función
  if (envioEnProceso) {
    return;
  }

  const formulario = document.getElementById("uploadForm");
  const botonesGuardar = document.querySelectorAll(
  "[data-guardar-movimiento]",
  );
  const fileInput = document.getElementById("file");

  const titulo = document.getElementById("titulo").value.trim();
  const costo = parseFloat(document.getElementById("costo").value);
  const descripcion = document.getElementById("descripcion").value.trim();
  const fecha = document.getElementById("fecha").value;
  const tipo = document.getElementById("tipo").value;
  const nombrede = document.getElementById("nombrede").value;
  const bancoCheck = document.getElementById("banco-check");

  // VALIDACIONES BÁSICAS
  if (!titulo) {
    await Swal.fire("Error", "Debes ingresar un título.", "error");
    return;
  }

  if (isNaN(costo) || costo < 1) {
    await Swal.fire(
      "Error",
      "El costo debe ser un número mayor o igual a 1.",
      "error",
    );
    return;
  }

  if (!descripcion) {
    await Swal.fire("Error", "Debes ingresar una descripción.", "error");
    return;
  }

  if (!fecha) {
    await Swal.fire("Error", "Debes seleccionar una fecha.", "error");
    return;
  }

  if (!tipo) {
    await Swal.fire("Error", "Debes seleccionar un tipo.", "error");
    return;
  }

  if (!nombrede) {
    await Swal.fire("Error", "Debes seleccionar 'A nombre de'.", "error");
    return;
  }

  // Bloquear inmediatamente cualquier otro envío
  envioEnProceso = true;

  botonesGuardar.forEach((boton) => {
  boton.disabled = true;
  boton.setAttribute("aria-busy", "true");
  });

  try {
    const formData = new FormData(formulario);

    // set() evita que el mismo campo se agregue dos veces
    formData.set("titulo", titulo);
    formData.set("costo", costo.toString());
    formData.set("descripcion", descripcion);
    formData.set("fecha", fecha);
    formData.set("tipo", tipo);
    formData.set("nombrede", nombrede);
    formData.set("banco", bancoCheck?.checked ? "1" : "0");

    // Archivo solo si fue seleccionado
    if (fileInput?.files.length > 0) {
      formData.set("file", fileInput.files[0]);
    } else {
      formData.delete("file");
    }

    const respuesta = await fetch("../php/insertGasto.php", {
      method: "POST",
      body: formData,
    });

    const textoRespuesta = (await respuesta.text()).trim();

    if (!respuesta.ok) {
      throw new Error(
        `Error HTTP ${respuesta.status}: ${textoRespuesta}`,
      );
    }

    if (textoRespuesta === "ok") {
      limpiarFormularioGasto();

      await Swal.fire(
        "¡Se ha guardado!",
        "El movimiento fue registrado correctamente.",
        "success",
      );
    } else {
      console.error("Respuesta del servidor:", textoRespuesta);

      await Swal.fire(
        "Error",
        "No se pudo guardar el movimiento.",
        "error",
      );
    }
  } catch (error) {
    console.error("Error al guardar el movimiento:", error);

    const mensaje = document.getElementById("message");

    if (mensaje) {
      mensaje.innerHTML =
        '<span style="color:red;">Error al enviar los datos.</span>';
    }

    await Swal.fire(
      "Error de conexión",
      "No fue posible enviar los datos. Inténtalo nuevamente.",
      "error",
    );
  } finally {
    // Habilitar nuevamente, sin importar si hubo éxito o error
    envioEnProceso = false;

    botonesGuardar.forEach((boton) => {
      boton.disabled = false;
      boton.removeAttribute("aria-busy");
    });
  }
}

function formatFileSize(bytes) {
  if (bytes === 0) {
    return "0 KB";
  }

  const sizes = ["Bytes", "KB", "MB", "GB"];
  const indice = Math.floor(Math.log(bytes) / Math.log(1024));
  const tamaño = bytes / Math.pow(1024, indice);

  return `${tamaño.toFixed(2)} ${sizes[indice]}`;
}

function clearFilePreview() {
  const fileInput = document.getElementById("file");
  const filePreview = document.getElementById("file-preview");
  const previewVisual = document.getElementById("preview-visual");
  const previewName = document.getElementById("preview-name");
  const previewInfo = document.getElementById("preview-info");

  if (fileInput) {
    fileInput.value = "";
  }

  filePreview?.classList.add("hidden");

  if (previewVisual) {
    previewVisual.innerHTML = "";
  }

  if (previewName) {
    previewName.textContent = "";
  }

  if (previewInfo) {
    previewInfo.textContent = "";
  }
}

function showFilePreview(file) {
  if (!file) {
    clearFilePreview();
    return;
  }

  const previewVisual = document.getElementById("preview-visual");
  const previewName = document.getElementById("preview-name");
  const previewInfo = document.getElementById("preview-info");
  const filePreview = document.getElementById("file-preview");

  if (!previewVisual) {
    return;
  }

  const fileName = file.name;
  const fileType = file.type || "Archivo";
  const fileSize = formatFileSize(file.size);

  if (previewName) {
    previewName.textContent = fileName;
  }

  if (previewInfo) {
    previewInfo.textContent = `${fileType} · ${fileSize}`;
  }

  filePreview?.classList.remove("hidden");
  previewVisual.innerHTML = "";

  // Si es una imagen, mostrar la vista previa
  if (file.type.startsWith("image/")) {
    const reader = new FileReader();

    reader.onload = function (evento) {
      const imagen = document.createElement("img");

      imagen.src = evento.target.result;
      imagen.alt = "Vista previa";
      imagen.className = "w-full h-full object-cover";

      previewVisual.innerHTML = "";
      previewVisual.appendChild(imagen);
    };

    reader.readAsDataURL(file);
    return;
  }

  // Si es PDF
  if (file.type === "application/pdf") {
    previewVisual.innerHTML = `
      <div class="flex flex-col items-center justify-center text-center px-3">
        <i class="fa-solid fa-file-pdf text-red-300 text-4xl mb-2"></i>
        <span class="text-xs text-cyan-100/60">PDF</span>
      </div>
    `;
    return;
  }

  // Cualquier otro archivo
  previewVisual.innerHTML = `
    <div class="flex flex-col items-center justify-center text-center px-3">
      <i class="fa-solid fa-file text-cyan-300 text-4xl mb-2"></i>
      <span class="text-xs text-cyan-100/60">Archivo</span>
    </div>
  `;
}

function limpiarFormularioGasto() {
  const formulario = document.getElementById("uploadForm");
  const divBanco = document.getElementById("div-banco");
  const bancoCheck = document.getElementById("banco-check");
  const costoInput = document.getElementById("costo");
  const mensaje = document.getElementById("message");

  formulario?.reset();

  clearFilePreview();

  divBanco?.classList.add("hidden");

  if (bancoCheck) {
    bancoCheck.checked = false;
  }

  costoInput?.classList.remove("verde", "rojo");

  if (mensaje) {
    mensaje.innerHTML = "";
  }

  establecerFechaActual();
}

function actualizarTipoMovimiento() {
  const tipoSelect = document.getElementById("tipo");
  const costoInput = document.getElementById("costo");
  const divBanco = document.getElementById("div-banco");
  const bancoCheck = document.getElementById("banco-check");

  if (!tipoSelect) {
    return;
  }

  const tipo = tipoSelect.value;

  // Colores en costo según tipo
  if (tipo === "1" || tipo === "4") {
    // Gasto o Banco Gasto
    costoInput?.classList.remove("verde");
    costoInput?.classList.add("rojo");
  } else {
    costoInput?.classList.remove("rojo");
    costoInput?.classList.add("verde");
  }

  // Mostrar checkbox banco solo en tipos bancarios
  if (tipo === "3" || tipo === "4") {
    // Banco Ingreso o Banco Gasto
    divBanco?.classList.remove("hidden");
  } else {
    divBanco?.classList.add("hidden");

    if (bancoCheck) {
      bancoCheck.checked = false;
    }
  }
}

function establecerFechaActual() {
  const fechaInput = document.getElementById("fecha");

  if (!fechaInput) {
    return;
  }

  const hoy = new Date();
  const año = hoy.getFullYear();
  const mes = String(hoy.getMonth() + 1).padStart(2, "0");
  const dia = String(hoy.getDate()).padStart(2, "0");

  fechaInput.value = `${año}-${mes}-${dia}`;
}