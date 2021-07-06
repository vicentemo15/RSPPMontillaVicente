<?php

class Usuario
{
    public int $id;
    public string $mail;
    public string $clave;
    public string $tipo_usuario;

    public static function obtenerUsuario($mail, $tipo_usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM e_usuarios WHERE mail = :mail and tipo_usuario = :tipo_usuario");
        $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipo_usuario', $tipo_usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function obtenerUsuarioPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM e_usuarios WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    /** Reportes */
    public static function obtenerUsuariosCompraronMonedaSegunNombre($nombreMoneda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT eu.* FROM e_venta ev
        INNER JOIN e_usuarios eu on eu.id = ev.id_cliente
        INNER JOIN e_crypto ec on ec.id = ev.id_crypto
        WHERE ec.nombre = :nombreCrypto");
        $consulta->bindValue(':nombreCrypto', $nombreMoneda, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
}