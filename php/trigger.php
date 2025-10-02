<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('optimizador.php');
    include('../includes/verificator.php');

    if(isset($_GET['create_triggers']) && $_GET['create_triggers'] === $clav){
         $mess = "";
        $con->query("DROP TRIGGER IF EXISTS after_insert_venta");
        $con->query("DROP TRIGGER IF EXISTS after_delete_venta");

        $trigger_after_insert = "
            CREATE TRIGGER after_insert_venta
            AFTER INSERT ON ventas
            FOR EACH ROW
            BEGIN
                DECLARE v_unidades DECIMAL(10,3);
                DECLARE v_porciones INT;

                SELECT unidades, porciones
                INTO v_unidades, v_porciones
                FROM active_products
                WHERE id_producto = NEW.id_producto
                AND sucursal   = NEW.sucursal
                LIMIT 1;

                IF v_porciones > 0 THEN
                    SET v_porciones = v_porciones - NEW.cantidad;
                    SET v_unidades = v_porciones / 8;
                ELSE
                    SET v_unidades = v_unidades - NEW.cantidad;
                    SET v_porciones = 0;
                END IF;

                UPDATE active_products
                SET unidades = v_unidades,
                    porciones = v_porciones
                WHERE id_producto = NEW.id_producto
                AND sucursal   = NEW.sucursal;

                IF v_unidades <= 0 OR v_porciones <= 0 THEN
                    UPDATE productos
                    SET estado = 0
                    WHERE id = NEW.id_producto
                    AND sucursal = NEW.sucursal;
                END IF;

                DELETE FROM sell_cart
                WHERE unico = NEW.unico;
            END
        ";

        if ($con->query($trigger_after_insert)) {
            $mess .= "<span>Trigger after_insert_venta creado</span><br>";
        } else {
            $mess .= "<span>Error creando after_insert_venta: " . $con->error . "</span><br>";
        }

        $trigger_after_delete = "
            CREATE TRIGGER after_delete_venta
            AFTER DELETE ON ventas
            FOR EACH ROW
            BEGIN
                DECLARE v_unidades DECIMAL(10,3);
                DECLARE v_porciones INT;

                SELECT unidades, porciones
                INTO v_unidades, v_porciones
                FROM active_products
                WHERE id_producto = OLD.id_producto
                AND sucursal = OLD.sucursal
                LIMIT 1;

                IF v_porciones > 0 THEN
                    SET v_porciones = v_porciones + OLD.cantidad;
                    SET v_unidades = v_porciones / 8;
                ELSE
                    SET v_unidades = v_unidades + OLD.cantidad;
                    SET v_porciones = 0;
                END IF;

                UPDATE active_products
                SET unidades = v_unidades,
                    porciones = v_porciones
                WHERE id_producto = OLD.id_producto
                AND sucursal   = OLD.sucursal;

                IF v_unidades > 0 OR v_porciones > 0 THEN
                    UPDATE productos
                    SET estado = 1
                    WHERE id = OLD.id_producto
                    AND sucursal = OLD.sucursal;
                END IF;
            END
        ";

        if ($con->query($trigger_after_delete)) {
            $mess .= "<span>Trigger after_delete_venta creado</span><br>";
        } else {
            $mess .= "<span>Error creando after_delete_venta: " . $con->error . "</span><br>";
        }

        echo json_encode([
            "status" => "info",
            "title"  => "Triggers",
            "message"=> $mess
        ]);

    }

?>