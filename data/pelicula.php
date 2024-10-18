<?php

require_once 'database.php';
require_once 'validator.php';
require_once 'validatorexception.php';

class Pelicula
{
    private Database $db;


    public function __construct()
    {
        $this->db = new Database();
    }
    public function getAll()
    {
        $resultado = $this->db->query("SELECT id, titulo, precio, id_director FROM pelicula;");
        return $resultado->fetch_All(MYSQLI_ASSOC);
    }
    public function getById($id)
    {
        $resultado = $this->db->query("SELECT id, titulo, precio, id_director FROM pelicula WHERE id = ?", [$id]);
        return $resultado->fetch_assoc();
    }

    public function crearPelicula($titulo, $precio, $id_director)
    {
        $data = ['titulo' => $titulo, 'precio' => $precio, 'id_director'=> $id_director];
        $dataSaneados = Validator::sanear($data);
        $errors = Validator::validarPelicula($dataSaneados);

        if (!empty($errors)) {
           return $errors;
        }
        $tituloSaneado = $dataSaneados['titulo'];
        $precioSaneado = $dataSaneados['precio'];
        $id_directorSaneado = $dataSaneados['id_director'];

        $resultado = $this->db->query("SELECT id FROM director WHERE id = ?", [$id_directorSaneado]);
        if ($resultado->num_rows == 0) {
            return ["director" => "No existe ningun director"];
        }
        //lanzamos la consulta
        $this->db->query("INSERT INTO  pelicula (titulo, precio, id_director) VALUES (?, ?, ?)", [$tituloSaneado, $precioSaneado, $id_directorSaneado]);

        return $this->db->query("SELECT LAST_INSERT_ID() as id")->fetch_assoc()['id'];

    }
    public function updatePelicula($id, $titulo, $precio, $id_director)
    {
        $data = ['id' => $id, 'titulo' => $titulo, 'precio' => $precio, 'id_director'=> $id_director];
        $dataSaneados = Validator::sanear($data);
        $errors = Validator::validarPelicula($dataSaneados);

        if (!empty($errors)) {
            return $errors;
        }

        $tituloSaneado = $dataSaneados['titulo'];
        $precioSaneado = $dataSaneados['precio'];
        $id_directorSaneado = $dataSaneados['id_director'];
        $idSaneado = $dataSaneados['id'];
    
        $resultado = $this->db->query("SELECT id FROM director WHERE id = ?", [$id_directorSaneado]);
        if ($resultado->num_rows == 0) {
            return ["director" => "No existe ningun director"];
        }

        $this->db->query("UPDATE pelicula SET titulo = ?, precio = ?, id_director = ? WHERE id = ?", [$tituloSaneado, $precioSaneado, $id_directorSaneado,$idSaneado]);
        return $this->db->query("SELECT ROW_COUNT() as affected")->fetch_assoc()['affected'];

    }
    public function delete($id): mixed
    {
        $this->db->query('DELETE FROM pelicula WHERE id = ?', [$id]);
        return $this->db->query("SELECT ROW_COUNT() as affected")->fetch_assoc()['affected'];

    }
















}