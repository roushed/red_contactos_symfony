<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
alert("hola mundo");

$(document).ready(function() {
       // Ocultar el formulario de mensaje al principio
       
       
    // Manejar el clic en los usuarios
    $('.usuario').click(function() {
        const usuarioId = $(this).data('id');
        $('#lista-usuarios').attr('data-id-usuario-seleccionado', usuarioId); // Actualizar el ID del usuario seleccionado
        cargarConversacion(usuarioId);
       
    });

    // Manejar el envío del formulario de mensaje
    $('#form_mensajes').submit(function(event) {
        event.preventDefault(); // Evitar envío normal del formulario
        var usuarioId = $('#lista-usuarios').attr('data-id-usuario-seleccionado'); // Obtener el ID del usuario seleccionado
        $.ajax({
            url: '/enviar_mensaje/' + usuarioId,
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    cargarConversacion(usuarioId);
                    $('#form_mensajes')[0].reset();
                } else {
                    console.error('Error al enviar el mensaje.');
                }
            },
            error: function() {
                console.error('Error al enviar el mensaje.');
            }
        });
    });
    

    function mostrarFormularioMensaje(mostrar) {
        if (mostrar) {
            $('#form_mensajes').show();
        } else {
            $('#form_mensajes').hide();
        }
    }

    // Función para cargar la conversación con el usuario seleccionado
    function cargarConversacion(usuarioId) {
        $.ajax({
            url: '/cargar_conversacion/' + usuarioId,
            method: 'GET',
            success: function(data) {
                $('#conversacion').html(data);
                 mostrarFormularioMensaje(true);
            // Mover el scroll al final del panel derecho
            var panelDerecho = $('#panel-derecho');
            panelDerecho.scrollTop(panelDerecho.prop("scrollHeight"));
            actualizarListaUsuarios();
            },
            error: function() {
                console.error('Error al cargar la conversación.');
            }
        });
    }

    function actualizarListaUsuarios() {
        $.ajax({
            url: '/actualizar_lista_usuarios',
            method: 'GET',
            success: function(data) {
                $('#list_usuarios').html(data);
            },
            error: function() {
                console.error('Error al actualizar la lista de usuarios.');
            }
        });
    }

});

