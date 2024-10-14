<?php

require_once 'database.php';
require_once 'validator.php';
require_once 'validatorexception.php';

class Usuario
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }
    public function getAll()
    {
        $result = $this->db->query("SELECT id, nombre, email FROM usuario;");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id)
    {
        $result = $this->db->query("SELECT id, nombre, email FROM usuario WHERE id = ?", [$id]);
        return $result->fetch_assoc();
    }

    public function create($nombre, $email)
    {
        $data = ['nombre' => $nombre, 'email' => $email];
        $dataSaneados = Validator::sanear($data);
        $errors = Validator::validarUsuario($dataSaneados);

        if (!empty($errors)) {
            $erroresString = '';
            if (!empty($errors['nombre'])){
                $erroresString .= $errors['nombre'] . ' ';
            }
            if (!empty($errors['email'])){
                $erroresString .= $errors['email'] . ' ';
            }
            
            return $erroresString;
                // throw new ValidatorException($errors);
                // $errores = new ValidatorException($errors);
                // return $errores->getErrors();
        }
        $nombreSaneado = $dataSaneados['nombre'];
        $emailSaneado = $dataSaneados['email'];

        $result = $this->db->query("SELECT id FROM usuario WHERE email = ?", [$emailSaneado]);
        if ($result->num_rows > 0) {
            return "El email ya existe";
        }
        //lanzamos la consulta
        $this->db->query("INSERT INTO  usuario (nombre, email) VALUES (?, ?)", [$nombreSaneado, $emailSaneado]);

        return $this->db->query("SELECT LAST_INSERT_ID() as id")->fetch_assoc()['id'];

    }

    public function update($id, $nombre, $email)
    {
        $data = ['id' => $id, 'nombre' => $nombre, 'email' => $email];
        $dataSaneados = Validator::sanear($data);
        $errors = Validator::validarUsuario($dataSaneados);

        if (!empty($errors)) {
            $erroresString = '';
            if (!empty($errors['nombre'])){
                $erroresString .= $errors['nombre'] . ' ';
            }
            if (!empty($errors['email'])){
                $erroresString .= $errors['email'] . ' ';
            }
            
            return $erroresString;
                // throw new ValidatorException($errors);
                // $errores = new ValidatorException($errors);
                // return $errores->getErrors();
        }
        $nombreSaneado = $dataSaneados['nombre'];
        $emailSaneado = $dataSaneados['email'];
        $idSaneado = $dataSaneados['id'];

        $result = $this->db->query("SELECT id FROM usuario WHERE email = ? AND id != ? ", [$emailSaneado, $idSaneado]);

        if ($result->num_rows > 0) {
            return "El email ya esta en uso por otro usuario";
        }
        $this->db->query("UPDATE usuario SET nombre = ?, email = ? WHERE id = ?", [$nombreSaneado, $emailSaneado, $idSaneado]);
        return $this->db->query("SELECT ROW_COUNT() as affected")->fetch_assoc()['affected'];


    }

    public function delete($id): mixed
    {
        $this->db->query('DELETE FROM usuario WHERE id = ?', [$id]);
        return $this->db->query("SELECT ROW_COUNT() as affected")->fetch_assoc()['affected'];

    }
}