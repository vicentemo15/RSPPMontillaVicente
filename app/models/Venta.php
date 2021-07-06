<?php

class Venta
{
    public int $id_crypto;
    public int $id_cliente;
    public string $fecha;
    public int $cantidad_vendida;

    public static function cargarVenta(Venta $venta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO e_venta (id_crypto, id_cliente, fecha_hora, cantidad_vendida) 
        VALUES (:id_crypto, :id_cliente, :fecha, :cantidad)");
        $consulta->bindValue(':id_crypto', $venta->id_crypto, PDO::PARAM_INT);
        $consulta->bindValue(':id_cliente', $venta->id_cliente, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $venta->cantidad, PDO::PARAM_INT);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT ev.id_crypto, ev.id_cliente, ev.fecha_hora as fecha, ev.cantidad_vendida
        FROM e_venta ev");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');
    }
    /** Reportes */

    public static function obtenerTodasLasCryptosAlemanasVendidas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT ev.id_crypto, ev.id_cliente, ev.fecha_hora as fecha, ev.cantidad_vendida
         FROM e_venta ev
        INNER JOIN e_crypto ec on ec.id = ev.id_crypto
        where ec.nacionalidad = 'alemana' AND cast(ev.fecha_hora AS date) BETWEEN '2021-06-10' AND '2021-06-15'");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');
    }

    public static function obtenerVentasMayorImporte()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT ev.id_crypto, ev.id_cliente, ev.fecha_hora as fecha, ev.cantidad_vendida
         FROM e_venta ev
        INNER JOIN (SELECT `id`, MAX(`precio`) FROM `e_crypto` GROUP BY `id` ORDER BY precio DESC LIMIT 1) ec on ec.id = ev.id_crypto");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');
    }

    public static function obtenerMasTransacciones()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT ev.id_crypto, ev.id_cliente, ev.fecha_hora as fecha, ev.cantidad_vendida
         FROM e_venta ev
        INNER JOIN (SELECT `id_crypto`, SUM(`cantidad_vendida`) FROM `e_venta` GROUP BY `id_crypto` ORDER BY cantidad_vendida DESC LIMIT 1) ec on ec.id_crypto = ev.id_crypto");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');
    }

    public static function toString(Venta $venta){
        return 'Id crypto: ' . $venta->id_crypto . ' Id cliente: ' . $venta->id_cliente . ' Fecha: ' . $venta->fecha . ' Cantidad: ' .$venta->cantidad_vendida;
    }

}