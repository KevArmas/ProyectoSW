document.addEventListener("DOMContentLoaded", function() {
    var formProductos = document.getElementById("mostrarProductosCategoria");
    var formDetalles = document.getElementById("mostrarDetallesProducto");
    var formAgregarProducto = document.getElementById("insertarProducto"); // Agregado
    var formActualizarDetalles = document.getElementById("actualizarDetalles"); // Agregado
    var formEliminarProducto = document.getElementById("eliminarProducto"); // Agregado
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

    formAgregarProducto.addEventListener("submit", function(event) { // Agregado
        event.preventDefault();

        // Obtener los valores del formulario
        var categoria = document.getElementById("categoria").value;

        var ISBN = document.getElementById("ISBN").value;
        var Autor = document.getElementById("Autor").value;
        var Nombre = document.getElementById("Nombre").value;
        var Editorial = document.getElementById("Editorial").value;
        var Fecha = parseInt(document.getElementById("Fecha").value);
        var Precio = parseFloat(document.getElementById("Precio").value);
        var Descuento = parseFloat(document.getElementById("Descuento").value);

        var Preciofloat = parseFloat(Precio.toFixed(2));
        var Descuentofloat = parseFloat(Descuento.toFixed(2));

        // Crear un objeto con los datos en el formato adecuado
        var data = {
            "ISBN": ISBN.toString(),
            "Autor": Autor.toString(),
            "Nombre": Nombre.toString(),
            "Editorial": Editorial.toString(),
            "Fecha": parseInt(Fecha),
            "Precio": Preciofloat,
            "Descuento": Descuentofloat
        };

        agregarProducto(data, categoria);
    });

    formActualizarDetalles.addEventListener("submit", function(event) { // Agregado
        event.preventDefault();

         // Obtener los valores del formulario
        var ISBN = document.getElementById("ISBN_Actualizar").value;
        var Autor = document.getElementById("Autor_Actualizar").value;
        var Nombre = document.getElementById("Nombre_Actualizar").value;
        var Editorial = document.getElementById("Editorial_Actualizar").value;
        var Fecha = parseInt(document.getElementById("Fecha_Actualizar").value);
        var Precio = parseFloat(document.getElementById("Precio_Actualizar").value);
        var Descuento = parseFloat(document.getElementById("Descuento_Actualizar").value);

        // Crear un objeto con los datos en el formato adecuado

        var Preciofloat = parseFloat(Precio.toFixed(2));
        var Descuentofloat = parseFloat(Descuento.toFixed(2));

        // Crear un objeto con los datos en el formato adecuado
        var data = {
            "ISBN": ISBN.toString(),
            "Autor": Autor.toString(),
            "Nombre": Nombre.toString(),
            "Editorial": Editorial.toString(),
            "Fecha": parseInt(Fecha),
            "Precio": Preciofloat,
            "Descuento": Descuentofloat
        };

        actualizarDetallesProducto(data, ISBN.toString());
    });

    formEliminarProducto.addEventListener("submit", function(event) { // Agregado
        event.preventDefault();

        var ISBN = document.getElementById("ISBN_Eliminar").value;

        eliminarProducto(ISBN);
    });

    function mostrarProductosPorCategoria(categoria) {
        fetch(`./productos/${categoria}`)
            .then(response => response.json())
            .then(data => {
                let formattedResults = ""; // Variable para almacenar los resultados formateados
                for (const [ISBN, Nombre] of Object.entries(data.data)) {
                    formattedResults += `ISBN: ${ISBN}<br>Nombre: ${Nombre}<br>--------------------------------<br>`;
                }
    
                if(data.code == 200){
                    resultados.innerHTML = `Mensaje: Categoria encontrada Exitosamente <br>--------------------------------<br> Productos: <br> ${formattedResults}`;
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
    
    

    function agregarProducto(data, categoria) { // Agregado
        
        fetch(`./producto/${categoria}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if(data.code == 202){
                resultados.textContent = `Mensaje: ${data.message} Fecha: ${data.data}`;
            }
            else{
                resultados.textContent = `Mensaje: ${data.message}`;
            }
        })
        .catch(error => {
            console.error('Error al agregar producto:', error);
            resultados.textContent = "Error al agregar producto.";
        });
        

    }

    function actualizarDetallesProducto(data, ISBN) { // Agregado
        fetch(`./producto/detalles/${ISBN}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if(data.code == 203){
                resultados.textContent = `Mensaje: ${data.message} Fecha: ${data.data}`;
            }
            else{
                resultados.textContent = `Mensaje: ${data.message}`;
            }
        })
        .catch(error => {
            console.error('Error al actualizar detalles del producto:', error);
            resultados.textContent = "Error al actualizar detalles del producto.";
        });
    }

    function eliminarProducto(ISBN) { // Agregado
        fetch(`./producto/${ISBN}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if(data.code == 204){
                resultados.textContent = `Mensaje: ${data.message} Fecha: ${data.data}`;
            }
            else{
                resultados.textContent = `Mensaje: ${data.message}`;
            }
        })
        .catch(error => {
            console.error('Error al eliminar producto:', error);
            resultados.textContent = "Error al eliminar producto.";
        });
    }
});
