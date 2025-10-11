<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('optimizador.php');
    include('../includes/verificator.php');

    if(isset($_GET['create_triggers']) && $_GET['create_triggers'] === $clav){
         $mess = "";
        $con->query("DROP TRIGGER IF EXISTS after_insert_venta");
        $con->query("DROP TRIGGER IF EXISTS after_update_venta");
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

                UPDATE caja
                SET ventas = COALESCE(ventas, 0) + NEW.total,
                descuentos = COALESCE(descuentos, 0) + NEW.descuento,
                ingresos = COALESCE(ingresos, 0) + NEW.total
                WHERE codcaja = NEW.idcaja;

                IF NEW.metodopago = 'efectivo' THEN 
                    UPDATE entidades SET efectivo = COALESCE(efectivo, 0) + NEW.total;
                ELSEIF NEW.metodopago = 'nequi' THEN 
                    UPDATE entidades SET nequi = COALESCE(nequi, 0) + NEW.total;
                ELSEIF NEW.metodopago = 'daviplata' THEN 
                    UPDATE entidades SET daviplata = COALESCE(daviplata, 0) + NEW.total;
                ELSEIF NEW.metodopago = 'bancolombia' THEN 
                    UPDATE entidades SET bancolombia = COALESCE(bancolombia, 0) + NEW.total;
                ELSEIF NEW.metodopago = 'davivienda' THEN 
                    UPDATE entidades SET davivienda = COALESCE(davivienda, 0) + NEW.total;
                ELSEIF NEW.metodopago = 'consignacion' THEN 
                    UPDATE entidades SET consignacion = COALESCE(consignacion, 0) + NEW.total;
                ELSEIF NEW.metodopago = 'otros' THEN 
                    UPDATE entidades SET otros = COALESCE(otros, 0) + NEW.total;
                END IF;

                INSERT INTO movimientos
                (codcaja,tipo,concepto,entidad,valor,sucursal)
                VALUES (NEW.idcaja,'venta',CONCAT(NEW.producto,' vendido por ',NEW.usuario),NEW.metodopago,NEW.total,NEW.sucursal);

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

        $trigger_after_update = "
            CREATE TRIGGER after_update_venta
            AFTER UPDATE ON ventas
            FOR EACH ROW
            BEGIN
                DECLARE diff INT;
                DECLARE v_unidades DECIMAL(10,3);
                DECLARE v_porciones INT;

                -- diferencia de cantidad (antes vs después)
                SET diff = OLD.cantidad - NEW.cantidad;

                IF diff > 0 THEN
                    -- se devolvieron diff unidades
                    SELECT unidades, porciones
                    INTO v_unidades, v_porciones
                    FROM active_products
                    WHERE id_producto = NEW.id_producto
                    AND sucursal = NEW.sucursal
                    LIMIT 1;

                    IF v_porciones > 0 THEN
                        SET v_porciones = v_porciones + diff;
                        SET v_unidades = v_porciones / 8;
                    ELSE
                        SET v_unidades = v_unidades + diff;
                    END IF;

                    UPDATE caja
                    SET ventas = COALESCE(ventas, 0) - OLD.total,
                    descuentos = COALESCE(descuentos, 0) - OLD.descuento,
                    ingresos = COALESCE(ingresos, 0) - OLD.total
                    WHERE codcaja = OLD.idcaja;

                    IF OLD.metodopago = 'efectivo' THEN 
                        UPDATE entidades SET efectivo = COALESCE(efectivo, 0) - OLD.total;
                    ELSEIF OLD.metodopago = 'nequi' THEN 
                        UPDATE entidades SET nequi = COALESCE(nequi, 0) - OLD.total;
                    ELSEIF OLD.metodopago = 'daviplata' THEN 
                        UPDATE entidades SET daviplata = COALESCE(daviplata, 0) - OLD.total;
                    ELSEIF OLD.metodopago = 'bancolombia' THEN 
                        UPDATE entidades SET bancolombia = COALESCE(bancolombia, 0) - OLD.total;
                    ELSEIF OLD.metodopago = 'davivienda' THEN 
                        UPDATE entidades SET davivienda = COALESCE(davivienda, 0) - OLD.total;
                    ELSEIF OLD.metodopago = 'consignacion' THEN 
                        UPDATE entidades SET consignacion = COALESCE(consignacion, 0) - OLD.total;
                    ELSEIF OLD.metodopago = 'otros' THEN 
                        UPDATE entidades SET otros = COALESCE(otros, 0) - OLD.total;
                    END IF;

                    INSERT INTO movimientos
                    (codcaja,tipo,concepto,entidad,valor,sucursal)
                    VALUES (OLD.idcaja,'devolucion',CONCAT('Devolución de ',OLD.producto,' por ',OLD.usuario),OLD.metodopago,OLD.total,OLD.sucursal);

                    UPDATE active_products
                    SET unidades = v_unidades,
                        porciones = v_porciones
                    WHERE id_producto = NEW.id_producto
                    AND sucursal   = NEW.sucursal;

                    -- si vuelve a haber stock, reactivar
                    IF v_unidades > 0 OR v_porciones > 0 THEN
                        UPDATE productos
                        SET estado = 1
                        WHERE id = NEW.id_producto
                        AND sucursal = NEW.sucursal;
                    END IF;
                END IF;
            END
        ";
        if ($con->query($trigger_after_update)) {
            $mess .= "<span>Trigger after_update_venta creado</span><br>";
        } else {
            $mess .= "<span>Error creando after_update_venta: " . $con->error . "</span><br>";
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

                UPDATE caja
                SET ventas = COALESCE(ventas, 0) - OLD.total,
                    descuentos = COALESCE(descuentos, 0) - OLD.descuento,
                    ingresos = COALESCE(ingresos, 0) - OLD.total
                WHERE codcaja = OLD.idcaja;

                IF OLD.metodopago = 'efectivo' THEN 
                    UPDATE entidades SET efectivo = COALESCE(efectivo, 0) - OLD.total;
                ELSEIF OLD.metodopago = 'nequi' THEN 
                    UPDATE entidades SET nequi = COALESCE(nequi, 0) - OLD.total;
                ELSEIF OLD.metodopago = 'daviplata' THEN 
                    UPDATE entidades SET daviplata = COALESCE(daviplata, 0) - OLD.total;
                ELSEIF OLD.metodopago = 'bancolombia' THEN 
                    UPDATE entidades SET bancolombia = COALESCE(bancolombia, 0) - OLD.total;
                ELSEIF OLD.metodopago = 'davivienda' THEN 
                    UPDATE entidades SET davivienda = COALESCE(davivienda, 0) - OLD.total;
                ELSEIF OLD.metodopago = 'consignacion' THEN 
                    UPDATE entidades SET consignacion = COALESCE(consignacion, 0) - OLD.total;
                ELSEIF OLD.metodopago = 'otros' THEN 
                    UPDATE entidades SET otros = COALESCE(otros, 0) - OLD.total;
                END IF;

                INSERT INTO movimientos
                (codcaja,tipo,concepto,entidad,valor,sucursal)
                VALUES (OLD.idcaja,'devolucion',CONCAT('Devolución de ',OLD.producto,' por ',OLD.usuario),OLD.metodopago,OLD.total,OLD.sucursal);

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