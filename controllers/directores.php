<?php

require_once '../data/director.php';
require_once 'utilidades.php';

header('Content-Type: application/json');

$director = new Director();
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$parametros = Utilidades::parseUriParameters($uri);
$id = Utilidades::getParameterValue($parametros, 'id');


switch ($method) {
    case 'GET':
        if($id){
            $respuesta = getDirectorById($director, $id);
        }else{
            $respuesta = getAllDirectores($director);
        }
        echo json_encode($respuesta);
    break;
    case 'POST':
        setDirector($director);
    break;
    case 'PUT':
        if($id)
        {
            updateDirector($director, $id);
        }else
        {
            http_response_code(400);
            echo json_encode(['error'=>'ID no proporcionado']);
        }
        break;
    case 'DELETE':
        if($id)
        {
            deleteDirector($director, $id);
        }else
        {
            http_response_code(400);
            echo json_encode(['error'=>'ID no proporcionado']);
        }
        break;
    default:
            http_response_code(405);
            echo json_encode(['error'=>'Metodo no permitido']);


}
function getDirectorById($director, $id)
{
    return $director->getById($id);
}

function getAllDirectores($director){
    return $director->getAll();
}
function setDirector($director){
    $data = json_decode( file_get_contents('php://input'), true );
    
    if(isset($data['nombre']) && isset($data['apellido']) && isset($data['f_nacimiento']) && isset($data['biografia'])){
        $id = $director->crearDirector($data['nombre'], $data['apellido'], $data['f_nacimiento'], $data['biografia']);
        echo json_encode(['id' => $id]);
    }else{
        echo json_encode(['Error' => 'Datos Insuficientes']);
    }
 }
 
 function updateDirector($director, $id){
    $data = json_decode( file_get_contents('php://input'), true );

    
    if(isset($data['nombre']) && isset($data['apellido']) && isset($data['f_nacimiento']) && isset($data['biografia'])){
        $affected = $director->updateDirector($id, $data['nombre'], $data['apellido'], $data['f_nacimiento'], $data['biografia']);
        echo json_encode(['affected' => $affected]);
        }else{
        echo json_encode(['Error'=>'Verifica los datos']);
    }
 }


 function deleteDirector($director, $id){
    $affected = $director->delete($id);
    echo json_encode(['affected'=> $affected]);

 }






