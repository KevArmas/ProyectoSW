document.addEventListener("DOMContentLoaded", function() {
    // Selecciona el formulario
    var form = document.querySelector('form');

    // Agrega un evento de escucha para el envío del formulario
    form.addEventListener('submit', function(event) {
        // Evita que se envíe el formulario automáticamente
        event.preventDefault();

        // Obtiene los valores de usuario y contraseña
        var user = document.getElementById('user').value;
        var pass = document.getElementById('pass').value;

        // Crea un objeto FormData para enviar los datos del formulario
        var formData = new FormData();
        formData.append('user', user);
        formData.append('pass', pass);

        // Realiza una solicitud AJAX al script PHP
        var xhr = new XMLHttpRequest();
        xhr.open('GET', './autenticacion?' + new URLSearchParams(formData).toString(), true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {

                    var respuestaServicio = JSON.parse(xhr.responseText);
                    console.log(respuestaServicio);
                    // Accede a las propiedades message y status
                    var message = respuestaServicio.message;
                    var code = respuestaServicio.code;

                    if(code == 200){
                        alert("Inicio de Sesion Correcto");
                        window.location.replace('./Menu.html');
                    }else{
                        alert("---- Estado: Error --- \n--- Mensaje: " + message+" ---");
                    }
                    
                } else {
                    // Maneja el error de la solicitud
                    alert('Error en la solicitud: ' + xhr.status);
                }
            }
        };
        xhr.send();
    });
});
