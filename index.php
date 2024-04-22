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
    $app->setBasePath("/WS/Practicas/Proyectop1/Proyecto");


    $app->get('/', function($request, $response, $args){
        // Genera el HTML para el botón de redirección
        $buttonHtml = '<form action="http://localhost:8080/WS/Practicas/Proyectop1/Proyecto/formularioAutenticacionPost.html" method="GET">
        <button type="submit">Iniciar Sesion</button>
        </form>';

        // Agrega el botón al cuerpo de la respuesta
        $response->getBody()->write($buttonHtml);

        return $response;
    });

    $app->post('/pruebapost', function($request, $response, $args){
        $reqPost = $request->getParsedBody();
        $val1 = $reqPost['val1'];
        $val2 = $reqPost['val2'];

        $response->write('Valores: '.$val1." ".$val2);
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
                "code" => 000,
                "message" => "Se necesita un usuario",
                "status" => "error"
            );
            
            return $resp;
        }

        if($pass == ''){
            $resp = array(
                "code" => 000,
                "message" => "Se necesita una contraseña",
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

    function Categoria($user, $pass, $categoria) {

        global $firebase;
        $categoria = strtolower($categoria);

        $RAuntenticar = Autenticar($user, $pass);

        $status = $RAuntenticar['status'];

        if($status == "OK"){
            if($firebase->isCategoryInDB($categoria)){
                $resp = array(
                    'code' =>500,
                    'message' => $firebase->obtainMessage(500),
                    'data' => $firebase->obtainProducts($categoria),
                    'status' => 'OK'
                    );
                return $resp;
            }
            $resp = array(
                'code' =>300,
                'message' => $firebase->obtainMessage(300),
                'data' => "",
                'status' => "error"
                );
            return $resp;
        }
        $resp = array(
            'code' =>$RAuntenticar['code'],
            'message' => $RAuntenticar['message'],
            'data' => "",
            'status' => "error"
            );
        return $resp;
    };

    //Productos por Detalles

    function Detalles($user, $pass, $clave) {

        global $firebase;

        $RAuntenticar = Autenticar($user, $pass);

        $status = $RAuntenticar['status'];

        if($status == "OK"){
            if($firebase->isLsbnInDB($clave)){
                $detalles=$firebase->obtainDetails($clave);
                $resp = array(
                    'code' =>500,
                    'message' => $firebase->obtainMessage(500),
                    'data' => $detalles,
                    'status' => 'OK',
                    'oferta' => ""
                    );
                return $resp;
            }
            $resp = array(
                'code' =>301,
                'message' => $firebase->obtainMessage(301),
                'data' => "",
                'status' => "error",
                'oferta' => ""
                );
            return $resp;
        }
        $resp = array(
            'code' =>$RAuntenticar['code'],
            'message' => $RAuntenticar['message'],
            'data' => "",
            'status' => "error",
            'oferta' => ""
            );
        return $resp;
    };


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

        $user = $request->getHeader('user')[0];
        $pass = $request->getHeader('pass')[0];

        $RespuestaT1 = Autenticar($user, $pass);

        $response->write(json_encode($RespuestaT1, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response;
    });

    //Operacion 1
    $app->get('/productos[/{categoria}]', function($request, $response, $args){

        $user = $request->getHeader('user')[0];
        $pass = $request->getHeader('pass')[0];

        $categoria = $args["categoria"];

        $respuesta = Categoria($user, $pass, $categoria);        

        $response->write(json_encode($respuesta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response;
    });

    //Operacion 2
    $app->get('/detalles[/{clave}]', function($request, $response, $args){

        $user = $request->getHeader('user')[0];
        $pass = $request->getHeader('pass')[0];

        $clave = $args["clave"];

        $respuesta = Detalles($user, $pass, $clave);        

        $response->write(json_encode($respuesta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response;
    });


    function($detalles){
        global $firebase;

    };
    //Operacion 3

    $app->post('/producto[/categoria]', function($request, $response, $args){
        

        $response->write(json_encode($RespuestaT1, JSON_PRETTY_PRINT));
        return $response;
    });

    $app->run();
?>