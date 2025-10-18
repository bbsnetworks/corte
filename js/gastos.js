function submitForm() {
    let titulo = $('#titulo').val().trim();
    let costo = parseFloat($('#costo').val());
    let descripcion = $('#descripcion').val().trim();
    let fecha = $('#fecha').val();
    let tipo = $('#tipo').val();
    let nombrede = $('#nombrede').val();
    let fileInput = $('#file')[0];

    // VALIDACIONES BÁSICAS
    if (!titulo) {
        Swal.fire("Error", "Debes ingresar un título.", "error");
        return;
    }

    if (isNaN(costo) || costo < 1) {
        Swal.fire("Error", "El costo debe ser un número mayor o igual a 1.", "error");
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
    $("#ingresar").prop('disabled', true);

    // PREPARAR DATOS
    var formElement = document.getElementById("uploadForm");
    var formData = new FormData(formElement);

    formData.append('titulo', titulo);
    formData.append('costo', costo);
    formData.append('descripcion', descripcion);
    formData.append('fecha', fecha);
    formData.append('tipo', tipo);
    formData.append('nombrede', nombrede);
    formData.append('banco', $("#banco-check").is(":checked") ? '1' : '0');

    // Archivo solo si se selecciona
    if (fileInput.files.length > 0) {
        formData.append('file', fileInput.files[0]);
    }

    // ENVIAR POR AJAX
    $.ajax({
        url: '../php/insertGasto.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            response = response.trim();
            if (response === "ok") {
                Swal.fire("¡Se ha guardado!", "Haz clic en OK.", "success");
                $('#uploadForm')[0].reset();
                $("#ingresar").prop('disabled', false);
                $("#div-banco").addClass("hidden"); // Ocultamos el check otra vez
            } else {
                console.log(response);
                Swal.fire("Error", "No se pudo guardar el gasto.", "error");
                $("#ingresar").prop('disabled', false);
            }
        },
        error: function () {
            $('#message').html('<span style="color:red;">Error al enviar los datos.</span>');
            $("#ingresar").prop('disabled', false);
        }
    });
}



$("#tipo").on("change", function() {
    let tipo = $("#tipo").val();

    // Colores en costo según tipo
    if (tipo == "1" || tipo == "4") { // Gasto o Banco Gasto
        $("#costo").removeClass("verde").addClass("rojo");
    } else {
        $("#costo").removeClass("rojo").addClass("verde");
    }

    // Mostrar checkbox banco solo en tipos bancarios
    if (tipo == "3" || tipo == "4") { // Banco Ingreso o Banco Gasto
        $("#div-banco").removeClass("hidden");
    } else {
        $("#div-banco").addClass("hidden");
        $("#banco-check").prop("checked", false);
    }
});

// Fecha por defecto hoy
let date = moment().format('YYYY-MM-DD');
document.getElementById('fecha').value = date;