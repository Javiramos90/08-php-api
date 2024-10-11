<?php

class Validator
{
    public static function sanear($datos)
    {
        $saneado = [];
        foreach ($datos as $key => $value) {
            $saneado[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
        }
        return $saneado;
    }
    public static function validarUsuario($data)
    {
        $errors = [];

        //validar nombre
        if (!isset($data['nombre']) || empty(trim($data['nombre']))) {
            $errors['nombre'] = "El nombre es necesario";
        } elseif (strlen($data['nombre']) < 2 || strlen($data['nombre']) > 50) {
            $errors['nombre'] = "El nombre debe tener entre 2 y 50 caracteres";
        } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ' -]+$/u", $data["nombre"])) {
            $errors['nombre'] = "El nombre solo debe contener letras y espacios";
        }

        //validar correo
        if (!isset($data['email']) || empty(trim($data['email']))) {
            $errors['email'] = "El email es necesario";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "El formato de email no es valido";
        } elseif (strlen($data['email']) > 255) {
            $errors['email'] = "El email no puede exceder de 255 caracteres";
        }

        return $errors;

    }

    public static function validarPelicula($data)
    {
        $errors = [];
        // validar titulo
        if (!isset($data['titulo']) || empty(trim($data['titulo']))) {
            $errors['titulo'] = "El titulo es necesario";

        } elseif (strlen($data['titulo']) < 2 || strlen($data['titulo']) > 70) {
            $errors['titulo'] = "El titulo debe tener entre 2 y 70 caracteres";

        }//elseif (!preg_match("^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ'’“,.:\-\s]+$", $data['titulo']))
        // {
        //     $errors['titulo'] = '';
        // }
        // validar precio
        if (!isset($data['precio']) || empty(trim($data['precio']))) {
            $errors['precio'] = "El precio es necesario";
        } elseif ($data['precio'] < 0) {
            $errors['precio'] = "El precio debe ser mayor de 0";
        }
        if (!isset($data['id_director']) || empty(trim($data['id_director']))) {
            $errors['id_director'] = "El id del director es necesario";
           
        }
        return $errors;
    }
    public static function esFormatoFecha($string, $formato = 'Y-m-d'){
        $fecha = Datetime::createFromFormat($formato, $string);
        return $fecha && $fecha->format($formato) == $string;
    }
    public static function validarDirector($data)
    {
        $errors = [];
        // validar nombre  biografia
        if(!isset($data['nombre']) || empty(trim($data['nombre']))){
            $errors['nombre'] = 'El nombre del director es necesario';
        }elseif(strlen($data['nombre']) < 2 || strlen(trim($data['nombre'])) > 30){
            $errors['nombre'] = 'El nombre del director debe contener entre 2 y 30 caracteres';
        }
        // validar apellido
        if(!isset($data['apellido']) || empty(trim($data['apellido']))){
            $errors['apellido'] = 'El apellido del director es necesario';
        }elseif(strlen($data['apellido']) < 2 || strlen(trim($data['apellido'])) > 30){
            $errors['apellido'] = 'El apellido del director debe contener entre 2 y 30 caracteres';
        }
        // validar fecha de nacimiento
        if(!isset($data['f_nacimiento']) && !self::esFormatoFecha($data['f_nacimiento'])){
            $errors['f_nacimiento'] = 'El formato de la fecha no es valido';
        }
        //validar biografia
        if(!isset($data['biografia']))  
        {
            $errors['biografia'] = 'La biografia es necesaria';
        }elseif(strlen($data['biografia']) > 10000){
        
            $errors['biografia'] = 'La biografia es muy extensa';
        }
        return $errors;

    }


}