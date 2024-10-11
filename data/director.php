<?php

require_once 'database.php';
require_once 'validator.php';
require_once 'validatorexception.php';

class Director
{
    private Database $db;


    public function __construct()
    {
        $this->db = new Database();
    }
    public function getAll()
    {
        $resultado = $this->db->query("SELECT id, nombre, apellido, f_nacimiento, biografia FROM director;");
        return $resultado->fetch_All(MYSQLI_ASSOC);
    }
    public function getById($id)
    {
        $resultado = $this->db->query("SELECT id, nombre, apellido, f_nacimiento, biografia FROM director WHERE id = ?", [$id]);
        return $resultado->fetch_assoc();
    }

    public function crearDirector($nombre, $apellido, $f_nacimiento, $biografia)
    {
        $data = ['nombre' => $nombre, 'apellido' => $apellido, 'f_nacimiento'=> $f_nacimiento, 'biografia'=> $biografia];
        $dataSaneados = Validator::sanear($data);
        $errors = Validator::validarDirector($dataSaneados);

        if (!empty($errors)) {
            // throw new ValidatorException($errors);
            $errors = new ValidatorException($errors);
            return $errors->getErrors();
        }
        $nombreSaneado = $dataSaneados['nombre'];
        $apellidoSaneado = $dataSaneados['apellido'];
        $f_nacimientoSaneado = $dataSaneados['f_nacimiento'];
        $biografiaSaneado = $dataSaneados['biografia'];
        //lanzamos la consulta
        $this->db->query("INSERT INTO  director (nombre, apellido, f_nacimiento, biografia) VALUES (?, ?, ?, ?)", [$nombreSaneado, $apellidoSaneado, $f_nacimientoSaneado, $biografiaSaneado]);

        return $this->db->query("SELECT LAST_INSERT_ID() as id")->fetch_assoc()['id'];

    }
    public function updateDirector($id, $nombre, $apellido, $f_nacimiento, $biografia)
    {
        $data = ['id' => $id, 'nombre' => $nombre, 'apellido' => $apellido, 'f_nacimiento'=> $f_nacimiento, 'biografia'=> $biografia];
        $dataSaneados = Validator::sanear($data);
        $errors = Validator::validarDirector($dataSaneados);

        if (!empty($errors)) {
            // throw new ValidatorException($errors);
            $errors = new ValidatorException($errors);
            return $errors->getErrors();
        }

        $nombreSaneado = $dataSaneados['nombre'];
        $apellidoSaneado = $dataSaneados['apellido'];
        $f_nacimientoSaneado = $dataSaneados['f_nacimiento'];
        $idSaneado = $dataSaneados['id'];
        $biografiaSaneado = $dataSaneados['biografia'];
    

        $this->db->query("UPDATE director SET nombre = ?, apellido = ?, f_nacimiento = ?, biografia = ? WHERE id = ?", [$nombreSaneado, $apellidoSaneado, $f_nacimientoSaneado, $biografiaSaneado, $idSaneado]);
        return $this->db->query("SELECT ROW_COUNT() as affected")->fetch_assoc()['affected'];


    }
    public function delete($id): mixed
    {
        $this->db->query('DELETE FROM director WHERE id = ?', [$id]);
        return $this->db->query("SELECT ROW_COUNT() as affected")->fetch_assoc()['affected'];

    }

}