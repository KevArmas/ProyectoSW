<?php
namespace MyFirebase;

class Firebase
{
    private $project;

    public function __construct($project)
    {
        $this->project = $project;
    }

    public function runCurl($collection, $document)
    {
        $url = 'https://' . $this->project . '.firebaseio.com/' . $collection . '/' . $document . '.json';

        $ch =  curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response);
    }

    public function isUserInDB($name)
    {
        $res = $this->runCurl('usuarios', $name);

        if (!is_null($res)) {
            return true;
        } else {
            return false;
        }
    }

    public function obtainPassword($user){
        $res = $this->runCurl('usuarios', $user);
        return $res;
    }

    public function isCategoryInDB($name){
        $res = $this->runCurl('productos', $name);

        if (!is_null($res)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function obtainProducts($category){
        $res = $this->runCurl('productos', $category);
        return $res;
    }
    
    public function isLsbnInDB($clave){
        $res = $this->runCurl('detalles', $clave);
        
        if (!is_null($res)) {
            return true;
        } else {
            return false;
        }
    }

    public function obtainDetails($isbn){
        $res = $this->runCurl('detalles', $isbn);
        return $res;
    }
    
    public function obtainMessage($code){
        $res = $this->runCurl('respuestas', $code);
        return $res;
    }

    public function InsertProduct($categoria, $id, $nombre){
        $url = 'https://' . $this->project . '.firebaseio.com/productos/'. $categoria .'/' . $id . '.json';
        $data = json_encode($nombre);
        $ch =  curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        $response = curl_exec($ch);
    
        if($response === false) {
            return false;
        } else {
            return date('Y-m-d H:i:s');
        }
    
        curl_close($ch);
    }
    
    public function InsertDetails($categoria, $id, $nombre){
        $url = 'https://' . $this->project . '.firebaseio.com/detalles/'. $id . '.json';
    
        $data = json_encode(array("Titulo" => $nombre));
    
        $ch =  curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        $response = curl_exec($ch);
    
        if($response === false) {
            return false;
        } else {
            return date('Y-m-d H:i:s');
        }
    
        curl_close($ch);
    }
    
    public function UpdateDetails($id, $data){
        // Construir la URL para actualizar los datos del producto
        $url = 'https://' . $this->project . '.firebaseio.com/detalles/' . $id . '.json';

        // Construir los datos en el formato deseado
        $data = json_encode($data);

        // Configurar la solicitud cURL para enviar los datos del producto
        $ch =  curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); // Utilizamos PUT para actualizar el recurso
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Ejecutar la solicitud cURL
        $response = curl_exec($ch);

        // Verificar si la solicitud fue exitosa
        if($response === false) {
            // Si la solicitud falla, puedes manejar el error aquí
            return false;
        } else {
            // Si la solicitud fue exitosa, devolver la respuesta
            return $response;
        }

        // Cerrar la conexión cURL
        curl_close($ch);
    }

    public function deleteProduct($categoria, $id){
        // Construir la URL para eliminar los datos del producto
        $url = 'https://' . $this->project . '.firebaseio.com/productos/'. $categoria .'/' . $id . '.json';
    
        // Configurar la solicitud cURL para eliminar los datos del producto
        $ch =  curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE'); // Utilizamos DELETE para eliminar el recurso
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        // Ejecutar la solicitud cURL
        $response = curl_exec($ch);
    
        // Verificar si la solicitud fue exitosa
        if($response === false) {
            // Si la solicitud falla, puedes manejar el error aquí
            return false;
        } else {
            // Si la solicitud fue exitosa, devolver la respuesta
            return $response;
        }
    
        // Cerrar la conexión cURL
        curl_close($ch);
    }

    public function deleteDetails($id){
        // Construir la URL para eliminar los datos del producto
        $url = 'https://' . $this->project . '.firebaseio.com/detalles/' . $id . '.json';
    
        // Configurar la solicitud cURL para eliminar los datos del producto
        $ch =  curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE'); // Utilizamos DELETE para eliminar el recurso
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        // Ejecutar la solicitud cURL
        $response = curl_exec($ch);
    
        // Verificar si la solicitud fue exitosa
        if($response === false) {
            // Si la solicitud falla, puedes manejar el error aquí
            return false;
        } else {
            // Si la solicitud fue exitosa, devolver la respuesta
            return $response;
        }
    
        // Cerrar la conexión cURL
        curl_close($ch);
    }

}

/*
$project = 'serviciosweb-84c51-default-rtdb';
$firebase = new Firebase($project);

$respuesta = $firebase->obtainMessage('200');
print_r($respuesta);
*/
?>
