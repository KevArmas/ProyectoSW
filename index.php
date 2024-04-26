<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Factory\AppFactory;
    
    require __DIR__ . '/vendor/autoload.php';
    use MyFirebase\Firebase as Fb;
    require_once 'MyFirebase.php';

    $project = 'proyectosw-70349-default-rtdb';
    $firebase = new Fb($project);

    $app = AppFactory::create();
    $app->setBasePath("/WS/Practicas/Proyecto/ProyectoSW");


    $app->get('/', function($request, $response, $args){
        $response->write("Serviocio SW");
        return $response;
    });

    //--------------------Inicio Proyecto----------------------//

    //-------Funciones-------

    //Autenticacion

    function Autenticar($user, $pass) {

        global $firebase;
       // $categoria = strtolower("usuarios");

        if($user == ''){
            $resp = array(
                'code' =>500,
                'message' =>$firebase->obtainMessage(500),
                "status" => "error"
            );
            
            return $resp;
        }

        if($pass == ''){
            $resp = array(
                'code' => 501,
                'message' => $firebase->obtainMessage(501),
                "status" => "error"
            );
            
            return $resp;
        }

        if($firebase->isUserInDB($user)){
            if ($firebase->obtainPassword($user) == md5($pass)){
                $resp = array(
                    "code" => 200,
                    "message" => "Inicio Correcto",
                    "status" => "OK"
                );
                
                return $resp;


            }else{
                $resp = array(
                    'code' => 501,
                    'message' => $firebase->obtainMessage(501),
                    'status' => 'error'
                    );
                    
                    return $resp;
            }
        }else{
            
            $resp = array(
                'code' =>500,
                'message' =>$firebase->obtainMessage(500),
                'status' => 'error'
                );
            
            return $resp;
        }
    };

    //Productos por categoria

    function obtenerListaCategoria($categoria) {

        global $firebase;
        $categoria = strtolower($categoria);

        if($firebase->isCategoryInDB($categoria)){
            $resp = array(
                'code' =>200,
                'message' => $firebase->obtainMessage(200),
                'data' => $firebase->obtainProducts($categoria),
                'status' => 'OK'
                );
            return $resp;
        }
        else{
            $resp = array(
                'code' =>300,
                'message' => $firebase->obtainMessage(300),
                'data' => "",
                'status' => "error"
                );
            return $resp;
        }
        return $resp;
    };

    //Productos por Detalles

    function obtenerDetalles($clave) {

        global $firebase;

        if($firebase->isLsbnInDB($clave)){
            $detalles=$firebase->obtainDetails($clave);
            $resp = array(
                'code' =>201,
                'message' => $firebase->obtainMessage(201),
                'data' => $detalles,
                'status' => 'OK',
                'oferta' => ""
                );
        }
        else{
            $resp = array(
                'code' =>301,
                'message' => $firebase->obtainMessage(301),
                'data' => "",
                'status' => 'error',
                'oferta' => ""
                );
        }
            return $resp;
    };
    //Saber que categoria es con el ISBN
    function saberCategoria($clave){
        $claveSinNumeros = preg_replace('/\d/', '', $clave);

        // Convertir a mayúsculas
        $claveEnMayusculas = strtoupper($claveSinNumeros);

        if($claveEnMayusculas == "LIB"){
            return "libros";
        }

        if($claveEnMayusculas == "COM"){
            return "comics";
        }

        if($claveEnMayusculas == "MAN"){
            return "mangas";
        }

        return "Error";
    }

    //Operacion 0 con post

    $app->post('/autenticacionPost', function($request, $response, $args){
        $reqPost = $request->getParsedBody();
        $user = $reqPost['user'];
        $pass = $reqPost['pass'];

        $RespuestaT1 = Autenticar($user, $pass);

        $response->write(json_encode($RespuestaT1, JSON_PRETTY_PRINT));
        return $response;
    });

    //Operacion 0 con get

    $app->get('/autenticacion', function($request, $response, $args){

        if(empty($request->getHeader('user')[0])){
            $user = $request->getQueryParams()['user'] ?? null;
            $pass = $request->getQueryParams()['pass'] ?? null;
        }else{
            $user = $request->getHeader('user')[0];
            $pass = $request->getHeader('pass')[0];
        }

        $RespuestaT1 = Autenticar($user, $pass);

        $response->write(json_encode($RespuestaT1, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response;
    });

    //Operacion 1 Obtener lista de Productos por categoria
    $app->get('/productos[/{categoria}]', function($request, $response, $args){

        $categoria = $args["categoria"];

        $respuesta = obtenerListaCategoria($categoria);        

        $response->write(json_encode($respuesta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response;
    });

    //Operacion 2
    $app->get('/detalles[/{clave}]', function($request, $response, $args){

        $clave = $args["clave"];

        $respuesta = obtenerDetalles($clave);        

        $response->write(json_encode($respuesta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response;
    });


    //Operacion 3 Insertar Producto simple con formulario

    $app->post('/productoformulario', function($request, $response, $args){

        global $firebase;

        $reqPost = $request->getParsedBody();
        $categoria = strtolower($reqPost['categoria']);
        $nombre = $reqPost['nombre'];
        $id = $reqPost['id'];

        $data = array(
            "ISBN" => $id,
            "Autor" => "",
            "Nombre" => $nombre,
            "Editorial" => "",
            "Fecha" => 0,
            "Precio" => 0,
            "Descuento" => 0
        );

        $respuesta = $firebase->InsertProduct($categoria, $id, $nombre);
        $respuesta = $firebase->InsertDetails($id, $data);

        $response->write(json_encode($respuesta, JSON_PRETTY_PRINT));
        return $response;
    });


    //validar json obtenido antes de procesarlo

    function validarJsonDetalles($json_data) {
        $expected_keys = array(
            "ISBN",
            "Autor",
            "Nombre",
            "Editorial",
            "Fecha",
            "Precio",
            "Descuento"
        );
        
        // Verificar si el JSON tiene la misma cantidad de claves esperadas
        if (count(array_diff($expected_keys, array_keys($json_data))) !== 0 || count(array_diff(array_keys($json_data), $expected_keys)) !== 0) {
            return false;
        }
        
        $expected_types = array(
            "ISBN" => "string",
            "Autor" => "string",
            "Nombre" => "string",
            "Editorial" => "string",
            "Fecha" => "integer",
            "Precio" => array("double", "integer"),
            "Descuento" => array("double", "integer")
        );
        
        foreach ($expected_types as $key => $expected_type) {
            if (is_array($expected_type)) {
                $valid = false;
                foreach ($expected_type as $type) {
                    if (gettype($json_data[$key]) === $type) {
                        $valid = true;
                        break;
                    }
                }
                if (!$valid) {
                    return false;
                }
            } else {
                if (gettype($json_data[$key]) !== $expected_type) {
                    return false;
                }
            }
        }
        
        return true;
    }
    

    //Insertar el producto en la categoria y con los detalles dados

    function InsertarProducto($categoria, $data){
        global $firebase;

        $isbn = $data["ISBN"];
        $nombre = $data["Nombre"];
        
        $ResultadoValidarJsonDetalles = validarJsonDetalles($data);
        $ResultadoCategoriaExiste = $firebase->isCategoryInDB($categoria);
        $ResultadoIsbnUsable = $firebase->isLsbnInDB($isbn);
        

        if($ResultadoCategoriaExiste){
            if($ResultadoValidarJsonDetalles){

                if($firebase->isLsbnInDB($isbn) == false){

                    $firebase->InsertProduct($categoria, $isbn, $nombre);
                    $firebase->InsertDetails($isbn, $data);
                    
                    $resp = array(
                        'code' =>202,
                        'message' => $firebase->obtainMessage(202),
                        'data' => date('Y-m-d H:i:s'),
                        'status' => 'OK'
                        );
                    return $resp;
                }
                else{
                    $resp = array(
                        'code' =>302,
                        'message' => $firebase->obtainMessage(302),
                        'data' => "",
                        'status' => 'error'
                        );
                }
                    return $resp;
                
            }else{
                $resp = array(
                    'code' =>303,
                    'message' => $firebase->obtainMessage(303),
                    'data' => "",
                    'status' => 'error'
                );
                return $resp;
            }
        }

        $resp = array(
            'code' =>300,
            'message' => $firebase->obtainMessage(300),
            'data' => "",
            'status' => 'error'
        );

        return $resp;
    }
    //Operacion 3 Insertar Producto
    $app->post('/producto[/{categoria}]', function($request, $response, $args){

        $categoria = $args["categoria"];
        $body = $request->getBody()->getContents();
        if(empty($body)) {
            global $firebase;

            $resp = array(
                'code' =>400,
                'message' => $firebase->obtainMessage(400),
                'data' => "",
                'status' => 'error'
                );
            $response->write(json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return $response;
        }

        $data = json_decode($body, true);

        $categoriaOK = saberCategoria($data["ISBN"]);

        if($categoriaOK != $categoria){
            global $firebase;

            $resp = array(
                'code' =>305,
                'message' => $firebase->obtainMessage(305),
                'data' => "",
                'status' => 'error'
                );
            $response->write(json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return $response;
        }

        $respuesta = InsertarProducto($categoria, $data);

        $response->write(json_encode($respuesta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response;
    });

    //Funcion ActualizarProducto
    function ActualizarProducto($clave, $data){
        global $firebase;

        $nombre = $data["Nombre"];
        $categoria = saberCategoria($clave);
        
        $ResultadoValidarJsonDetalles = validarJsonDetalles($data);
        $ResultadoCategoriaExiste = $firebase->isCategoryInDB($categoria);
        

        if($ResultadoCategoriaExiste){
            if($ResultadoValidarJsonDetalles){
                if($firebase->isLsbnInDB($clave)){

                    $firebase->UpdateDetails($clave, $data);
                    $firebase->UpdateName($categoria, $clave, $nombre);

                    $resp = array(
                        'code' =>203,
                        'message' => $firebase->obtainMessage(203),
                        'data' => date('Y-m-d H:i:s'),
                        'status' => 'OK'
                        );
                    return $resp;
                }
                else{
                    $resp = array(
                        'code' =>301,
                        'message' => $firebase->obtainMessage(301),
                        'data' => "",
                        'status' => 'error'
                        );
                }
                    return $resp;
                
            }else{
                $resp = array(
                    'code' =>303,
                    'message' => $firebase->obtainMessage(303),
                    'data' => "",
                    'status' => 'error'
                );
                return $resp;
            }
        }

        $resp = array(
            'code' =>300,
            'message' => $firebase->obtainMessage(300),
            'data' => "",
            'status' => 'error'
        );

        return $resp;
    }

    //operacion para obtener los detalles de un producto con el ISBN

    $app->put('/producto/detalles[/{clave}]', function($request, $response, $args){

        global $firebase;

        $clave = $args["clave"];
    
        $body = $request->getBody()->getContents();
        if(empty($body)) {
            global $firebase;

            $resp = array(
                'code' =>400,
                'message' => $firebase->obtainMessage(400),
                'data' => "",
                'status' => 'error'
                );
            $response->write(json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return $response;
        }

        $data = json_decode($body, true);

        $respuesta = ActualizarProducto($clave, $data);

        $response->write(json_encode($respuesta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response;
    });

    //Eliminar producto con el ISBN dado
    function EliminarProducto($clave){
        global $firebase;
        
        if($firebase->isLsbnInDB($clave)){

            $categoria = saberCategoria($clave);

            $firebase->deleteProduct($categoria, $clave);
            $firebase->deleteDetails($clave);

            $resp = array(
                'code' =>204,
                'message' => $firebase->obtainMessage(204),
                'data' => date('Y-m-d H:i:s'),
                'status' => 'OK'
                );
            return $resp;
        }
        else{
            $resp = array(
                'code' =>301,
                'message' => $firebase->obtainMessage(301),
                'data' => "",
                'status' => 'error'
                );
        }
            return $resp;

        return $resp;
    }

    // operacion para eliminar el producto 

    $app->delete('/producto[/{clave}]', function($request, $response, $args){

        global $firebase;

        $clave = $args["clave"];

        $respuesta = EliminarProducto($clave);

        $response->write(json_encode($respuesta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response;
    });

    

    $app->run();

    //<!--variables de sesion-->
?>

