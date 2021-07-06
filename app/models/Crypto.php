<?php

class Crypto
{
    public $id;
    public string $nombre;
    public float $precio;
    public string $foto;
    public string $nacionalidad;

    public function crear()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO e_crypto (nombre, precio, foto, nacionalidad) 
        VALUES (:nombre, :precio, :foto, :nacionalidad)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->bindValue(':nacionalidad', $this->nacionalidad, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM e_crypto");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Crypto');
    }

    public static function obtenerCrypto($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM e_crypto WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Crypto');
    }

    public static function obtenerSegunNacionalidad($nacionalidad)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM e_crypto WHERE nacionalidad = :nacionalidad");
        $consulta->bindValue(':nacionalidad', $nacionalidad, PDO::PARAM_STR);
        $consulta->execute();

        return  $consulta->fetchAll(PDO::FETCH_CLASS, 'Crypto');
    }

    public static function modificar(Crypto $crypto)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE e_crypto SET precio = :precio WHERE id = :id");
        $consulta->bindValue(':precio', $crypto->precio, PDO::PARAM_INT);
        $consulta->bindValue(':id', $crypto->id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrar($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM e_crypto WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }


}