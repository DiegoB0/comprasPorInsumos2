<?php

    require_once('../db/db.php');

    class Proveedores
    {

        function __construct()
        {
            
        }

        function proveedores_reporte()
        {

            $conexion = new Conexion();
            $db = $conexion->con();

            try
            {
                $query = $db->prepare("SELECT cm.idcompra,
                p.nombre,
                c.fechaaplicacion,
                cm.idinsumo,
                i.descripcion,
                cm.costo,
                cm.cantidad,
                i.unidad
                FROM comprasmovtos cm
                LEFT JOIN compras c ON c.idcompra = cm.idcompra
                LEFT JOIN insumos i ON i.idinsumo = cm.idinsumo
                LEFT JOIN proveedores p ON c.idproveedor = p.idproveedor
                ORDER BY cm.idcompra ASC");

                $query->execute();

                $fila = $query->fetchAll();

                return $fila;

            } catch (PDOException $e) {
                echo "Error en la consulta->" . $e;
              }

        }

    }

?>