document.addEventListener("DOMContentLoaded", function() {
    var formProductos = document.getElementById("mostrarProductosCategoria");
    var formDetalles = document.getElementById("mostrarDetallesProducto");
    var resultados = document.getElementById("resultados");

    formProductos.addEventListener("submit", function(event) {
        event.preventDefault(); // Evitar el envío del formulario por defecto
        var categoria = document.getElementById("buscarCategoria").value;
        mostrarProductosPorCategoria(categoria);
    });

    formDetalles.addEventListener("submit", function(event) {
        event.preventDefault(); // Evitar el envío del formulario por defecto
        var clave = document.getElementById("buscarProducto").value;
        mostrarDetallesProducto(clave);
    });

    function mostrarProductosPorCategoria(categoria) {
        fetch(`./productos/${categoria}`)
            .then(response => response.json())
            .then(data => {
                let formattedResults = ""; // Variable para almacenar los resultados formateados
                for (const [ISBN, Nombre] of Object.entries(data.data)) {
                    formattedResults += `ISBN: ${ISBN}<br>Nombre: ${Nombre}<br>--------------------------------`;
                }
    
                if(data.code == 200){
                    resultados.innerHTML = `Mensaje: Categoria encontrada Exitosamente <br>--------------------------------<br> Productos: ${formattedResults}`;
                }
                else{
                    resultados.innerHTML = `Mensaje:  Categoria No encontrada`;
                }
            })
            .catch(error => {
                console.error('Error al obtener los productos:', error);
                resultados.innerHTML = "Error al obtener los productos.";
            });
    }
    
    function mostrarDetallesProducto(clave) {
        fetch(`./detalles/${clave}`)
            .then(response => response.json())
            .then(data => {
                let formattedResults = ""; // Variable para almacenar los resultados formateados
    
                // Iterar sobre los datos recibidos y formatearlos
                for (const [key, value] of Object.entries(data.data)) {
                    if (key === "Descuento") {
                        if (value === true) {
                            formattedResults += "Descuento: Si tiene descuento<br>";
                        } else {
                            formattedResults += "Descuento: No tiene descuento<br>";
                        }
                    } else {
                        formattedResults += `${key}: ${value}<br>`;
                    }
                }
    
                if(data.code == 201){
                    resultados.innerHTML = `Mensaje: Producto encontrado Exitosamente <br>--------------------------------<br>  Detalles: ${formattedResults}`;
                }
                else{
                    resultados.innerHTML = `Mensaje:  Producto No encontrado`;
                }
            })
            .catch(error => {
                console.error('Error al obtener los detalles del producto:', error);
                resultados.innerHTML = "Error al obtener los detalles del producto.";
            });
    }
});
