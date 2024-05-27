function initializeLoadingScreen() {
    console.log("loading.js is loaded");  // Log para confirmar que el archivo se carga
    alert("hola");
    const loadingDiv = "<div id='loading' style='width: 1175px; height: 60vh; background: white; text-align: center;'><img src='/image/elephant.webp' alt='Loading...'></div>";

    $('.contenido').before(loadingDiv);
    $('.contenido').css('display', 'none');

    setTimeout(function() {
        $('#loading').fadeOut('slow', function() {
            $('.contenido').fadeIn('slow');
        });
    }, 2000);

    
}