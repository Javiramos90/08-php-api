<?php

require_once 'config.php';

class Database 
{
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        if ($this->conn->connect_error) {
            die('Error de conexion: '. $this->conn->connect_error);
        }
    }

    public function query($sql, $params = [])
    {
        $estamento = $this->conn->prepare($sql);
        if ($estamento === false) 
        {
            die('Error en la preparación: '. $this->conn->error);
        }
        if(!empty($params))
        {
            //Count($params) cuenta los parametros que hay en el array.
            //str_repeat('s', count($params)) crea una cadena con tantas 's' como parametros hay
            //'s' indica que todos los parametros seran tratados como strings
            $types = str_repeat('s', count($params));
            //añade los parametros a la consulta
            //$types es la cadena de tipos que acabamos de crear
            //...$params es el operador de expansion que desempaqueta el array $params en argumentos individuales
            $estamento->bind_param($types, ...$params); 
        }
        //ejecuta la consulta
        $estamento->execute();
        return $estamento->get_result();
    }
    public function close()
    {
        $this->conn->close();
    }
}

$conexion = new Database();