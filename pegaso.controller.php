<?php

//session_start();
session_cache_limiter('private_no_expire');
require_once('app/model/pegaso.model.php');
require_once('app/fpdf/fpdf.php');
require_once('app/views/unit/commonts/numbertoletter.php');

class pegaso_controller {
    /* Metodo que envía a login */

    var $contexto = "http://SERVIDOR:8081/pegasoFTC/app/";

    function Login() {
        $pagina = $this->load_templateL('Login');
        $html = $this->load_page('app/views/modules/m.login.php');
        $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
        $this->view_page($pagina);
    }

    function Autocomp() {
        $arr = array('prueba1', 'trata2', 'intento3', 'prueba4', 'prueba5');
        echo json_encode($arr);
        exit;
    }

    function LoginA($user, $pass) {
        session_cache_limiter('private_no_expire');
        $data = new pegaso;
        $rs = $data->AccesoLogin($user, $pass);
        //var_dump($rs);
        if (count($rs) > 0) {
            $r = $data->CompruebaRol($user);
            //var_dump($r);
            switch ($r->USER_ROL) {
                case 'administrador':
                    $this->MenuAdmin();
                    break;
                case 'administracion':
                    $this->MenuAd();
                    break;
                case 'usuario':
                    $this->MenuUsuario();
                    break;
                case 'ventas':
                    $this->MenuVentas();
                    break;
                case 'compras':
                    $this->MenuCompras();
                    break;
                case 'tesoreria':
                    $this->MenuTesoreria();
                    break;
                case 'logistica':
                    $this->MenuLogistica();
                    break;
                case 'recibo':
                    $this->MenuRecibo();
                    break;
                case 'bodega':
                    $this->MenuBodega();
                    break;
                case 'glogistica':
                    $this->MenuGLogistica();
                    break;
                case 'cxcr':             //14062016
                    $this->MenuCxCRevision();
                    break;
                case 'cxcc':
                    $this->MenuCxCCobranza();
                    break;
                case 'contabilidad':
                    $this->MenuContabilidad();
                    break;
                case 'embarques':
                    $this->MenuEmbarques();
                    break;
                default:
                    $e = "Error en acceso 1, favor de revisar usuario y/o contraseña";
                    header('Location: index.php?action=login&e=' . urlencode($e));
                    exit;
                    break;
            }
        } else {
            $e = "Error en acceso 2, favor de revisar usuario y/o contraseña";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function Inicio() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $o = $_SESSION['user'];
            switch ($o->USER_ROL) {
                case 'administrador':
                    $this->MenuAdmin();
                    break;
                case 'administracion':
                    $this->MenuAd();
                    break;
                case 'usuario':
                    $this->MenuUsuario();
                    break;
                case 'ventas':
                    $this->MenuVentas();
                    break;
                case 'compras':
                    $this->MenuCompras();
                    break;
                case 'tesoreria':
                    $this->MenuTesoreria();
                    break;
                case 'logistica':
                    $this->MenuLogistica();
                    break;
                case 'recibo':
                    $this->MenuRecibo();
                    break;
                case 'bodega':
                    $this->MenuBodega();
                    break;
                case 'glogistica':
                    $this->MenuGLogistica();
                    break;
                case 'cxcr':             //14062016
                    $this->MenuCxCRevision();
                    break;
                case 'cxcc':
                    $this->MenuCxCCobranza();
                    break;
                case 'contabilidad':
                    $this->MenuContabilidad();
                    break;
                case 'embarques':
                    $this->MenuEmbarques();
                    break;
                default:
                    $e = "Error en acceso 1, favor de revisar usuario y/o contraseña";
                    header('Location: index.php?action=login&e=' . urlencode($e));
                    exit;
                    break;
            }
        }
    }

    /* nuevos menus */

    function MenuVentas() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user']) && $_SESSION['user']->USER_ROL == 'ventas') {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/modules/m.mventas.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function MenuEmbarques() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user']) && $_SESSION['user']->USER_ROL == 'embarques') {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/modules/m.mEmbarques.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function VerPago() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verpago.php');
            ob_start();
            $exec = $data->VerPagadas();
            if (count($exec) > 0) {
                include 'app/views/pages/p.verpago.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function DetallePedido($doc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.detallepedidodoc.php');
            ob_start();
            $cabecera = $data->CabeceraPedidoDoc($doc);
            $detalle = $data->DetallePedidoDoc($doc);
            if (count($detalle) > 0) {
                include 'app/views/pages/p.detallepedidodoc.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function DetalleDocumento($doc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {

            if (substr($doc, 0, 1) == 'O') {
                $data = new pegaso;
                $pagina = $this->load_template('Pagos');
                $html = $this->load_page('app/views/pages/p.detalledoc.php');
                ob_start();
                //generamos consultas
                $cabecera = $data->CabeceraDoc($doc);
                $detalle = $data->DetalleDoc($doc);
                if (count($detalle) > 0) {
                    include 'app/views/pages/p.detalledoc.php';
                    $table = ob_get_clean();
                    $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
                } else {
                    $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
                }
            } else {
                $data = new pegaso;
                $pagina = $this->load_template('Pagos');
                $html = $this->load_page('app/views/pages/p.detalleSol.php');
                ob_start();
                $solicitud = $data->Solicitudes($doc);
                $sol = $data->verSol($doc);
                if (count($solicitud) > 0) {
                    include 'app/views/pages/p.detalleSol.php';
                    $table = ob_get_clean();
                    $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
                } else {
                    $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
                }
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

/////imprime comprobante.



    function AsignaRuta($docu, $unidad, $edo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $redireccionar = 'aruta';
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            //$html = $this->load_page('app/views/pages/p.aruta_r.php');
            ob_start();
            //generamos consultas
            $exec1 = $data->ActualizaRuta($docu, $unidad);
            $regoper = $data->RegistroOperadores($docu, $unidad);
            $entrega = $data->ARutaEntrega();
            //$exec = $data->ARuta();
            $unidad = $data->TraeUnidades();
            if (count($exec1) > 0 or count($entrega) > 0)
                include 'app/views/pages/p.redirectform.php';
            else
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            /* 	if(count($exec1) > 0 or count($entrega) > 0){
              include 'app/views/pages/p.aruta_r.php';
              $table = ob_get_clean();
              $pagina = $this->replace_content('/\#CONTENIDO\#/ms' ,$table , $pagina);
              }else{
              $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html.'<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
              }
              $this->view_page($pagina); */
        }else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ARuta() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;

            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.aruta.php');
            ob_start();
            $exec = $data->ARuta();
            $entrega = $data->ARutaEntrega();
            $unidad = $data->TraeUnidades();
            if (count($exec) > 0 or count($entrega) > 0) {
                include 'app/views/pages/p.aruta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ARutaEdoMex() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;

            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.aruta.php');
            ob_start();
            //generamos consultas
            $exec = $data->ARutaEdoMex();
            $unidad = $data->TraeUnidades();
            if (count($exec) > 0) {
                include 'app/views/pages/p.aruta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function altaunidades() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $pagina = $this->load_templateL('Alta Unidad');
            $html = $this->load_page('app/views/pages/p.altaunidad.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function altaunidadesdata($numero, $marca, $modelo, $placas, $operador) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;

            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.altaunidad_r.php');
            ob_start();
            //generamos consultas
            $exec = $data->altaunidades1($numero, $marca, $modelo, $placas, $operador);
            if (count($exec) > 0) {
                include 'app/views/pages/p.altaunidad_r.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function PagoW() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;

            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.pagosw.php');
            ob_start();
            //generamos consultas
            $error = "Favor de verificar que los datos ingresados sean correctos";
            $exec = $data->Pagos();
            if (count($exec) > 0) {
                include 'app/views/pages/p.pagosw.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function Pedido() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/pages/p.pedido.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function MuestraPedidos($ped) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;

            $pagina = $this->load_template('Pedidos');
            //$html = $this->load_page('app/views/modules/m.reporte_result.php');
            $html = $this->load_page('app/views/pages/p.pedido_r.php');
            /* OB_START a partir de aqui guardara un buffer con la informacion que haya entre este y ob_get_clean(),
             * es necesario incluir la vista donde haremos uso de los datos como aqui el arreglo $exec */
            ob_start();
            //generamos consultas
            $exec = $data->ConsultaPreoc($ped);
            $options = $data->ConsultaMov($ped);
            //var_dump($exec);
            if (count($exec) > 0) {
                include 'app/views/pages/p.pedido_r.php';
                /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                 * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                 * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function MenuCompras() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user']) && $_SESSION['user']->USER_ROL == 'compras') {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/modules/m.mcompras.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function MenuContabilidad() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user']) && $_SESSION['user']->USER_ROL == 'contabilidad') {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/modules/m.mcontabilidad.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function MenuTesoreria() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user']) && $_SESSION['user']->USER_ROL == 'tesoreria') {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/modules/m.mtes.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function MenuLogistica() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user']) && $_SESSION['user']->USER_ROL == 'logistica') {
            $pagina = $this->load_template('Menu Admin');
            if ($_SESSION['user']->USER_LOGIN == 'glogistica') {
                $html = $this->load_page('app/views/modules/m.mlogisticaA.php');
            } else {
                $html = $this->load_page('app/views/modules/m.mlogistica.php');
            }
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function MenuGLogistica() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user']) && $_SESSION['user']->USER_ROL == 'glogistica') {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/modules/m.mgerencialogistica.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function MenuRecibo() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user']) && $_SESSION['user']->USER_ROL == 'recibo') {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/modules/m.mrecibo.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function MenuEmpaque() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user']) && $_SESSION['user']->USER_ROL == 'empaque') {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/modules/m.mempaque.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /* Carga menu de administrador */

    function MenuAdmin() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user']) && $_SESSION['user']->USER_ROL == 'administrador') {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/modules/m.madmin.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function MenuAd() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/modules/m.mad.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function MenuUsuario() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/modules/m.muser.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function MenuBodega() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/modules/m.mbodega.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function MenuCxCRevision() {     //14062016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user']) && $_SESSION['user']->USER_ROL == 'cxcr') {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/modules/m.mcxcrevision.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function MenuCxCCobranza() {     //14062016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user']) && $_SESSION['user']->USER_ROL == 'cxcc') {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/modules/m.mcxccobranza.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function Pxr() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('PXR');
            $html = $this->load_page('app/views/pages/p.pxr.php');
            ob_start();
            $exec = $data->ListaPartidasNoRecibidas();
            if ($exec != '') {
                include 'app/views/pages/p.pxr.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    //	$asigna = $data->ListaPartidasNoRecibidas();
    //	if(count($asigna) > 0){
    //		$table = ob_get_clean();
    //		$pagina = $this->replace_content('/\#CONTENIDO\#/ms' ,$table , $pagina);
    //		$this->view_page($pagina);
    //		}
//		}else{
    //		$e = "Favor de Iniciar Sesión";
    //		header('Location: index.php?action=login&e='.urlencode($e)); exit;
    //	}
    //ORDEN DE COMPRA


    function AsignaAFactf($factura, $componente) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;

            $pagina = $this->load_template('Asigna Componentes');
            //$html = $this->load_page('app/views/modules/m.reporte_result.php');
            $html = $this->load_page('app/views/pages/p.aflujo.php');
            /* OB_START a partir de aqui guardara un buffer con la informacion que haya entre este y ob_get_clean(),
             * es necesario incluir la vista donde haremos uso de los datos como aqui el arreglo $exec */
            ob_start();
            //generamos consulta
            $asigna = $data->ActualizaFactf($factura, $componente);
            if (count($asigna) > 0) {
                $exec = $data->ConsultaFac();
                $options = $data->ConsultaFlu();
                $facturas = $data->MuestraFact();
                $componentes = $data->MuestraDisp();
                //var_dump($exec);
                if ($exec > 0) {
                    include 'app/views/pages/p.aflujo.php';
                    /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                     * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                     * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                    $table = ob_get_clean();
                    $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
                } else {
                    $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
                }
                $this->view_page($pagina);
            }
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /* Carga modulo Asigna Flujo */
    /* Carga modulo Crea Flujo */

    function AFlujo() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;

            $pagina = $this->load_template('Asigna Componentes');
            //$html = $this->load_page('app/views/modules/m.reporte_result.php');
            $html = $this->load_page('app/views/pages/p.aflujo.php');
            /* OB_START a partir de aqui guardara un buffer con la informacion que haya entre este y ob_get_clean(),
             * es necesario incluir la vista donde haremos uso de los datos como aqui el arreglo $exec */
            ob_start();
            //generamos consulta
            $exec = $data->ConsultaFac();
            $options = $data->ConsultaFlu();
            $facturas = $data->MuestraFact();
            $componentes = $data->MuestraDisp();
            //var_dump($exec);
            if ($exec > 0) {
                include 'app/views/pages/p.aflujo.php';
                /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                 * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                 * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /* Carga modulo Crea Flujo */

    function CFlujo() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;

            $pagina = $this->load_template('Asigna Componentes');
            //$html = $this->load_page('app/views/modules/m.reporte_result.php');
            $html = $this->load_page('app/views/pages/p.asigcomp.php');
            /* OB_START a partir de aqui guardara un buffer con la informacion que haya entre este y ob_get_clean(),
             * es necesario incluir la vista donde haremos uso de los datos como aqui el arreglo $exec */
            ob_start();
            //generamos consulta
            $exec = $data->ConsultaComp();
            $asignados = $data->AsignadosComp();
            if ($exec > 0) {
                include 'app/views/pages/p.asigcomp.php';
                /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                 * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                 * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al capturar el componente</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function AUsuarios() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $pagina = $this->load_template('Asigna Flujo');
            $html = $this->load_page('app/views/pages/p.ausuarios.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /* muestra la vista del formulario componente */

    function CComp() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;

            $pagina = $this->load_template('Asigna Flujo');
            //$html = $this->load_page('app/views/modules/m.reporte_result.php');
            $html = $this->load_page('app/views/pages/p.ccomp.php');
            /* OB_START a partir de aqui guardara un buffer con la informacion que haya entre este y ob_get_clean(),
             * es necesario incluir la vista donde haremos uso de los datos como aqui el arreglo $exec */
            ob_start();
            //generamos consulta
            $exec = $data->MuestraComp();

            if ($exec > 0) {
                include 'app/views/pages/p.ccomp.php';
                /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                 * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                 * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al capturar el componente</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function SFact() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $pagina = $this->load_template('Asigna Flujo');
            $html = $this->load_page('app/views/pages/p.sfact.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function AUsers() {
        /* session_cache_limiter('private_no_expire');
          if(isset($_SESSION['user'])){
          $pagina = $this->load_template('Asigna Flujo');
          $html = $this->load_page('app/views/pages/p.ausers.php');
          $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
          $this-> view_page($pagina);
          }else{
          $e = "Favor de Revisar sus datos";
          header('Location: index.php?action=login&e='.urlencode($e)); exit;
          } */
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            //$html = $this->load_page('app/views/modules/m.reporte_result.php');
            $html = $this->load_page('app/views/pages/p.ausers.php');
            /* OB_START a partir de aqui guardara un buffer con la informacion que haya entre este y ob_get_clean(),
             * es necesario incluir la vista donde haremos uso de los datos como aqui el arreglo $exec */
            ob_start();
            //generamos consulta
            $exec = $data->ConsultaUsur();
            if ($exec != '') {
                include 'app/views/pages/p.ausers.php';
                /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                 * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                 * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay usuarios registrados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /* Obtiene y carga el template */

    function load_template($title = 'Sin Titulo') {
        $pagina = $this->load_page('app/views/master.php');
        $header = $this->load_page('app/views/sections/s.header.php');
        $pagina = $this->replace_content('/\#HEADER\#/ms', $header, $pagina);
        $pagina = $this->replace_content('/\#TITLE\#/ms', $title, $pagina);
        return $pagina;
    }

    /* Header para login */

    function load_templateL($title = 'Sin Titulo') {
        $pagina = $this->load_page('app/views/master.php');
        $header = $this->load_page('app/views/sections/header.php');
        $pagina = $this->replace_content('/\#HEADER\#/ms', $header, $pagina);
        $pagina = $this->replace_content('/\#TITLE\#/ms', $title, $pagina);
        return $pagina;
    }

    /* inserta los nuevos componentes */

    function InsertaCcomp($nombre, $duracion, $tipo) {
        session_cache_limiter('private_no_expire');
        $data = new pegaso;
        if (isset($_SESSION['user'])) {
            $comprueba = $data->CompruebaComp($nombre);
            //var_dump($comprueba);
            //print_r($comprueba);
            if ($comprueba > 0) {
                $pagina = $this->load_template('Compra Venta');
                //$html = $this->load_page('app/views/modules/m.reporte_result.php');
                $html = $this->load_page('app/views/pages/p.ccomp.php');
                /* OB_START a partir de aqui guardara un buffer con la informacion que haya entre este y ob_get_clean(),
                 * es necesario incluir la vista donde haremos uso de los datos como aqui el arreglo $exec */
                ob_start();
                //generamos consulta
                $exec = $data->MuestraComp();
                include 'app/views/pages/p.ccomp_r.php';
                /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                 * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                 * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>El componente ingresado ya existe</h2><center></div>', $pagina);
            } else {
                $pagina = $this->load_template('Compra Venta');
                //$html = $this->load_page('app/views/modules/m.reporte_result.php');
                $html = $this->load_page('app/views/pages/p.ccomp.php');
                /* OB_START a partir de aqui guardara un buffer con la informacion que haya entre este y ob_get_clean(),
                 * es necesario incluir la vista donde haremos uso de los datos como aqui el arreglo $exec */
                ob_start();
                //generamos consulta
                $rs = $data->InsertaCompo($nombre, $duracion, $tipo, $_SESSION['user']);
                if ($rs > 0) {
                    $exec = $data->MuestraComp();
                    include 'app/views/pages/p.ccomp_r.php';
                    /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                     * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                     * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                    $table = ob_get_clean();
                    $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
                } else {
                    $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al capturar el componente</h2><center></div>', $pagina);
                }
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function InsertaUsuarioN($usuario, $contra, $email, $rol, $letra) {
        session_cache_limiter('private_no_expire');
        $data = new pegaso;
        $html = '';
        $pagina = '';
        /* obtenemos el rol */
        for ($i = 0; $i < count($rol); $i++) {
            $roll = $rol[$i];
        }
        $pagina = $this->load_template('Reporte');
        //$html = $this->load_page('app/views/modules/m.reporte_result.php');
        $html = $this->load_page('app/views/pages/p.ausers.php');
        /* obtenemos numero de ultimo registro */
        $rs = $data->ObtieneReg();
        $id = (int) $rs->COUNT + 1; /* Forzamos a convertir la variable en entero */
        $nuser = $data->NuevoUser($usuario, $contra, $email, $roll, $id, $letra);
        //print_r($nuser);
        //var_dump($nuser);
        if ($nuser != 0) {
            ob_start();
            $exec = $data->ConsultaUsur();
            include 'app/views/pages/p.ausers_r.php';
            /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
             * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
             * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
            $table = ob_get_clean();
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
        } else {
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>Algo salió mal</h2>', $pagina);
        }
        $this->view_page($pagina);
    }

    function CCompVent() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            //$html = $this->load_page('app/views/modules/m.reporte_result.php');
            $html = $this->load_page('app/views/pages/p.ccompvent.php');
            /* OB_START a partir de aqui guardara un buffer con la informacion que haya entre este y ob_get_clean(),
             * es necesario incluir la vista donde haremos uso de los datos como aqui el arreglo $exec */
            ob_start();
            //generamos consulta
            $exec = $data->ConsultaProd();
            if ($exec != '') {
                include 'app/views/pages/p.ccompvent.php';
                /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                 * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                 * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /* #########################################CAmbios de OFA######################################### */

    //Pantallas para Costos
    function Ccp() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $pagina = $this->load_template('Costos');
            $html = $this->load_page('app/views/pages/p.ccp.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    //Pantalla para seguimiento de los productos.

    function Pantalla1($cat) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.pantalla1.php');
            ob_start();
            $exec = $data->Idproducto($cat);
            if ($exec != '') {
                include 'app/views/pages/p.pantalla1.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function Lista_Pedidos() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.lpedidos.php');
            ob_start();
            $exec = $data->LPedidos();
            if ($exec != '') {
                include 'app/views/pages/p.lpedidos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión.";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function Lista_Pedidos_Todos() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.lpedidosT.php');
            ob_start();
            $exec = $data->LPedidosTodos();
            if ($exec != '') {
                include 'app/views/pages/p.lpedidosT.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión.";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /// Pantalla para poder visualizar lo pendiente por facturar.

    function Pantalla2() {       //2306-
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.pantalla2.php');
            ob_start();
            $exec = $data->PorFacturar();
            $notascred = $data->PendientesGenNC();
            $reenruta = $data->PendientesGenRee();
            if ($exec != '') {
                include 'app/views/pages/p.pantalla2.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    //ORDEN DE COMPRA
    function OrdComp() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            //$html = $this->load_page('app/views/modules/m.reporte_result.php');
            $html = $this->load_page('app/views/pages/p.ordcomp.php');
            /* OB_START a partir de aqui guardara un buffer con la informacion que haya entre este y ob_get_clean(),
             * es necesario incluir la vista donde haremos uso de los datos como aqui el arreglo $exec */
            ob_start();
            //generamos consulta
            $exec = $data->ConsultaOrdenComp('1'); #<-- enviamos en id para la consulta correspondiente a esta funcion
            if ($exec != '') {
                //unset($_SESSION['correcto']);
                include 'app/views/pages/p.ordcomp.php';
                /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                 * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                 * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    //ORDEN DE COMPRA
    function OrdComp1($cat) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            //$html = $this->load_page('app/views/modules/m.reporte_result.php');
            $html = $this->load_page('app/views/pages/p.ordcomp_cat1.php');
            /* OB_START a partir de aqui guardara un buffer con la informacion que haya entre este y ob_get_clean(),
             * es necesario incluir la vista donde haremos uso de los datos como aqui el arreglo $exec */
            ob_start();
            //generamos consulta
            $exec = $data->ConsultaOrdenComp($cat); #<-- enviamos en id para la consulta correspondiente a esta funcion
            if ($exec != '') {
                //unset($_SESSION['correcto']);
                include 'app/views/pages/p.ordcomp_cat1.php';
                /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                 * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                 * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    //ORDEN DE COMPRA REGISTRO
    //function OrdCompAlt($PROVEEDOR,$CVE_DOC,$TOTAL,$TIME,$HOY,$IdPreoco,$Consecutivo,$Doc,$Prod,$Costo,$unimed,$facconv,$Cantidad,$Rest){
    //cafaray->function OrdCompAlt($PROVEEDOR,$CVE_DOC,$TOTAL,$TIME,$HOY,$IdPreoco,$Consecutivo,$Doc,$Prod,$Costo,$unimed,$facconv,$Cantidad,$Rest,$consecutivo2){
    function OrdCompAlt($PARTIDAS) {

        //	echo $PROVEEDOR.$CVE_DOC.$TOTAL.$TIME.$HOY;
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.ordcompVal.php');

            //cafaray -> $rs = $data->ObtieneReg(); <-
            //$id = (int) $rs["COUNT"] + 1;
            //echo $id;
            //$nuvOrdComp = $data->NuevoOrdComp($PROVEEDOR,$CVE_DOC,$TOTAL,$TIME,$HOY,$IdPreoco,$Consecutivo,$Doc,$Prod,$Costo,$unimed,$facconv,$Cantidad,$Rest);
            //$nuvOrdComp = $data->NuevoOrdComp($PROVEEDOR,$CVE_DOC,$TOTAL,$TIME,$HOY,$IdPreoco,$Consecutivo,$Doc,$Prod,$Costo,$unimed,$facconv,$Cantidad,$Rest,$consecutivo2);

            asort($PARTIDAS); //$PROVEEDOR, $CVE_DOC, $TOTAL, $Doc, $TIME, $HOY, $IdPreoco, $Rest, $Prod, $Cantidad, $Costo, $unimed, $facconv
            $proveedorPrevio = '';
            $cantidadTotal = 0;
            $impuestoTotal = 0;
            $importeTotal = 0;
            $documento = '';
            foreach ($PARTIDAS as $partida) {
                if ($partida[0] != $proveedorPrevio) {
                    // registra orden y primer partida
                    $documento = $data->NuevoOrdComp($partida[0], $partida[1], $partida[2], $partida[4], $partida[5], $partida[3]);
                    //$nuvOrdComp = $data->NuevoOrdComp($PROVEEDOR,$CVE_DOC,$TOTAL,$TIME,$HOY,$Doc);
                    $proveedorPrevio = $partida[0];
                    $cantidadTotal = 0;
                    $impuestoTotal = 0;
                    $importeTotal = 0;
                }
                $cveuser = $_SESSION['user']->USER_LOGIN;
                // registra partida
                // $CVE_DOC, $TOTAL, $Doc, $IdPreoco, $Rest, $Prod, $Cantidad, $Costo, $unimed, $facconv
                $rs = $data->NuevaPartidaOrdenCompra($documento, $partida[6], $partida[7], $partida[8], $partida[9], $partida[10], $partida[11], $partida[12], $cveuser);
                $cantidadTotal += $partida[9];
                $impuestoTotal += ($partida[9] * $partida[10] * .16);
                $importeTotal += ($partida[9] * $partida[10]);
                //echo "actualiza totales: $proveedorPrevio para $documento con $cantidadTotal, $impuestoTotal, $importeTotal";
                $resultado = $data->actualizaTotalOrdenCompra($documento, $cantidadTotal, $impuestoTotal, $importeTotal);
                $resultado = $data->actualizaTotalPaga($proveedorPrevio, $documento, $importeTotal);
                //echo $resultado;
            }




            //$exec = $data->ConsultaOrdenCompAlta();

            if ($documento != 0) {
                ob_start();
                include 'app/views/pages/p.ordcompVal.php';
                //header('Location: index.php?action=ordcomp');

                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);

                //header('Location: index.php?action=ordcomp&ok=ok');
                //$_SESSION['correcto']="LA ORDEN SE CREO CORRECTAMENTE";
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

//MODIFICA Updatepreoc
    function modificaPreOc($provcostid, $provedor, $costo, $total, $nombreprovedor, $cantidad, $rest) {
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.ordcomp.php');
            ob_start();
            $exec = $data->actualizaPreOc($provcostid, $provedor, $costo, $total, $nombreprovedor, $cantidad, $rest);
            if ($exec != 0) {
                header('Location: app/views/pages/p.ordcompMod.php');
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay usuarios registrados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    //VERIFICAR PROVEEDOR
    function verificaPreOcProvedor($provedor) {
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.ordcomp.php');
            ob_start();
            $exec = $data->valorPreOcProvedor($provedor);
            if ($exec != '') {
                header('Location: app/views/pages/p.ordcompVerifica.php?nombreProv=' . $exec);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay usuarios registrados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    //VERIFICAR CVE_ART de INVE01
    function verificaArticulo($Prod) {
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.ordcomp.php');
            ob_start();

            $exec = $data->valorArticulo($Prod);

            if ($exec != '') {

                list($unimed, $facconv) = explode("|", $exec);
                //header('Location: app/views/pages/p.ordcompVerifica.php?nombreProv='.$exec);
                //header('Location: index.php?action=ok&unimed='.$unimed);

                $_SESSION['unimed'] = $unimed;

                $_SESSION["facconv"] = $facconv;
            } else {
                //unset($_SESSION['unimed']);
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay usuarios registrados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /* #########################################Terminan cambios de OFA######################################### */

    function EUsuarios() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Consulta Usuario');
            //$html = $this->load_page('app/views/modules/m.reporte_result.php');
            $html = $this->load_page('app/views/pages/p.ausers.php');
            /* OB_START a partir de aqui guardara un buffer con la informacion que haya entre este y ob_get_clean(),
             * es necesario incluir la vista donde haremos uso de los datos como aqui el arreglo $exec */
            ob_start();
            //generamos consulta
            $exec = $data->ConsultaUsur();
            if ($exec != '') {
                include 'app/views/pages/p.ausers.php';
                /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                 * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                 * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay usuarios registrados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ModificaUnidad($unidad) {
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Modifica Usuario');
            //$html = $this->load_page('app/views/modules/m.reporte_result.php');
            $html = $this->load_page('app/views/pages/p.modificaunidad.php');
            /* OB_START a partir de aqui guardara un buffer con la informacion que haya entre este y ob_get_clean(),
             * es necesario incluir la vista donde haremos uso de los datos como aqui el arreglo $exec */
            ob_start();
            //generamos consulta
            $munidad = $data->ConsultaUnidad($unidad);
            if ($munidad != '') {
                include 'app/views/pages/p.modificaUnidad.php';
                /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                 * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                 * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay usuarios registrados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ActualizaUnidades($numero, $marca, $modelo, $placas, $operador, $tipo, $tipo2, $coordinador, $idu) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $redireccionar = 'funidades';
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            ob_start();
            $response = true;
            $insertaU = $data->ActualizaNUnidad($numero, $marca, $modelo, $placas, $operador, $tipo, $tipo2, $coordinador, $idu);
            include 'app/views/pages/p.redirectform.php';
            /*
              if (count($unidades)){
              include 'app/views/pages/p.formunidades.php';
              $table = ob_get_clean();
              $pagina = $this->replace_content('/\#CONTENIDO\#/ms',$table,$pagina);
              }else{
              $pagina = $this->replace_content('/\CONTENIDO\#/ms',$html.'<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
              }
              $this->view_page($pagina); */
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ModificaU($mail) {
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Modifica Usuario');
            //$html = $this->load_page('app/views/modules/m.reporte_result.php');
            $html = $this->load_page('app/views/pages/p.modifica.php');
            /* OB_START a partir de aqui guardara un buffer con la informacion que haya entre este y ob_get_clean(),
             * es necesario incluir la vista donde haremos uso de los datos como aqui el arreglo $exec */
            ob_start();
            //generamos consulta
            $exec = $data->ConsultaUsurEmail($mail);
            if ($exec != '') {
                include 'app/views/pages/p.modifica.php';
                /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                 * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                 * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay usuarios registrados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /* Funcion que actualiza el usuarios */

    function Actualiza($mail, $usuario, $contrasena, $email, $rol, $estatus) {
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            //$html = $this->load_page('app/views/modules/m.reporte_result.php');
            $html = $this->load_page('app/views/pages/p.ausers.php');
            for ($i = 0; $i < count($rol); $i++) {
                $roll = $rol[$i];
            }
            for ($i = 0; $i < count($rol); $i++) {
                $est = $estatus[$i];
            }
            /* OB_START a partir de aqui guardara un buffer con la informacion que haya entre este y ob_get_clean(),
             * es necesario incluir la vista donde haremos uso de los datos como aqui el arreglo $exec */
            ob_start();
            //generamos consulta
            $exec = $data->ActualizaUsr($mail, $usuario, $contrasena, $email, $roll, $est);
            //print_r($exec);
            //var_dump($exec);
            if ($exec != '') {
                include 'app/views/pages/p.ausers_r.php';
                /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                 * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                 * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay usuarios registrados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /* Metodo para asignar componentes */

    function AsignaComp($componentes, $nombre, $desc) {
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Asigna Proceso');
            /* obtener los checkbox seleccionados */
            foreach ($componentes as $componente) {
                $comp [] = $componente;
            }
            //var_dump($comp);
            $html = $this->load_page('app/views/pages/p.cflujo_r.php');
            //generamos query
            ob_start();
            $ejec = $data->InsertaComp($comp, $nombre, $desc);
            //var_dump($ejec);
            if ($ejec > 0) {
                $exec = $data->ConsultaComp();
                include 'app/views/pages/p.cflujo_r.php';
                /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                 * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                 * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay usuarios registrados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function CSesion() {
        session_destroy($_SESSION['user']);
        session_unset($_SESSION['user']);
        $e = "Session Finalizada";
        header('Location: index.php?action=login&e=' . urlencode($e));
        exit;
    }

    /* METODO QUE CARGA UNA PAGINA DE LA SECCION VIEW Y LA MANTIENE EN MEMORIA
      INPUT
      $page | direccion de la pagina
      OUTPUT
      STRING | devuelve un string con el codigo html cargado
     */

    private function load_page($page) {
        return file_get_contents($page);
    }

    /* METODO QUE ESCRIBE EL CODIGO PARA QUE SEA VISTO POR EL USUARIO
      INPUT
      $html | codigo html
      OUTPUT
      HTML | codigo html
     */

    private function view_page($html) {
        echo $html;
    }

    /* PARSEA LA PAGINA CON LOS NUEVOS DATOS ANTES DE MOSTRARLA AL USUARIO
      INPUT
      $out | es el codigo html con el que sera reemplazada la etiqueta CONTENIDO
      $pagina | es el codigo html de la pagina que contiene la etiqueta CONTENIDO
      OUTPUT
      HTML 	| cuando realiza el reemplazo devuelve el codigo completo de la pagina
     */

    private function replace_content($in = '/\#CONTENIDO\#/ms', $out, $pagina) {
        return preg_replace($in, $out, $pagina);
    }

    function RegPago() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.regpago.php');
            ob_start();
            $exec = $data->ConsultaPagadas();
            if (count($exec) > 0) {
                include 'app/views/pages/p.regpago.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function insertaDocumento($documento, $archivo, $archivoPdf, $emisorRFC, $emisorNombre, $receptorRFC, $receptorNombre, $fecha, $uuid, $importe) {
        $data = new pegaso;
        if ($this->validaReceptor($receptorRFC)) {
            if ($data->validaEmisor($documento, $emisorRFC)) {
                $response = $data->insertaDocumentoXML($documento, $archivo, $archivoPdf, $emisorRFC, $emisorNombre, $receptorRFC, $receptorNombre, $fecha, $uuid, $importe);
                return $response;
            } else {
                print"No se ha logrado validar el emisor del documento [$emisorRFC]";
            }
        } else {
            print"No se ha logrado validar el receptor del documento [$receptorRFC]";
        }
    }

    function validaReceptor($receptorRFC) {
        if (strtoupper($receptorRFC) == 'FPE980326GH9') {
            return true;
        } else {
            return false;
        }
    }

    function Ordenes() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.ordenes.php');
            ob_start();
            $exec = $data->VerOrdenes();
            if (count($exec) > 0) {
                include 'app/views/pages/p.ordenes.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function detalleOrdenCompra($doco) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.detalleOC.php');
            ob_start();
            $cabecera = $data->OC($doco);
            $detalle = $data->detalleOC($doco);
            if (count($detalle) > 0) {
                include 'app/views/pages/p.detalleOC.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function idpor($idd) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.idpor.php');
            ob_start();
            $pedido = $data->idPreoc($idd);
            $orden = $data->idCompo($idd);
            $recepcion = $data->idCompr($idd);
            if (count($pedido) > 0) {
                include 'app/views/pages/p.idpor.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verpago1() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verpago1.php');
            ob_start();
            $pagos = $data->verPagos();
            if (count($pagos) > 0) {
                include 'app/views/pages/p.verpago1.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function Multipagos() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.multipagos.php');
            ob_start();
            $efectivos = $data->verEfectivos();
            $cheques = $data->verCheques();
            $trans = $data->verTrans();
            $creditos = $data->verCreditos();
            if (count($efectivos) > 0 or count($cheques) > 0 or count($trans) > 0 or count($creditos) > 0) {
                include 'app/views/pages/p.multipagos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function PXL() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.pxl.php');
            ob_start();
            $pedidos = $data->verPXL();
            if (count($pedidos) > 0) {
                include 'app/views/pages/p.pxl.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function RechazarPedido($docp, $motivo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.pxl.php');
            ob_start();
            $rechazar = $data->RechazarPedido($docp, $motivo);
            $pedidos = $data->verPXL();
            if (count($pedidos) > 0) {
                include 'app/views/pages/p.pxl.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function LiberaPedido($pedido) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.pxl.php');
            ob_start();
            $libera = $data->liberaPedido($pedido);
            $pedidos = $data->verPXL();
            if (count($pedidos) > 0) {
                include 'app/views/pages/p.pxl.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function Pagos_OLD() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;

            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.pagos_old.php');
            ob_start();
            //generamos consultas
            $exec = $data->Pagos_OLD();
            if (count($exec) > 0) {
                include 'app/views/pages/p.pagos_old.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function PagoCorrectoOLD($docuOLD, $tipopOLD, $montoOLD, $nomprovOLD, $cveclpvOLD) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;

            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.pagoc_old.php');
            ob_start();

            $error = "Datos guardados correctamente";
            $guarda = $data->GuardaPagoCorrectoOLD($docuOLD, $tipopOLD, $montoOLD, $nomprovOLD, $cveclpvOLD);
            $exec = $data->Pagos_OLD();
            if (count($guarda) > 0) {
                include 'app/views/pages/p.pagoc_old.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function OCIMP() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.pagosImp.php');
            ob_start();
            $efectivosImp = $data->verEfectivosImp();
            $chequesImp = $data->verChequesImp();
            $transImp = $data->verTransImp();
            $creditosImp = $data->verCreditosImp();
            if (count($efectivosImp) > 0 or count($chequesImp) > 0 or count($transImp) > 0 or count($creditosImp) > 0) {
                include 'app/views/pages/p.pagosImp.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ImprimeOC($oc) {
        $data = new pegaso;
        $cabecera = $data->OCL($oc);
        $detalle = $data->detalleOC($oc);


        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerpdf.jpg', 10, 15, 205, 55);
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->Ln(70);
        foreach ($cabecera as $t) {
            $tipo = $t->CAMPLIB2;
            if ($tipo == 'E') {
                $tipo = 'Entrega';
            } elseif ($tipo == 'R') {
                $tipo = 'Recoleccion';
            } else {
                $tipo = 'No reconocido';
            }


            $pdf->Cell(60, 10, "Fecha: ");
            $pdf->Cell(60, 10, $t->FECHAELAB);
            $pdf->Ln(12);
            $pdf->Cell(60, 10, "Documento: ");
            $pdf->Cell(60, 10, $t->CVE_DOC);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Elaborado por: ");
            $pdf->Cell(60, 10, $t->REALIZA);
            $pdf->Ln(12);
            $pdf->Cell(60, 10, "Nombre: ");
            $pdf->Cell(60, 10, $t->NOMBRE);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Direccion: ");
            $pdf->Cell(60, 10, $t->CALLE . " No Ext: " . $t->NUMEXT . " No. Int:" . $t->NUMINT . " Colonia: " . $t->COLONIA);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Direccion 2: ");
            $pdf->Cell(60, 10, $t->CODIGO . $t->MUNICIPIO . " Telefono :" . $t->TELEFONO);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Confirmado con: ");
            $pdf->Cell(60, 10, $t->CAMPLIB4);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Tipo: ");
            $pdf->Cell(60, 10, $tipo);
        }

        $pdf->SetFont('Times', 'I', 9);

        $pdf->Cell(20, 7, 'Partida', 1);
        $pdf->Cell(40, 7, 'Articulo', 1);
        $pdf->Cell(90, 7, 'Descr', 1);
        $pdf->Cell(20, 7, 'Cantidad', 1);
        $pdf->Cell(25, 7, 'Total Partida', 1);
        $pdf->Ln();
        foreach ($detalle as $col) {
            $pdf->Cell(20, 7, $col->NUM_PAR, 1);
            $pdf->Cell(40, 7, $col->CVE_ART, 1);
            $pdf->Cell(90, 7, $col->DESCR, 1);
            $pdf->Cell(20, 7, $col->CANT, 1);
            $pdf->Cell(25, 7, $col->TOT_PARTIDA, 1);
            $pdf->Ln();
        }

        foreach ($cabecera as $t1) {
            $pdf->Output('Transferencia' . $t1->CVE_DOC . '.pdf', 'i');
        }
        /* Falta crear consulta que traiga el número de folio generado */
    }

    function ImprimeTrans($id, $doc) {
        $data = new pegaso;
        $actstatus = $data->ActStatusImpresoTrans($id, $doc);
        $actruta = $data->ActRuta($id, $doc);
        $datostrans = $data->ObtieneDatosTrans($id);
        $cabecera = $data->OCL($doc);
        $detalle = $data->detalleOC($doc);

        $banco = $datostrans->BANCO;
        if ($baco = 0) {
            $banco = 'No Registrado';
        } elseif ($banco = 2) {
            $banco = 'Banamex';
        } elseif ($banco = 12) {
            $banco = 'BBVA Bancomer';
        } elseif ($banco = 14) {
            $banco = 'Santander';
        } elseif ($banco = 21) {
            $banco = 'HSBC';
        } elseif ($banco = 36) {
            $banco = 'Inbursa';
        } elseif ($banco = 44) {
            $banco = 'Scotiabank';
        } elseif ($banco = 72) {
            $banco = 'Banorte';
        } elseif ($banco = -1) {
            $banco = 'Efectivo';
        } elseif ($banco = 127) {
            $banco = 'Azteca';
        }


        $pdf = new FPDF('P', 'mm', 'Letter');

        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->SetTextColor(198, 23, 23);
        $pdf->SetXY(180, 5);
        $pdf->CELL(60, 5, $datostrans->TRANS);
        $pdf->Image('app/views/images/headerpdf.jpg', 10, 15, 205, 55);
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->SetTextColor(14, 3, 3);
        $pdf->Ln(70);
        $pdf->Cell(60, 10, "Fecha: ");
        $pdf->Cell(60, 10, $datostrans->FECHA);
        $pdf->Ln(15);
        $pdf->Cell(60, 10, "Beneficiario: ");
        $pdf->Cell(60, 10, $datostrans->BENEFICIARIO);
        $pdf->Ln(15);
        $pdf->Cell(60, 10, "Banco Receptor: ");
        $pdf->Cell(60, 10, $banco);
        $pdf->Ln(15);
        $pdf->Cell(60, 10, "Documento: ");
        $pdf->Cell(60, 10, $datostrans->DOCUMENTO);
        $pdf->Ln(15);
        $pdf->Cell(60, 10, "Clabe Interbancaria: ");
        $pdf->Cell(60, 10, $datostrans->CLABE);
        $pdf->Ln(15);
        $pdf->Cell(60, 10, "Importe: ");
        $pdf->Cell(60, 10, "$ " . $datostrans->MONTO);
        $pdf->Ln(15);
        $pdf->Cell(60, 10, "Fecha Realizacion: ");
        $pdf->Cell(60, 10, $datostrans->FECHA_APLI);
        $pdf->Ln(15);
        $pdf->Cell(60, 10, "Fecha de Recoleccion: ");
        foreach ($cabecera as $clib) {
            $pdf->Cell(60, 10, $clib->CAMPLIB3);
            $pdf->Ln(15);
            $pdf->Cell(60, 10, "Observaciones: ");
            $pdf->Cell(60, 10, $clib->STR_OBS);
            $pdf->Ln(30);
            $pdf->Cell(60, 10, "_______________________                               __________________");
            $pdf->Ln(5);
            $pdf->Cell(60, 10, "Firma de Recibido                                                  Fecha de Recibo ");
            //$pdf->Output('Transferencia '.$datostrans->DOCUMENTO .'.pdf', 'i');
            /* Falta crear consulta que traiga el número de folio generado */
            $pdf->AddPage();
            $pdf->Image('app/views/images/headerOCpdf.jpg', 10, 15, 205, 55);
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->Ln(70);
        }
        foreach ($cabecera as $t) {

            $tipo = $t->CAMPLIB2;
            if ($tipo == 'E') {
                $tipo = 'Entrega';
            } elseif ($tipo == 'R') {
                $tipo = 'Recoleccion';
            } else {
                $tipo = 'No reconocido';
            }

            $pdf->Cell(60, 10, "Fecha: ");
            $pdf->Cell(60, 10, $t->FECHAELAB);
            $pdf->Ln(12);
            $pdf->Cell(60, 10, "Documento: ");
            $pdf->Cell(60, 10, $t->CVE_DOC);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Elaborado por: ");
            $pdf->Cell(60, 10, $t->REALIZA);
            $pdf->Ln(12);
            $pdf->Cell(60, 10, "Nombre: ");
            $pdf->Cell(60, 10, $t->NOMBRE);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Direccion: ");
            $pdf->Cell(60, 10, $t->CALLE . " No Ext: " . $t->NUMEXT . " No. Int:" . $t->NUMINT . " Colonia: " . $t->COLONIA);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Direccion 2: ");
            $pdf->Cell(60, 10, $t->CODIGO . $t->MUNICIPIO . " Telefono :" . $t->TELEFONO);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Confirmado con: ");
            $pdf->Cell(60, 10, $t->CAMPLIB4);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Tipo: ");
            $pdf->Cell(60, 10, $tipo);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Fecha De Entrega: ");
            $pdf->Cell(60, 10, $t->CAMPLIB3);

            $pdf->Ln(12);
        }

        $pdf->SetFont('Times', 'I', 9);

        $pdf->Cell(20, 7, 'Partida', 1);
        $pdf->Cell(23, 7, 'Pedido', 1);
        $pdf->Cell(33, 7, 'Articulo', 1);
        $pdf->Cell(70, 7, 'Descr', 1);
        $pdf->Cell(20, 7, 'Cantidad', 1);
        $pdf->Cell(25, 7, 'Total Partida', 1);
        $pdf->Ln();
        foreach ($detalle as $col) {
            $pdf->Cell(20, 7, $col->NUM_PAR, 1);
            $pdf->Cell(23, 7, $col->COTIZA, 1);
            $pdf->Cell(33, 7, $col->CVE_ART, 1);
            $pdf->Cell(70, 7, $col->DESCR, 1);
            $pdf->Cell(20, 7, $col->CANT, 1);
            $pdf->Cell(25, 7, $col->TOT_PARTIDA, 1);
            $pdf->Ln();
        }

        foreach ($cabecera as $t1) {
            $pdf->Output('Transferencia' . $t1->CVE_DOC . '.pdf', 'i');
        }
    }

    function ImprimeEfectivo($id, $doc) {
        $data = new pegaso;
        $actstatus = $data->ActStatusImpresoEfectivo($id);
        $actruta = $data->ActRuta($id, $doc);
        $datostrans = $data->ObtieneDatosEfectivo($id);
        $cabecera = $data->OCL($doc);
        $detalle = $data->detalleOC($doc);

        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->SetTextColor(198, 23, 23);
        $pdf->SetXY(180, 5);
        $pdf->CELL(60, 5, $datostrans->EFECTIVO);
        $pdf->Image('app/views/images/headerpdf_efe.jpg', 10, 15, 205, 55);
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->SetTextColor(14, 3, 3);
        $pdf->Ln(70);
        $pdf->Cell(60, 10, "Fecha: ");
        $pdf->Cell(60, 10, $datostrans->FECHA);
        $pdf->Ln(20);
        $pdf->Cell(60, 10, "Beneficiario: ");
        $pdf->Cell(60, 10, $datostrans->BENEFICIARIO);
        $pdf->Ln(20);
        $pdf->Cell(60, 10, "Documento: ");
        $pdf->Cell(60, 10, $datostrans->DOCUMENTO);
        $pdf->Ln(20);
        $pdf->Cell(60, 10, "Importe: ");
        $pdf->Cell(60, 10, "$ " . $datostrans->MONTO);
        $pdf->Ln(20);
        $pdf->Cell(60, 10, "Fecha Realizacion: ");
        $pdf->Cell(60, 10, $datostrans->FECHA_APLI);
        $pdf->Ln(15);
        $pdf->Cell(60, 10, "Fecha de Recoleccion: ");
        foreach ($cabecera as $clib) {
            $pdf->Cell(60, 10, $clib->CAMPLIB3);
            $pdf->Ln(15);
            $pdf->Cell(60, 10, "Observaciones: ");
            $pdf->Cell(60, 10, $clib->STR_OBS);
            $pdf->Ln(30);
        }
        $pdf->Cell(60, 10, "_______________________          _______________________                              __________________");
        $pdf->Ln(5);
        $pdf->Cell(60, 10, "Nombre de quien Recibe                  Firma de Recibido                                                  Fecha de Recibo ");

        //$pdf->Output('Transferencia '.$datostrans->DOCUMENTO .'.pdf', 'i');
        /* Falta crear consulta que traiga el número de folio generado */
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerpdf_efe.jpg', 10, 15, 205, 55);
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->Ln(70);
        foreach ($cabecera as $t) {
            $tipo = $t->CAMPLIB2;
            if ($tipo == 'E') {
                $tipo = 'Entrega';
            } elseif ($tipo == 'R') {
                $tipo = 'Recoleccion';
            } else {
                $tipo = 'No reconocido';
            }
            $pdf->Cell(60, 10, "Fecha: ");
            $pdf->Cell(60, 10, $t->FECHAELAB);
            $pdf->Ln(12);
            $pdf->Cell(60, 10, "Documento: ");
            $pdf->Cell(60, 10, $t->CVE_DOC);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Elaborado por: ");
            $pdf->Cell(60, 10, $t->REALIZA);
            $pdf->Ln(12);
            $pdf->Cell(60, 10, "Nombre: ");
            $pdf->Cell(60, 10, $t->NOMBRE);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Direccion: ");
            $pdf->Cell(60, 10, $t->CALLE . " No Ext: " . $t->NUMEXT . " No. Int:" . $t->NUMINT . " Colonia: " . $t->COLONIA);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Direccion 2: ");
            $pdf->Cell(60, 10, $t->CODIGO . $t->MUNICIPIO . " Telefono :" . $t->TELEFONO);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Confirmado con: ");
            $pdf->Cell(60, 10, $t->CAMPLIB4);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Fecha De Entrega: ");
            $pdf->Cell(60, 10, $t->CAMPLIB3);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Tipo: ");
            $pdf->Cell(60, 10, $tipo);
            $pdf->Ln(12);
        }

        $pdf->SetFont('Times', 'I', 9);

        $pdf->Cell(20, 7, 'Partida', 1);
        $pdf->Cell(23, 7, 'Pedido', 1);
        $pdf->Cell(33, 7, 'Articulo', 1);
        $pdf->Cell(70, 7, 'Descr', 1);
        $pdf->Cell(20, 7, 'Cantidad', 1);
        $pdf->Cell(25, 7, 'Total Partida', 1);
        $pdf->Ln();
        foreach ($detalle as $col) {
            $pdf->Cell(20, 7, $col->NUM_PAR, 1);
            $pdf->Cell(23, 7, $col->COTIZA, 1);
            $pdf->Cell(33, 7, $col->CVE_ART, 1);
            $pdf->Cell(70, 7, $col->DESCR, 1);
            $pdf->Cell(20, 7, $col->CANT, 1);
            $pdf->Cell(25, 7, $col->TOT_PARTIDA, 1);
            $pdf->Ln();
        }

        foreach ($cabecera as $t1) {
            $pdf->Output('Transferencia' . $t1->CVE_DOC . '.pdf', 'i');
        }
    }

    function ImprimeCheque($id, $doc) {
        $data = new pegaso;
        $actstatus = $data->ActStatusImpresoCheque($id);
        $actruta = $data->ActRuta($id, $doc);
        $datostrans = $data->ObtieneDatosCheque($id);
        $cabecera = $data->OCL($doc);
        $detalle = $data->detalleOC($doc);

        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->SetTextColor(198, 23, 23);
        $pdf->SetXY(180, 5);
        $pdf->CELL(60, 5, $datostrans->CHEQUE);
        $pdf->Image('app/views/images/headerpdf_ch.jpg', 10, 15, 205, 55);
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->SetTextColor(14, 3, 3);
        $pdf->Ln(70);
        $pdf->Cell(60, 10, "Fecha: ");
        $pdf->Cell(60, 10, $datostrans->FECHA);
        $pdf->Ln(20);
        $pdf->Cell(60, 10, "Beneficiario: ");
        $pdf->Cell(60, 10, $datostrans->BENEFICIARIO);
        $pdf->Ln(20);
        $pdf->Cell(60, 10, "Documento: ");
        $pdf->Cell(60, 10, $datostrans->DOCUMENTO);
        $pdf->Ln(20);
        $pdf->Cell(60, 10, "Importe: ");
        $pdf->Cell(60, 10, "$ " . $datostrans->MONTO);
        $pdf->Ln(20);
        $pdf->Cell(60, 10, "Fecha Realizacion: ");
        $pdf->Cell(60, 10, $datostrans->FECHA_APLI);
        $pdf->Ln(15);
        $pdf->Cell(60, 10, "Fecha de Recoleccion: ");
        foreach ($cabecera as $clib) {
            $pdf->Cell(60, 10, $clib->CAMPLIB3);
            $pdf->Ln(15);
            $pdf->Cell(60, 10, "Observaciones: ");
            $pdf->Cell(60, 10, $clib->STR_OBS);
            $pdf->Ln(30);
        }
        $pdf->Cell(60, 10, "_______________________          _______________________                              __________________");
        $pdf->Ln(5);
        $pdf->Cell(60, 10, "Nombre de quien Recibe                  Firma de Recibido                                                  Fecha de Recibo ");

        //$pdf->Output('Transferencia '.$datostrans->DOCUMENTO .'.pdf', 'i');
        /* Falta crear consulta que traiga el número de folio generado */
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerpdf_ch.jpg', 10, 15, 205, 55);
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->Ln(70);
        foreach ($cabecera as $t) {

            $tipo = $t->CAMPLIB2;
            if ($tipo == 'E') {
                $tipo = 'Entrega';
            } elseif ($tipo == 'R') {
                $tipo = 'Recoleccion';
            } else {
                $tipo = 'No reconocido';
            }
            $pdf->Cell(60, 10, "Fecha: ");
            $pdf->Cell(60, 10, $t->FECHAELAB);
            $pdf->Ln(12);
            $pdf->Cell(60, 10, "Documento: ");
            $pdf->Cell(60, 10, $t->CVE_DOC);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Elaborado por: ");
            $pdf->Cell(60, 10, $t->REALIZA);
            $pdf->Ln(12);
            $pdf->Cell(60, 10, "Nombre: ");
            $pdf->Cell(60, 10, $t->NOMBRE);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Direccion: ");
            $pdf->Cell(60, 10, $t->CALLE . " No Ext: " . $t->NUMEXT . " No. Int:" . $t->NUMINT . " Colonia: " . $t->COLONIA);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Direccion 2: ");
            $pdf->Cell(60, 10, $t->CODIGO . $t->MUNICIPIO . " Telefono :" . $t->TELEFONO);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Confirmado con: ");
            $pdf->Cell(60, 10, $t->CAMPLIB4);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Fecha De Entrega: ");
            $pdf->Cell(60, 10, $t->CAMPLIB3);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Tipo: ");
            $pdf->Cell(60, 10, $tipo);
            $pdf->Ln(12);
        }

        $pdf->SetFont('Times', 'I', 9);

        $pdf->Cell(20, 7, 'Partida', 1);
        $pdf->Cell(23, 7, 'Pedido', 1);
        $pdf->Cell(33, 7, 'Articulo', 1);
        $pdf->Cell(70, 7, 'Descr', 1);
        $pdf->Cell(20, 7, 'Cantidad', 1);
        $pdf->Cell(25, 7, 'Total Partida', 1);
        $pdf->Ln();
        foreach ($detalle as $col) {
            $pdf->Cell(20, 7, $col->NUM_PAR, 1);
            $pdf->Cell(23, 7, $col->COTIZA, 1);
            $pdf->Cell(33, 7, $col->CVE_ART, 1);
            $pdf->Cell(70, 7, $col->DESCR, 1);
            $pdf->Cell(20, 7, $col->CANT, 1);
            $pdf->Cell(25, 7, $col->TOT_PARTIDA, 1);
            $pdf->Ln();
        }

        foreach ($cabecera as $t1) {
            $pdf->Output('Transferencia' . $t1->CVE_DOC . '.pdf', 'i');
        }
    }

    function ImprimeCredito($id, $doc) {
        $data = new pegaso;
        $actstatus = $data->ActStatusImpresoCredito($id);
        $actruta = $data->ActRuta($id, $doc);
        $datostrans = $data->ObtieneDatosCredito($id);
        $cabecera = $data->OCL($doc);
        $detalle = $data->detalleOC($doc);

        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->SetTextColor(198, 23, 23);
        $pdf->SetXY(180, 5);
        $pdf->CELL(60, 5, $datostrans->CREDITO);
        $pdf->Image('app/views/images/headerpdf_cr.jpg', 10, 15, 205, 55);
        $pdf->SetFont('Arial', 'I', 11);
        $pdf->SetTextColor(14, 3, 3);
        $pdf->Ln(70);
        $pdf->Cell(60, 10, "Fecha: ");
        $pdf->Cell(60, 10, $datostrans->FECHA);
        $pdf->Ln(15);
        $pdf->Cell(60, 10, "Beneficiario: ");
        $pdf->Cell(60, 10, $datostrans->BENEFICIARIO);
        $pdf->Ln(15);
        $pdf->Cell(60, 10, "Documento: ");
        $pdf->Cell(60, 10, $datostrans->DOCUMENTO);
        $pdf->Ln(15);
        $pdf->Cell(60, 10, "Importe: ");
        $pdf->Cell(60, 10, "$ " . $datostrans->MONTO);
        $pdf->Ln(15);
        $pdf->Cell(60, 10, "Fecha Realizacion: ");
        $pdf->Cell(60, 10, $datostrans->FECHA_APLI);
        $pdf->Ln(15);
        $pdf->Cell(60, 10, "Fecha de Recoleccion: ");
        foreach ($cabecera as $clib) {
            $pdf->Cell(60, 10, $clib->CAMPLIB3);
            $pdf->Ln(15);
            $pdf->Cell(60, 10, "Observaciones: ");
            $pdf->Cell(60, 10, $clib->STR_OBS);
            $pdf->Ln(10);
        }
        $pdf->Ln(20);
        $pdf->Cell(60, 10, "_______________________          _______________________                              __________________");
        $pdf->Ln(5);
        $pdf->Cell(60, 10, "Nombre de quien Recibe                  Firma de Recibido                                                  Fecha de Recibo ");

        //$pdf->Output('Transferencia '.$datostrans->DOCUMENTO .'.pdf', 'i');

        /* Falta crear consulta que traiga el número de folio generado */
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerpdf_cr.jpg', 10, 15, 205, 55);
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->Ln(70);
        foreach ($cabecera as $t) {

            $tipo = $t->CAMPLIB2;
            if ($tipo == 'E') {
                $tipo = 'Entrega';
            } elseif ($tipo == 'R') {
                $tipo = 'Recoleccion';
            } else {
                $tipo = 'No reconocido';
            }
            $pdf->Cell(60, 10, "Fecha: ");
            $pdf->Cell(60, 10, $t->FECHAELAB);
            $pdf->Ln(12);
            $pdf->Cell(60, 10, "Documento: ");
            $pdf->Cell(60, 10, $t->CVE_DOC);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Elaborado por: ");
            $pdf->Cell(60, 10, $t->REALIZA);
            $pdf->Ln(12);
            $pdf->Cell(60, 10, "Nombre: ");
            $pdf->Cell(60, 10, $t->NOMBRE);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Direccion: ");
            $pdf->Cell(60, 10, $t->CALLE . " No Ext: " . $t->NUMEXT . " No. Int:" . $t->NUMINT . " Colonia: " . $t->COLONIA);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Direccion 2: ");
            $pdf->Cell(60, 10, $t->CODIGO . $t->MUNICIPIO . " Telefono :" . $t->TELEFONO);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Confirmado con: ");
            $pdf->Cell(60, 10, $t->CAMPLIB4);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Fecha De Entrega: ");
            $pdf->Cell(60, 10, $t->CAMPLIB3);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Tipo: ");
            $pdf->Cell(60, 10, $tipo);
            $pdf->Ln(12);
        }

        $pdf->SetFont('Times', 'I', 9);

        $pdf->Cell(20, 7, 'Partida', 1);
        $pdf->Cell(23, 7, 'Pedido', 1);
        $pdf->Cell(33, 7, 'Articulo', 1);
        $pdf->Cell(70, 7, 'Descr', 1);
        $pdf->Cell(20, 7, 'Cantidad', 1);
        $pdf->Cell(25, 7, 'Total Partida', 1);
        $pdf->Ln();
        foreach ($detalle as $col) {
            $pdf->Cell(20, 7, $col->NUM_PAR, 1);
            $pdf->Cell(23, 7, $col->COTIZA, 1);
            $pdf->Cell(33, 7, $col->CVE_ART, 1);
            $pdf->Cell(70, 7, $col->DESCR, 1);
            $pdf->Cell(20, 7, $col->CANT, 1);
            $pdf->Cell(25, 7, $col->TOT_PARTIDA, 1);
            $pdf->Ln();
        }

        foreach ($cabecera as $t1) {
            $pdf->Output('Transferencia' . $t1->CVE_DOC . '.pdf', 'i');
        }
    }

    function verUniRutas($unidad) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verruta.php');
            ob_start();
            $rutas = $data->verUnidadesRutas($unidad);
            if (count($rutas)) {
                include 'app/views/pages/p.verruta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verUniRuta() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verruta.php');
            ob_start();
            $rutas = $data->verUnidadesRuta();
            if (count($rutas)) {
                include 'app/views/pages/p.verruta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verUniRutasEdoMex() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verruta.php');
            ob_start();
            $rutas = $data->verUnidadesRutasEdoMex();
            if (count($rutas)) {
                include 'app/views/pages/p.verruta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function FUnidades() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.formunidades.php');
            ob_start();
            $unidades = $data->verUnidades();

            if (count($unidades)) {
                include 'app/views/pages/p.formunidades.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function AltaUnidadesF($numero, $marca, $modelo, $placas, $operador, $tipo, $tipo2, $coordinador) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.formunidades.php');
            ob_start();
            $insertaU = $data->InsertaNUnidad($numero, $marca, $modelo, $placas, $operador, $tipo, $tipo2, $coordinador);
            $unidades = $data->verUnidades();

            if (count($unidades)) {
                include 'app/views/pages/p.formunidades.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verUnidad($unidad) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verunidad.php');
            ob_start();
            $unidades = $data->verUnidad($unidad);
            if (count($unidades)) {
                include 'app/views/pages/p.verunidad.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function asignaSec($docu, $secu, $unidad, $fechai, $fechaf) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verruta_r.php');
            ob_start();
            $secuencia = $data->asignaSecu($docu, $secu, $unidad, $fechai, $fechaf);
            $rutas = $data->verUnidadesRuta3($unidad);
            if (count($rutas)) {
                include 'app/views/pages/p.verruta_r.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function RutaUnidad($id) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verruta_r.php');
            ob_start();
            $rutas = $data->verRutasxUnidad($id);
            if (count($rutas)) {
                include 'app/views/pages/p.verruta_r.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function AdminRuta() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.msublogistica.php');
            ob_start();
            $unidad = $data->CreaSubMenu();
            if (count($unidad)) {
                include 'app/views/modules/m.msublogistica.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

// AdmnRutan nuevo final

    function SubMenuSecuencias() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.msublogsec.php');
            ob_start();
            $unidad = $data->CreaSubMenu();
            if (count($unidad)) {
                include 'app/views/modules/m.msublogsec.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function AdmonUnidad($idr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.admonruta.php');
            ob_start();
            $unidad = $data->AdmonRutasxUnidad($idr);
            $entrega = $data->AdmonRutasxUnidadEntrega($idr);
            if (count($unidad) > 0 or count($entrega) > 0) {
                $_SESSION['unidad_idr'] = $idr;
                include 'app/views/pages/p.admonruta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function AdmonUnidadForaneo($idr) {  // Controller especial para las entregas foraneas solo se activa cuando la idr = 23 ya que ese id esta asignado a la ruta 102, si algún usuario irresponsable asigna la ruta 102 a otra unidad sin aviso podria provocar que este controller falle 10.08.2016 ICA
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.admonrutaForaneo.php');
            ob_start();
            #$unidad=$data->AdmonRutasxUnidad($idr);
            $entrega = $data->AdmonRutasxUnidadEntregaForaneo($idr);
            if (count($entrega) > 0) {
                $_SESSION['unidad_idr'] = $idr;
                include 'app/views/pages/p.admonrutaForaneo.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function DefRuta($doc, $secuencia, $uni, $tipo, $idu) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.admonruta.php');
            ob_start();
            $define = $data->DefineRuta($doc, $secuencia, $uni, $tipo);
            $entrega = $data->AdmonRutasxUnidadEntrega2($uni);
            $unidad = $data->AdmonRutasxUnidad2($doc, $secuencia, $uni, $tipo);
            $RO = $data->DefineResultadoFinRO($doc, $tipo);
            if (count($unidad) or count($entrega) > 0) {
                include 'app/views/pages/p.admonruta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function DefRutaForaneo($doc, $idu, $guia, $fletera, $cpdestino, $destino, $fechaestimada) { //Define la ruta de la caja para envió foreaneo
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "RutaUnidad&idr={$idu}";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $exec = $data->defineRutaForaneo($doc, $guia, $fletera, $cpdestino, $destino, $fechaestimada);
            $RO = $data->DefineResultadoFinRO($doc, 'Envio');
            //var_dump($exec);
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function GuardarGuiaForaneo($ped, $target_file_cc, $idr) { // guarda en la BD la ruta del comprobante guia foraneo ICA
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "RutaUnidad&idr={$idr}";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $exec = $data->guardaGuiaForaneo($ped, $target_file_cc);
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function SecuenciaUnidad($prove, $secuencia, $uni, $fecha, $idu) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.secunidad.php');
            ob_start();
            $secuenciaDetalle = $data->AsignaSecDetalle($idu);
            $cvedoc = $data->ObiteneDataSecRO($prove, $uni);
            $datasec = $data->SecRo($cvedoc, $secuencia);
            $AS = $data->SecUni($prove, $secuencia, $uni, $fecha);
            $secuenciaentrega = $data->AsignaSecEntrega2($prove, $secuencia, $uni, $fecha, $idu); /// Muestra las OC
            $secuencia = $data->AsignaSec2($prove, $secuencia, $uni, $fecha, $idu); /// Muestra las OC
            if (count($secuencia)) {
                include 'app/views/pages/p.secunidad.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function SubMenuFallidos() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.msubfallidos.php');
            ob_start();
            $unidad = $data->CreaSubMenu();
            if (count($unidad)) {
                include 'app/views/modules/m.msubfallidos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verFallidos($idf) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.rutafallida.php');
            ob_start();
            $fallido = $data->VerFallidos($idf);
            if (count($fallido)) {
                include 'app/views/pages/p.rutafallida.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function FinalizaRuta($idf, $secuencia, $uni, $motivo, $doc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.rutafallida.php');
            ob_start();
            $motivo = $data->FinalizaRuta($idf, $secuencia, $uni, $motivo, $doc);
            $fallido = $data->VerFallidos($idf, $secuencia, $uni, $motivo, $doc);
            if (count($fallido)) {
                include 'app/views/pages/p.rutafallida.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function FinalizaReEnRuta($idf, $motivo, $doc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.reenrutar.php');
            ob_start();
            $motivo = $data->FinalizaReEnRuta($idf, $motivo, $doc);       // <---- Finaliza re enruta consulta de actualización
            $fallido = $data->VerReEnrutar($idf, $motivo, $doc);
            if (count($fallido)) {
                include 'app/views/pages/p.reenrutar.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ocFallidas() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.repfallida.php');
            ob_start();
            $fallido = $data->VerOCFallidas();
            if (count($fallido)) {
                include 'app/views/pages/p.repfallida.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function VerRutaDia() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.repruta.php');
            ob_start();
            $rutadia = $data->VerRutaDia();
            if (count($rutadia)) {
                include 'app/views/pages/p.repruta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /* function SubMenuRutaDia(){
      session_cache_limiter('private_no_expire');
      if(isset($_SESSION['user']) && $_SESSION['user']->USER_ROL == 'logistica'){
      $pagina = $this->load_template('Menu Admin');
      $html = $this->load_page('app/views/modules/m.msubrutadia.php');
      $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
      $this-> view_page($pagina);
      }else{
      $e = "Favor de Revisar sus datos";
      header('Location: index.php?action=login&e='.urlencode($e)); exit;
      }
      } */

    function SubMenuRutaDia() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.msubrutadia.php');
            ob_start();
            $unidad = $data->CreaSubMenu();
            if (count($unidad)) {
                include 'app/views/modules/m.msubrutadia.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function RutaXUnidad($idr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.repruta.php');
            $funcion_actual = __FUNCTION__;
            echo $funcion_actual;
            ob_start();
            $rutaxdia = $data->VerRutaXDia($idr);
            if (count($rutaxdia)) {
                include 'app/views/pages/p.repruta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ImpResultadosXdiaXuni($unidad) {
        $data = new Pegaso;
        $rutaxdia = $data->VerRutaXDia($unidad);
        $datauni = $data->DatosUnidad($unidad);
        $hoy = date("d-m-Y");
        $pdf = new FPDF('P', 'mm', 'Letter');

        $pdf->AddPage();
        $pdf->Image('app/views/images/headerOCpdf.jpg', 10, 15, 205, 55);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Ln(60);
        $pdf->Cell(30, 10, "Fecha: ");
        $pdf->Cell(60, 10, $hoy);
        $pdf->Ln(8);
        $pdf->Cell(30, 10, "Unidad: ");
        $pdf->Cell(60, 10, $datauni[0][0] . "  Placas: " . $datauni[0][3]);
        $pdf->Ln(8);
        $pdf->Cell(30, 10, "Operador: ");
        $pdf->Cell(60, 10, $datauni[0][4]);
        $pdf->Ln(8);
        $pdf->Cell(30, 10, "Coordinador: ");
        $pdf->Cell(60, 10, $datauni[0][5]);
        $pdf->Ln(12);

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(13, 6, "Orden", 1);
        $pdf->Cell(70, 6, "Proveedor", 1);
        $pdf->Cell(20, 6, "Estado", 1);
        $pdf->Cell(12, 6, "C.P.", 1);
        $pdf->Cell(20, 6, "Fecha Orden", 1);
        $pdf->Cell(8, 6, "Dias", 1);
        $pdf->Cell(8, 6, "Sec", 1);
        $pdf->Cell(20, 6, "Fecha cita", 1);
        $pdf->Cell(12, 6, "Urgente", 1);
        $pdf->Cell(15, 6, "Resultado", 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 8);
        foreach ($rutaxdia as $row) {
            if (($row->URGENTE) == 'U') {
                $pdf->SetDrawColor(255, 133, 102);
                $pdf->SetFillColor(255, 133, 102);
            } else {
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetFillColor(255, 255, 255);
            }
            $estado = ($row->ESTADOPROV = "ESTADO DE MEXICO") ? "Edo. Mex" : $row->ESTADOPROV;
            $pdf->Cell(13, 6, $row->CVE_DOC, 1, 0, 'C', true);
            $pdf->Cell(70, 6, $row->NOMBRE, 1, 0, 'C', true);
            $pdf->Cell(20, 6, $estado, 1, 0, 'C', true);
            $pdf->Cell(12, 6, $row->CODIGO, 1, 0, 'C', true);
            $pdf->Cell(20, 6, $row->FECHA, 1, 0, 'C', true);
            $pdf->Cell(8, 6, $row->DIAS, 1, 0, 'C', true);
            $pdf->Cell(8, 6, $row->SECUENCIA, 1, 0, 'C', true);
            $pdf->cell(20, 6, $row->CITA, 1, 0, 'C', true);
            $pdf->Cell(12, 6, $row->URGENTE, 1, 0, 'C', true);
            $pdf->Cell(15, 6, $row->STATUS_LOG, 1, 0, 'C', true);
            $pdf->Ln();
        }

        $pdf->Output('Resultados unidad ' . $datauni[0][0] . '.pdf', 'i');
    }

    function defineHoraInicio($documento) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $unidad = $data->asignaHoraInicio($documento);
            $unidad = $data->asignaHoraInicioRO($documento);
            $this->AdmonUnidad($_SESSION['unidad_idr']);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function defineHoraFin($documento) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $unidad = $data->asignaHoraFin($documento);
            $unidad = $data->asignaHoraFinRO($documento);
            $this->AdmonUnidad($_SESSION['unidad_idr']);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /*
      function SubMenuTotales(){
      session_cache_limiter('private_no_expire');
      if(isset($_SESSION['user']) && $_SESSION['user']->USER_ROL == 'logistica'){
      $pagina = $this->load_template('Menu Admin');
      $html = $this->load_page('app/views/modules/m.msubtotales.php');
      $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
      $this-> view_page($pagina);
      }else{
      $e = "Favor de Revisar sus datos";
      header('Location: index.php?action=login&e='.urlencode($e)); exit;
      }
      } */

    function SubMenuTotales() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.msubtotales.php');
            ob_start();
            $unidad = $data->CreaSubMenu();
            if (count($unidad)) {
                include 'app/views/modules/m.msubtotales.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verTotales($idf) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.vertotal.php');
            ob_start();
            $fallido = $data->VerTotales($idf);
            if (count($fallido)) {
                include 'app/views/pages/p.vertotal.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /*
      function SubMenuPnoenrutar(){									//Israel---------------
      session_cache_limiter('private_no_expire');
      if(isset($_SESSION['user']) && $_SESSION['user']->USER_ROL == 'logistica'){
      $pagina = $this->load_template('Menu Admin');
      $html = $this->load_page('app/views/modules/m.msubnoenrutar.php');
      $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
      $this-> view_page($pagina);
      }else{
      $e = "Favor de Revisar sus datos";
      header('Location: index.php?action=login&e='.urlencode($e)); exit;
      }
      } */

    function SubMenuPnoenrutar() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.msubnoenrutar.php');
            ob_start();
            $unidad = $data->CreaSubMenu();
            if (count($unidad)) {
                include 'app/views/modules/m.msubnoenrutar.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verPnoEnrutar($idf) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.parcialnoenrutar.php');
            ob_start();
            $fallido = $data->VerPnoEnrutar($idf);
            if (count($fallido)) {
                include 'app/views/pages/p.parcialnoenrutar.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /* function SubMenuReEnrutar(){									//Israel---------------
      session_cache_limiter('private_no_expire');
      if(isset($_SESSION['user']) && $_SESSION['user']->USER_ROL == 'logistica'){
      $pagina = $this->load_template('Menu Admin');
      $html = $this->load_page('app/views/modules/m.msubenrutar.php');
      $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
      $this-> view_page($pagina);
      }else{
      $e = "Favor de Revisar sus datos";
      header('Location: index.php?action=login&e='.urlencode($e)); exit;
      }
      } */

    function SubMenuReEnrutar() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.msubenrutar.php');
            ob_start();
            $unidad = $data->CreaSubMenu();
            if (count($unidad)) {
                include 'app/views/modules/m.msubenrutar.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verReEnrutar($idf) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.reenrutar.php');
            ob_start();
            $fallido = $data->VerReEnrutar($idf);
            if (count($fallido)) {
                include 'app/views/pages/p.reenrutar.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function CorrigeRuta() {        //22-03-2016 ICA
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.corrigeruta.php');
            ob_start();
            $unidad = $data->Logistica();
            if (count($unidad)) {
                include 'app/views/pages/p.corrigeruta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    //// ver recepciones OFA.

    function verRecepciones() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.recepcion.php');
            ob_start();
            echo 'Bloqueado por el area de Sistemas';
            //break;
            $Recepciones = $data->verRecepciones();
            // var_dump($Recepciones);
            if (count($Recepciones) > 0) {
                include 'app/views/pages/p.recepcion.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function imprimeRecepcion($doc) {
        $data = new pegaso;
        //$actRecep = $data->ActStatusImpresoRecep($doc);
        //$datos = $data->ObtieneDatosCredito($id);
        $cabecera = $data->RECEP($doc);
        $detalle = $data->detalleRECEP($doc);

        $pdf = new FPDF('P', 'mm', 'Letter');
        //$pdf->Output('Transferencia '.$datostrans->DOCUMENTO .'.pdf', 'i');
        /* Falta crear consulta que traiga el número de folio generado */
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerpdf_cr.jpg', 10, 15, 205, 55);
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->Ln(70);
        foreach ($cabecera as $t) {

            $tipo = $t->CAMPLIB2;
            if ($tipo == 'E') {
                $tipo = 'Entrega';
            } elseif ($tipo == 'R') {
                $tipo = 'Recoleccion';
            } else {
                $tipo = 'No reconocido';
            }
            $pdf->Cell(60, 10, "Fecha Recepcion: ");
            $pdf->Cell(60, 10, $t->FECHAELAB);
            $pdf->Ln(12);
            $pdf->Cell(60, 10, "No de Recepcion: ");
            $pdf->Cell(60, 10, $t->CVE_DOC);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "PROVEEDOR: ");
            $pdf->Cell(60, 10, $t->NOMBRE);
            $pdf->Ln(12);
            $pdf->Cell(60, 10, "FECHA Orden de Compra: ");
            $pdf->Cell(60, 10, $t->FECHAOC);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Orden de Compra: ");
            $pdf->Cell(60, 10, $t->DOC_ANT);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Operador: ");
            $pdf->Cell(60, 10, $t->TELEFONO);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Confirmado con: ");
            $pdf->Cell(60, 10, $t->CAMPLIB4);
            $pdf->Ln(8);
        }

        $pdf->SetFont('Times', 'I', 9);

        $pdf->Cell(20, 7, 'Partida', 1);
        $pdf->Cell(23, 7, 'Pedido', 1);
        $pdf->Cell(33, 7, 'Articulo', 1);
        $pdf->Cell(70, 7, 'Descr', 1);
        $pdf->Cell(20, 7, 'Cantidad', 1);
        $pdf->Cell(25, 7, 'Total Partida', 1);
        $pdf->Ln();
        foreach ($detalle as $col) {
            $pdf->Cell(20, 7, $col->NUM_PAR, 1);
            $pdf->Cell(23, 7, $col->COTIZA, 1);
            $pdf->Cell(33, 7, $col->CVE_ART, 1);
            $pdf->Cell(70, 7, $col->DESCR, 1);
            $pdf->Cell(20, 7, $col->CANT, 1);
            $pdf->Cell(25, 7, $col->TOT_PARTIDA, 1);
            $pdf->Ln();
        }

        foreach ($cabecera as $t1) {
            $pdf->Output('Transferencia' . $t1->CVE_DOC . '.pdf', 'i');
        }
    }

    function CorregirRuta($doc, $tipo, $uni, $tipoA) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.corrigeruta.php');
            ob_start();
            $define = $data->LinUniOC($doc, $tipo, $uni, $tipoA);
            $unidad = $data->Logistica();
            if (count($unidad)) {
                include 'app/views/pages/p.corrigeruta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ComprasMaestro() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.mcompras.php');
            ob_start();
            $consulta1 = $data->CotizacionSinCompra();
            $consulta2 = $data->OCSinPago();
            $consulta3 = $data->OCSinRuta();
            $consulta4 = $data->OCSinRecepcion();
            if (count($consulta1 > 0)) {
                include 'app/views/pages/p.mcompras.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ValidaRecepcion($docr, $doco) {  //28-03-2016 OFA
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.validarec.php');
            ob_start();
            $foliovalidacion = $data->FolioValidaRecepcion($docr, $doco);
            $redireccionar = "ValidaRecepcionConFolio&docr={$docr}&doco={$doco}&fval={$foliovalidacion}";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function validaRecepcionConFolio($docr, $doco, $fval) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.validarec.php');
            ob_start();
            $foliovalidacion = $fval;
            $recep = $data->ValidarRecepcion($docr, $doco);
            $parRecep = $data->PartidasRecep($docr, $doco);
            $parNoRecep = $data->PartidasNoRecep($docr, $doco);
            if (count($recep) > 0 or count($parRecep) > 0 or count($parNoRecep) > 0) {
                include 'app/views/pages/p.validarec.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /*   Ya no se utiliza, revisar el funcionamiento sin esta funcion.... OFA 09 de abril 2016 Revisar.
      function CambiaRecepCant($docr, $doco, $cantn, $coston, $cantorig, $costoorig, $idpreoc){		//28-03-2016 OFA
      session_cache_limiter('private_no_expire');
      if (isset($_SESSION['user'])){
      $data = new pegaso;
      $pagina=$this->load_template('Pedidos');
      $html=$this->load_page('app/views/pages/p.validarec.php');
      ob_start();
      $actcantidad=$data->ActCantParRecep($docr, $doco, $cantn, $coston, $cantorig, $costoorig, $idpreoc);
      $actpxr=$data->ActPXR($docr, $doco, $cantn, $coston, $cantorig, $costoorig, $idpreoc);
      $actCostoPar->ActCostoPar($docr, $doco, $cantn, $coston, $cantorig, $costoorig, $idpreoc);
      $actCostoCab->ActCostoCab($docr, $doco, $cantn, $coston, $cantorig, $costoorig, $idpreoc);
      $parNoRecep=$data->PartidasNoRecep($docr, $doco); //// Debe de traer la nueva vista de las partidas que no tengan una validacion.

      if (count($recep)){
      include 'app/views/pages/p.validarec.php';
      $table = ob_get_clean();
      $pagina = $this->replace_content('/\#CONTENIDO\#/ms',$table,$pagina);
      }else{
      $pagina = $this->replace_content('/\CONTENIDO\#/ms',$html.'<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
      }
      $this->view_page($pagina);
      }else{
      $e = "Favor de iniciar Sesión";
      header('Location: index.php?action=login&e='.urlencode($e)); exit;
      }
      }
     */

    /// Cambio OFA OK recpecion modificacion 08 de Abril 2016
    function ValRecepOK($docr, $doco, $cantn, $coston, $cantorig, $costoorig, $idpreoc, $idordencompra, $par, $fechadoco, $descripcion, $cveart, $fval, $desc1, $desc2, $desc3) { //28-03-2016 OFA
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.validarec.php');
            ob_start();
            $actRecepOk = $data->ActRecepOk($docr, $doco, $cantn, $coston, $cantorig, $costoorig, $idpreoc, $idordencompra, $par, $fechadoco, $descripcion, $cveart, $fval, $desc1, $desc2, $desc3);
            $parRecep = $data->PartidasRecep($docr, $doco);
            $congruencia = "R";
            $actRecepOkRO = $data->ActRecepRO($idordencompra, $congruencia);
            if ($parRecep) {
                $redireccionar = "ValidaRecepcionConFolio&docr={$docr}&doco={$doco}&fval={$fval}";
                $pagina = $this->load_template('Pedidos');
                $html = $this->load_page('app/views/pages/p.redirectform.php');
                include 'app/views/pages/p.redirectform.php';
                $this->view_page($pagina);
            } else {
                $this->verRecepciones();
            }
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function NoSuministrable($id) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.ordcompVal.php');
            $status = $data->StatusNoSuministrable($id);
            include 'app/views/pages/p.ordcompVal.php';
            //ob_start();
            //generamos consultas
            /* 		$status = $data->StatusNoSuministrable($id);
              $exec = $data->ConsultaOrdenComp('1');
              if(count($exec) > 0){
              include 'app/views/pages/p.ordcomp.php';
              $table = ob_get_clean();
              $pagina = $this->replace_content('/\#CONTENIDO\#/ms' ,$table , $pagina);
              }else{
              $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html.'<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
              }
              $this->view_page($pagina); */
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function VerNoSuministrableCompras() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.pednosumuniscom.php');
            ob_start();
            //generamos consultas
            $exec = $data->VerNoSuministrableC();
            $execm = $data->VerNoSuministrableCMotivo();
            if (count($exec) > 0) {
                include 'app/views/pages/p.pednosumuniscom.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function MotivoNS($id, $motivo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/redirect/p.pednosumisVal.php');
            include 'app/views/pages/redirect/p.pednosumisVal.php';
            ob_start();
            //generamos consultas
            //var_dump($id,$motivo);
            $actualizamotivo = $data->MotivoNoSuministrable($id, $motivo);
            /* 	$exec = $data->VerNoSuministrableC();
              if(count($exec) > 0){
              include 'app/views/pages/p.pednosumuniscom.php';
              $table = ob_get_clean();
              $pagina = $this->replace_content('/\#CONTENIDO\#/ms' ,$table , $pagina);
              }else{
              $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html.'<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
              }
              $this->view_page($pagina); */
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function VerNoSuministrableVentas() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.PedNoSuministrablesV.php');
            ob_start();
            //generamos consultas
            $exec = $data->VerNoSuministrableV();
            if (count($exec) > 0) {
                include 'app/views/pages/p.PedNoSuministrablesV.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function NoSuministrableV($id, $status) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.PedNoSuministrablesV.php');
            ob_start();
            //generamos consultas
            //var_dump($id,$motivo);
            $actualizamotivo = $data->StatusNoSuministrableV($id, $status);
            $exec = $data->VerNoSuministrableV();
            if (count($exec) > 0) {
                include 'app/views/pages/p.PedNoSuministrablesV.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function CambiaRecepCost($docr, $doco, $cantn, $coston, $cantorig, $costoorig, $idpreoc) { //28-03-2016 OFA
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.validarec.php');
            ob_start();
            ///$actCostoPar=$data->ActPXR($docr, $doco, $cantn, $coston, $cantorig, $costoorig, $idpreoc);
            $actCostoDoc = $data->ActRecCosto($docr, $doco, $cantn, $coston, $cantorig, $costoorig, $idpreoc);
            $actStatusPar = $data->ActStatusParRec($docr, $doco, $cantn, $coston, $cantorig, $costoorig, $idpreoc);
            $parNoRecep = $data->PartidasNoRecep($docr, $doco, $cantn, $coston, $cantorig, $costoorig, $idpreoc); //// Debe de traer la nueva vista de las partidas que no tengan una validacion.

            if (count($parNoRecep)) {
                include 'app/views/pages/p.validarec.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ImprimeRecepV() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.imprecep.php');
            ob_start();
            $Recepciones = $data->verRecepV();
            if (count($Recepciones) > 0) {
                include 'app/views/pages/p.imprecep.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ImpSaldoRec($doc) {
        $data = new pegaso;
        $cabecera = $data->RECEP($doc);
        $detalle = $data->detalleRECEP($doc);

        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerpdf.jpg', 10, 15, 205, 55);
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->Ln(70);
        foreach ($cabecera as $t) {

            $pdf->Cell(60, 10, "Fecha: ");
            $pdf->Cell(60, 10, $t->FECHAELAB);
            $pdf->Ln(12);
            $pdf->Cell(60, 10, "Documento: ");
            $pdf->Cell(60, 10, $t->CVE_DOC);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Nombre: ");
            $pdf->Cell(60, 10, $t->NOMBRE);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Direccion 2: ");
            $pdf->Cell(60, 10, $t->CODIGO . $t->MUNICIPIO . " Telefono :" . $t->TELEFONO);
            $pdf->Ln(8);
            $pdf->Cell(60, 10, "Recibido por: ");
            $pdf->Cell(60, 10, $t->CAMPLIB4);
            $pdf->Ln(8);
        }

        $pdf->SetFont('Times', 'I', 9);

        $pdf->Cell(10, 7, 'Par', 1);
        $pdf->Cell(20, 7, 'Articulo', 1);
        $pdf->Cell(60, 7, 'Descr', 1);
        $pdf->Cell(20, 7, 'Cant SAE', 1);
        $pdf->Cell(20, 7, 'Cant Prov', 1);
        $pdf->Cell(20, 7, 'Costo SAE', 1);
        $pdf->Cell(20, 7, 'Costo Prov', 1);
        $pdf->Cell(25, 7, 'Saldo Doc', 1);
        $pdf->Ln();
        foreach ($detalle as $col) {

            $dif = (($col->COST * $col->CANT) - $col->COST_REC);
            if ($dif > 1) {
                $dif = $dif;
            } else {
                $dif = 0;
            }

            $pdf->Cell(10, 7, $col->NUM_PAR, 1);
            $pdf->Cell(20, 7, $col->CVE_ART, 1);
            $pdf->Cell(60, 7, $col->DESCR, 1);
            $pdf->Cell(20, 7, $col->CANT, 1);
            $pdf->Cell(20, 7, $col->CANT_REC, 1);
            $pdf->Cell(20, 7, ($col->COST * $col->CANT), 1);
            $pdf->Cell(20, 7, $col->COST_REC, 1);
            $pdf->Cell(25, 7, $dif, 1);
            $pdf->Ln();
        }

        foreach ($cabecera as $t1) {
            $pdf->Output('ValRecep' . $t1->CVE_DOC . '.pdf', 'i');
        }
        //$pdf->Output('ValRecep'.$t1->CVE_DOC.'.pdf', 'i');
    }

    function ValRecepNo($docr, $doco, $cantn, $coston, $cantorig, $costoorig, $idpreoc) {  //28-03-2016 OFA
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.validarec.php');
            ob_start();
            $actRecepNo = $data->ActRecepNo($docr, $doco, $cantn, $coston, $cantorig, $costoorig, $idpreoc);
            $recep = $data->ValidarRecepcion($docr, $doco);
            $parRecep = $data->PartidasRecep($docr, $doco);
            $parNoRecep = $data->PartidasNoRecep($docr, $doco);
            $congruencia = "R";
            $actRecepOkRO = $data->ActRecepRO($idordencompra, $congruencia);
            ////$Recepciones=$data->verRecepcion($docr, $doco, $cantn, $coston, $cantorig, $costoorig, $idpreoc); //// Debe de traer la nueva vista de las partidas que no tengan una validacion.
            if (count($recep)) {
                include 'app/views/pages/p.validarec.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

//// INICIA EL MODULO DE COTIZACIONES CFA--


    function consultarCotizaciones($cerradas = false) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.cotizacion.php');
            ob_start();
            $exec = $data->consultarCotizaciones($cerradas);
            if (count($exec) > 0) {
                include 'app/views/pages/p.cotizacion.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>No se ha logrado encontrar registros.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function actualizaCotizacion($folio, $partida, $articulo, $precio, $descuento, $cantidad) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $data->actualizaCotizacion($folio, $partida, $articulo, $precio, $descuento, $cantidad);
            $this->verDetalleCotizacion($folio);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function insertaCotizacion($cliente, $identificadorDocumento) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $data->insertaCotizacion($cliente, $identificadorDocumento);
            $this->consultarCotizaciones();
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function actualizaPedidoCotizacion($folio, $pedido) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $data->actualizaPedidoCotizacion($folio, $pedido);
            $this->verDetalleCotizacion($folio);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function avanzaCotizacion($folio) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $data->avanzaCotizacion($folio);
            $this->consultarCotizaciones();
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function cancelaCotizacion($folio) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $data->cancelaCotizacion($folio);
            $this->consultarCotizaciones();
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verDetalleCotizacion($folio) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = '<div class="alert-info"><center><h2>No se han localizado partidas</h2><center></div>';
            ob_start();
            $cabecera = $data->cabeceraCotizacion($folio);
            $detalle = $data->detalleCotizacion($folio);
            if (count($detalle) > 0) {
                include 'app/views/pages/p.detalleCotizacion.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                include 'app/views/pages/p.detalleCotizacion.php';
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
                //$pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function consultarArticulo($cliente, $folio, $partida, $articulo, $descripcion) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            //$html = $this->load_page('app/views/pages/p.buscaArticulo.php');
            $html = '<div class="alert-info"><center><h2>No se han localizado registros</h2><center></div>';
            ob_start();
            $detalle = $data->listaArticulos($cliente, $articulo, $descripcion);
            $_SESSION['cliente'] = $cliente;
            $_SESSION['folio_cotizacion'] = $folio;
            $_SESSION['partida_cotizacion'] = $partida;
            if (count($detalle) > 0) {
                include 'app/views/pages/p.buscaArticulo.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                include 'app/views/pages/p.buscaArticulo.php';
                //$pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No se han localizado registros</h2><center></div>', $pagina);
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function consultarClientes($clave, $cliente) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = '<div class="alert-info"><center><h2>No se han localizado registros</h2><center></div>';
            ob_start();
            $detalle = $data->listadoClientes($clave, $cliente);
            if (count($detalle) > 0) {
                include 'app/views/pages/p.buscaCliente.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                include 'app/views/pages/p.buscaCliente.php';
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function quitarPartida($folio, $partida) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {

            $data = new pegaso;
            $data->quitarCotizacionPartida($folio, $partida);
            $this->verDetalleCotizacion($folio);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function moverClienteCotizacion($folio, $cliente) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $data->moverClienteCotizacion($folio, $cliente);
            $this->consultarCotizaciones();
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    ///// Termina Cotizaciones CFA-
//// Inicia el Modulo de Productos


    function CapturaProductos() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/pages/p.capturaproductos.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function TraeProveedores($prov) {
        $data = new pegaso;
        $exec = $data->TraeProveedores($prov);
        return $exec;
    }

    function TraeProductos($prod) {
        $data = new pegaso;
        $exec = $data->TraeProductos($prod);
        return $exec;
    }

    function VerCat10($alm) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = '<div class="alert-info"><center><h2>No se han localizado registros</h2><center></div>';
            ob_start();
            $productos = $data->VerCat10($alm);
            if (count($productos) > 0) {
                include 'app/views/pages/p.productos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                include 'app/views/pages/p.productos.php';
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function editProd($id) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.editproductos.php');
            ob_start();
            //generamos consultas
            $prod = $data->EditProd($id);
            if (count($prod) > 0) {
                include 'app/views/pages/p.editproductos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

// CAJAS
    function VerCajas($docf) {        //muestra el formulario para crear cajas y la tabla de cajas asignadas a la factura
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/pages/p.formcajasporfactura.php');
            ob_start();
            $validacion = $data->ValidaCajasAbiertas($docf);
            //echo $validacion[0][0];
            $_SESSION['factura'] = $docf;
            $datafact = $data->DataFactCaja($docf);
            @$exec = $data->CajasXFactura($docf);
            if (count($exec) > 0) {
                include 'app/views/pages/p.formcajasporfactura.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="col-lg-12"><div class="alert-danger"><center><h2>Factura sin cajas asignadas.</h2><center></div></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function CrearNuevaCaja($facturanuevacaja) {   //Generar una nueva caja para la factura en el parametro.
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $validacion = $data->ValidaCajasAbiertas($_SESSION['factura']);
            if ($validacion[0][0] == 0)
                $nuevacaja = $data->NuevaCaja($_SESSION['factura']);
            $this->VerCajas($_SESSION['factura']);
        }else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

//FIN CAJAS
/// Asignar el Material a las facturas, primer pantalla para seleccionar la Factura.

    function AsignaMaterial() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.pedidos.php');
            ob_start();
            $pedidos = $data->PorFacturarEntrega(); //// se utiliza la misma que GUstavo
            ///$facturas=$data->FacturaSinMaterial(); /// se deja la consulta actual para las que ya facturo Gustavo
            if (count($pedidos > 0)) {
                include 'app/views/pages/p.pedidos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function PreparaMaterial($docf, $idcaja) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.preparamaterial.php');
            ob_start();
            $facturas = $data->FacturasSinMat($docf);
            $parfacturaspar = $data->ParFactMaterialPar($docf, $idcaja);
            $parfacturas = $data->ParFactMaterial($docf, $idcaja);
            if (count($parfacturas) > 0 or count($facturas) > 0 or count($parfacturaspar) > 0) {
                include 'app/views/pages/p.preparamaterial.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function actualizarProducto($id, $clave, $descripcion, $marca1, $categoria, $desc1, $desc2, $desc3, $desc4, $desc5, $iva, $costo_total, $clave_prov, $codigo_prov1, $costo_prov1, $prov2, $codigo_prov2, $costo_prov2, $unidadcompra, $factorcompra, $unidadventa, $factorventa, $activo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;

            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.editproductos.php');
            ob_start();
            //generamos consultas
            $exec = $data->ActualizaProductos($id, $clave, $descripcion, $marca1, $categoria, $desc1, $desc2, $desc3, $desc4, $desc5, $iva, $costo_total, $clave_prov, $codigo_prov1, $costo_prov1, $prov2, $codigo_prov2, $costo_prov2, $unidadcompra, $factorcompra, $unidadventa, $factorventa, $activo);
            $productos = $data->VerCat10(10);
            if (count($exec) > 0) {
                include 'app/views/pages/p.productos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function AltaProductos($clave, $descripcion, $marca1, $categoria, $desc1, $desc2, $desc3, $desc4, $desc5, $iva, $costo_total, $clave_prov, $codigo_prov1, $costo_prov1, $prov2, $codigo_prov2, $costo_prov2, $unidadcompra, $factorcompra, $unidadventa, $factorventa) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;

            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.capturaproductos.php');
            ob_start();
            //generamos consultas
            $exec = $data->AltaProductos($clave, $descripcion, $marca1, $categoria, $desc1, $desc2, $desc3, $desc4, $desc5, $iva, $costo_total, $clave_prov, $codigo_prov1, $costo_prov1, $prov2, $codigo_prov2, $costo_prov2, $unidadcompra, $factorcompra, $unidadventa, $factorventa);
            if (count($exec) > 0) {
                include 'app/views/pages/p.capturaproductos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ModificaPreOrden($id) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            //$html = $this->load_page('app/views/modules/m.reporte_result.php');
            $html = $this->load_page('app/views/pages/p.modificapreorden.php');
            /* OB_START a partir de aqui guardara un buffer con la informacion que haya entre este y ob_get_clean(),
             * es necesario incluir la vista donde haremos uso de los datos como aqui el arreglo $exec */
            ob_start();
            //generamos consultas
            $exec = $data->DatosPreorden($id);
            //var_dump($exec);
            if (count($exec) > 0) {
                include 'app/views/pages/p.modificapreorden.php';
                /* hasta aqui podemos utilizar los datos almacenados en buffer desde la vista, por ejemplo el arreglo $exec
                 * sin tener que aparecer el arreglo en la vista, ya que lo llama desde memoria (Y), de nuevo, es necesario incluir la vista
                 * desde la cual haremos uso de los datos y luego mandarlo en el replace content como la nueva vista */
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function AlteraPedidoCotizacion($idPreorden, $claveproducto, $nombreproducto, $costo, $precio, $marca, $claveproveedor, $nombreproveedor, $cotizacion, $partida, $motivo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "VerNoSuministrableVentas";
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            ob_start();
            $updatepreoc = $data->UpdatePreoc($idPreorden, $motivo, $costo, $claveproveedor, $nombreproveedor);
            include 'app/views/pages/p.redirectform.php';
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function CancelaPreorden($id, $cotizacion, $partida, $motivo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/redirect/p.vernosuministrableVVal.php');
            ob_start();
            //generamos consultas
            $cancelaparfactp = $data->CancelaParFactP($cotizacion, $partida);
            $cabcelapreoc = $data->CancelaPreoc($id, $motivo);
            include 'app/views/pages/redirect/p.vernosuministrableVVal.php';
            /* 	$exec = $data->VerNoSuministrableV();
              if(count($exec) > 0){
              include 'app/views/pages/p.PedNoSuministrablesV.php';
              $table = ob_get_clean();
              $pagina = $this->replace_content('/\#CONTENIDO\#/ms' ,$table , $pagina);
              }else{
              $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html.'<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
              }
              $this->view_page($pagina); */
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function AsignaEmpaque($docf, $par, $canto, $idpreoc, $cantn, $empaque, $art, $desc, $idcaja, $tipopaq) {        //23062016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.preparamaterial.php');
            ob_start();
            $ActPartidas = $data->ActEmpaque($docf, $par, $canto, $idpreoc, $cantn, $empaque, $art, $desc, $idcaja); /// actualiza las partidas para meter la cantida de bultos.
            $insPaquete = $data->InsPaquete($docf, $par, $canto, $idpreoc, $cantn, $empaque, $art, $desc, $idcaja, $tipopaq);
            $actEmpaque = $data->ActEmpaqueDoc($docf, $par, $canto, $idpreoc, $cantn, $empaque, $art, $desc, $idcaja);
            //$parfacturaspar=$data->ParFactDoc($docf, $par, $canto, $idpreoc, $cantn, $empaque);///
            $facturas = $data->FacturasSinMat($docf);
            @$parfacturaspar = $data->ParFactMaterialPar($docf); //// Muestra las partidas que ya tienen un recepcion.
            $parfacturas = $data->ParFactMaterial($docf);
            if (count($facturas) > 0 or count($parfacturaspar) > 0 or count($parfacturas) > 0) {
                include 'app/views/pages/p.preparamaterial.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function Embalar() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.embalarmaterial1.php');
            ob_start();
            $paquetes = $data->verCajasAbiertas();
            if (count($paquetes) > 0) {
                include 'app/views/pages/p.embalarmaterial1.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function embalaje($docf) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.embalarmaterial.php');
            ob_start();
            $emba = $data->embalados($docf);
            $paquetespar = $data->verPaquetesEmb($docf);
            $detallepaq = $data->verDetallePaq($docf);
            if (count($paquetespar) > 0 or count($emba) > 0) {
                include 'app/views/pages/p.embalarmaterial.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ImpContenidoCaja($docf, $caja) {
        $data = new Pegaso;
        $emba = $data->embalados($docf);
        $datacaja = $data->DataCaja($caja);
        $totales = $data->embaladosTotales($docf, $caja);

        //$hoy = date("d-m-Y");
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerOCpdf.jpg', 10, 15, 205, 55);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Ln(60);

        foreach ($datacaja AS $caj) {
            $pdf->Cell(30, 10, "Creada: ");
            $pdf->Cell(60, 10, $caj->FECHA_CREACION);
            $pdf->Ln(8);
            $pdf->Cell(30, 10, "Caja: ");
            $pdf->Cell(60, 10, $caj->ID . "  Status: " . $caj->STATUS);
            $pdf->Ln(8);
            $pdf->Cell(30, 10, "Documento: ");
            $pdf->Cell(60, 10, $caj->CVE_FACT);
            $pdf->Ln(8);
            $pdf->Cell(30, 10, "Unidad: ");
            $pdf->Cell(60, 10, $caj->UNIDAD);
            $pdf->Ln(12);
        }

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(15, 6, "Id", 1);
        $pdf->Cell(10, 6, "Paq", 1);
        $pdf->Cell(25, 6, "Calve", 1);
        $pdf->Cell(75, 6, "Descripcion", 1);
        $pdf->Cell(15, 6, "Cantidad", 1);
        #$pdf->Cell(25,6,"Status Logistica",1);
        $pdf->Cell(15, 6, "Peso", 1);
        $pdf->Cell(25, 6, "Tipo", 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 8);
        foreach ($emba as $row) {
            $pdf->Cell(15, 6, $row->ID_PREOC, 1);
            $pdf->Cell(10, 6, $row->EMPAQUE, 1);
            $pdf->Cell(25, 6, $row->ARTICULO, 1);
            $pdf->Cell(75, 6, $row->DESCRIPCION, 1);
            $pdf->Cell(15, 6, $row->CANTIDAD, 1);
            #$pdf->Cell(25,6,$row->STATUS_LOG,1);
            $pdf->Cell(15, 6, $row->PESO, 1);
            $pdf->Cell(25, 6, $row->PAQUETE1 . " de " . $row->PAQUETE2, 1);
            $pdf->Ln();
        }
        /*
          $pdf->SetFont('Arial', 'I',12);
          $pdf->Ln(60);
          $pdf->SetX(140);
          $pdf->Write(6,"Subtotal       $ ".number_format($total_subtotal,4,'.',','));
          $pdf->Ln();
          $pdf->SetX(140);
          $pdf->Write(6,"I.V.A.         $ ".number_format($total_iva,4,'.',','));
          $pdf->Ln();
          $pdf->SetX(140);
          $pdf->Write(6,"Total          $ ".number_format($total_final,2,'.',','));
          $pdf->Ln();

          $pdf->SetFont('Arial', 'I', 8);
          $pdf->Cell(20,6,"Peso :");
          foreach($totales as $row2){
          $pdf->Cell(20,6,"Peso Total : ");
          $pdf->Cell(60,6, $row2->PESO);
          $pdf->Ln(8);
          $pdf->Cell(20,6,"Total Productos: ");
          $pdf->Cell(60,6, $row2->PARTIDAS);
          #$pdf->Cell(20,6,"Productos : " . $tot->PARTIDAS);
          #$pdf->

          }
         */
        ob_end_clean();
        $pdf->Output('Contenid.pdf', 'i');
    }

    function asignaembalaje($docf, $paquete1, $paquete2, $tipo, $peso, $alto, $largo, $ancho, $pesovol, $idc, $idemp) {      //23062016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.embalarmaterial.php');
            ob_start();
            $actembalaje = $data->AsignaEmbalaje($docf, $paquete1, $paquete2, $tipo, $peso, $alto, $largo, $ancho, $pesovol, $idc, $idemp);
            $emba = $data->embalados($docf, $paquete1, $paquete2, $tipo, $id, $peso, $alto, $largo, $ancho, $pesovol, $idc);
            $paquetespar = $data->verPaquetesEmb($docf);
            if (count($paquetespar) > 0 or count($emba > 0)) {
                include 'app/views/pages/p.embalarmaterial.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function VerRegistroOperadores($buscar) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/pages/p.formregistrooperadores.php');
            ob_start();
            //$exec = $data->ConsultaPreoc($ped);
            $operador = $data->CabeceraConsultaRO($buscar);
            $exec = $data->ConsultaRO($buscar);
            if (count($exec) > 0) {
                include 'app/views/pages/p.formregistrooperadores.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function VerRutasDelDia() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/pages/p.RutasDelDia.php');
            ob_start();
            $entrega = $data->RutasDelDiaEntrega();
            $exec = $data->RutasDelDia();
            if (count($exec) > 0 or count($entrega) > 0) {
                include 'app/views/pages/p.RutasDelDia.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    //Cancelar recepciones
    function VerRecepcionesAC() {      //ver recepciones a cancelar
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.recepcionesacancelar.php');
            ob_start();
            $recepcion = $data->DataRecepcionesAC();
            //vard_dump($recepcion);
            if (count($recepcion) > 0) {
                include 'app/views/pages/p.recepcionesacancelar.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function FormCR($orden, $recepcion) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.formCancelarRecepcion.php');
            ob_start();
            $recepcioncabecera = $data->DataRecepcionAC($recepcion);
            $partrecepcion = $data->PartidasRecepcionAC($recepcion);
            if (count($recepcioncabecera) > 0) {
                include 'app/views/pages/p.formCancelarRecepcion.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function CancelarRecepcion($orden, $recepcion) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.recepcionesacancelar.php');
            ob_start();
            $statusrecep = $data->StatusComprCR($recepcion);
            $statuspartrecep = $data->StatusPartComprCR($recepcion);
            $statuspreoc = $data->StatusPreocCR($recepcion);
            $statuscompo = $data->StatusCompoCR($recepcion);

            $recepcion = $data->DataRecepcionesAC();
            if (count($recepcion) > 0) {
                include 'app/views/pages/p.recepcionesacancelar.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function VerOrdenesSR() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/pages/p.ordenesSinrecepcion.php');
            ob_start();
            $exec = $data->OrdenSinRecepcion();
            if (count($exec) > 0) {
                include 'app/views/pages/p.ordenesSinrecepcion.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function Cajas() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/pages/p.vercajas.php');
            ob_start();
            $exec = $data->Cajas();
            if (count($exec) > 0) {
                include 'app/views/pages/p.vercajas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    ////cerrarCaja   UnidadEntrega($idcaja, $docf, $idcaja, $estado, $unidad)

    function cerrarCaja($idcaja, $docf) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/pages/p.vercajas.php');
            ob_start();
            $cerrar = $data->CerrarCaja($idcaja, $docf);
            ###$actpar=$data->cierraPar($idcaja, $docf);   FUNCION EN REVISION PENDIENTE
            $this->ImpContenidoCaja($docf, $idcaja);
            $exec = $data->Cajas();
            if (count($exec) > 0) {
                include 'app/views/pages/p.vercajas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function UnidadEntrega($idcaja, $docf, $idcaja, $estado, $unidad) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.aruta.php');
            ob_start();
            //generamos consultas
            $exec = $data->RutaEntregaSecuencia($idcaja, $docf, $idcaja, $estado, $unidad);
            ///$regoper = $data->RegistroOperadores($docu,$unidad); se le pude cambiar el docu por docf
            $entrega = $data->ARutaEntrega();
            $exec = $data->ARuta();  //// estas son las rutas de recoleccion.
            $unidad = $data->TraeUnidades();
            if (count($exec) > 0 or count($entrega) > 0) {
                include 'app/views/pages/p.aruta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function SecUnidadEntrega($idu, $clie, $unidad, $secuencia, $docf, $idcaja) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.secunidad.php');
            ob_start();
            $AS = $data->AsignaSecuenciaEntrega($idu, $clie, $unidad, $secuencia, $docf, $idcaja); //// Actualiza la secuencia de las Facturas.
            $secuenciaentrega = $data->AsignaSecEntrega($idu); /// muestra las Facturas
            $secuencia = $data->AsignaSec($idu); ///Muesta las Ordenes de compra.
            if (count($secuencia) > 0 or count($secuenciaentrega) > 0) {
                include 'app/views/pages/p.secunidad.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

//################# Ordenes de compra a avanzar #################
    function VerOrdenesAA() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.ordenesaavanzar.php');
            ob_start();
            $orden = $data->DataOrdenesAA();
            if (count($orden) > 0) {
                include 'app/views/pages/p.ordenesaavanzar.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function FormAvanzarOrden($idorden) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.formavanzarorden.php');
            ob_start();
            $orden = $data->DataOrdenAA($idorden);
            //var_dump($orden);
            $partorden = $data->PartidasOrdenAA($idorden);
            if (count($partorden) > 0) {
                include 'app/views/pages/p.formavanzarorden.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function AvanzarOC($idorden, $idpreoc, $partida) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $foliofp = $data->ObtienFolioFalsoPar();
            $foliop = $foliofp[0][0] + 1;
            $avanzaparcompo = $data->AvanzaParCompo($idorden, $partida, $foliop);
            $validapar = $data->ValidarPartidas($idorden);
            if ($validapar[0][0] == 0) {
                $foliof = $data->ObtienFolioFalso();
                $folio = $foliof[0][0] + 1;
                $avanzaparcompo = $data->AvanzaCompo($idorden, $folio);
                $this->VerOrdenesAA();
            } else
                $this->FormAvanzarOrden($idorden);
        }else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

//################# FINALIZA Ordenes de compra a avanzar #################
    /// Ver Productos por RFC   VerProdRFC2
    function VerProdRFC() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/pages/p.verprodrfc.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function VerProdRFC2($rfc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verprodrfc_r.php');
            ob_start();
            $productos = $data->prodxrfc($rfc);
            //// var_dump($productos);
            if (count($productos) > 0) {
                include 'app/views/pages/p.verprodrfc_r.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ImprimirSecuencia($unidad) {
        $data = new Pegaso;
        $secuencia = $data->AsignaSec($unidad);
        $datauni = $data->DatosUnidad($unidad);
        $secuenciaDetalle = $data->AsignaSecDetalle($unidad);
        $hoy = date("d-m-Y");
        $pdf = new FPDF('P', 'mm', 'Letter');

        $pdf->AddPage();
        $pdf->Image('app/views/images/headerOCpdf.jpg', 10, 15, 205, 55);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Ln(60);
        $pdf->Cell(30, 10, "Fecha: ");
        $pdf->Cell(60, 10, $hoy);
        $pdf->Ln(8);
        $pdf->Cell(30, 10, "Unidad: ");
        $pdf->Cell(60, 10, $datauni[0][0] . "  Placas: " . $datauni[0][3]);
        $pdf->Ln(8);
        $pdf->Cell(30, 10, "Operador: ");
        $pdf->Cell(60, 10, $datauni[0][4]);
        $pdf->Ln(8);
        $pdf->Cell(30, 10, "Coordinador: ");
        $pdf->Cell(60, 10, $datauni[0][5]);
        $pdf->Ln(12);

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(90, 6, "Proveedor", 1);
        $pdf->Cell(20, 6, "Estado", 1);
        $pdf->Cell(12, 6, "CP", 1);
        $pdf->Cell(20, 6, "Fecha Orden", 1);
        $pdf->Cell(8, 6, "Dias", 1);
        $pdf->Cell(15, 6, "Orden", 1);
        $pdf->Cell(15, 6, "Unidad", 1);
        $pdf->Cell(10, 6, "Sec", 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 8);
        foreach ($secuencia as $row) {
            $estado = ($row->ESTADOPROV = "ESTADO DE MEXICO") ? "Edo. Mex" : $row->ESTADOPROV;
            $pdf->Cell(90, 6, $row->NOMBRE . "\n", 1);
            $pdf->Cell(20, 6, $estado, 1);
            $pdf->Cell(12, 6, $row->CODIGO, 1);
            $pdf->Cell(20, 6, $row->FECHA, 1);
            $pdf->Cell(8, 6, $row->DIAS, 1);
            $pdf->Cell(15, 6, $row->CVE_DOC, 1);
            $pdf->Cell(15, 6, $row->UNIDAD, 1);
            $pdf->Cell(10, 6, "", 1);
            $pdf->Ln();
            foreach ($secuenciaDetalle as $oc) {
                if ($oc->CVE_CLPV == $row->PROV) {
                    $pdf->Cell(90, 6, $oc->CVE_DOC . "    Fecha: " . substr($oc->FECHA_DOC, 0, 10) . "     Dias: " . $oc->DIAS);
                    $pdf->Ln();
                }
            }
            $pdf->Ln();
        }

        $pdf->Output('Secuencia unidad ' . $datauni[0][0] . '.pdf', 'i');
    }

    function ImprimirSecuenciaEnt($unidad) {
        $data = new Pegaso;
        $secuenciaentrega = $data->AsignaSecEntrega($unidad);
        $datauni = $data->DatosUnidad($unidad);
        $hoy = date("d-m-Y");
        $pdf = new FPDF('P', 'mm', 'Letter');

        $pdf->AddPage();
        $pdf->Image('app/views/images/headerAsignacionSecuencia.jpg', 10, 15, 205, 55);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Ln(60);
        $pdf->Cell(30, 10, "Fecha: ");
        $pdf->Cell(60, 10, $hoy);
        $pdf->Ln(8);
        $pdf->Cell(30, 10, "Unidad: ");
        $pdf->Cell(60, 10, $datauni[0][0] . "  Placas: " . $datauni[0][3]);
        $pdf->Ln(8);
        $pdf->Cell(30, 10, "Operador: ");
        $pdf->Cell(60, 10, $datauni[0][4]);
        $pdf->Ln(8);
        $pdf->Cell(30, 10, "Coordinador: ");
        $pdf->Cell(60, 10, $datauni[0][5]);
        $pdf->Ln(12);

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(65, 6, "Cliente", 1);
        $pdf->Cell(12, 6, "Estado", 1);
        $pdf->Cell(10, 6, "CP", 1);
        $pdf->Cell(18, 6, "Fecha Factura", 1);
        $pdf->Cell(6, 6, "Dias", 1);
        $pdf->Cell(13, 6, "Pedido", 1);
        $pdf->Cell(38, 6, "Remision / Factura", 1);
        $pdf->Cell(15, 6, "Importe", 1);
        $pdf->Cell(8, 6, "Sec", 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 7);
        foreach ($secuenciaentrega as $row) {
            $estado = ($row->ESTADO = "ESTADO DE MEXICO") ? "Edo. Mex" : $row->ESTADOPROV;
            $pdf->Cell(65, 6, $row->NOMBRE, 1);
            $pdf->Cell(12, 6, $estado, 1);
            $pdf->Cell(10, 6, $row->CODIGO, 1);
            $pdf->Cell(18, 6, substr($row->FECHAELAB, 0, 10), 1);
            $pdf->Cell(6, 6, $row->DIAS, 1);
            $pdf->Cell(13, 6, $row->CVE_FACT, 1);
            $pdf->Cell(38, 6, $row->REMISION . " / " . $row->FACTURA, 1);
            $pdf->Cell(15, 6, '$ ' . number_format($row->IMPORTE, 2), 1);
            $pdf->Cell(8, 6, "", 1);
            $pdf->Ln();
        }

        $pdf->Output('Secuencia entrega unidad ' . $datauni[0][0] . '.pdf', 'i');
    }

    ///RecibeDocs

    function RecibeDocs($doc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.aruta.php');
            ob_start();
            $recibedoc = $data->recibirDoc($doc);
            $exec = $data->ARuta();
            $entrega = $data->ARutaEntrega();
            $unidad = $data->TraeUnidades();
            if (count($exec) > 0 or count($entrega) > 0) {
                include 'app/views/pages/p.aruta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function CierreRuta() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.msublogistica_c.php');
            ob_start();
            $unidad = $data->CreaSubMenu();
            if (count($unidad)) {
                include 'app/views/modules/m.msublogistica_c.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function CierraRutaUnidad($idr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.cierreruta.php');
            ob_start();
            $close = $data->habilitaImpresionCierre($idr);
            $close_ent = $data->habilitaImpresionCierreEnt($idr);
            $rutaunidadrec = $data->RutaUnidadRec($idr);
            $rutaunidadent = $data->RutaUnidadEnt($idr);
            if (count($rutaunidadrec) > 0 or count($rutaunidadent) > 0) {
                include 'app/views/pages/p.cierreruta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function AsignaSecuencia($unidad) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.secunidad.php');
            ob_start();
            $secuenciaDetalle = $data->AsignaSecDetalle($unidad);
            $secuenciaentrega = $data->AsignaSecEntrega($unidad);
            $secuencia = $data->AsignaSec($unidad);
            if (count($secuencia) or ( count($secuenciaentrega) > 0)) {
                include 'app/views/pages/p.secunidad.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function RecogeDocs($doc, $idr, $docs) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.cierreruta_r.php');
            ob_start();
            $regresadocs = $data->RegresaDocs($doc, $idr, $docs);
            $rutaunidadrec = $data->RutaUnidadRec($idr);
            $rutaunidadent = $data->RutaUnidadEnt($idr);
            if (count($rutaunidadrec) or ( count($rutaunidadent) > 0)) {
                include 'app/views/pages/p.cierreruta_r.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function CerrarRuta($doc, $idr, $tipo, $idc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.cierreruta.php');
            ob_start();
            $close = $data->habilitaImpresionCierre($idr);
            $close_ent = $data->habilitaImpresionCierreEnt($idr);
            $cerraroc = $data->CerrarOC($doc, $idr, $tipo, $idc);
            $rutaunidadrec = $data->RutaUnidadRec($idr);
            $rutaunidadent = $data->RutaUnidadEnt($idr);
            if (count($rutaunidadrec) or ( count($rutaunidadent) > 0)) {
                include 'app/views/pages/p.cierreruta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function cierrerutagen() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.cierrerutagenrec.php');
            ob_start();
            $permitircerrar = $data->CerrarGen();
            $rutaunidadrec = $data->RutaUnidadRecGen();
            if (count($rutaunidadrec)) {
                include 'app/views/pages/p.cierrerutagenrec.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function CerrarRecoleccion() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.cierrerutagenrec.php');
            ob_start();
            $permitircerrar = $data->CerrarGen();
            $rutaunidadrec = $data->RutaUnidadRecGen();
            if (count($rutaunidadrec)) {
                include 'app/views/pages/p.cierrerutagenrec.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /*
      function CerrarRec($documentos){
      session_cache_limiter('private_no_expire');
      if (isset($_SESSION['user'])){
      $data = new pegaso;
      $pagina=$this->load_template('Pedidos');
      $redireccionar = "cierrerutagen";
      $html=$this->load_page('app/views/pages/p.redirectform.php');
      ob_start();

      $data->CerrarRutasRecoleccion($documentos);
      include 'app/views/pages/p.redirectform.php';
      }else{
      $e = "Favor de iniciar Sesión";
      header('Location: index.php?action=login&e='.urlencode($e)); exit;
      }
      } */

    function CerrarRec($documentos) {        //27062016
        ob_start();
        $data = new Pegaso;
        $cierre = $data->insCierreRutaRecoleccion();
        $recoleccion = $data->RutaUnidadRecGen();
        $data->CerrarRutasRecoleccion($documentos);

        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerRECEPpdf.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);

        $pdf->SetFont('Arial', 'B', 7);

        $pdf->SetX(180);
        $pdf->Write(8, "Folio: ");
        foreach ($cierre as $fl) {
            $pdf->Write(8, $fl->NUEVOFOLIO);
        }

        $pdf->Ln();
        $pdf->Cell(12, 6, "OC", 1);
        $pdf->Cell(72, 6, "PROVEEDOR", 1);
        $pdf->Cell(24, 6, "FECHA ORDEN", 1);
        $pdf->Cell(10, 6, "PAGO T", 1);
        $pdf->Cell(24, 6, "FECHA PAGO", 1);

        $pdf->Cell(10, 6, "UNIDAD", 1);
        $pdf->Cell(11, 6, "TIPO", 1);

        $pdf->Cell(8, 6, "DCC", 1);
        $pdf->Cell(15, 6, "CERRADO?", 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 7);

        foreach ($recoleccion as $row) {
            $pdf->Cell(12, 6, $row->CVE_DOC, 1);
            $pdf->Cell(72, 6, $row->NOMBRE, 1);
            $pdf->Cell(24, 6, $row->FECHAELAB, 1);
            $pdf->Cell(10, 6, $row->PAGO_TES, 1);
            $pdf->Cell(24, 6, $row->FECHA_PAGO, 1);

            $pdf->Cell(10, 6, $row->UNIDAD, 1);
            $pdf->Cell(11, 6, $row->STATUS_LOG, 1);

            $pdf->Cell(8, 6, $row->DOCS, 1);
            $pdf->Cell(15, 6, $row->CIERRE_UNI, 1);
            $pdf->Ln();
        }


        ob_get_clean();
        $pdf->Output('Cierre Recoleccion.pdf', 'i');
    }

    function RVentasVsCobrado($fechaini, $fechafin, $vend) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.reportevendidovscobrado.php');
            ob_start();
            $ventcob = $data->VentasVsCobrado($fechaini, $fechafin, $vend);
            if (count($ventcob)) {
                include 'app/views/pages/p.reportevendidovscobrado.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ImprimirRecepcion($orden) {
        $data = new Pegaso;
        $parRecep = $data->PartidasNoRecep("0", $orden);
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerValacionRecepcion.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(10, 6, "ID", 1);
        $pdf->Cell(15, 6, "Recep", 1);
        $pdf->Cell(5, 6, "Par", 1);
        $pdf->Cell(55, 6, "Descripcion", 1);
        $pdf->Cell(10, 6, "Unidad", 1);
        $pdf->Cell(10, 6, "Orde", 1);
        $pdf->Cell(10, 6, "Valida", 1);
        $pdf->Cell(15, 6, "Monto", 1);
        $pdf->Cell(10, 6, "Saldo", 1);
        $pdf->Cell(10, 6, "PXR", 1);

        $pdf->Cell(15, 6, "SubTot", 1);
        $pdf->Cell(15, 6, "IVA", 1);
        $pdf->Cell(15, 6, "Total", 1);


        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 7);

        $total_oc = 0;
        $total_subtotal = 0;
        $total_iva = 0;
        $total_final = 0;
        foreach ($parRecep as $row) {

            $total_subtotal += ($row->COST_REC * $row->CANT_REC);
            $total_iva += ($row->COST_REC * $row->CANT_REC) * 0.16;
            $total_final += ($row->COST_REC * $row->CANT_REC) * 1.16;
            $total_oc += $row->TOT_PARTIDA;

            $pdf->Cell(10, 6, $row->ID_PREOC, 'L,T,R');
            $pdf->Cell(15, 6, trim($row->CVE_DOC), 'L,T,R');
            $pdf->Cell(5, 6, $row->NUM_PAR, 'L,T,R');
            $pdf->Cell(55, 6, substr($row->DESCR, 0, 34), 'L,T,R');
            $pdf->Cell(10, 6, $row->UNI_ALT, 'L,T,R');
            $pdf->Cell(10, 6, $row->CANT, 'L,T,R');
            $pdf->Cell(10, 6, $row->CANT_REC, 'L,T,R');
            $pdf->Cell(15, 6, round($row->TOT_PARTIDA, 2), 'L,T,R');
            $pdf->Cell(10, 6, round($row->SALDO, 2), 'L,T,R');
            $pdf->Cell(10, 6, $row->PXR, 'L,T,R');
            $pdf->Cell(15, 6, round(($row->COST_REC * $row->CANT_REC), 2), 'L,T,R'); ///  Subtotal
            $pdf->Cell(15, 6, round((($row->COST_REC * $row->CANT_REC) * 0.16), 2), 'L,T,R'); /// Costo antes de IVA
            $pdf->Cell(15, 6, round((($row->COST_REC * $row->CANT_REC) * 1.16), 2), 'L,T,R'); /// Costo Total con IVA s
            $pdf->Ln();        // Segunda linea descripcion
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(5, 6, "", 'L,B,R');
            $pdf->Cell(55, 6, substr($row->DESCR, 34, 70), 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');

            $pdf->Ln();
        }

        $res = round($total_oc, 2);
        $res2 = (round($total_subtotal, 2));

        if ((round($total_oc, 2) - round($total_subtotal, 2)) < 2 and ( round($total_oc, 2) - round($total_subtotal, 2) > -2))
            $mensaje = "SALDADO";
        elseif (round($total_oc, 2) - round > round($total_subtotal, 2))
            $mensaje = "DEUDOR";
        else
            $mensaje = "ACREDOR";

        $pdf->SetFont('Arial', 'B', 44);
        $pdf->Ln(8);
        $pdf->SetX(30);
        $pdf->Write(6, $mensaje);

        $pdf->SetFont('Arial', 'B', 12);

        $pdf->Ln(60);
        $pdf->SetX(140);
        $pdf->Write(6, "Subtotal       $ " . number_format($total_subtotal, 2, '.', ','));
        $pdf->Ln();
        $pdf->SetX(140);
        $pdf->Write(6, "I.V.A.         $ " . number_format($total_iva, 2, '.', ','));
        $pdf->Ln();
        $pdf->SetX(140);
        $pdf->Write(6, "Total          $ " . number_format($total_final, 2, '.', ','));
        $pdf->Ln();

        $pdf->Output('Secuencia entrega unidad .pdf', 'i');
    }

    function VerCatalogoGastos() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.catalogogastos.php');
            ob_start();
            $exec = $data->VerCatGastos();
            if (count($exec)) {
                include 'app/views/pages/p.catalogogastos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function GuardarNuevaCuenta($concepto, $descripcion, $iva, $cc, $cuenta, $gasto, $presupuesto, $retieneiva, $retieneisr, $retieneflete) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $redireccionar = 'Catalogo_Gastos';
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            ob_start();
            echo $retieneiva . $retieneisr . $retieneflete;
            $gastos = $data->guardarNuevaCuenta($concepto, $descripcion, $iva, $cc, $cuenta, $gasto, $presupuesto, $retieneiva, $retieneisr, $retieneflete);
            include 'app/views/pages/p.redirectform.php';
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /* editado por GDELEON 3/Ago/2016 */

    function EditCuentaGasto($id) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.formeditcuentagasto.php');
            ob_start();
            $exec = $data->editCuentaGasto($id);
            $provgasto = $data->traeProveedoresGasto();
            if (count($exec)) {
                include 'app/views/pages/p.formeditcuentagasto.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function NuevaCtaGasto() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.formnuevacuentagasto.php');
            //$pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            ob_start();
            $tipog = $data->traeTipoGasto();
            include 'app/views/pages/p.formnuevacuentagasto.php';
            $table = ob_get_clean();
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            $this->view_page($pagina);
            // ob_start();
            // $gastos=$data->VerCatGastos();
            //include 'app/views/pages/p.formnuevacuentagasto.php';
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function GuardarCambiosCuenta($concepto, $descripcion, $iva, $cc, $cuenta, $gasto, $presupuesto, $id, $retieneiva, $retieneisr, $retieneflete, $activo, $cveprov) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $redireccionar = 'Catalogo_Gastos';
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            ob_start();
            $gastos = $data->guardarCambiosCuenta($concepto, $descripcion, $iva, $cc, $cuenta, $gasto, $presupuesto, $id, $retieneiva, $retieneisr, $retieneflete, $activo, $cveprov);
            include 'app/views/pages/p.redirectform.php';
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function DelCuentaGasto($id) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $redireccionar = 'Catalogo_Gastos';
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            ob_start();
            $gastos = $data->delCuentaGasto($id);
            include 'app/views/pages/p.redirectform.php';
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ImpCatalogoCuentas() {
        $data = new Pegaso;
        $exec = $data->VerCatGastos();

        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerCC.jpg', 8, 15, 200, 55);
        $pdf->Ln(70);

        $pdf->SetFont('Arial', 'B', 7);

        $pdf->Cell(8, 5, "ID", 1);
        $pdf->Cell(23, 5, "Clave", 1);
        $pdf->Cell(39, 5, "Concepto", 1);
        $pdf->Cell(44, 5, "Descripcion", 1);
        $pdf->Cell(7, 5, "IVA", 1);
        $pdf->Cell(22, 5, "Centro de costos", 1);
        $pdf->Cell(21, 5, "Cuenta Contable", 1);
        $pdf->Cell(10, 5, "Gasto", 1);
        $pdf->Cell(23, 5, "Presupuesto", 1);
        $pdf->Ln(10);

        //$pdf->SetFont('Arial', 'I', 7);

        foreach ($exec as $row) {

            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell(8, 10, "$row->ID", 1);
            $pdf->Cell(23, 10, "$row->CLAVE", 1);
            $pdf->Cell(39, 10, "$row->CONCEPTO", 1);

            $pdf->Cell(44, 10, "$row->DESCRIPCION", 1);

            $pdf->Cell(7, 10, "$row->CAUSA_IVA", 1);
            $pdf->Cell(22, 10, "$row->CENTRO_COSTOS", 1);
            $pdf->Cell(21, 10, "$row->CUENTA_CONTABLE", 1);
            $pdf->Cell(10, 10, "$row->GASTO", 1);
            $pdf->Cell(23, 10, '$ ' . number_format($row->PRESUPUESTO, 2, '.', ','), 1);
            $pdf->Ln(10);
        }


        $pdf->Output('Catalogo de cuentas.pdf', 'i');
    }

    function reEnrutar($doco, $id_preoc, $pxr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('PXR');
            $html = $this->load_page('app/views/pages/p.pxr.php');
            ob_start();
            $liberar = $data->ReEnrutar($id_preoc, $pxr, $doco);
            $exec = $data->ListaPartidasNoRecibidas();
            if ($exec != '') {
                include 'app/views/pages/p.pxr.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function liberaPendientes($doco, $id_preoc, $pxr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('PXR');
            $html = $this->load_page('app/views/pages/p.pxr.php');
            ob_start();
            $liberar = $data->LiberarPartidasNoRecibidas($id_preoc, $pxr, $doco);
            $exec = $data->ListaPartidasNoRecibidas();
            header("Location: index.php?action=pxr");
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function FormCapturaGasto() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('PXR');
            $html = $this->load_page('app/views/pages/p.formnuevogasto.php');
            ob_start();
            $exec = $data->traeConceptoGastos();
            $prov = $data->traeProveedoresGastos();
            $clasificacion = $data->traeClasificacionGastos();
            if ($exec != '') {
                include 'app/views/pages/p.formnuevogasto.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function GuardarNuevoGasto($concepto, $proveedor, $referencia, $autorizacion, $presupuesto, $tipopago, $monto, $movpar, $numpar, $usuario, $fechadoc, $fechaven, $clasificacion) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('pedidos');
            $html = $this->load_page('app/views/pages/p.formnuevogasto.php');
            ob_start();
            $exec = $data->traeImpuestoGasto($concepto);
            $gasto = $data->guardarNuevoGasto($concepto, $proveedor, $referencia, $autorizacion, $presupuesto, $tipopago, $monto, $movpar, $numpar, $usuario, $fechadoc, $fechaven, $exec, $clasificacion);
            if ($gasto != '') {
                //include 'app/views/pages/p.formnuevogasto.php';
                header('Location: index.php?action=form_capturagastos');
                //$table = ob_get_clean();
                //$pagina = $this->replace_content('/\#CONTENIDO\#/ms' , $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function Clasificacion_gastos() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('pedidos');
            $html = $this->load_page('app/views/pages/p.clasificacionesgastos.php');
            ob_start();
            $exec = $data->traeClasificacionGastos();
            if (count($exec) > 0) {
                include 'app/views/pages/p.clasificacionesgastos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function NuevaClaGasto() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.formnuevaclasificaciongasto.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
            // ob_start();
            // $gastos=$data->VerCatGastos();
            // include 'app/views/pages/p.formnuevacuentagasto.php';
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function EditClaGasto($id) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('pedidos');
            $html = $this->load_page('app/views/pages/p.formeditacg.php');
            ob_start();
            $exec = $data->dataClasificacion($id);
            if (count($exec) > 0) {
                include 'app/views/pages/p.formeditacg.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function GuardaCambiosClasG($id, $clasif, $descripcion, $activo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('pedidos');
            $html = $this->load_page('app/views/pages/p.clasificacionesgastos.php');
            ob_start();
            $exec = $data->guardaCambiosCG($id, $clasif, $descripcion, $activo);
            if ($exec != '') {
                header('Location: index.php?action=clasificacion_gastos');
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function GuardaNuevaClaGasto($clasif, $descripcion) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('pedidos');
            $html = $this->load_page('app/views/pages/p.clasificacionesgastos.php');
            ob_start();
            $exec = $data->guardaNuevaClaGasto($clasif, $descripcion);
            if ($exec != '') {
                header('Location: index.php?action=clasificacion_gastos');
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verEntregas() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verentregas.php');
            ob_start();
            $entregas = $data->verEntregas();
            if (count($entregas)) {
                include 'app/views/pages/p.verentregas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function insContra($cr, $idc, $docf) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verentregas.php');
            ob_start();
            $insertcr = $data->insContra($cr, $idc, $docf);
            $entregas = $data->verEntregas();
            if (count($entregas)) {
                include 'app/views/pages/p.verentregas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function recibirMercancia() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.vernoentregas.php');
            ob_start();
            $entregas = $data->verNoEntregas();
            if (count($entregas)) {
                include 'app/views/pages/p.vernoentregas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function recmercancia($id, $docf) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.recmercancia.php');
            ob_start();
            $embalaje = $data->verembalaje($id, $docf);
            if (count($embalaje)) {
                include 'app/views/pages/p.recmercancia.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function recibirCaja($id, $docf, $idc) {     //21062016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $redireccionar = 'recibirMercancia';
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            ob_start();
            $embalaje = $data->recibeCaja($id, $docf, $idc);
            include 'app/views/pages/p.redirectform.php';
            $this->recibirMercancia();
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function recibirCajaNC($id, $docf, $idc, $idpreoc, $cantr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            #$redireccionar = 'recibirMercancia';
            #$redireccionar ='recmercancia';
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            ob_start();
            $recibirnc = $data->recibirCajaNC($id, $docf, $idc, $idpreoc, $cantr);
            include 'app/views/pages/p.redirectform.php';
            $this->recmercancianc($idc, $docf);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function recmercancianc($id, $docf) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.recmercancianc.php');
            ob_start();
            $embalaje = $data->verembalaje($id, $docf);
            $devuelto = $data->devueltoNC($id, $docf);
            if (count($embalaje) or count($devuelto)) {
                include 'app/views/pages/p.recmercancianc.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function recDocFact($docf, $docp, $idcaja, $tipo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verFacturas.php');
            ob_start();
            $actDoc = $data->recDocFact($docf, $docp, $idcaja, $tipo);
            $facturas = $data->verFacturas();
            if (count($facturas)) {
                include 'app/views/pages/p.verFacturas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function recDocFactNC($docf, $docp, $idcaja, $tipo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verFacturasNc.php');
            ob_start();
            $actDoc = $data->recDocFactNC($docf, $docp, $idcaja, $tipo);
            $nc = $data->verNCFactura();
            if (count($nc)) {
                include 'app/views/pages/p.verFacturasNc.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function avanzaCobranza($docf, $docp, $idcaja, $tipo, $nstatus) {          //21
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verFacturas.php');
            ob_start();
            $actDoc = $data->avanzaCobranza($docf, $docp, $idcaja, $tipo, $nstatus);
            $facturas = $data->verFacturas();
            if (count($facturas)) {
                include 'app/views/pages/p.verFacturas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function impCompFact($docf, $docp, $idcaja, $tipo, $idcliente) {       //21
        $data = new Pegaso;
        $factura = $data->verFacturasCompF($docf, $docp, $idcaja);
        $revision = $data->insertaRevFact($docf, $docp, $idcaja, $tipo);
        $documentos = $data->traeDocumentosCliente($idcliente);
        $statuscaja = $data->actualizaStatusCaja($idcaja);

        $pdf = new FPDF('P', 'mm', 'Letter');

        $pdf->AddPage();

        $pdf->Image('app/views/images/headerRECEPpdf.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);

        $pdf->SetFont('Arial', 'B', 10);
        foreach ($revision as $data) {
            $pdf->SetX(160);
            $pdf->Write(6, "Folio de revision : " . $data->IDREVISION);
        }
        $pdf->Ln();
        foreach ($factura as $row) {
            $pdf->Cell(40, 6, "Factura: " . $row->FACTURA);
            $pdf->Ln();
            $pdf->Cell(40, 6, "Fecha de factura: " . $row->FECHA_FACTURA);
            $pdf->Ln();
            $pdf->Cell(100, 6, "Cliente: " . $row->CLIENTE);
            $pdf->Ln();
            $pdf->Cell(50, 6, "Pedido: " . $row->PEDIDO, 0, 30);
            $pdf->Cell(30, 6, "Caja: " . $row->CAJA);
            $pdf->Cell(50, 6, "Unidad: " . $row->UNIDAD);
            $pdf->Ln();
            $pdf->Cell(40, 6, "Status losgistica: " . $row->RESULTADO);
            $pdf->Ln();
        }
        $pdf->Ln(5);
        $pdf->SetX(80);
        $pdf->Write(8, "Documentos entregados:");
        $pdf->Ln(9);
        $pdf->Cell(60, 6, "Documento", 1);
        $pdf->Cell(90, 6, "Descripcion", 1);
        $pdf->Cell(35, 6, "Copias requeridas", 1);
        $pdf->Ln();
        foreach ($documentos as $doc) {
            $pdf->Cell(60, 6, $doc->NOMBRE, 1);
            $pdf->Cell(90, 6, $doc->DESCRIPCION, 1);
            $pdf->Cell(35, 6, $doc->COPIAS, 1);
            $pdf->Ln();
        }

        $pdf->Ln(75);
        $pdf->Cell(20, 6, "");
        $pdf->Cell(60, 6, "Nombre y firma de entrega", 'T');
        $pdf->Cell(20, 6, "");
        $pdf->Cell(60, 6, "Nombre y firma de recibido", 'T');

        ob_get_clean();
        $pdf->Output('Secuencia entrega unidad .pdf', 'i');
    }

    function impRecMercancia($id, $docf, $docr, $fact) {
        ob_start();
        $data = new Pegaso;

        $folio = $data->FolioRecMcia($id, $docf, $docr, $fact);
        $exec = $data->statusImpresoCaja($id);
        $embalaje = $data->verembalaje($id, $docf);


        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();

        $pdf->Image('app/views/images/headerRECEPpdf.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);

        $pdf->SetFont('Arial', 'B', 7);

        $pdf->SetX(145);
        foreach ($folio as $fl) {
            $pdf->Write(8, "Folio: " . $fl->ID);
            $pdf->Ln(4);
            $pdf->SetX(145);
            $pdf->Write(8, "Usuario que Recibo : " . $fl->USUARIO);
            $pdf->Ln(4);
            $pdf->SetX(145);
            $pdf->Write(8, "Fecha Recepcion: " . $fl->FECHA_RECEP);
            $pdf->Ln(4);
            $pdf->SetX(145);
            $pdf->Write(8, "Factura: " . $fl->FACTURA);
            $pdf->Ln(4);
            $pdf->SetX(145);
            $pdf->Write(8, "Remision: " . $fl->REMISION);
        }

        $pdf->Ln();
        $pdf->Cell(10, 6, "ID", 1);
        $pdf->Cell(8, 6, "Env", 1);
        $pdf->Cell(15, 6, "Documento", 1);
        $pdf->Cell(10, 6, "Caja", 1);
        $pdf->Cell(15, 6, "Fecha", 1);
        $pdf->Cell(8, 6, "PAQ", 1);
        $pdf->Cell(15, 6, "Clave", 1);
        $pdf->Cell(50, 6, "Descripcion", 1);
        $pdf->Cell(10, 6, "Cant", 1);
        $pdf->Cell(10, 6, "PAQ1", 1);
        $pdf->Cell(8, 6, "De", 1);
        $pdf->Cell(10, 6, "PAQ2", 1);
        $pdf->Cell(15, 6, "Tipo", 1);
        $pdf->Cell(10, 6, "Peso", 1);

        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 7);

        foreach ($embalaje as $row) {
            $pdf->Cell(10, 6, $row->ID_PREOC, 'L,T,R');
            $pdf->Cell(8, 6, $row->TIPO_ENVIO, 'L,T,R');
            $pdf->Cell(15, 6, $row->DOCUMENTO, 'L,T,R');
            $pdf->Cell(10, 6, $row->IDCAJA, 'L,T,R');
            $pdf->Cell(15, 6, $row->FECHA_PAQUETE, 'L,T,R');
            $pdf->Cell(8, 6, $row->EMPAQUE, 'L,T,R');
            $pdf->Cell(15, 6, $row->ARTICULO, 'L,T,R');
            $pdf->Cell(50, 6, substr($row->DESCRIPCION, 0, 30), 'L,T,R');
            $pdf->Cell(10, 6, $row->CANTIDAD, 'L,T,R');
            $pdf->Cell(10, 6, $row->PAQUETE1, 'L,T,R');
            $pdf->Cell(8, 6, "de", 'L,T,R');
            $pdf->Cell(10, 6, $row->PAQUETE2, 'L,T,R');
            $pdf->Cell(15, 6, $row->TIPO_EMPAQUE, 'L,T,R');
            $pdf->Cell(10, 6, $row->PESO, 'L,T,R');
            $pdf->Ln();
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(8, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(8, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(50, 6, substr($row->DESCRIPCION, 30, 60), 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(8, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Ln();
        }

        $pdf->Ln(50);
        $pdf->Cell(50, 8, "FECHA IMPRESION : " . date("d-m-Y"));
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(45, 8, "Nombre y firma recibido", 'T');
        $pdf->Cell(35, 8, "");
        $pdf->Cell(55, 8, "Nombre y firma de quien entrega", 'T');

        ob_get_clean();
        $pdf->Output('Rmercancia.pdf', 'i');
    }

    /* modificado por GDELEON 3/Ago/2016 */

    function DelClaGasto($id) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('pedidos');
            ob_start();
            $exec = $data->delClaGasto($id); //delClaGasto
            if ($exec != '') {
                header('Location: index.php?action=clasificacion_gastos');
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    //14062016
    function CatalogoDocumentos() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.catalgo_documentos.php');
            ob_start();
            $exec = $data->traeDocumentosxCliente();
            if (count($exec)) {
                include 'app/views/pages/p.catalgo_documentos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function NuevoDocumentoC() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.form_nuevodocc.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function GuardaNuevoDocC($nombre, $descripcion) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $exec = $data->guardaNuevoDocC($nombre, $descripcion);
            header('Location: index.php?action=catalogo_documentos');
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function FormEditaDocumentoC($id) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.form_editdocc.php');
            ob_start();
            $exec = $data->traeDocumentoC($id);
            if (count($exec)) {
                include 'app/views/pages/p.form_editdocc.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function EditaDocumentoC($activo, $nombre, $descripcion, $id) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $exec = $data->guardaCambiosDocC($activo, $nombre, $descripcion, $id);
            header('Location: index.php?action=catalogo_documentos');
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function CatDocumentosXCliente() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.catalogo_documentosxcliente.php');
            ob_start();
            $exec = $data->traeClientesParaDocs();
            if (count($exec)) {
                include 'app/views/pages/p.catalogo_documentosxcliente.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function VerDocumentosCliente($clave) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.documentosdelcliente.php');
            ob_start();
            $_SESSION['ClaveCliente'] = $clave;
            $exec = $data->traeDocumentosCliente($clave);
            if (count($exec)) {
                include 'app/views/pages/p.documentosdelcliente.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function formNuevoDocCliente($clave) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            //var_dump($clave);
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.form_nuevodoccliente.php');
            ob_start();
            $exec = $data->traeDocumentosxCliente();
            if (count($exec)) {
                include 'app/views/pages/p.form_nuevodoccliente.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function asignaNuevoDocCliente($cliente, $requerido, $copias, $documento) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "documentos_cliente";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php'); //
            $exec = $data->NuevoDocCliente($cliente, $requerido, $copias, $documento);
            if ($exec) {
                $mensaje = '<div class="alert-info"><center><h2>Requisito asignado correctamente</h2><center></div>';
                //header('Location: index.php?action=documentos_cliente');
                include 'app/views/pages/p.redirectform.php';
            } else {
                $mensaje = '<div class="alert-info"><center><h2>Error: El requisito no se asigno.</h2><center></div>';
                include 'app/views/pages/p.redirectform.php';
            }

            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function recibosMercanciaImp() {         //21062016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verentregasimpresas.php');
            ob_start();
            $entregas = $data->verNoEntregasImpresas();
            if (count($entregas)) {
                include 'app/views/pages/p.verentregasimpresas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    //21062016 final

    function guardaContraRecibo($contrarecibo, $idcaja) {     //22062016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "mercanciaRecibidaImp";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $exec = $data->guardaContraRecibo($contrarecibo, $idcaja);
            include 'app/views/pages/p.redirectform.php';
            //header('Location: index.php?action=mercanciaRecibidaImp');
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ReenviarCaja($factura, $caja) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "pantalla2";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $exec = $data->reenviaCaja($factura, $caja);
            include 'app/views/pages/p.redirectform.php';
            //header('Location: index.php?action=mercanciaRecibidaImp');
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function formDataCobranzaC($idCliente) {     //24062016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.form_nuevosdatoscobranza.php');
            ob_start();
            $exec = $data->datosCobranzaC($idCliente);
            $datosMaestro = $data->traeMaestros();
            $cli = $idCliente;
            if (count($exec)) {
                include 'app/views/pages/p.form_editadatoscobranza.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                include 'app/views/pages/p.form_nuevosdatoscobranza.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function salvaCambiosDatosCob($cliente, $carteraCob, $carteraRev, $diasRevision, $diasPago, $dosPasos, $plazo, $addenda, $portal, $usuario, $contrasena, $observaciones, $envio, $cp, $maps, $tipo, $ln, $pc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "documentos_cliente";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $exec = $data->salvarCambiosCobranza($cliente, $carteraCob, $carteraRev, $diasRevision, $diasPago, $dosPasos, $plazo, $addenda, $portal, $usuario, $contrasena, $observaciones, $envio, $cp, $maps, $tipo, $ln, $pc);
            var_dump($exec);
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function salvaDatosCob($cliente, $carteraCob, $carteraRev, $diasRevision, $diasPago, $dosPasos, $plazo, $addenda, $portal, $usuario, $contrasena, $observaciones, $envio, $cp, $maps, $tipo, $ln, $pc) { //28062016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "documentos_cliente";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $exec = $data->salvarDatosCobranza($cliente, $carteraCob, $carteraRev, $diasRevision, $diasPago, $dosPasos, $plazo, $addenda, $portal, $usuario, $contrasena, $observaciones, $envio, $cp, $maps, $tipo, $ln, $pc);
            var_dump($exec);
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function cierreReparto() {       //27062016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verdocscierreentrega.php');
            ob_start();
            $entregas = $data->verCierreDiaEntregas();
            if (count($entregas)) {
                include 'app/views/pages/p.verdocscierreentrega.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function generarCierreEnt() {      //27062016
        ob_start();
        $data = new Pegaso;

        $entregas = $data->verCierreDiaEntregas();
        $cierre = $data->insertCierreDiaEntregas();

        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();

        $pdf->Image('app/views/images/headerRECEPpdf.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);

        $pdf->SetFont('Arial', 'B', 7);

        $pdf->SetX(180);
        $pdf->Write(8, "Folio: ");
        foreach ($cierre as $fl) {
            $pdf->Write(8, $fl->NUEVOFOLIO);
        }

        $pdf->Ln();
        $pdf->Cell(10, 6, "CAJA", 1);
        $pdf->Cell(12, 6, "PEDIDO", 1);
        $pdf->Cell(75, 6, "CLIENTE", 1);
        $pdf->Cell(15, 6, "FACTURA", 1);
        $pdf->Cell(24, 6, "FECHA FACTURA", 1);
        $pdf->Cell(20, 6, "REMISION", 1);
        $pdf->Cell(24, 6, "FECHA REMISION", 1);
        $pdf->Cell(15, 6, "ESTATUS", 1);

        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 7);

        foreach ($entregas as $row) {
            $pdf->Cell(10, 6, $row->ID, 1);
            $pdf->Cell(12, 6, $row->CVE_FACT, 1);
            $pdf->Cell(75, 6, $row->NOMBRE, 1);
            $pdf->Cell(15, 6, $row->FACTURA, 1);
            $pdf->Cell(24, 6, $row->FECHAFAC, 1);
            $pdf->Cell(20, 6, trim($row->REMISION), 1);
            $pdf->Cell(24, 6, $row->FECHAREM, 1);
            $pdf->Cell(15, 6, $row->STATUS_LOG, 1);
            $pdf->Ln();
        }


        ob_get_clean();
        $pdf->Output('Rmercancia.pdf', 'i');
    }

    function imprimeCierre($idu) {
        //ob_start();
        $data = new pegaso;
        $exec = $data->imprimeCierre($idu);
        $cabecera = $data->imprimeCierreCab($idu);
        $actcierre = $data->actCierreUni($idu);
        $pdf = new FPDF('P', 'mm', 'Letter');

        $pdf->AddPage();

        $pdf->Image('app/views/images/headerRECEPpdf.jpg', 10, 15, 205, 55);

        //$pdf->SetFont('Arial','B',12);
        //$pdf->Ln(65);

        foreach ($cabecera as $cab) {
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->Ln(65);
            $pdf->Cell(10, 6, "Unidad: " . $cab->UNIDAD);
            $pdf->Ln();
            $pdf->Cell(10, 6, "Operador: ");
            $pdf->Ln();
            $pdf->Cell(10, 6, "Fecha Secuencia: " . $cab->FECHA_SECUENCIA);
            $pdf->Ln();
            $pdf->Cell(10, 6, "Resultado:");
            $pdf->Ln();
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Ln();
            $pdf->Cell(18, 6, "DOCUMENTO", 1);
            $pdf->Cell(28, 6, "FECHA DOCUMENTO", 1);
            $pdf->Cell(30, 6, "PROVEDOR", 1);
            $pdf->Cell(18, 6, "COSTO DOC", 1);
            $pdf->Cell(11, 6, "PAGO", 1);
            $pdf->Cell(27, 6, "FECHA PAGO", 1);
            $pdf->Cell(13, 6, "UNDIDAD", 1);
            $pdf->Cell(8, 6, "SEC", 1);
            $pdf->Cell(10, 6, "FIN", 1);
            //$pdf->Cell(10,6,"REALIZA",1);
            //$pdf->Cell(15,6,"DOCS",1);
            $pdf->Cell(15, 6, "CIERRRE", 1);
        }
        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 7);
        foreach ($exec as $row) {
            $pdf->Cell(18, 6, $row->CVE_DOC, 'L,T,R');
            $pdf->Cell(28, 6, $row->FECHA_DOC, 'L,T,R');
            $pdf->Cell(30, 6, $row->CVE_CLPV, 'L,T,R');
            $pdf->Cell(18, 6, $row->CAN_TOT, 'L,T,R');
            $pdf->Cell(11, 6, $row->TP_TES, 'L,T,R');
            $pdf->Cell(27, 6, $row->FECHA_PAGO, 'L,T,R');
            $pdf->Cell(13, 6, $row->UNIDAD, 'L,T,R');
            $pdf->Cell(8, 6, $row->SECUENCIA, 'L,T,R');
            $pdf->Cell(10, 6, $row->STATUS_LOG, 'L,T,R');
            //$pdf->Cell(10,6,$row->REALIZA,'L,T,R');
            // $pdf->Cell(10,6,$row->DOCS,'L,T,R');
            $pdf->Cell(15, 6, $row->CIERRE_UNI, 'L,T,R');
            $pdf->Ln();
        }

        $pdf->Ln(20);
        //$pdf->Cell(50,8,"FECHA: ".date("d-m-Y"));
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(45, 8, "Nombre y firma recibido", 'T');
        $pdf->Cell(35, 8, "");
        $pdf->Cell(55, 8, "Nombre y firma de quien entrega", 'T');
        ob_get_clean();
        $pdf->Output('cierre.pdf', 'i');
    }

    function SMCarteraRevision() {     //2806
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.msubcarterasrevision.php');
            ob_start();
            $exec = $data->traeCarteras();
            if (count($exec)) {
                include 'app/views/modules/m.msubcarterasrevision.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function VarCartera($cr) {           //04072016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.vercarterarevision.php');
            ob_start();
            $cart = $cr; // Variable para el boton de imprimir cartera del día en la plantilla html no borrar
            $carteradia = $data->verCarteraDia($cr);
            $exec = $data->verCartera($cr);
            if ($cr == "CR1")
                @$sincartera = $data->sinCartera();
            if (count($exec)) {
                include 'app/views/pages/p.vercarterarevision.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ImprimirCarteraDia($cr) { //04072016
        $data = new Pegaso;
        $carteradia = $data->verCarteraDia($cr);
        $hoy = date("d-m-Y");
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();

        $pdf->Image('app/views/images/headerRECEPpdf.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);
        $pdf->SetFont('Arial', 'B', 7);


        $pdf->Cell(100, 6, "Cartera: {$cr}     Dia: {$hoy}");

        $pdf->Ln();
        $pdf->Cell(16, 6, "PEDIDO", 1);
        $pdf->Cell(75, 6, "CLIENTE", 1);
        $pdf->Cell(15, 6, "FACTURA", 1);
        $pdf->Cell(15, 6, "IMP FACT", 1);
        $pdf->Cell(15, 6, "FECHA FAC", 1);
        $pdf->Cell(15, 6, "REMISION", 1);
        $pdf->Cell(15, 6, "IMP REM", 1);
        $pdf->Cell(15, 6, "FECHA REM", 1);
        $pdf->Cell(10, 6, "DIAS", 1);


        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 7);

        foreach ($carteradia as $row) {
            $pdf->Cell(16, 6, $row->CVE_FACT, 1);
            $pdf->Cell(75, 6, $row->CLIENTE, 1);
            $pdf->Cell(15, 6, $row->FACTURA, 1);
            $pdf->Cell(15, 6, "$ " . number_format($row->IMPFAC, 2, ".", ","), 1);
            $pdf->Cell(15, 6, substr($row->FECHAFAC, 0, 10), 1);
            $pdf->Cell(15, 6, trim($row->REMISION), 1);
            $pdf->Cell(15, 6, "$ " . number_format($row->IMPREM, 2, ".", ","), 1);
            $pdf->Cell(15, 6, substr($row->FECHAREM, 0, 10), 1);
            $pdf->Cell(10, 6, $row->DIAS, 1);
            $pdf->Ln();
        }


        ob_get_clean();
        $pdf->Output('Cartera Revision' . $hoy . '.pdf', 'i');
    }

    function salvarContraRecibo($caja, $cr, $contraRecibo, $factura, $remision) {     //02082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "verCR&cr={$cr}"; // aquí ocupo la variable cr para redireccionar a la vista despues de ejecutar la consulta de actualización
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $exec = $data->salvarContraRecibo($contraRecibo, $caja);
            //var_dump($exec);
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function emitirContraRecibo($caja, $factura, $remision) {  //02082016
        $data = new Pegaso;
        $contrarecibo = $data->traeDataContraRecibo($caja);
        $emitir = $data->salvarStatusECR($caja);
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();

        $pdf->Image('app/views/images/headerRECEPpdf.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);
        $pdf->SetFont('Arial', 'B', 7);

        foreach ($contrarecibo as $title) {
            $pedido = $title->CVE_FACT;
        }

        $pdf->Cell(100, 6, "CAJA: {$caja}     Pedido: {$pedido}");

        $pdf->Ln();
        $pdf->Cell(70, 6, "CLIENTE", 1);
        $pdf->Cell(15, 6, "FACTURA", 1);
        $pdf->Cell(24, 6, "FECHA FACTURA", 1);
        $pdf->Cell(20, 6, "REMISION", 1);
        $pdf->Cell(24, 6, "FECHA REMISION", 1);
        $pdf->Cell(15, 6, "ESTATUS", 1);
        $pdf->Cell(10, 6, "DIAS", 1);
        $pdf->Cell(22, 6, "CONTRARECIBO", 1);

        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 7);

        foreach ($contrarecibo as $row) {
            $pdf->Cell(70, 6, $row->NOMBRE, 1);
            $pdf->Cell(15, 6, $row->FACTURA, 1);
            $pdf->Cell(24, 6, $row->FECHAFAC, 1);
            $pdf->Cell(20, 6, trim($row->REMISION), 1);
            $pdf->Cell(24, 6, $row->FECHAREM, 1);
            $pdf->Cell(15, 6, $row->STATUS_LOG, 1);
            $pdf->Cell(10, 6, $row->DIAS, 1);
            $pdf->Cell(22, 6, $row->CONTRARECIBO_CR, 1);
            $pdf->Ln();
        }


        ob_get_clean();
        $pdf->Output('Contra Recibo Cartera Revision.pdf', 'i');
    }

    function SMCarteraRev10() {      //30062016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.msubcarterarevision10.php');
            ob_start();
            $exec = $data->traeCarteras();
            if (count($exec)) {
                include 'app/views/modules/m.msubcarterarevision10.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function VarCartera10($cr) {     //3006
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.vercarterarevision.php');
            ob_start();
            $carteradia = $data->verCarteraDia10($cr);
            $exec = $data->verCartera10($cr);
            if ($cr == "CR1")
                @$sincartera = $data->sinCartera10();
            if (count($exec)) {
                include 'app/views/pages/p.vercarterarevision.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function catCierreCarteraR($cr) {   //07072016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.vercierreCR.php');
            ob_start();
            $cartera = $cr;
            $nocontrarecibo = $data->verCarteraCierreDiaSinCR($cr);
            $exec = $data->verCarteraCierreDia($cr);
            if (count($exec) || count($nocontrarecibo)) {
                include 'app/views/pages/p.vercierreCR.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function SMCierreCartera() {     //07072016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.subcarteracierre.php');
            ob_start();
            $exec = $data->traeCarteras();
            if (count($exec)) {
                include 'app/views/modules/m.subcarteracierre.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function salvarMotivoSinCR($motivo, $factura, $remision, $cr) {     //06072016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "catCierreCr";
            //$pagina=$this->load_template('Pedidos');
            //$html = $this->load_page('app/views/pages/p.redirectform.php');
            $exec = $data->salvarMotivoSinContraR($motivo, $factura, $remision);
            //var_dump($exec);
            //include 'app/views/pages/p.redirectform.php';
            header("Location: index.php?action=catCierreCr&cr=$cr");
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function emitirCierreCR($cr) {       //08072016
        $data = new Pegaso;
        $exec = $data->verCarteraCierreDiaSinCR($cr);
        $sicontrarecibo = $data->verCarteraCierreDia($cr);
        //$gen = $data->GenerarCierreCR($cr);
        $gen = $data->emitirCierreCR($cr);
        $finalizados = 0;
        $pendientes = 0;
        $totalLogrado = 0;
        $totalFaltante = 0;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();

        $pdf->Image('app/views/images/headerRECEPpdf.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(70, 6, "Cartera Revision {$cr}");
        $pdf->Cell(70, 6, "Fecha: " . date("d/m/Y"));
        $pdf->Ln();
        $pdf->Cell(70, 6, "Documentos con contra recibo.");
        $pdf->Ln();
        $pdf->Cell(50, 6, "CLIENTE", 1);
        $pdf->Cell(15, 6, "FACTURA", 1);
        $pdf->Cell(16, 6, "FECHA FAC", 1);
        $pdf->Cell(20, 6, "IMPORTE FAC", 1);
        $pdf->Cell(20, 6, "REMISION", 1);
        $pdf->Cell(16, 6, "FECHA REM", 1);
        $pdf->Cell(20, 6, "IMPORTE REC", 1);
        $pdf->Cell(10, 6, "DIAS", 1);
        $pdf->Cell(22, 6, "CONTRARECIBO", 1);

        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 7);

        foreach ($sicontrarecibo as $row) {
            $pdf->Cell(50, 6, substr($row->CLIENTE, 0, 31), 'L,T,R');
            $pdf->Cell(15, 6, $row->FACTURA, 'L,T,R');
            $pdf->Cell(16, 6, substr($row->FECHAFAC, 0, 10), 'L,T,R');
            $pdf->Cell(20, 6, "$ " . number_format($row->IMPFAC, 2, ".", ","), 'L,T,R');
            $pdf->Cell(20, 6, trim($row->REMISION), 'L,T,R');
            $pdf->Cell(16, 6, substr($row->FECHAREM, 0, 10), 'L,T,R');
            $pdf->Cell(20, 6, "$ " . number_format($row->IMPREM, 2, ".", ","), 'L,T,R');
            $pdf->Cell(10, 6, $row->DIAS, 'L,T,R');
            $pdf->Cell(22, 6, $row->CONTRARECIBO_CR, 'L,T,R');
            $pdf->Ln();
            $pdf->Cell(50, 6, substr($row->CLIENTE, 31, 70), 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(16, 6, "", 'L,B,R');
            $pdf->Cell(20, 6, "", 'L,B,R');
            $pdf->Cell(20, 6, "", 'L,B,R');
            $pdf->Cell(16, 6, "", 'L,B,R');
            $pdf->Cell(20, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(22, 6, "", 'L,B,R');
            $pdf->Ln();
            $finalizados += 1;
            $totalLogrado += ($row->IMPFAC + $row->IMPREM);
        }


        $pdf->Cell(70, 6, "Documentos sin contra recibo.");
        $pdf->Ln();
        $pdf->Cell(50, 6, "CLIENTE", 1);
        $pdf->Cell(15, 6, "FACTURA", 1);
        $pdf->Cell(16, 6, "FECHA FAC", 1);
        $pdf->Cell(20, 6, "IMPORTE FAC", 1);
        $pdf->Cell(20, 6, "REMISION", 1);
        $pdf->Cell(16, 6, "FECHA REM", 1);
        $pdf->Cell(20, 6, "IMPORTE REC", 1);
        $pdf->Cell(10, 6, "DIAS", 1);
        $pdf->Cell(22, 6, "MOTIVO", 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 7);

        foreach ($exec as $row) {
            $pendientes += 1;
            $totalFaltante += ($row->IMPFAC + $row->IMPREM);
            $pdf->Cell(50, 6, substr($row->CLIENTE, 0, 31), 'L,T,R');
            $pdf->Cell(15, 6, $row->FACTURA, 'L,T,R');
            $pdf->Cell(16, 6, substr($row->FECHAFAC, 0, 10), 'L,T,R');
            $pdf->Cell(20, 6, "$ " . number_format($row->IMPFAC, 2, ".", ","), 'L,T,R');
            $pdf->Cell(20, 6, trim($row->REMISION), 'L,T,R');
            $pdf->Cell(16, 6, substr($row->FECHAREM, 0, 10), 'L,T,R');
            $pdf->Cell(20, 6, "$ " . number_format($row->IMPREM, 2, ".", ","), 'L,T,R');
            $pdf->Cell(10, 6, $row->DIAS, 'L,T,R');
            $pdf->Cell(22, 6, $row->MOTIVO, 'L,T,R');
            $pdf->Ln();
            $pdf->Cell(50, 6, substr($row->CLIENTE, 31, 70), 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(16, 6, "", 'L,B,R');
            $pdf->Cell(20, 6, "", 'L,B,R');
            $pdf->Cell(20, 6, "", 'L,B,R');
            $pdf->Cell(16, 6, "", 'L,B,R');
            $pdf->Cell(20, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(22, 6, "", 'L,B,R');
            $pdf->Ln();
        }
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(60, 8, "Documentos logrados: " . $finalizados);
        $pdf->Cell(45, 8, "Total logrado: $" . number_format($totalLogrado, 2, ".", ","));
        $pdf->Ln();
        $pdf->Cell(60, 8, "Documentos faltantes: " . $pendientes);
        $pdf->Cell(45, 8, "Total faltante: $" . number_format($totalFaltante, 2, ".", ","));
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(35, 8, "Eficacia: " . round((($totalLogrado / ($totalLogrado + $totalFaltante)) * 100), 2) . " %");

        ob_get_clean();
        $pdf->Output('Cierre cartera revision.pdf', 'i');
    }

    function catCobranza($cc) {     //07072016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verCatCobranza.php');
            ob_start();
            $cobdia = $data->verCatCobranzaDia($cc);
            $exec = $data->verCatCobranza($cc);
            if (count($exec) || count($cobdia)) {
                include 'app/views/pages/p.verCatCobranza.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function catCorteCredito() {     //06072016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verCorteCredito.php');
            ob_start();
            $exec = $data->verCatCobranza10d();
            if (count($exec)) {
                include 'app/views/pages/p.verCorteCredito.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function SMCarteraCobranza() {     //07072016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.msubcarteracobranza.php');
            ob_start();
            $exec = $data->traeCarterasCobranza();
            if (count($exec)) {
                include 'app/views/modules/m.msubcarteracobranza.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function acuse_revision() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.acuse_revision.php');
            ob_start();
            $acuse = $data->acuse_revision();
            if (count($acuse)) {
                include 'app/views/pages/p.acuse_revision.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function info_foraneo($caja, $doccaja, $guia, $fletera) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.acuse_revision.php');
            ob_start();
            $infofletera = $data->info_foraneo($caja, $doccaja, $guia, $fletera);
            $acuse = $data->acuse_revision();
            if (count($acuse)) {
                include 'app/views/pages/p.acuse_revision.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function FacturarRemision() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verRemisiones.php');
            ob_start();
            $remisiones = $data->VerRemisiones();
            if (count($remisiones)) {
                include 'app/views/pages/p.verRemisiones.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function asociarFactura($caja, $docp, $factura) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "FacturarRemision";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $asociar = $data->asociarFactura($caja, $docp, $factura);
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function NCFactura() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verFacturasNc.php');
            ob_start();
            $nc = $data->VerNCFactura();
            if (count($nc)) {
                include 'app/views/pages/p.verFacturasNc.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function DesNC($idc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verFacturasNc.php');
            ob_start();
            $deslinde = $data->DesNC($idc);
            $nc = $data->VerNCFactura();
            if (count($nc)) {
                include 'app/views/pages/p.verFacturasNc.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function asociarNC($caja, $docp, $nc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "NCFactura";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $asociar = $data->asociarNC($caja, $docp, $nc);
            $nc = $data->VerNCFactura();
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function VerFacturasDeslinde() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verFacturasDeslinde.php');
            ob_start();
            $nc = $data->VerFacturasDeslinde();
            if (count($nc)) {
                include 'app/views/pages/p.verFacturasDeslinde.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function avanzaDeslinde($caja, $pedido, $motivo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "VerFacturasDeslinde";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $exec = $data->AvanzaDeslinde($caja, $pedido, $motivo);
            //var_dump($exec);
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function VerFacturasAcuse() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verFacturasAcuse.php');
            ob_start();
            $nc = $data->VerFacturasAcuse();
            if (count($nc)) {
                include 'app/views/pages/p.verFacturasAcuse.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function guardaAcuse($caja, $pedido, $guia, $fletera) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "VerFacturasAcuse";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $exec = $data->GuardaAcuse($caja, $pedido, $guia, $fletera);
            //var_dump($exec);
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function imprimirFacturasNC() {
        $data = new Pegaso;
        $nc = $data->VerNCFactura();
        $cierranc = $data->CierreNcFactura();
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();

        $pdf->Image('app/views/images/headerRECEPpdf.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);
        $pdf->SetFont('Arial', 'B', 7);

        $pdf->Cell(12, 6, "PEDIDO", 1);
        $pdf->Cell(15, 6, "FACTURA", 1);
        $pdf->Cell(15, 6, "FECHA FAC", 1);
        $pdf->Cell(55, 6, "CLIENTE", 1);
        $pdf->Cell(10, 6, "CAJA", 1);
        $pdf->Cell(12, 6, "UNIDAD", 1);
        $pdf->Cell(20, 6, "STATUSLOG", 1);
        $pdf->Cell(13, 6, "DOC OP", 1);
        $pdf->Cell(25, 6, "NOTA CREDITO", 1);

        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 7);

        foreach ($nc as $row) {
            $pdf->Cell(12, 6, $row->CVE_FACT, 'L,T,R');
            $pdf->Cell(15, 6, $row->DOCFACTURA, 'L,T,R');
            $pdf->Cell(15, 6, substr($row->FECHAELAB, 0, 10), 'L,T,R');
            $pdf->Cell(55, 6, substr($row->NOMBRE, 0, 31), 'L,T,R');
            $pdf->Cell(10, 6, $row->ID, 'L,T,R');
            $pdf->Cell(12, 6, $row->UNIDAD, 'L,T,R');
            $pdf->Cell(20, 6, $row->STATUS_LOG, 'L,T,R');
            $pdf->Cell(13, 6, $row->DOCS, 'L,T,R');
            $pdf->Cell(25, 6, $row->NC, 'L,T,R');
            $pdf->Ln();
            $pdf->Cell(12, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(55, 6, substr($row->NOMBRE, 31, 70), 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(12, 6, "", 'L,B,R');
            $pdf->Cell(20, 6, "", 'L,B,R');
            $pdf->Cell(13, 6, "", 'L,B,R');
            $pdf->Cell(25, 6, "", 'L,B,R');
            $pdf->Ln();
        }

        $pdf->Ln();

        ob_get_clean();
        $pdf->Output('Cierre facturas note de credito.pdf', 'i');
    }

    function imprimirFacturasDeslinde() {
        $data = new Pegaso;
        $nc = $data->VerFacturasDeslinde();
        $des = $data->CierreFacturaDeslinde();
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();

        $pdf->Image('app/views/images/headerRECEPpdf.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);
        $pdf->SetFont('Arial', 'B', 7);

        $pdf->Cell(12, 6, "PEDIDO", 1);
        $pdf->Cell(15, 6, "FACTURA", 1);
        $pdf->Cell(15, 6, "FECHA FAC", 1);
        $pdf->Cell(55, 6, "CLIENTE", 1);
        $pdf->Cell(10, 6, "CAJA", 1);
        $pdf->Cell(12, 6, "UNIDAD", 1);
        $pdf->Cell(20, 6, "STATUSLOG", 1);
        $pdf->Cell(13, 6, "DOC OP", 1);
        $pdf->Cell(25, 6, "MOTIVO DES", 1);

        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 7);

        foreach ($nc as $row) {
            $pdf->Cell(12, 6, $row->CVE_FACT, 'L,T,R');
            $pdf->Cell(15, 6, $row->DOCFACTURA, 'L,T,R');
            $pdf->Cell(15, 6, substr($row->FECHAELAB, 0, 10), 'L,T,R');
            $pdf->Cell(55, 6, substr($row->NOMBRE, 0, 31), 'L,T,R');
            $pdf->Cell(10, 6, $row->ID, 'L,T,R');
            $pdf->Cell(12, 6, $row->UNIDAD, 'L,T,R');
            $pdf->Cell(20, 6, $row->STATUS_LOG, 'L,T,R');
            $pdf->Cell(13, 6, $row->DOCS, 'L,T,R');
            $pdf->Cell(25, 6, $row->MOTIVODES, 'L,T,R');
            $pdf->Ln();
            $pdf->Cell(12, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(55, 6, substr($row->NOMBRE, 31, 70), 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(12, 6, "", 'L,B,R');
            $pdf->Cell(20, 6, "", 'L,B,R');
            $pdf->Cell(13, 6, "", 'L,B,R');
            $pdf->Cell(25, 6, "", 'L,B,R');
            $pdf->Ln();
        }

        $pdf->Ln();

        ob_get_clean();
        $pdf->Output('Cierre deslide facturas.pdf', 'i');
    }

    function imprimirFacturasAcuse() {
        $data = new Pegaso;
        $nc = $data->VerFacturasAcuse();
        $acuse = $data->CierreFacturaAcuse();
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();

        $pdf->Image('app/views/images/headerRECEPpdf.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);
        $pdf->SetFont('Arial', 'B', 7);

        $pdf->Cell(12, 6, "PEDIDO", 1);
        $pdf->Cell(15, 6, "FACTURA", 1);
        $pdf->Cell(15, 6, "FECHA FAC", 1);
        $pdf->Cell(55, 6, "CLIENTE", 1);
        $pdf->Cell(10, 6, "CAJA", 1);
        $pdf->Cell(12, 6, "UNIDAD", 1);
        $pdf->Cell(20, 6, "STATUSLOG", 1);
        $pdf->Cell(13, 6, "DOC OP", 1);
        $pdf->Cell(25, 6, "GUIA", 1);
        $pdf->Cell(25, 6, "FLETERA", 1);

        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 7);

        foreach ($nc as $row) {
            $pdf->Cell(12, 6, $row->CVE_FACT, 'L,T,R');
            $pdf->Cell(15, 6, $row->DOCFACTURA, 'L,T,R');
            $pdf->Cell(15, 6, substr($row->FECHAELAB, 0, 10), 'L,T,R');
            $pdf->Cell(55, 6, substr($row->NOMBRE, 0, 31), 'L,T,R');
            $pdf->Cell(10, 6, $row->ID, 'L,T,R');
            $pdf->Cell(12, 6, $row->UNIDAD, 'L,T,R');
            $pdf->Cell(20, 6, $row->STATUS_LOG, 'L,T,R');
            $pdf->Cell(13, 6, $row->DOCS, 'L,T,R');
            $pdf->Cell(25, 6, $row->GUIA_FLETERA, 'L,T,R');
            $pdf->Cell(25, 6, $row->FLETERA, 'L,T,R');
            $pdf->Ln();
            $pdf->Cell(12, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(55, 6, substr($row->NOMBRE, 31, 70), 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(12, 6, "", 'L,B,R');
            $pdf->Cell(20, 6, "", 'L,B,R');
            $pdf->Cell(13, 6, "", 'L,B,R');
            $pdf->Cell(25, 6, "", 'L,B,R');
            $pdf->Cell(25, 6, "", 'L,B,R');
            $pdf->Ln();
        }

        $pdf->Ln();

        ob_get_clean();
        $pdf->Output('Cierre acuse facturas.pdf', 'i');
    }

    //

    function imprimirFacturasRemision() {
        $data = new Pegaso;
        $remisiones = $data->VerRemisiones();
        $rem = $data->CierrePendienteFacturar();
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();

        $pdf->Image('app/views/images/headerRECEPpdf.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);
        $pdf->SetFont('Arial', 'B', 7);

        $pdf->Cell(12, 6, "PEDIDO", 1);
        $pdf->Cell(15, 6, "REMISION", 1);
        $pdf->Cell(15, 6, "FECHA REM", 1);
        $pdf->Cell(55, 6, "CLIENTE", 1);
        $pdf->Cell(10, 6, "CAJA", 1);
        $pdf->Cell(12, 6, "UNIDAD", 1);
        $pdf->Cell(20, 6, "STATUSLOG", 1);
        $pdf->Cell(13, 6, "DOC OP", 1);
        $pdf->Cell(25, 6, "FACTURA", 1);


        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 7);

        foreach ($remisiones as $row) {
            $pdf->Cell(12, 6, $row->CVE_FACT, 'L,T,R');
            $pdf->Cell(15, 6, trim($row->REM), 'L,T,R');
            $pdf->Cell(15, 6, substr($row->FECHAELAB, 0, 10), 'L,T,R');
            $pdf->Cell(55, 6, substr($row->NOMBRE, 0, 31), 'L,T,R');
            $pdf->Cell(10, 6, $row->ID, 'L,T,R');
            $pdf->Cell(12, 6, $row->UNIDAD, 'L,T,R');
            $pdf->Cell(20, 6, $row->STATUS_LOG, 'L,T,R');
            $pdf->Cell(13, 6, $row->DOCS, 'L,T,R');
            $pdf->Cell(25, 6, $row->FACTURA, 'L,T,R');

            $pdf->Ln();
            $pdf->Cell(12, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(55, 6, substr($row->NOMBRE, 31, 70), 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(12, 6, "", 'L,B,R');
            $pdf->Cell(20, 6, "", 'L,B,R');
            $pdf->Cell(13, 6, "", 'L,B,R');
            $pdf->Cell(25, 6, "", 'L,B,R');

            $pdf->Ln();
        }

        $pdf->Ln();

        ob_get_clean();
        $pdf->Output('Cierre Factura remision.pdf', 'i');
    }

    function verCarteraCobranza() {      //19072016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $cartera = $_SESSION['user']->CC;
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.CarteraCobranza.php');
            ob_start();
            //$actualizaSaldo = $data->actualizaSaldoVencido();
            //echo 'Esta es la cartera '.$cartera;
            $saldoxmaestro = $data->saldoMaestro($cartera);
            $saldoxmaestrodia = $data->saldoMaestrodia($cartera);
            $saldoAcumulado = $data->saldoAcumulado();
            $saldoVencido = $data->saldoVCD();
            $saldoCartera = $data->SaldoCD();
            if (count($saldoxmaestro)) {
                include 'app/views/pages/p.CarteraCobranza.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function SaldosxDocumento($cliente) {    //19072016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.saldosxdocumento.php');
            ob_start();
            $historico = 'No';
            $datacli = $data->traeDatacliente($cliente);    // trae los datos del cliente
            $saldo = $data->SaldosDelCliente($cliente);
            $exec = $data->traeSaldosDoc($cliente, $historico);
            $csaldo = $data->saldoCliente($cliente);
            $saldovencido = $data->saldoVencidoCliente($cliente);
            if (count($datacli)) {
                include 'app/views/pages/p.saldosxdocumento.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function SaldosxDocumentoH($cliente) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.saldosxdocumento.php');
            ob_start();
            $historico = 'Si';
            $datacli = $data->traeDatacliente($cliente);    // trae los datos del cliente
            $saldo = $data->SaldosDelCliente($cliente);
            $exec = $data->traeSaldosDoc($cliente, $historico);
            $csaldo = $data->saldoCliente($cliente);
            $saldovencido = $data->saldoVencidoCliente($cliente);
            if (count($datacli)) {
                include 'app/views/pages/p.saldosxdocumento.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ContactosCliente($cliente) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.contactoscliente.php');
            ob_start();
            $exec = $data->ContactosDelCliente($cliente);
            if (count($exec)) {
                include 'app/views/pages/p.contactoscliente.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function CarteraxCliente($cve_maestro) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.saldosxcliente.php');
            ob_start();
            //$rfcc = $rfc;
            //$saldosCliente = $data->traeSaldosCliente($rfc);
            $saldoIndividual = $data->saldoIndividual($cve_maestro);
            $saldoIMaestro = $data->saldoIndMaestro($cve_maestro);
            if (count($saldoIndividual)) {
                include 'app/views/pages/p.saldosxcliente.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function GuardarComprobantesCaja($caja, $ruta, $origen) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.blank.php');
            ob_start();
            $exec = $data->guardaCompCaja($caja, $ruta);
            if (count($exec)) {
                include 'app/views/pages/p.blank.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
                header("Refresh:5; url=index.php?action=$origen");
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function PedidosAnticipados() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.pedidosAnticipados.php');
            ob_start();
            $pedidos = $data->pedidosAnticipados();
            if (count($pedidos > 0)) {
                include 'app/views/pages/p.pedidosAnticipados.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function AnticipadosUrgencias() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.pedidosAnticipadosSD.php');
            ob_start();
            $pedidos = $data->anticipadosUrgencias();
            if (count($pedidos)) {
                include 'app/views/pages/p.pedidosAnticipadosUrgencias.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Algo no salio como se esperaba o No Existen Pedidos que sean urgentes, Si usted cree que es un error, favor de verificarlo con sistemas. Gracias!!!</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function SubMenuCxCC() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/modules/m.subfacturacion.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function FacturacionDia() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.pfacturasdeldia.php');
            ob_start();
            $exec = $data->facturasDelDia();
            $resumen = $data->resumenFacturasDelDia();
            $totaduana = $data->resumenFacturasDelDiaAduana();
            $totlog = $data->resumenFacturasDelDiaLogistica();
            if (count($exec > 0)) {
                include 'app/views/pages/p.pfacturasdeldia.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function FacturacionAyer() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.pfacturasdeldia.php');
            ob_start();
            $exec = $data->facturasAyer();
            if (count($exec > 0)) {
                include 'app/views/pages/p.pfacturasdeldia.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function utilidadFacturas($fechaini, $fechafin, $rango, $utilidad, $letras, $status) {    //01082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.putilidadfact.php');
            ob_start();
            @$total = $data->UtilidadFacturasTot($fechaini, $fechafin, $rango, $utilidad, $letras, $status);
            @$exec = $data->UtilidadFacturas($fechaini, $fechafin, $rango, $utilidad, $letras, $status);
            if (count($exec > 0)) {
                include 'app/views/pages/p.putilidadfact.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function utilidadXFactura($fact) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.putilidadfactxpart.php');
            ob_start();
            $exec = $data->UtilidadXFacturaHead($fact);
            $partidas = $data->UtilidadXFactura($fact);
            $total = $data->TotalesUtilidadxFactura($fact);
            if (count($exec > 0)) {
                include 'app/views/pages/p.putilidadfactxpart.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function deslindecr() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.vercrdeslinde.php');
            ob_start();
            $deslindes = $data->deslindecr();
            if (count($deslindes > 0)) {
                include 'app/views/pages/p.vercrdeslinde.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function deslindearevision($caja, $docf, $docr, $sol, $cr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.vercrdeslinde.php');
            ob_start();
            $actualiza = $data->deslindearevision($caja, $docf, $docr, $sol, $cr);
            $deslindes = $data->deslindecr();
            if (count($deslindes > 0)) {
                include 'app/views/pages/p.vercrdeslinde.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function GuardarXMLF($doc, $archivo, $origen) {        //03082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.blank.php');
            ob_start();
            $exec = $data->guardarXmlDocF($doc, $archivo);
            if (count($exec)) {
                include 'app/views/pages/p.blank.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
                header("Refresh:5; url=index.php?action=$origen");
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function GuardarXMLD($doc, $archivo, $origen) {        //03082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.blank.php');
            ob_start();
            $exec = $data->guardarXmlDocD($doc, $archivo);
            if (count($exec)) {
                include 'app/views/pages/p.blank.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
                header("Refresh:5; url=index.php?action=$origen");
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function revConDosPasos($cr) {   //05082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.revisiondospasos.php');
            ob_start();
            if ($cr == 'CR1') {
                $nocr = $data->revConDosPasosNoCr();
            }
            $revdia = $data->revConDosPasosDia($cr);
            $exec = $data->revConDosPasos($cr);
            if (count($exec)) {
                include 'app/views/pages/p.revisiondospasos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function revSinDosPasos($cr) {       //05082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.revisionNOdospasos.php');
            ob_start();
            if ($cr == 'CR1') {
                $nocr = $data->revSinDosPasosNoCr();
            }
            $revdia = $data->revSinDosPasosDia($cr);
            $exec = $data->revSinDosPasos($cr);
            if (count($exec)) {
                include 'app/views/pages/p.revisionNOdospasos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function DeslindeConDosPasos($caja, $cr) {        //05082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "RevConDosP&cr={$cr}";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $exec = $data->statusDeslindeConDP($caja);
            //var_dump($exec);
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function DeslindeSinDosPasos($caja, $cr, $numcr) {        //05082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "RevSinDosP&cr={$cr}";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $exec = $data->statusDeslindeSinDP($caja, $numcr);
            //var_dump($exec);
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function DeslindeRevConDosP($cr) {       //05082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.deslindeRevDP.php');
            ob_start();
            $revdia = $data->DeslindeDosPasosDia($cr);
            $exec = $data->DeslindeDosPasos($cr);
            if (count($exec)) {
                include 'app/views/pages/p.deslindeRevDP.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function DeslindeRevSinDosP($cr) {       //05082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.deslindeNoRevDP.php');
            ob_start();
            $revdia = $data->DeslindeNoDosPasosDia($cr);
            $exec = $data->DeslindeNoDosPasos($cr);
            if (count($exec)) {
                include 'app/views/pages/p.deslindeNoRevDP.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function salvaMotivoDeslindeDP($caja, $motivo, $cr) {      //05082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "DesRevConDosP&cr={$cr}";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $exec = $data->salvaMotivoDesDP($caja, $motivo);
            //var_dump($exec);
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function salvaMotivoDeslindeNoDP($caja, $motivo, $cr) {    //05082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "DesRevSinDosP&cr={$cr}";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $exec = $data->salvaMotivoDesNoDP($caja, $motivo);
            //var_dump($exec);
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function avanzarCajaCobranza($caja, $revdp, $numcr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            if ($revdp == 'S')
                $redireccionar = "RevConDosP";
            else
                $redireccionar = "RevSinDosP";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $exec = $data->avanzarCajaCobranza($caja, $numcr);
            //var_dump($exec);
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        }else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function CajaCobranza($caja, $revdp, $numcr, $cr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $redireccionar = "RevSinDosP&cr={$cr}";

            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            $exec = $data->CajaCobranza($caja, $revdp, $numcr);
            //var_dump($exec);
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /* function created by GDELEON 3/Ago/2016 */

    function TraePresupuestoConceptGasto($concept) {
        $data = new pegaso;
        $result = $data->TraePresupuestoConceptGasto($concept);
        foreach ($result as $rs) {
            $re = $rs->PRESUPUESTO;
        }
        return $re;
    }

    function SMRevisionDosPasos() {     //05082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.msubrevisiondospasos.php');
            ob_start();
            $exec = $data->traeCarteras();
            if (count($exec)) {
                include 'app/views/modules/m.msubrevisiondospasos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function SMSinRevisionDosPasos() {     //05082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.msubrevisionSINdospasos.php');
            ob_start();
            $exec = $data->traeCarteras();
            if (count($exec)) {
                include 'app/views/modules/m.msubrevisionSINdospasos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function SMDesRevisionDosPasos() {     //05082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.msubdeslindedospasos.php');
            ob_start();
            $exec = $data->traeCarteras();
            if (count($exec)) {
                include 'app/views/modules/m.msubdeslindedospasos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function SMDesSinRevisionDosPasos() {     //05082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.msubdeslindeNOpasos.php');
            ob_start();
            $exec = $data->traeCarteras();
            if (count($exec)) {
                include 'app/views/modules/m.msubdeslindeNOpasos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function deslindeaduana() {     //05082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verDeslindeAduana.php');
            ob_start();
            $documentos = $data->deslindeaduana();
            if (count($documentos)) {
                include 'app/views/pages/p.verDeslindeAduana.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function DesaAdu($caja, $solucion) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verDeslindeAduana.php');
            ob_start();
            $soldeslinde = $data->DesaAdu($caja, $solucion);
            $documentos = $data->deslindeaduana();
            if (count($documentos)) {
                include 'app/views/pages/p.verDeslindeAduana.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function BuscarCajasxPedido() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/pages/p.BusquedaCajas.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function MuestraCaja($docp) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.MuestraCaja.php');
            ob_start();
            $exec = $data->traeCajasxPedido($docp);
            if (count($exec) > 0) {
                include 'app/views/pages/p.MuestraCaja.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>ESTE PEDIDO NO HA SIDO EMPACADO NI EMBALADO, FAVOR DE REVISAR CON BODEGA</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function RecibirDocsRevision() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.vercarterarev.php');
            ob_start();
            $docsrevision = $data->RecibirDocsRevision();
            $habilitaImpresion = $data->impresionCierre();
            if (count($docsrevision) > 0) {
                include 'app/views/pages/p.vercarterarev.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function recDocCob($idc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.vercarterarev.php');
            ob_start();
            $recibir = $data->recDocCob($idc);
            $docsrevision = $data->RecibirDocsRevision();
            if (count($docsrevision) > 0) {
                include 'app/views/pages/p.vercarterarev.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function desDocCob($idc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.vercarterarev.php');
            ob_start();
            $recibir = $data->desDocCob($idc);
            $docsrevision = $data->RecibirDocsRevision();
            if (count($docsrevision) > 0) {
                include 'app/views/pages/p.vercarterarev.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function SMCCobranza() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/modules/m.msubccobranza.php');
            ob_start();
            $exec = $data->traeCarterasCobranza();
            if (count($exec)) {
                include 'app/views/modules/m.msubccobranza.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function VerCobranza($cc) {       //05082016
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.VerCobranza.php');
            ob_start();
            //echo $cc;
            if ($cc == 'CCA') {
                $nocr = $data->VerCobranza();
            }
            $revdia = $data->VerCobranzaDia($cc);
            $exec = $data->VerCobranzaC($cc);
            if (count($exec)) {
                include 'app/views/pages/p.VerCobranza.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ImprimirDevolucion($idc, $docf) {
        ob_start();
        $data = new Pegaso;
        $actcaja = $data->cajabodeganc($idc, $docf); #Actualiza la caja para que deje de aparecer en la lista.
        $actpaquete = $data->paquetedevolucion($idc, $docf); #Actualiza el paquete con lo devuelto e impreso, crea folio de Devolucion de paquetes.
        $devueltos = $data->ImprimirDevolucion($idc, $docf); # Obtiene los datos para la impresion de lo que esta devuelto.
        $entregados = $data->ImprimirDevolucionEntrega($idc, $docf); # Obtiene los datos para la impresion de lo que se entrego.
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerRECEPpdf.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);

        $pdf->SetFont('Arial', 'B', 10);

        foreach ($devueltos as $data) {
            $pdf->SetX(160);
            $pdf->Write(6, "Folio de devolucion : DNC" . $data->FOLIO_DEV);
        }
        $pdf->Ln();
        foreach ($actpaquete as $row) {
            $pdf->Cell(40, 6, "Factura: " . $row->FACTURA);
            $pdf->Ln();
            $pdf->Cell(40, 6, "Fecha de factura: " . $row->FECHA_FACTURA);
            $pdf->Ln();
            $pdf->Cell(100, 6, "Cliente: " . $row->CLIENTE);
            $pdf->Ln();
            $pdf->Cell(50, 6, "Pedido: " . $row->PEDIDO, 0, 30);
            $pdf->Cell(30, 6, "Caja: " . $row->CAJA);
            $pdf->Cell(50, 6, "Unidad: " . $row->UNIDAD);
            $pdf->Ln();
            $pdf->Cell(40, 6, "Status losgistica: " . $row->STATUS_LOG);
            $pdf->Ln();
        }
        foreach ($dev as $dev) {
            $pdf->Ln(5);
            $pdf->SetX(60);
            $pdf->Write(8, $dev->ARTICULO);
        }

        $pdf->Ln(5);
        $pdf->SetX(80);
        $pdf->Write(8, "PRODUCTOS DE INGRESO A BODEGA:");
        $pdf->Ln(9);
        $pdf->Cell(60, 6, "Articulo", 1);
        $pdf->Cell(90, 6, "Descripcion", 1);
        $pdf->Cell(35, 6, "Cantidad", 1);
        $pdf->Ln();

        foreach ($devueltos as $doc) {
            $pdf->Cell(60, 6, $doc->ARTICULO, 1);
            $pdf->Cell(90, 6, $doc->DESCRIPCION, 1);
            $pdf->Cell(35, 6, $doc->CANTIDAD, 1);
            $pdf->Ln();
        }

        $pdf->Ln(75);
        $pdf->Cell(20, 6, "");
        $pdf->Cell(60, 6, "Nombre y firma de entrega", 'T');
        $pdf->Cell(20, 6, "");
        $pdf->Cell(60, 6, "Nombre y firma de recibido", 'T');

        ob_get_clean();
        $pdf->Output('DEVOLUCION.pdf', 'i');
    }

    function verCajasLogistica() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verCajasLogistica.php');
            ob_start();
            $listacajas = $data->verCajasLogistica();
            if (count($listacajas)) {
                include 'app/views/pages/p.verCajasLogistica.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function cambiarStatus($idcaja, $docp, $secuencia, $unidad, $idu, $ntipo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verCajasLogistica.php');
            ob_start();
            $actstatus = $data->cambiarStatus($idcaja, $docp, $secuencia, $unidad, $idu, $ntipo);
            $listacajas = $data->verCajasLogistica();
            if (count($listacajas)) {
                include 'app/views/pages/p.verCajasLogistica.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verLoteEnviar() {          //21
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verLoteEnvio.php');
            ob_start();
            $reenrutar = $data->verLoteEnviarReenrutar();
            $entrega = $data->verLoteEnviar();
            if (count($entrega) or count($reEnrutar)) {
                include 'app/views/pages/p.verLoteEnvio.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function entaduana($idc, $docf, $docp) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verLoteEnvio.php');
            ob_start();
            $actstatus = $data->entaduana($idc, $docf, $docp);
            $reenrutar = $data->verLoteEnviarReenrutar();
            $entrega = $data->verLoteEnviar();
            if (count($entrega) or count($reEnrutar)) {
                include 'app/views/pages/p.verLoteEnvio.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function recbodega($idc, $docf, $docp) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verLoteEnvio.php');
            ob_start();
            $actstatus = $data->recbodega($idc, $docf, $docp);
            $reenrutar = $data->verLoteEnviarReenrutar();
            $entrega = $data->verLoteEnviar();
            if (count($entrega) or count($reEnrutar)) {
                include 'app/views/pages/p.verLoteEnvio.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function reclogistica($idc, $docf, $docp) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verLoteEnvio.php');
            ob_start();
            $actstatus = $data->reclogistica($idc, $docf, $docp);
            $reenrutar = $data->verLoteEnviarReenrutar();
            $entrega = $data->verLoteEnviar();
            if (count($entrega) or count($reEnrutar)) {
                include 'app/views/pages/p.verLoteEnvio.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function impLoteFact() {
        ob_start();
        $usuario = $_SESSION['user']->USER_LOGIN;
        $fecha = date("Y-m-d H:i:s");
        $data = new Pegaso;
        #$actfact = $data->(); ##Actualiza la factura para que se desapasrezca de la pantala.
        #$actcaja = $data->(); ##Actualiza la caja para que aparezca en Asignacion de unidad.
        $lotedia = $data->impLoteDia(); #Obtiene los datos para las factura que son de el mismo dia.
        $loter = $data->impLoteReeenrutar(); # Obtiene los datos para las cajas que se reenrutan.
        $factn = $data->totfactn();
        $factr = $data->totfactr();
        $actcajas = $data->actimpcajas();

        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerRECEPpdf.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);

        $pdf->SetFont('Arial', 'B', 10);

        $pdf->SetX(10);
        $pdf->Write(6, "Facturas Nuevas : " . $factn);
        $pdf->Ln();
        $pdf->Cell(40, 6, "Facturas Reenrutar : " . $factr);
        $pdf->Ln();
        $pdf->Cell(40, 6, "Fecha de Reporte : " . $fecha);
        $pdf->Ln();
        $pdf->Cell(100, 6, "Usuario : " . $usuario);
        $pdf->Ln();

        $pdf->Ln(5);
        $pdf->SetX(80);
        $pdf->Write(5, "Relacion de Lote de facturas Nuevas del " . $fecha);
        $pdf->Ln(9);
        $pdf->Cell(25, 6, "Factura", 1);
        $pdf->Cell(60, 6, "Cliente", 1);
        $pdf->Cell(30, 6, "Usuario Aduana", 1);
        $pdf->Cell(30, 6, "Usuario Bodega", 1);
        $pdf->Cell(32, 6, "Usuario Logistica", 1);
        $pdf->Cell(15, 6, "Caja", 1);
        $pdf->Ln();
        foreach ($lotedia as $doc) {
            $pdf->Cell(25, 6, $doc->FACTURA, 1);
            $pdf->Cell(60, 6, $doc->IDC, 1);
            $pdf->Cell(30, 6, $doc->U_ENTREGA, 1);
            $pdf->Cell(30, 6, $doc->U_BODEGA, 1);
            $pdf->Cell(32, 6, $doc->U_LOGISTICA, 1);
            $pdf->Cell(15, 6, $doc->ID, 1);
            $pdf->Ln();
        }
        $pdf->Ln(5);
        $pdf->SetX(80);
        $pdf->Write(5, "Relacion de Lote de facturas para Reenrutar del " . $fecha);
        $pdf->Ln(9);
        $pdf->Cell(25, 6, "Factura", 1);
        $pdf->Cell(60, 6, "Cliente", 1);
        $pdf->Cell(30, 6, "Usuario Aduana", 1);
        $pdf->Cell(30, 6, "Usuario Bodega", 1);
        $pdf->Cell(32, 6, "Usuario Logistica", 1);
        $pdf->Cell(15, 6, "Caja", 1);
        $pdf->Ln();
        foreach ($loter as $doc) {
            $pdf->Cell(25, 6, $doc->FACTURA, 1);
            $pdf->Cell(60, 6, $doc->IDC, 1);
            $pdf->Cell(30, 6, $doc->U_ENTREGA, 1);
            $pdf->Cell(30, 6, $doc->U_BODEGA, 1);
            $pdf->Cell(32, 6, $doc->U_LOGISTICA, 1);
            $pdf->Cell(15, 6, $doc->ID, 1);
            $pdf->Ln();
        }
        $pdf->Ln(75);
        $pdf->Cell(5, 6, "");
        $pdf->Cell(55, 6, "Nombre y firma de Aduana", 'T');
        $pdf->Cell(10, 6, "");
        $pdf->Cell(55, 6, "Nombre y firma de Bodega", 'T');
        $pdf->Cell(10, 6, "");
        $pdf->Cell(55, 6, "Nombre y firma de Logistica", 'T');
        ob_get_clean();
        $pdf->Output('DEVOLUCION.pdf', 'i');
    }

    function VerInventarioEmpaque() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.InventarioPatio.php');
            ob_start();
            $invempaque = $data->VerInventarioEmpaque();
            if (count($invempaque)) {
                include 'app/views/pages/p.InventarioPatio.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verPedidosPendientes() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verPedidosPendientes.php');
            ob_start();
            $pedidosList = $data->verPedidosPendientes();
            if (count($pedidosList)) {
                include 'app/views/pages/p.verPedidosPendientes.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function docfact($docfact, $idc) {       //2306-
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.pantalla2.php');
            ob_start();
            $actdocfact = $data->docfact($docfact, $idc);
            $exec = $data->PorFacturar();
            $notascred = $data->PendientesGenNC();
            $reenruta = $data->PendientesGenRee();
            if ($exec != '') {
                include 'app/views/pages/p.pantalla2.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<h2>No hay resultados</h2>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function CancelarFactura() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/pages/p.CancelarFactura.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function CancelaFactura($docp) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.CancelaFactura.php');
            ob_start();
            $exec = $data->traeFacturaxCancelar($docp);
            if (count($exec) > 0) {
                include 'app/views/pages/p.CancelaFactura.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>LA FACTURA NO EXISTE, FAVOR DE REVISAR LOS DATOS.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function CancelarF($docf, $idc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.CancelaFactura.php');
            ob_start();
            $cancelar = $data->CancelaF($docf, $idc);
            $exec = $data->traeFacturaxCancelar($docf);
            if (count($exec) > 0) {
                include 'app/views/pages/p.CancelaFactura.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>LA FACTURA NO EXISTE, FAVOR DE REVISAR LOS DATOS.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function UtilidadBaja() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verUtilidadBaja.php');
            ob_start();
            $exec = $data->UtilidadBaja();
            if (count($exec) > 0) {
                include 'app/views/pages/p.verUtilidadBaja.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>FAVOR DE INICIAR SESION.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function solAutoUB($docc, $par) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verUtilidadBaja.php');
            ob_start();
            $solicitar = $data->solAutoUB($docc, $par);
            $exec = $data->UtilidadBaja();
            if (count($exec) > 0) {
                include 'app/views/pages/p.verUtilidadBaja.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>FAVOR DE INICIAR SESION.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verSolicitudesUB() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verSolicitudesUB.php');
            ob_start();
            $exec = $data->verSolicitudesUB();
            if (count($exec) > 0) {
                include 'app/views/pages/p.verSolicitudesUB.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>FELICIDADES, AL PARECER TODOS TUS VENDEDORES VENDEN CON UTILIDAD MAYOR AL 23%.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function AutorizarUB($docc, $par) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verSolicitudesUB.php');
            ob_start();
            $autorizar = $data->AutorizarUB($docc, $par);
            $exec = $data->verSolicitudesUB();
            if (count($exec) > 0) {
                include 'app/views/pages/p.verSolicitudesUB.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>FELICIDADES, AL PARECER TODOS TUS VENDEDORES VENDEN CON UTILIDAD MAYOR AL 23%.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function RechazoUB($docc, $par) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verSolicitudesUB.php');
            ob_start();
            $autorizar = $data->RechazoUB($docc, $par);
            $exec = $data->verSolicitudesUB();
            if (count($exec) > 0) {
                include 'app/views/pages/p.verSolicitudesUB.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>FELICIDADES, AL PARECER TODOS TUS VENDEDORES VENDEN CON UTILIDAD MAYOR AL 23%.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function Pagos() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.pagos.listado.php');
            ob_start();
            //generamos consultas
            // $cuentab = $data->CuentasBancos();	// cafaray 03/sep/2016
            $exec = $data->Pagos();
            if (count($exec) > 0) {
                include 'app/views/pages/p.pagos.listado.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function pagoGastos() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.pagos.gastos.listado.php');
            ob_start();
            $exec = $data->listadoGastos();
            if (count($exec) > 0) {
                include 'app/views/pages/p.pagos.gastos.listado.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function pagoGasto($identificador) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.pago.gasto.php');
            ob_start();
            $cuentaBancarias = $data->CuentasBancos();
            $exec = $data->PagosGastos($identificador);
            if (count($exec) > 0) {
                include 'app/views/pages/p.pago.gasto.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function realizaPago($documento) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.pagos.php');
            ob_start();
            $cuentab = $data->CuentasBancos();
            $exec = $data->detallePago($documento);
            if (count($exec) > 0) {
                include 'app/views/pages/p.pagos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    //function PagoCorrecto($docu, $tipop, $monto, $entregadoa){ editado por GDELEON 3/Ago/2016
    function PagoCorrecto($cuentabanco, $documento, $tipopago, $monto, $proveedor, $claveProveedor, $fechadocumento) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            // $html = $this->load_page('app/views/pages/p.pagoc.php'); cafaray 3/sep/2016
            $html = $this->load_page('app/views/pages/p.pagos.listado.php');
            ob_start();
            //generamos consultas
            $error = "Datos guardados correctamente";
            //$guarda = $data->GuardaPagoCorrecto($docu, $tipop, $monto, $entregadoa);
            $guarda = $data->GuardaPagoCorrecto($cuentabanco, $documento, $tipopago, $monto, $proveedor, $claveProveedor, $fechadocumento);
            $exec = $data->Pagos();
            if (count($guarda) > 0) {
//            	if ($tipop == 'ch') {
//                	$prov = $nomprov;
//                	$cant = $monto;
//                	$clve = $cveclpv;
//                	$cta = $cuentaban;
//                	$fecha = $fechadoc;
//                	include 'app/views/pages/p.pagoch.php';
//            	} else {
                //include 'app/views/pages/p.pagoc.php'; cafaray 3/sep/2016
                include 'app/views/pages/p.pagos.listado.php';
//            	}
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
                $pagina .= "<script>alert('$error');</script>";
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function PagoGastoCorrecto($cuentabanco, $documento, $tipopago, $monto, $proveedor, $claveProveedor, $fechadocumento) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;

            $pagina = $this->load_template('Pagos');
            // $html = $this->load_page('app/views/pages/p.pagoc.php'); cafaray 3/sep/2016
            $html = $this->load_page('app/views/pages/p.pagos.gastos.listado.php');
            ob_start();
            //generamos consultas
            //$guarda = $data->GuardaPagoCorrecto($docu, $tipop, $monto, $entregadoa);
            $guarda = $data->GuardaPagoGastoCorrecto($cuentabanco, $documento, $tipopago, $monto, $proveedor, $claveProveedor, $fechadocumento);
            if ($guarda != null) {
                $error = "Datos guardados correctamente";
            } else {
                $error = "Hubieron errores al registrar el pago. Revise la bitacora de operación.";
            }
            $exec = $data->listadoGastos();
            if (count($guarda) > 0) {
                include 'app/views/pages/p.pagos.gastos.listado.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
                $pagina .= "<script>alert('$error');</script>";
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verXautorizar() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.pagos.xautorizar.php');
            ob_start();
            $pagos = $data->listadoXautorizar();
            if (count($pagos) > 0) {
                include 'app/views/pages/p.pagos.xautorizar.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>No se han localizado pagos por autorizar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function xAutorizar($tipo, $identificador) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.pagos.xdictaminar.php');
            ob_start();
            $pagos = $data->xAutorizar($tipo, $identificador);
            if (count($pagos) > 0) {
                include 'app/views/pages/p.pagos.xdictaminar.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>No se han localizado pagos por autorizar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function xAutorizarDictamen($tipo, $identificador, $dictamen, $comentarios) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.pagos.xautorizar.php');
            ob_start();
            $dictamen = $data->xAutorizarDictamen($tipo, $identificador, $dictamen, $comentarios);
            if ($dictamen != null) {
                echo "<script>alert('El pago fue dictaminado correctamente.')</script>";
            }
            $pagos = $data->listadoXautorizar();
            if (count($pagos) > 0) {
                include 'app/views/pages/p.pagos.xautorizar.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>No se han localizado pagos por autorizar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function Cheques() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.impCheques.php');
            ob_start();
            $listado = $data->Cheques();
            $folios = $data->folioReal();
            if (count($listado)) {
                include 'app/views/pages/p.impCheques.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>NO ESISTEN CHEQUES POR IMPRIMIR</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ImpChBanamex($cheque, $fecha, $folio) {
        $data = new pegaso;
        $letras = new NumberToLetterConverter;
        $actdatos = $data->impChBanamex($cheque, $fecha, $folio); /// Actualiza los datos de la fecha y folio de cheque.
        $datos = $data->DatosCheque($cheque);
        $m = $datos->MONTO;
        $Monto = number_format($m, 0);
        $M1 = number_format($m, 2);
        $M4 = substr($M1, 0, -2);
        $centavos = substr($M1, -2);
        $m5 = $M4 . '00';
        $res = $letras->to_word($m5);
        if ($centavos == 00) {
            $leyenda = 'PESOS CON 00/100 MN';
        } else {
            $leyenda = 'PESOS CON ' . $centavos . '/100 MN';
        }

        //$fecha=date("d-m-Y");
        //echo $res;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->SetTextColor(198, 23, 23);
        $pdf->SetXY(180, 5);
        $pdf->CELL(60, 5, '');
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->SetTextColor(14, 3, 3);
        $pdf->SetXY(160, 14);
        $pdf->Cell(60, 10, $fecha);
        $pdf->SetXY(10, 35);
        $pdf->Cell(60, 5, $datos->BENEFICIARIO);
        $pdf->SetXY(170, 32);
        $pdf->Cell(70, 13, $M1);
        $pdf->SetXY(10, 41);
        $pdf->Cell(10, 10, $res . $leyenda);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetXY(10, 88);
        $pdf->Cell(10, 10, 'Pago referente a la Orden de Compra Pegaso: ' . $datos->DOCUMENTO);
        $pdf->SetXY(10, 93);
        $pdf->Cell(10, 10, $datos->FECHAELAB . '   Folio Interno: ' . $datos->CHEQUE . ' Banamex No.' . $datos->FOLIO_REAL);


        $pdf->Output('Transferencia' . $datos->FOLIO_REAL . '.pdf', 'i');
    }

    function listadoPagosXImprimir() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;

            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.pagos.ximprimir.php');
            ob_start();
            $exec = $data->listadoPagosImpresion();
            if (count($exec) > 0) {
                include 'app/views/pages/p.pagos.ximprimir.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
                $pagina .= "<script>alert('$error');</script>";
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function impComprobantePago($identificador, $tipo) {
        $data = new pegaso;
        $act = $data->ActStatusImp($identificador);
        $datos = $data->DatosPago($identificador);

        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerpdf_PagoGasto.jpg', 10, 15, 205, 55);
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->SetTextColor(14, 3, 3);
        $pdf->Ln(60);
        $pdf->Cell(10, 10, 'Fecha Gasto : ' . $datos->FECHA_CREACION);
        $pdf->Ln(10);
        $pdf->Cell(10, 10, 'Proveedor : ' . $datos->NOMBRE);
        $pdf->Ln(10);
        $pdf->Cell(10, 10, 'Folio de Gatos : ' . $datos->FOLIO_PAGO);
        $pdf->Ln(10);
        $pdf->Cell(10, 10, 'Pagado por : ' . $datos->USUARIO_REGISTRA);
        $pdf->Ln(10);
        $pdf->Cell(10, 10, 'Referencia del Gasto : ' . $datos->REFERENCIA);
        $pdf->Ln(10);
        $pdf->Cell(10, 10, 'Fecha de Pago : ' . $datos->FECHA_REGISTRO);
        $pdf->Ln(10);
        $pdf->Cell(10, 10, 'Cuenta de Pago : ' . $datos->CUENTA_BANCARIA);
        $pdf->Ln(10);
        $pdf->Cell(10, 10, 'Tipo de Pago : ' . $datos->TIPO_PAGO);
        $pdf->Ln(10);
        $pdf->Cell(10, 10, 'Monto del Pago : ' . $datos->MONTO_PAGO);


        $pdf->Ln(45);
        $pdf->Cell(10, 10, '________________________');
        $pdf->Ln(5);
        $pdf->Cell(10, 10, 'Firma de Recibido');


        //$pdf->Output('Transferencia '.$datostrans->DOCUMENTO .'.pdf', 'i');
        /* Falta crear consulta que traiga el número de folio generado */

        $pdf->Output('Comprobante del Folio : ' . $datos->FOLIO_PAGO . '.pdf', 'i');
    }

    function cancelarPedidos() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.listaPedidos.php');
            ob_start();
            $exec = $data->cancelarPedidos();
            if (count($exec) > 0) {
                include 'app/views/pages/p.listaPedidos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function cancelaPedido($pedido, $motivo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.listaPedidos.php');
            ob_start();
            $cancelar = $data->cancelaPedido($pedido, $motivo);
            $exec = $data->cancelarPedidos();
            if (count($exec) > 0) {
                include 'app/views/pages/p.listaPedidos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function listaClientes() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.listaClientes.php');
            ob_start();
            $saldoxaplicar = $data->saldoXaplicar();
            $exec = $data->listaClientes();
            if (count($exec) > 0) {
                include 'app/views/pages/p.listaClientes.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function cargaPago($cliente) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.formIngresaPago.php');
            ob_start();
            $reg = $data->regPagos($cliente);
            $cli = $data->cargaPago($cliente);
            $cuenta = $data->CuentasBancos();

            if (count($cli)) {
                include 'app/views/pages/p.formIngresaPago.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function listadoCuentasBancarias() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.estadocuenta.listado.php');
            ob_start();
            $exec = $data->listarCuentasBancarias();
            if (count($exec) > 0) {
                include 'app/views/pages/p.estadocuenta.listado.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>No se han localizado cuentas bancarias para inicar el registro.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function estadoCuentaRegistro($identificador, $banco, $cuenta, $dia) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.estadocuenta.registro.php');
            //$html = "No se localizo contenido";
            ob_start();
            //echo "data->obtenerEdoCtaDetalle($identificador, $dia);";
            $exec = $data->obtenerEdoCtaDetalleDia($identificador, $dia);

//            if (count($exec) > 0) {
            $table = ob_get_clean();
            include 'app/views/pages/p.estadocuenta.registro.php';
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            //           } else {
            //               $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>No se ha localizado detalle para '.$banco.' - '.$cuenta.'.</h2><center></div>', $pagina);
            //           }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function estadoCuentaRegistrar($identificador, $banco, $cuenta, $fecha, $descripcion, $monto) {
        $data = new pegaso();

        $inserta = $data->estadoCuentaRegistrar($identificador, $fecha, $descripcion, $monto);
        //echo "Valor de inserta $inserta";
        $this->estadoCuentaRegistro($identificador, $banco, $cuenta, $fecha);
    }

    function estadoCuentaDetalle($identificador) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.estadocuenta.detalle.php');
            ob_start();
            $exec = $data->obtenerEdoCtaDetalle($identificador);
            if (count($exec) > 0) {
                include 'app/views/pages/p.estadocuenta.detalle.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>No se ha localizado detalle de esta cuenta bancaria.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verXrecibir() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.pagos.xrecibir.php');
            ob_start();
            $pagos = $data->listadoXrecibir();
            if (count($pagos) > 0) {
                include 'app/views/pages/p.pagos.xrecibir.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>No se han localizado pagos por autorizar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function pagosRecepcion($tipo, $identificador, $fecha, $banco, $monto) {
        $data = new pegaso;
        $recibido = $data->marcarRecibido($tipo, $identificador, $fecha, $banco, $monto);
        if ($recibido > 0) {
            $mensaje = "El pago ha sido marcado como recibido.";
        } else {
            $mensaje = "Algo ocurró y no se logro marcae el pago como recibido.";
        }
        echo "<script>alert('$mensaje');</script> ";
        $this->verXrecibir();
    }

    function verXconciliar() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.pagos.xconciliar.php');
            ob_start();
            $pagos = $data->listadoXconciliar();
            if (count($pagos) > 0) {
                include 'app/views/pages/p.pagos.xconciliar.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>No se han localizado pagos por autorizar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function pagoAConciliar($tipo, $identificador) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.pagos.conciliar.php');
            ob_start();
            $pagos = $data->pagoAconciliar($tipo, $identificador);
            if (count($pagos) > 0) {
                include 'app/views/pages/p.pagos.conciliar.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>No se han localizado pagos por autorizar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function pagoConciliar($tipo, $identificador, $fecha) {
        $data = new pegaso;
        $result = $data->pagoConciliar($tipo, $identificador, $fecha);
        if ($result > 0) {
            $mensaje = "El pago se ha conciliado correctamente.";
        } else {
            $mensaje = "Algo ocurrio y el pago no se logró conciliar.";
        }
        echo "<script>alert('$mensaje');</script>";
        $this->verXconciliar();
    }

    function guardaPago($cliente, $monto, $fechaA, $fechaR, $banco) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.formIngresaPago.php');
            ob_start();
            $guardar = $data->guardaPago($cliente, $monto, $fechaA, $fechaR, $banco);
            $reg = $data->regPagos($cliente);
            $cli = $data->cargaPago($cliente);
            $cuenta = $data->CuentasBancos();
            if (count($cli)) {
                include 'app/views/pages/p.formIngresaPago.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function aplicarPago($cliente) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.formAplicaPago.php');
            ob_start();
            $facturas = $data->traeFacturas($cliente);
            $cli = $data->aplicarPago($cliente);
            if (count($cli)) {
                include 'app/views/pages/p.formAplicaPago.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function capturaPagosConta($banco, $cuenta) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.formIngresaPagoCont.php');
            ob_start();
            if (empty($fecha)) {
                $fecha = date('m-d-Y');
            }
            $fecha = $fecha;

            $bancos = $data->CuentasBancarias($banco, $cuenta);
            $pagosA = $data->traePagosActual($banco, $cuenta);
            $pagosAn = $data->traePagosAnterior($banco, $cuenta);
            if (count($bancos)) {
                include 'app/views/pages/p.formIngresaPagoCont.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ingresarPago($banco, $monto, $fecha, $ref, $banco2, $cuenta) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.formIngresaPagoCont.php');
            ob_start();
            $fecha = $fecha;
            $ingresa = $data->ingresarPago($banco2, $monto, $fecha, $ref);
            $bancos = $data->CuentasBancarias($banco, $cuenta);
            $pagosA = $data->traePagosActual($banco, $cuenta);
            $pagosAn = $data->traePagosAnterior($banco, $cuenta);
            if (count($bancos)) {
                include 'app/views/pages/p.formIngresaPagoCont.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function listaCuentas() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.listadocuentas.php');
            ob_start();
            $exec = $data->listarCuentasBancarias();
            if (count($exec)) {
                include 'app/views/pages/p.listadocuentas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function selectBanco() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.selectBanco.php');
            ob_start();
            $exec = $data->listarCuentasBancarias();
            if (count($exec)) {
                include 'app/views/pages/p.selectBanco.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function estado_de_cuenta($banco, $cuenta) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.EstadoDeCuenta.php');
            ob_start();
            $mes = 0;
            $meses = $data->traeMeses();
            $bancos = $data->CuentasBancarias($banco, $cuenta);
            $exec = $data->estado_de_cuenta($banco, $cuenta);
            if (count($exec)) {
                include 'app/views/pages/p.EstadoDeCuenta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function estado_de_cuenta_mes($mes, $banco, $cuenta, $anio, $nvaFechComp) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.EstadoDeCuenta.php');
            ob_start();
            $meses = $data->traeMeses();
            $bancos = $data->CuentasBancarias($banco, $cuenta);
            $mesactual = $data->traeMes($mes);
            $exec = $data->estado_de_cuenta_mes($mes, $banco, $cuenta, $anio);
            $total = $data->totalMensual($mes, $banco, $cuenta, $anio);
            $ventas = $data->ventasMensual($mes, $banco, $cuenta, $anio);
            $transfer = $data->transfer($mes, $banco, $cuenta, $anio);
            $devCompra = $data->devCompra($mes, $banco, $cuenta, $anio);
            $devGasto = $data->devGasto($mes, $banco, $cuenta, $anio);
            $pcchica = $data->pcc($mes, $banco, $cuenta, $anio);
            $pagosaplicados = $data->pagosAplicados($mes, $banco, $anio, $cuenta);
            $pagosacreedores = $data->pagosAcreedores($mes, $banco, $anio, $cuenta);
            $totC = $data->totalCompras($mes, $banco, $anio, $cuenta);
            $totG = $data->totalGasto($mes, $banco, $anio, $cuenta);
            $totD = $data->totalDeudores($mes, $banco, $anio, $cuenta);
            $totCr = $data->totalCredito($mes, $banco, $anio, $cuenta);


            if (count($bancos)) {
                include 'app/views/pages/p.EstadoDeCuenta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function buscaFactura() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.buscaFactura.php');
            ob_start();
            include 'app/views/pages/p.buscaFactura.php';
            $table = ob_get_clean();
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function traeFactura($docf) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.verFactura.php');
            ob_start();
            $factura = $data->traeFactura($docf);
            if (count($factura)) {
                include 'app/views/pages/p.verFactura.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO EXISTEN RESULTADOS.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function cambiarFactura($docf1, $tipo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.verFactura.php');
            ob_start();
            $exce = $data->cambiarFactura($docf1, $tipo);
            $factura = $data->traeFactura($docf1);
            if (count($factura)) {
                include 'app/views/pages/p.verFactura.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO EXISTEN RESULTADOS.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function buscarCajaEmabalar() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $pagina = $this->load_template('Menu Admin');
            $html = $this->load_page('app/views/pages/p.BusquedaCajasEmbalar.php');
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Revisar sus datos";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function porFacturarEmbalar($docp) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.pedidos.php');
            ob_start();
            $pedidos = $data->porFacturarEmbalar($docp); //// se utiliza la misma que GUstavo
            ///$facturas=$data->FacturaSinMaterial(); /// se deja la consulta actual para las que ya facturo Gustavo
            if (count($pedidos > 0)) {
                include 'app/views/pages/p.pedidos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function filtrarCompras() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.ComprasSinEdoCta.php');
            ob_start();
            $meses = $data->traeMeses();
            $mes = 1;
            $comp = $data->regCompras($mes);
            if (count($comp > 0)) {
                include 'app/views/pages/p.ComprasSinEdoCta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function comprasXmes($mes) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.ComprasSinEdoCta.php');
            ob_start();
            $meses = $data->traeMeses();
            $dato = $data->traeNombreMes($mes);
            $comp = $data->regCompras($mes);
            if (count($comp > 0)) {
                include 'app/views/pages/p.ComprasSinEdoCta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function regCompEdoCta($fecha, $docc, $mes, $pago, $banco, $tptes) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Alta Unidades');
            $html = $this->load_page('app/views/pages/p.ComprasSinEdoCta.php');
            ob_start();
            $act = $data->regCompEdoCta($fecha, $docc, $mes, $pago, $banco, $tptes);
            $meses = $data->traeMeses();
            $dato = $data->traeNombreMes($mes);
            $comp = $data->regCompras($mes);
            if (count($comp > 0)) {
                include 'app/views/pages/p.ComprasSinEdoCta.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    /* function verListadoPagosCredito(){
      session_cache_limiter('private_no_expire');
      if (isset($_SESSION['user'])) {
      $data = new pegaso;
      $pagina = $this->load_template('Pagos');
      $html = $this->load_page('app/views/pages/p.pagos.credito.php');
      ob_start();
      $exec=$data->listarPagosCredito();
      if (count($exec)){
      include 'app/views/pages/p.pagos.credito.php';
      $table = ob_get_clean();
      $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
      } else {
      $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
      }
      $this->view_page($pagina);
      } else {
      $e = "Favor de Iniciar Sesión";
      header('Location: index.php?action=login&e=' . urlencode($e));
      exit;
      }
      }

      function detallePagoCreditoContrarecibo($tipo, $identificador){
      session_cache_limiter('private_no_expire');
      if (isset($_SESSION['user'])) {
      $data = new pegaso;
      $pagina = $this->load_template('Pagos');
      $html = $this->load_page('app/views/pages/p.pago.credito.contrarecibo.php');
      ob_start();
      $exec=$data->detallePagoCredito($tipo, $identificador);
      if (count($exec)){
      include 'app/views/pages/p.pago.credito.contrarecibo.php';
      $table = ob_get_clean();
      $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
      } else {
      $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
      }
      $this->view_page($pagina);
      } else {
      $e = "Favor de Iniciar Sesión";
      header('Location: index.php?action=login&e=' . urlencode($e));
      exit;
      }
      }

      function detallePagoCreditoContrareciboImprime($tipo, $identificador){
      $dao=new pegaso;
      //$act=$data->actualizaEstatusContrareciboImpreso($tipo, $identificador);
      $exec = $dao->detallePagoCredito($tipo, $identificador);
      $_SESSION['exec'] = $exec;
      $_SESSION['titulo'] = 'Contrarecibo de credito';
      echo "<script>window.open('".$this->contexto."impresion.contrarecibo.php', '_blank');</script>";
      $this->verListadoPagosCredito();
      }
     */

    function verListadoPagosCredito() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.pagos.credito.php');
            ob_start();
            $exec = $data->listarPagosCredito();
            if (count($exec)) {
                include 'app/views/pages/p.pagos.credito.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function detallePagoCreditoContrareciboImprime($tipo, $identificador, $montor, $facturap) {
        $dao = new pegaso;
        $folio = $dao->almacenarFolioContrarecibo($tipo, $identificador, $montor, $facturap);
        $exec = $dao->detallePagoCredito($tipo, $identificador);
        $_SESSION['folio'] = $folio;
        $_SESSION['exec'] = $exec;
        $_SESSION['titulo'] = 'Contrarecibo de credito';
        echo "<script>window.open('" . $this->contexto . "reports/impresion.contrarecibo.php', '_blank');</script>";
        include 'app/mailer/send.contrarecibo.php';
        $act = $dao->actualizaPagoCreditoContrarecibo($tipo, $identificador);
        $act += $dao->actualizarFolioContrarecibo($folio);
        $act += $dao->actualizarRecepcion($identificador);
        echo "Registro actualizado:$act";
        $this->verListadoPagosCredito();
    }

    function impresionContrarecibo($tipo, $identificador) {
        $dao = new pegaso;
        $exec = $dao->detallePagoCredito($tipo, $identificador);
        $folio = $dao->obtenerFolio($identificador);
        foreach ($exec as $data):
            $pdf = new FPDF('P', 'mm', 'Letter');
            $pdf->AddPage();
            $pdf->Image('app/views/images/headerContraReciboCompra.jpg', 10, 15, 205, 55);
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->SetTextColor(14, 3, 3);
            $pdf->Ln(60);
            $pdf->Cell(10, 10, 'R E I M P R E S I O N');
            $pdf->Ln(10);
            $pdf->Cell(10, 10, 'Tipo de documento : ' . $data->TIPO);
            $pdf->Ln(10);
            $pdf->Cell(10, 10, 'Folio : CRP-' . $folio);
            $pdf->Ln(10);
            $pdf->Cell(10, 10, 'Recepcion : ' . $data->RECEPCION);
            $pdf->Ln(10);
            $pdf->Cell(10, 10, 'Orde de Compra : ' . $data->OC);
            $pdf->Ln(10);
            $pdf->Cell(10, 10, 'Factura Proveedor : ' . $data->FACTURA);
            $pdf->Ln(10);
            $pdf->Cell(10, 10, 'Beneficiario : ' . $data->BENEFICIARIO);
            $pdf->Ln(10);
            $pdf->Cell(10, 10, 'Fecha documento : ' . $data->FECHA_DOC);
            $pdf->Ln(10);
            $pdf->Cell(10, 10, 'Vencimiento : ' . $data->VENCIMIENTO);
            $pdf->Ln(10);
            $pdf->Cell(10, 10, 'Fecha Promesa de pago : ' . $data->PROMESA_PAGO);
            $pdf->Ln(10);
            $pdf->Cell(10, 10, 'Monto : $ ' . number_format($data->MONTOR, 2, '.', ','));
            $pdf->Ln(45);
            $pdf->Cell(10, 10, '________________________');
            $pdf->Ln(5);
            $pdf->Cell(10, 10, 'Firma de Recibido');
            $pdf->Output("Reimpresion de Contrarecibo No" . trim($data->ID) . ".pdf", 'i');
        endforeach;
    }

    function detallePagoCreditoContrarecibo($tipo, $identificador) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.pago.credito.contrarecibo.php');
            ob_start();
            $exec = $data->detallePagoCredito($tipo, $identificador);
            if (count($exec)) {
                include 'app/views/pages/p.pago.credito.contrarecibo.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function registrarOCAduana($identificador, $aduana, $mes, $anio) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            if ($aduana != "--") {
                $data = new pegaso;
                $exec = $data->registrarOCAduana($identificador, $aduana);
                if ($exec > 0) {
                    $mensaje = "El registro se ha guardado correctamente.";
                } else {
                    $mensaje = "Debe seleccionar la Aduana.";
                }
                echo "<script>alert('$mensaje');</script>";
                $this->verListadoOCAduana($mes, $anio);
            } else {
                $e = "Favor de Iniciar Sesión";
                header('Location: index.php?action=login&e=' . urlencode($e));
                exit;
            }
        }
    }

    function verListadoOCAduana($mes, $anio) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            //$html = $this->load_page('app/views/pages/p.oc.listado.aduana.php');
            ob_start();
            echo "mes/anio = $mes/$anio";
            $exec = $data->listarOCAduana($mes, $anio);
            if (count($exec)) {
                include 'app/views/pages/p.oc.listado.aduana.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                //$pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los pagos para imprimir</h2><center></div>', $pagina);
                echo "<script>alert('No se han localizado resultados.');</script>";
                $this->MenuTesoreria();
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verFallidas() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verFallidas.php');
            ob_start();
            $fallidas = $data->verFallidas();
            // var_dump($Recepciones);
            if (count($fallidas) > 0) {
                include 'app/views/pages/p.verFallidas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function fallarOC($doco) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verFallidas.php');
            ob_start();
            $fallidas = $data->verFallidas();
            // var_dump($Recepciones);
            if (count($fallidas) > 0) {
                include 'app/views/pages/p.verFallidas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ImpresionFallido($doco) {

        $data = new Pegaso;
        //ob_start();
        $fallar = $data->fallarOC($doco);
        $usuario = $_SESSION['user']->USER_LOGIN;
        $fecha = date("Y-m-d H:i:s");
        $fallida = $data->impFallido($doco);
        $partidas = $data->impFallidoPar($doco);

        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 10);
        //$pdf->Image('app/views/images/factura3.png',10,15,205,55);
        $pdf->Image('app/views/images/headerOrdenFallida.jpg', 10, 15, 205, 55, 'JPG');


        $pdf->Ln(70);

        foreach ($fallida as $data) {
            $importe = number_format($data->IMPORTE, 2);
            $pdf->SetX(10);
            $pdf->Write(6, "Orden de compra : " . $data->CVE_DOC);
            $pdf->Ln();
            $pdf->Cell(40, 6, "Fecha y hora de fallo : " . $fecha);
            $pdf->Ln();
            $pdf->Cell(40, 6, "Nombre del usuario : " . $data->USUARIO_RECIBE);
            $pdf->Ln();
            $pdf->Cell(100, 6, "Unidad : " . $data->UNIDAD);
            $pdf->Ln();
            $pdf->Cell(100, 6, "Folio : " . $data->DOC_SIG);
            $pdf->Ln();
            $pdf->Cell(100, 6, "Proveedor: " . $data->NOMBRE);
            $pdf->Ln();
            $pdf->Cell(100, 6, "Monto: $ " . $importe);
            $pdf->Ln();
        }

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Ln(5);
        $pdf->Cell(20, 6, "ORDEN", 1);
        $pdf->Cell(25, 6, "CLAVE", 1);
        $pdf->Cell(70, 6, "DESCRIPCION", 1);
        $pdf->Cell(10, 6, "ID", 1);
        $pdf->Cell(10, 6, "PAR", 1);
        $pdf->Cell(20, 6, "CANTIDAD", 1);
        $pdf->Cell(20, 6, "COSTO", 1);
        $pdf->Ln();
        foreach ($partidas as $data) {


            $m = $data->COST;
            $Monto = number_format($m, 3);

            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell(20, 6, $data->CVE_DOC, 1);
            $pdf->Cell(25, 6, $data->CVE_ART, 1);
            $pdf->Cell(70, 6, $data->DESCR, 1);
            $pdf->Cell(10, 6, $data->ID_PREOC, 1);
            $pdf->Cell(10, 6, $data->NUM_PAR, 1);
            $pdf->Cell(20, 6, $data->CANT, 1);
            $pdf->Cell(20, 6, '$ ' . $Monto, 1);
            $pdf->Ln();
        }
        //ob_get_clean();
        $pdf->Output('ORDENFALLIDA.pdf', 'i');
    }

    function FacturaPago($cveclie) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verSaldoFacturas.php');
            ob_start();
            $facturas = $data->verSaldoFacturas($cveclie);
            // var_dump($Recepciones);
            if (count($facturas) > 0) {
                include 'app/views/pages/p.verSaldoFacturas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function PagoxFactura($docf, $clie, $rfc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verSaldoPagos.php');
            ob_start();
            $factura = $data->treaSaldoFacturas($docf, $clie, $rfc);
            $pagos = $data->verPagos2($docf, $clie, $rfc);
            // var_dump($Recepciones);
            if (count($pagos) > 0) {
                include 'app/views/pages/p.verSaldoPagos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function aplicarPagoxFactura($docf, $idpago, $monto, $saldof, $clie, $rfc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verSaldoPagos.php');
            ob_start();
            $aplica = $data->aplicarPagoxFactura($docf, $idpago, $monto, $saldof, $clie, $rfc);
            //echo 'este es el valor de data en el controller'.$aplica;
            if ($aplica <= 0) {
                $cveclie = $clie;
                $this->FacturaPago($cveclie);
            }
            $factura = $data->treaSaldoFacturas($docf, $clie, $rfc);
            $pagos = $data->verPagos2($docf, $clie, $rfc);
            // var_dump($Recepciones);
            if (count($pagos) > 0) {
                include 'app/views/pages/p.verSaldoPagos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function PagoFactura($clie) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verSaldoPagos2.php');
            ob_start();
            $docf = 0;
            $clie = $clie;
            $pagos = $data->verPagos2($clie, $docf);
            // var_dump($Recepciones);
            if (count($pagos) > 0) {
                include 'app/views/pages/p.verSaldoPagos2.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function aplicaPago($clie, $id) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verSaldoFacturas2.php');
            ob_start();
            $facturas = $data->verFacturas2($clie, $id);
            $verPago = $data->verPagoaAplicar($clie, $id);
            if (count($facturas) > 0) {
                include 'app/views/pages/p.verSaldoFacturas2.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function aplicaPagoFactura($clie, $id, $docf, $monto, $saldof, $rfc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verSaldoFacturas2.php');
            ob_start();
            $aplicar = $data->aplicaPagoFactura($clie, $id, $docf, $monto, $saldof, $rfc);

            if ($aplicar == 0) {
                //echo 'Se ha aplicado el monto total a la Facteste es el valor del aplicar'.$aplicar;
                $this->PagoFactura($clie, $id);
            }
            $facturas = $data->verFacturas2($clie, $id);
            $verPago = $data->verPagoaAplicar($clie, $id);
            if (count($facturas) > 0) {
                include 'app/views/pages/p.verSaldoFacturas2.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>Hubo un error al mostrar los datos</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function form_capruracrdirecto() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.form_crdirecto.php');
            ob_start();
            $banco = $data->CuentasBancos();
            $prov = $data->traeProv();
            $gastos = $data->traeGasto();
            include 'app/views/pages/p.form_crdirecto.php';
            $table = ob_get_clean();
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);

            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function guardaCompra($fact, $prov, $monto, $ref, $tipopago, $fechadoc, $fechaedocta, $banco, $tipo, $idg) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.form_crdirecto.php');
            $guarda = $data->guardaCompra($fact, $prov, $monto, $ref, $tipopago, $fechadoc, $fechaedocta, $banco, $tipo, $idg);
            $this->form_capruracrdirecto();
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verAplicaciones() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verAplicaciones.php');
            ob_start();
            $aplicaciones = $data->verAplicaciones();
            if (count($aplicaciones) > 0) {
                include 'app/views/pages/p.verAplicaciones.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON APLICACIONES PENDIENTE DE IMPRESION</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function impAplicacion($ida) {
        $data = new Pegaso;
        $aplicacion = $data->impAplicacion($ida);
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 10);
        //$pdf->Image('app/views/images/factura3.png',10,15,205,55);
        $pdf->Image('app/views/images/headerAplicacionPago.jpg', 10, 15, 205, 55, 'JPG');
        $pdf->Ln(70);

        foreach ($aplicacion as $data) {

            $saldof = $data->SALDO_DOC + $data->MONTO_APLICADO;

            $pdf->SetX(10);
            $pdf->Write(6, "Folio de Aplicacion : " . $data->ID);
            $pdf->Ln();
            $pdf->Cell(40, 6, "Fecha de Aplicacion : " . $data->FECHA);
            $pdf->Ln();
            $pdf->Cell(40, 6, "Ciente : " . $data->CLIENTE);
            $pdf->Ln();
            $pdf->Cell(100, 6, "Documento : " . $data->DOCUMENTO);
            $pdf->Ln();
            $pdf->Cell(100, 6, "Importe Total del Documento: $ " . number_format($data->IMPORTE, 2));
            $pdf->Ln();
            $pdf->Cell(100, 6, "Saldo Inicial Documento : $" . number_format($saldof, 2));
            $pdf->Ln();
            $pdf->Cell(100, 6, "Monto de Aplicacion: $ " . number_format($data->MONTO_APLICADO, 2));
            $pdf->Ln();
            $pdf->Cell(100, 6, "Saldo Final de Documento: $ " . number_format($data->SALDO_DOC, 2));
            $pdf->Ln();
            $pdf->Cell(100, 6, "Usuario que aplica: " . $data->USUARIO);
            $pdf->Ln();
            $pdf->Cell(100, 6, "");
            $pdf->Ln();
        }
        //ob_get_clean();
        $pdf->Output('Aplicacion.pdf', 'i');
    }

    function verPagosActivos($monto) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verPagosActivos.php');
            ob_start();
            $pagos = $data->verPagosActivos($monto);
            if (count($pagos) > 0) {
                include 'app/views/pages/p.verPagosActivos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON PAGOS POR APLICAR</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function buscaPagosActivos() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.buscaPagosActivos.php');
            ob_start();
            include 'app/views/pages/p.buscaPagosActivos.php';
            $table = ob_get_clean();
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function aplicaPagoDirecto($idp, $tipo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verPagoActivo.php');
            ob_start();
            $pagos = $data->verPagoActivo($idp, $tipo);
            //echo 'esto es el tipo: '.$tipo; Tipo es Nada.... o creo que es cuando es NC.
            $xaplicar = $data->facturasxaplicar($idp);
            $facturas = $data->listaFacturas();
            if (count($pagos) > 0) {
                include 'app/views/pages/p.verPagoActivo.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON PAGOS POR APLICAR</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function PagoDirecto($idp, $docf, $rfc, $monto, $saldof, $clie, $tipo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verPagoActivo.php');
            ob_start();
            if (empty($tipo)) {
                $tipo = 0;
            }
            $aplica = $data->aplicaPagoFactura($clie, $idp, $docf, $monto, $saldof, $rfc, $tipo);
            if ($tipo = 1 and $aplica == 0) {
                $maestro = $data->obtieneMaestro($docf);
                $redireccionar = "facturapagomaestro&maestro={$maestro}";
            } else {
                $redireccionar = "aplicaPagoDirecto&idp={$idp}&tipo={$tipo}";
            }
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function IdvsComp() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.IdvsComp.php');
            ob_start();
            $ids = $data->IdvsComp();
            if (count($ids)) {
                include 'app/views/pages/p.IdvsComp.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON PAGOS POR APLICAR</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function traeFacturaPago($idp, $monto, $docf, $tipo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verPagoActivoyFacturas.php');
            ob_start();
            $pagos = $data->verPagoActivo($idp, $tipo);
            $facturas = $data->listaFacturasOK($docf);
            $xaplicar = $data->facturasxaplicar($idp);
            if (count($pagos) > 0) {
                include 'app/views/pages/p.verPagoActivoyFacturas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON PAGOS POR APLICAR</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function buscaValidacionOC() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.buscaValidacionOC.php');
            ob_start();
            $validacion = False;
            include 'app/views/pages/p.buscaValidacionOC.php';
            $table = ob_get_clean();
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function traeValidacion($doco) {
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.buscaValidacionOC.php');
            ob_start();
            $validacion = $data->traeValidacion($doco);
            $doco = $doco;
            if (count($validacion) > 0) {
                include 'app/views/pages/p.buscaValidacionOC.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRO EL </h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ImprimeValidacionOC($orden) {
        $data = new Pegaso;
        $parRecep = $data->PartidasNoRecep("0", $orden);

        $pdf = new FPDF('P', 'mm', 'Letter');

        $pdf->AddPage();

        $pdf->Image('app/views/images/headerRECEPpdf.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(10, 6, "ID", 1);
        $pdf->Cell(15, 6, "Recep", 1);
        $pdf->Cell(5, 6, "Par", 1);
        $pdf->Cell(55, 6, "Descripcion", 1);
        $pdf->Cell(10, 6, "Unidad", 1);
        $pdf->Cell(10, 6, "Orde", 1);
        $pdf->Cell(10, 6, "Valida", 1);
        $pdf->Cell(15, 6, "Monto", 1);
        $pdf->Cell(10, 6, "Saldo", 1);
        $pdf->Cell(10, 6, "PXR", 1);

        $pdf->Cell(15, 6, "SubTot", 1);
        $pdf->Cell(15, 6, "IVA", 1);
        $pdf->Cell(15, 6, "Total", 1);


        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 7);

        $total_oc = 0;
        $total_subtotal = 0;
        $total_iva = 0;
        $total_final = 0;
        foreach ($parRecep as $row) {

            $total_subtotal += ($row->COST_REC * $row->CANT_REC);
            $total_iva += ($row->COST_REC * $row->CANT_REC) * 0.16;
            $total_final += ($row->COST_REC * $row->CANT_REC) * 1.16;
            $total_oc += $row->TOT_PARTIDA;

            $pdf->Cell(10, 6, $row->ID_PREOC, 'L,T,R');
            $pdf->Cell(15, 6, trim($row->CVE_DOC), 'L,T,R');
            $pdf->Cell(5, 6, $row->NUM_PAR, 'L,T,R');
            $pdf->Cell(55, 6, substr($row->DESCR, 0, 34), 'L,T,R');
            $pdf->Cell(10, 6, $row->UNI_ALT, 'L,T,R');
            $pdf->Cell(10, 6, $row->CANT, 'L,T,R');
            $pdf->Cell(10, 6, $row->CANT_REC, 'L,T,R');
            $pdf->Cell(15, 6, round($row->TOT_PARTIDA, 2), 'L,T,R');
            $pdf->Cell(10, 6, round($row->SALDO, 2), 'L,T,R');
            $pdf->Cell(10, 6, $row->PXR, 'L,T,R');
            $pdf->Cell(15, 6, round(($row->COST_REC * $row->CANT_REC), 2), 'L,T,R'); ///  Subtotal
            $pdf->Cell(15, 6, round((($row->COST_REC * $row->CANT_REC) * 0.16), 2), 'L,T,R'); /// Costo antes de IVA
            $pdf->Cell(15, 6, round((($row->COST_REC * $row->CANT_REC) * 1.16), 2), 'L,T,R'); /// Costo Total con IVA s
            $pdf->Ln();        // Segunda linea descripcion
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(5, 6, "", 'L,B,R');
            $pdf->Cell(55, 6, substr($row->DESCR, 34, 70), 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');

            $pdf->Ln();
        }

        if (round($total_oc, 2) == round($total_subtotal, 2) * 1.16)
            $mensaje = "SALDADO";
        elseif (round($total_oc, 2) > round($total_subtotal, 2))
            $mensaje = "DEUDOR";
        else
            $mensaje = "ACREDOR";

        $pdf->SetFont('Arial', 'B', 44);
        $pdf->Ln(8);
        $pdf->SetX(30);
        $pdf->Write(6, $mensaje);

        $pdf->SetFont('Arial', 'B', 12);

        $pdf->Ln(60);
        $pdf->SetX(140);
        $pdf->Write(6, "Subtotal       $ " . number_format($total_subtotal, 4, '.', ','));
        $pdf->Ln();
        $pdf->SetX(140);
        $pdf->Write(6, "I.V.A.         $ " . number_format($total_iva, 4, '.', ','));
        $pdf->Ln();
        $pdf->SetX(140);
        $pdf->Write(6, "Total          $ " . number_format($total_final, 2, '.', ','));
        $pdf->Ln();

        $pdf->Output('Secuencia entrega unidad .pdf', 'i');
    }

    function verAplivsFact() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verAplivsFact.php');
            ob_start();
            $aplicaciones = $data->verAplivsFact();
            if (count($aplicaciones) > 0) {
                include 'app/views/pages/p.verAplivsFact.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON PAGOS POR APLICAR</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function imprimirComprobante($idp) {
        $data = new Pegaso;
        $generales = $data->infoPago($idp);
        $movimientos = $data->movimientosPago($idp);
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();

        $pdf->Image('app/views/images/headerAplicacionPago.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);
        $pdf->SetFont('Arial', 'B', 8);
        foreach ($generales as $data) {

            $pdf->Write(6, 'ID:' . $data->ID);
            $pdf->Ln();
            $pdf->Write(6, 'Fecha Estado de Cuenta: ' . $data->FECHA_RECEP);
            $pdf->Ln();
            $pdf->Write(6, 'Banco: ' . $data->BANCO);
            $pdf->Ln();
            $pdf->Write(6, 'Monto: $' . number_format($data->MONTO, 2));
            $pdf->Ln();
            $pdf->Write(6, 'Saldo Actual: $' . number_format($data->SALDO, 2));
            $pdf->Ln();
            $pdf->Write(6, 'Usuario Registra: ' . $data->USUARIO);
            $pdf->Ln();
            $pdf->Write(6, 'Fecha y hora de Registro: ' . $data->FECHA);
        }
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(20, 6, "FACTURA", 1);
        $pdf->Cell(20, 6, "IMPORTE", 1);
        $pdf->Cell(30, 6, "SALDO DOCUMENTO", 1);
        $pdf->Cell(30, 6, "MONTO APLICADO", 1);
        $pdf->Cell(30, 6, "NUEVO SALDO DOC", 1);
        $pdf->Cell(30, 6, "USUARIO", 1);
        $pdf->Cell(20, 6, "MOVIMIENTO", 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'I', 7);
        $sumar = 0;

        foreach ($movimientos as $row) {

            $saldo = $row->SALDO_DOC + $row->MONTO_APLICADO;
            $suma = $row->MONTO_APLICADO;
            $sumar = $sumar + $suma;

            $pdf->Cell(20, 6, $row->DOCUMENTO, 1);
            $pdf->Cell(20, 6, '$ ' . number_format($row->IMPORTE, 2), 1, 0, 'R');
            $pdf->Cell(30, 6, '$ ' . number_format($saldo, 2), 1, 0, 'C');
            $pdf->Cell(30, 6, '$ ' . number_format($row->MONTO_APLICADO, 2), 1, 0, 'R');
            $pdf->Cell(30, 6, '$ ' . number_format($row->SALDO_DOC, 2), 1, 0, 'R');
            $pdf->Cell(30, 6, $row->USUARIO, 1);
            $pdf->Cell(20, 6, $row->ID, 1);
            $pdf->Ln();
        }

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Ln();
        $pdf->Write(6, 'Suma de Movimientos: $' . number_format($sumar, 2));

        $pdf->Output('Secuencia entrega unidad .pdf', 'i');
    }

    function listarOCContrarecibos() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            ob_start();
            $exec = $data->listarOCContrarecibos();
            if (count($exec)) {
                include 'app/views/pages/p.oc.listado.contrarecibos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                echo "<script>alert('No se han localizado resultados.');</script>";
                $this->MenuTesoreria();
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function pagarOCContrarecibos($cantidad, $folios, $monto) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            ob_start();
            $exec = $data->pagarOCContrarecibos($folios);
            $cuentaBancarias = $data->CuentasBancos();
            if (count($exec)) {
                include 'app/views/pages/p.oc.pago.contrarecibos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                echo "<script>alert('No se han localizado resultados.');</script>";
                $this->MenuTesoreria();
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function pagarOCContrarecibosAplicar($folios, $cuentaBancaria, $medio, $importe) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $dao = new pegaso;
            $misFolios = explode(",", $folios);
            $creaSP = $dao->NewSolicitudPago($cuentaBancaria, $medio, $importe, $misFolios);
            if ($creaSP > 2) {
                foreach ($misFolios as $folio):
                    $asignafolio = $dao->asignaFolioDocumento($folio, $creaSP);
                endforeach;
                $this->listarOCContrarecibos();
            }else {
                echo 'No se puede crear 1 misma solicitud de pago para varios proveedores, favor de seleccionar Recepciones de 1 solo Proveedor...';
                $this->listarOCContrarecibos();
            }
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function IngresoBodega() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            ob_start();
            include 'app/views/pages/p.IngresoBodega.php';
            $table = ob_get_clean();
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function IngresarBodega($desc, $cant, $marca, $proveedor, $costo, $unidad) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            ob_start();
            $ingresar = $data->IngresarBodega($desc, $cant, $marca, $proveedor, $costo, $unidad);
            if ($ingresar = True) {
                include 'app/views/pages/p.IngresoBodega.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                echo "<script>alert('NO se pudo Ingresar el producto a la Bodega, Favor de revisar que la descipcion no incluya comillas simples como  y como');</script>";
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verIngresoBodega() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            ob_start();
            $ingresos = $data->verIngresoBodega();
            if (count($ingresos) > 0) {
                include 'app/views/pages/p.verIngresoBodega.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                echo "<script>alert('NO se pudo Ingresar el producto a la Bodega, Favor de revisar que la descipcion no incluya comillas simples como  y como');</script>";
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function regCargosFinancieros() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            ob_start();
            $cuentaBancarias = $data->CuentasBancos();
            $cf = $data->asociaCF();
            include 'app/views/pages/p.regCargoFinanciero.php';
            $table = ob_get_clean();
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function guardaCargoFinanciero($monto, $fecha, $banco) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            ob_start();
            $registro = $data->guardaCargoFinanciero($monto, $fecha, $banco);
            $redireccionar = "regCargosFinancieros";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function impRecCobranza() {
        ob_start();
        $data = new Pegaso;
        $actualiza = $data->impRecCobranza();
        $datos = $data->recepcionCobranza($actualiza);
        $usuario = $_SESSION['user']->USER_LOGIN;
        $fecha = date("Y-m-d H:i:s");
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerCierreRevCob.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Write(6, 'Recepcion de documentos de Revision a Cobranza');
        $pdf->Ln();
        $pdf->Write(6, 'Fecha de Recepcion: ' . $fecha);
        $pdf->Ln();
        $pdf->Write(6, 'Usuario:' . $usuario);
        $pdf->Ln();
        $pdf->Write(6, 'Folio Recepcion Cobranza: ' . $actualiza);
        $pdf->Ln();
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(18, 6, "FACTURA", 1);
        $pdf->Cell(40, 6, "CLIENTE", 1);
        $pdf->Cell(30, 6, "FECHA", 1);
        $pdf->Cell(18, 6, "IMPORTE", 1);
        $pdf->Cell(25, 6, "USUARIO REVISION", 1);
        $pdf->Cell(28, 6, "FECHA REVISION", 1);
        $pdf->Cell(28, 6, "USUARIO COBRANZA", 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'I', 7);

        foreach ($datos as $row) {
            $pdf->Cell(18, 6, $row->FACTURA, 'L,T,R');
            $pdf->Cell(40, 6, substr($row->NOMBRE, 0, 23), 'L,T,R');
            $pdf->Cell(30, 6, $row->FECHAELAB, 'L,T,R');
            $pdf->Cell(18, 6, '$ ' . number_format($row->IMPORTE, 2), 'L,T,R', 0, 'R');
            $pdf->Cell(25, 6, $row->USUARIO_REV, 'L,T,R');
            $pdf->Cell(28, 6, $row->FECHA_REV, 'L,T,R');
            $pdf->Cell(28, 6, $row->USUARIO_REC_COBRANZA, 'L,T,R');
            $pdf->Ln();
            $pdf->Cell(18, 6, "", 'L,B,R');
            $pdf->Cell(40, 6, substr($row->NOMBRE, 23, 50), 'L,B,R');
            $pdf->Cell(30, 6, "", 'L,B,R');
            $pdf->Cell(18, 6, "", 'L,B,R');
            $pdf->Cell(25, 6, "", 'L,B,R');
            $pdf->Cell(28, 6, "", 'L,B,R');
            $pdf->Cell(28, 6, "", 'L,B,R');
            $pdf->Ln();
        }
        $pdf->Ln(12);
        $pdf->Write(6, '_____________________________________________                 _____________________________________________');
        $pdf->Ln();
        $pdf->Write(6, 'Nombre y Firma de quien Recibe los Documentos                                    Nombre y Firma de quien Entrega los Documentos');
        $pdf->Ln();
        $pdf->Write(6, '        C O B R AN Z A                                                                                              R E V I S I O N');
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Ln();
        ob_get_clean();
        $pdf->Output('Recibo_Cobranza_Folio' . $actualiza . '.pdf', 'i');
    }

    function imprimeCierreEnt() {
        ob_start();
        $data = new Pegaso;
        $actualiza = $data->imprimeCierreEnt();
        $datos = $data->cierre_uni_ent($actualiza);
        $usuario = $_SESSION['user']->USER_LOGIN;
        $fecha = date("Y-m-d H:i:s");
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerCierreRuta.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Write(6, 'Recepcion de documentos de Logistica a Aduana');
        $pdf->Ln();
        $pdf->Write(6, 'Fecha de Recepcion: ' . $fecha);
        $pdf->Ln();
        $pdf->Write(6, 'Usuario:' . $usuario);
        $pdf->Ln();
        $pdf->Write(6, 'Folio Cierre Logistica: ' . $actualiza);
        $pdf->Ln();
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Write(6, 'CIERRE CON ESTATUS ENTREGADO');
        $pdf->Ln();
        +
                $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(18, 6, "FACTURA", 1);
        $pdf->Cell(40, 6, "CLIENTE", 1);
        $pdf->Cell(30, 6, "FECHA", 1);
        $pdf->Cell(18, 6, "IMPORTE", 1);
        $pdf->Cell(25, 6, "USUARIO ADUANA", 1);
        $pdf->Cell(28, 6, "USUARIO LOGISTICA", 1);
        $pdf->Cell(28, 6, "USUARIO COBRANZA", 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'I', 7);
        foreach ($datos as $row) {
            if ($row->STATUS_LOG == 'Entregado' or $row->STATUS_LOG == 'Recibido') {
                $pdf->Cell(18, 6, trim($row->DOCUMENTO), 'L,T,R');
                $pdf->Cell(40, 6, substr($row->NOMBRE, 0, 23), 'L,T,R');
                $pdf->Cell(30, 6, $row->FECHAELAB, 'L,T,R');
                $pdf->Cell(18, 6, '$ ' . number_format($row->IMPORTE, 2), 'L,T,R', 0, 'R');
                $pdf->Cell(25, 6, $row->USUARIO_REV, 'L,T,R');
                $pdf->Cell(28, 6, $row->FECHA_REV, 'L,T,R');
                $pdf->Cell(28, 6, $row->USUARIO_REC_COBRANZA, 'L,T,R');
                $pdf->Ln();
                $pdf->Cell(18, 6, $row->ID, 'L,B,R');
                $pdf->Cell(40, 6, substr($row->NOMBRE, 23, 50), 'L,B,R');
                $pdf->Cell(30, 6, "", 'L,B,R');
                $pdf->Cell(18, 6, "", 'L,B,R');
                $pdf->Cell(25, 6, "", 'L,B,R');
                $pdf->Cell(28, 6, "", 'L,B,R');
                $pdf->Cell(28, 6, "", 'L,B,R');
                $pdf->Ln();
            }
        }
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Write(6, 'CIERRE CON ESTATUS RE-ENVIAR');
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(18, 6, "FACTURA", 1);
        $pdf->Cell(40, 6, "CLIENTE", 1);
        $pdf->Cell(30, 6, "FECHA", 1);
        $pdf->Cell(18, 6, "IMPORTE", 1);
        $pdf->Cell(25, 6, "USUARIO ADUANA", 1);
        $pdf->Cell(28, 6, "USUARIO LOGISTICA", 1);
        $pdf->Cell(28, 6, "USUARIO COBRANZA", 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'I', 7);

        foreach ($datos as $row) {
            if ($row->STATUS_LOG == 'Reenviar') {
                $pdf->Cell(18, 6, trim($row->DOCUMENTO), 'L,T,R');
                $pdf->Cell(40, 6, substr($row->NOMBRE, 0, 23), 'L,T,R');
                $pdf->Cell(30, 6, $row->FECHAELAB, 'L,T,R');
                $pdf->Cell(18, 6, '$ ' . number_format($row->IMPORTE, 2), 'L,T,R', 0, 'R');
                $pdf->Cell(25, 6, $row->USUARIO_REV, 'L,T,R');
                $pdf->Cell(28, 6, $row->FECHA_REV, 'L,T,R');
                $pdf->Cell(28, 6, $row->USUARIO_REC_COBRANZA, 'L,T,R');
                $pdf->Ln();
                $pdf->Cell(18, 6, $row->ID, 'L,B,R');
                $pdf->Cell(40, 6, substr($row->NOMBRE, 23, 50), 'L,B,R');
                $pdf->Cell(30, 6, "", 'L,B,R');
                $pdf->Cell(18, 6, "", 'L,B,R');
                $pdf->Cell(25, 6, "", 'L,B,R');
                $pdf->Cell(28, 6, "", 'L,B,R');
                $pdf->Cell(28, 6, "", 'L,B,R');
                $pdf->Ln();
            }
        }
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Write(6, 'CIERRE CON ESTATUS NC');
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(18, 6, "FACTURA", 1);
        $pdf->Cell(40, 6, "CLIENTE", 1);
        $pdf->Cell(30, 6, "FECHA", 1);
        $pdf->Cell(18, 6, "IMPORTE", 1);
        $pdf->Cell(25, 6, "USUARIO ADUANA", 1);
        $pdf->Cell(28, 6, "USUARIO LOGISTICA", 1);
        $pdf->Cell(28, 6, "USUARIO COBRANZA", 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'I', 7);
        foreach ($datos as $row) {
            if ($row->STATUS_LOG == 'NC') {
                $pdf->Cell(18, 6, trim($row->DOCUMENTO), 'L,T,R');
                $pdf->Cell(40, 6, substr($row->NOMBRE, 0, 23), 'L,T,R');
                $pdf->Cell(30, 6, $row->FECHAELAB, 'L,T,R');
                $pdf->Cell(18, 6, '$ ' . number_format($row->IMPORTE, 2), 'L,T,R', 0, 'R');
                $pdf->Cell(25, 6, $row->USUARIO_REV, 'L,T,R');
                $pdf->Cell(28, 6, $row->FECHA_REV, 'L,T,R');
                $pdf->Cell(28, 6, $row->USUARIO_REC_COBRANZA, 'L,T,R');
                $pdf->Ln();
                $pdf->Cell(18, 6, $row->ID, 'L,B,R');
                $pdf->Cell(40, 6, substr($row->NOMBRE, 23, 50), 'L,B,R');
                $pdf->Cell(30, 6, "", 'L,B,R');
                $pdf->Cell(18, 6, "", 'L,B,R');
                $pdf->Cell(25, 6, "", 'L,B,R');
                $pdf->Cell(28, 6, "", 'L,B,R');
                $pdf->Cell(28, 6, "", 'L,B,R');
                $pdf->Ln();
            }
        }
        $pdf->Ln(12);
        $pdf->Write(6, '_____________________________________________                 _____________________________________________');
        $pdf->Ln();
        $pdf->Write(6, 'Nombre y Firma de quien Recibe los Documentos                                    Nombre y Firma de quien Entrega los Documentos');
        $pdf->Ln();
        $pdf->Write(6, '        A D U A N A                                                                                              L O G I S T I C A');
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Ln();
        ob_get_clean();
        $pdf->Output('Recibo_Cierre_Ruta' . $actualiza . '.pdf', 'i');
    }

    function verCierreVal() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verCierreVal.php');
            ob_start();
            $validaciones = $data->verCierreVal();
            if (count($validaciones) > 0) {
                include 'app/views/pages/p.verCierreVal.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON PAGOS POR APLICAR</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function guardaFacturaProv($docr, $factura) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            ob_start();
            $guardar = $data->guardaFacturaProv($docr, $factura);
            $this->verCierreVal();
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function impCierreVal() {
        ob_start();
        $data = new Pegaso;
        $datos = $data->impCierreVal();

        foreach ($datos as $key) {
            $folio = $key->FOLIO_IMP_CIERRE_VAL;
        }
        $usuario = $_SESSION['user']->USER_LOGIN;
        $fecha = date("Y-m-d H:i:s");
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerCierreRuta.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Write(6, 'Cierre de Recepciones a Contabilidad');
        $pdf->Ln();
        $pdf->Write(6, 'Fecha de Recepcion: ' . $fecha);
        $pdf->Ln();
        $pdf->Write(6, 'Usuario:' . $usuario);
        $pdf->Ln();
        $pdf->Write(6, 'Folio Cierre Recepcion: ' . $folio);
        $pdf->Ln();
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Write(6, 'CIERRE DE RECEPCIONES');
        $pdf->Ln();
        +
                $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(18, 6, "RECEPCION", 1);
        $pdf->Cell(38, 6, "PROVEEDOR", 1);
        $pdf->Cell(14, 6, "FECHA", 1);
        $pdf->Cell(15, 6, "IMPORTE", 1);
        $pdf->Cell(12, 6, "O.C.", 1);
        $pdf->Cell(14, 6, "FECHA", 1);
        $pdf->Cell(15, 6, "IMPORTE", 1);
        $pdf->Cell(15, 6, "FACTURA", 1);
        $pdf->Cell(15, 6, "STATUS", 1);
        $pdf->Cell(15, 6, "USUARIO", 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'I', 6);
        foreach ($datos as $row) {

            $pdf->Cell(18, 6, trim($row->CVE_DOC), 'L,T,R');
            $pdf->Cell(38, 6, substr($row->NOMBRE, 0, 23), 'L,T,R');
            $pdf->Cell(14, 6, substr($row->FECHAELAB, 0, 10), 'L,T,R');
            $pdf->Cell(15, 6, '$ ' . number_format($row->IMPORTE, 2), 'L,T,R', 0, 'R');
            $pdf->Cell(12, 6, $row->OC, 'L,T,R');
            $pdf->Cell(14, 6, substr($row->OC_FECHAELAB, 0, 10), 'L,T,R');
            $pdf->Cell(15, 6, '$ ' . number_format($row->OC_IMPORTE, 2), 'L,T,R');
            $pdf->Cell(15, 6, $row->FACTURA_PROV, 'L', 'T', 'R');
            $pdf->Cell(15, 6, $row->OC_STATUS_VAL, 'L', 'T', 'R', 0, 'C');
            $pdf->Cell(15, 6, (empty($row->OC_USUARIO_VAL) ? "NO Resgitrado" : '$row->OC_USUARIO_VAL'), 'L,T,R');
            $pdf->Ln(3);
            $pdf->Cell(18, 6, "", 'L,B,R');
            $pdf->Cell(38, 6, substr($row->NOMBRE, 23, 50), 'L,B,R');
            $pdf->Cell(14, 6, substr($row->FECHAELAB, 10, 20), 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(12, 6, "", 'L,B,R');
            $pdf->Cell(14, 6, substr($row->OC_FECHAELAB, 10, 20), 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Ln();
        }
        $pdf->Ln(12);
        $pdf->Write(6, '_____________________________________________                 _____________________________________________');
        $pdf->Ln();
        $pdf->Write(6, 'Nombre y Firma de quien Recibe los Documentos                                    Nombre y Firma de quien Entrega los Documentos');
        $pdf->Ln();
        $pdf->Write(6, '       R E C E P C I O N                                                                                              C O N T A B I L I D A D');
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Ln();
        ob_clean();
        $pdf->Output('Recibo_Cierre_Validacion.pdf', 'i');
    }

    function asociaCF() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verCargosFinancieros.php');
            ob_start();
            $cf = $data->asociaCF();
            if (count($cf) > 0) {
                include 'app/views/pages/p.verCargosFinancieros.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON CARGOS FINANCIEROS PENDIENTES DE APLICAR</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function asociarCF($idcf, $rfc, $banco, $cuenta) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.asociaCF.php');
            ob_start();
            $cf = $data->traeCF($idcf);
            $pagos = False;
            if (count($cf) > 0) {
                include 'app/views/pages/p.asociaCF.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON CARGOS FINANCIEROS PENDIENTES DE APLICAR</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function traePagos($idcf, $monto) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.asociaCF.php');
            ob_start();
            $cf = $data->traeCF($idcf);
            $pagos = $data->traePagos($monto);
            if (count($cf) > 0) {
                include 'app/views/pages/p.asociaCF.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON CARGOS FINANCIEROS PENDIENTES DE APLICAR</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function cargaCF($idcf, $idp, $monto) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.asociaCF.php');
            ob_start();
            $aplicacion = $data->cargaCF($idcf, $idp, $monto);
            if ($aplicacion == 1) {
                $this->regCargosFinancieros();
            } else {
                $cf = $data->traeCF($idcf);
                $pagos = $data->traePagos($monto);
                if (count($cf) > 0) {
                    include 'app/views/pages/p.asociaCF.php';
                    $table = ob_get_clean();
                    $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
                } else {
                    $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON CARGOS FINANCIEROS PENDIENTES DE APLICAR</h2><center></div>', $pagina);
                }
                $this->view_page($pagina);
            }
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verPagosConSaldo() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verPagosConSaldo.php');
            ob_start();
            $pagos = $data->verPagosConSaldo();
            $clientes = $data->traeClientes();
            if (count($pagos) > 0) {
                include 'app/views/pages/p.verPagosConSaldo.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON CARGOS FINANCIEROS PENDIENTES DE APLICAR</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function enviaAcreedor($idp, $saldo, $rfc) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verPagosConSaldo.php');
            ob_start();
            $redireccionar = 'verPagosConSaldo';
            $aplicacion = $data->enviaAcreedor($idp, $saldo, $rfc);
            if ($aplicacion == 0) {
                $pagina = $this->load_template('Pedidos');
                $html = $this->load_page('app/views/pages/p.redirectform.php');
                include 'app/views/pages/p.redirectform.php';
                $this->view_page($pagina);
            } else {
                $this->verPagosConSaldo();
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verAcreedores() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verAcreedores.php');
            ob_start();
            $acreedores = $data->verAcreedores();
            if (count($acreedores) > 0) {
                include 'app/views/pages/p.verAcreedores.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON CARGOS FINANCIEROS PENDIENTES DE APLICAR</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function contabilizarAcreedor($ida) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verAcreedores.php');
            ob_start();
            $contabilizar = $data->contabilizarAcreedor($ida);
            $redireccionar = 'verAcreedores';
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function cancelaAplicacion($idp, $docf, $idap, $montoap) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verCierreVal.php');
            ob_start();
            $tipo = 0;
            $cancela = $data->cancelaAplicacion($idp, $docf, $idap, $montoap);
            $redireccionar = "aplicaPagoDirecto&idp={$idp}&tipo={$tipo}";
            //$redireccionar = "RevSinDosP&cr={$cr}";
            //echo $redireccionar;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            //$exec = $data->CajaCobranza($caja, $revdp, $numcr);
            //var_dump($exec);
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function procesarPago($idp, $tipo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.buscaPagosActivos.php');
            ob_start();
            $procesar = $data->procesarPago($idp, $tipo);
            if ($procesar) {
                include 'app/views/pages/p.buscaPagosActivos.php';
                $table = ob_get_clean();
                if ($tipo == 'DC') {
                    $desc = 'DEVOLUCION DE COMPRA.';
                } elseif ($tipo == 'DG') {
                    $desc = 'DEVOLUCION DE GASTO.';
                } elseif ($tipo == 'oTEC') {
                    $desc = 'TRANSFERENCIA ENTRE CUENTAS PROPIAS.';
                } elseif ($tipo == 'oPCC') {
                    $desc = 'PRESTAMO CAJA CHICA,';
                }
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div><center><h2>EL PAGO ' . $idp . ' SE HA CAMBIADO A ' . $desc . ' Y NO PODRA APLICARSE A FACTURAS.</h2><center></div>', $pagina);
                $this->view_page($pagina);
            } else {
                include 'app/views/pages/p.buscaPagosActivos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>EL PAGO ' . $idp . ' QUE INTENTA CAMBIAR YA TIENE MOVIMIENTOS ASOCIADOS O SE SUSITO UN ERROR AL TRATAR DE ACTUALIZAR LOS DATOS, SI CREE QUE ESTO ES UN ERROR REPORTE A SISTEMAS</h2><center></div>', $pagina);
                $this->view_page($pagina);
            }
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function errorPago($idp, $tipo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.buscaPagosActivos.php');
            ob_start();
            if ($tipo == 'SS') {
                include 'app/views/pages/p.buscaPagosActivos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>FAVOR DE SELECCIONAR UN TIPO VALIDO</h2><center></div>', $pagina);
                $this->view_page($pagina);
            } else {
                include 'app/views/pages/p.buscaPagosActivos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>EL PAGO ' . $idp . ' QUE INTENTA CAMBIAR YA TIENE MOVIMIENTOS ASOCIADOS, SI CREE QUE ESTO ES UN ERROR REPORTE A SISTEMAS</h2><center></div>', $pagina);
                $this->view_page($pagina);
            }
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function regEdoCta($idtrans, $monto, $tipo, $mes, $banco, $cuenta, $cargo, $anio, $nvaFechComp, $nf, $valor) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.EstadoDeCuenta.php');
            ob_start();
            $aplicar = $data->regEdoCta($idtrans, $monto, $tipo, $cargo, $anio, $nvaFechComp, $nf, $valor);

            if ($banco == 'Banco Az') {
                $banco = 'Banco Azteca';
                $cuenta = '0110239668';
            } elseif ($banco == 'Scotiaba') {
                $banco = 'Scotiabank';
                $cuenta = '044180001025870734';
            }
            if ($nf == '1') {

            } else {
                $redireccionar = "estado_de_cuenta_mes&mes={$mes}&banco={$banco}&cuenta={$cuenta}&anio={$anio}&nvaFechComp={$nvaFechComp}";
                $pagina = $this->load_template('Pedidos');
                $html = $this->load_page('app/views/pages/p.redirectform.php');
                include 'app/views/pages/p.redirectform.php';
                $this->view_page($pagina);
            }
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verValidaciones() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verValidaciones.php');
            ob_start();
            $validaciones = $data->verValidaciones();

            if ($validaciones) {
                include 'app/views/pages/p.verValidaciones.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {

                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON
                	 VALIDACIONES PENDIENTES DE IMPRESIÓN</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function imprimeValidacion($idval) {
        $data = new Pegaso;
        $validacion = $data->datosValidacion($idval);
        $partidasValidadas = $data->ValidacionPartidad($idval);
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerRECEPpdf.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);
        foreach ($validacion as $data) {
            $folio = $data->IDVAL;
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Write(6, 'Folio Validacion:' . $data->IDVAL);
            $pdf->Ln();
            $pdf->Write(6, 'Fecha de Validacion' . $data->FECHA_VALIDACION);
            $pdf->Ln();
            $pdf->Write(6, 'Usuario: ' . $data->USUARIO);
            $pdf->Ln();
            $pdf->Write(6, 'Se valido la OC :' . $data->OC);
            $pdf->Ln();
            $pdf->Write(6, 'Con la recepcion :' . $data->RECEPCION);
            $pdf->Ln();
            $pdf->Write(6, 'Resultado: ' . $data->RESULTADO);
            $pdf->Ln();
            $pdf->Ln();
        }

        $pdf->SetFont('Arial', 'B', 7);
        //$pdf->Cell(10,6,"FOLIO",1);
        $pdf->Cell(15, 6, "OC", 1);
        $pdf->Cell(17, 6, "RECEPCION", 1);
        $pdf->Cell(50, 6, "Descripcion", 1);
        $pdf->Cell(10, 6, "Unidad", 1);
        $pdf->Cell(10, 6, "Orden", 1);
        $pdf->Cell(10, 6, "Valida", 1);
        $pdf->Cell(15, 6, "Monto", 1);
        $pdf->Cell(10, 6, "Saldo", 1);
        $pdf->Cell(10, 6, "PXR", 1);
        $pdf->Cell(15, 6, "SubTot", 1);
        $pdf->Cell(15, 6, "IVA", 1);
        $pdf->Cell(15, 6, "Total", 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'I', 7);
        $total_oc = 0;
        $total_subtotal = 0;
        $total_iva = 0;
        $total_final = 0;
        foreach ($partidasValidadas as $row) {
            /* $total_subtotal += ($row->COST_REC * $row->CANT_REC);
              $total_iva += ($row->COST_REC * $row->CANT_REC)* 0.16;
              $total_final += ($row->COST_REC * $row->CANT_REC) * 1.16;
              $total_oc += $row->TOT_PARTIDA;
              $pdf->Cell(10,6,$row->FOLIO_VALIDACION,'L,T,R'); */
            $pdf->Cell(15, 6, trim($row->CVE_DOC), 'L,T,R');
            $pdf->Cell(17, 6, trim($row->DOC_SIG), 'L,T,R');
            $pdf->Cell(50, 6, substr($row->DESCR, 0, 29), 'L,T,R');
            $pdf->Cell(10, 6, $row->UNI_ALT, 'L,T,R');
            $pdf->Cell(10, 6, $row->CANT, 'L,T,R');
            $pdf->Cell(10, 6, $row->CANT_REC, 'L,T,R');
            $pdf->Cell(15, 6, '$ ' . number_format($row->TOT_PARTIDA, 2), 'L,T,R');
            $pdf->Cell(10, 6, '$ ' . number_format($row->SALDO, 2), 'L,T,R');
            $pdf->Cell(10, 6, $row->PXR, 'L,T,R');
            $pdf->Cell(15, 6, '$ ' . number_format(($row->COST_REC * $row->CANT_REC), 2), 'L,T,R'); ///  Subtotal
            $pdf->Cell(15, 6, '$ ' . (number_format(($row->COST_REC * $row->CANT_REC), 2) * 0.16), 'L,T,R'); /// Costo antes de IVA
            $pdf->Cell(15, 6, '$ ' . (number_format(($row->COST_REC * $row->CANT_REC), 2) * 1.16), 'L,T,R'); /// Costo Total con IVA s
            $pdf->Ln();    // Segunda linea descripcion
            //$pdf->Cell(10,6,"",'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(17, 6, "", 'L,B,R');
            $pdf->Cell(50, 6, substr($row->DESCR, 29, 70), 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(10, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');

            $pdf->Ln();
        }
        /*
          if(round($total_oc,2) == round($total_subtotal,2) * 1.16) $mensaje = "SALDADO";
          elseif(round($total_oc,2) > round($total_subtotal,2)) $mensaje = "DEUDOR";
          else $mensaje = "ACREDOR";

          $pdf->SetFont('Arial', 'B',44);
          $pdf->Ln(8);
          $pdf->SetX(30);
          $pdf->Write(6,$mensaje);

          $pdf->SetFont('Arial', 'B',12);

          $pdf->Ln(60);
          $pdf->SetX(140);
          $pdf->Write(6,"Subtotal       $ ".number_format($total_subtotal,4,'.',','));
          $pdf->Ln();
          $pdf->SetX(140);
          $pdf->Write(6,"I.V.A.         $ ".number_format($total_iva,4,'.',','));
          $pdf->Ln();
          $pdf->SetX(140);
          $pdf->Write(6,"Total          $ ".number_format($total_final,2,'.',','));
          $pdf->Ln();
         */
        $pdf->Output('Validacion Recepcion' . $folio . '.pdf', 'i');
    }

    function verSolicitudes() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verSolicitudes.php');
            ob_start();
            $solicitudes = $data->verSolicitudes();
            if (count($solicitudes) > 0) {
                include 'app/views/pages/p.verSolicitudes.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON
                	 SOLICITUDES PENDIENTES DE IMPRESIÓN</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ImpSolicitud($idsol) {
        $data = new Pegaso;
        $dSol = $data->datosSolicitud($idsol);
        $crSol = $data->crSolicitud($idsol);
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerSPCR.jpg', 10, 15, 205, 55);
        $pdf->Ln(70);

        foreach ($dSol as $data) {
            $folio = $data->IDSOL;

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Write(6, 'Solicitud No:' . $data->IDSOL);
            $pdf->Ln();
            $pdf->Write(6, 'Fecha de Elaboracion: ' . $data->FECHAELAB);
            $pdf->Ln();
            $pdf->Write(6, 'Usuario: ' . $data->USUARIO);
            $pdf->Ln();
            $pdf->Write(6, 'Tipo de pago :' . $data->TIPO);
            $pdf->Ln();
            $pdf->Write(6, 'Banco Preferido :' . $data->BANCO);
            $pdf->Ln();
            $pdf->Write(6, 'Proveedor: ' . $data->NOM_PROV);
            $pdf->Ln();
            $pdf->Write(6, 'Monto: $ ' . number_format($data->MONTO, 2));
            $pdf->Ln();
            $pdf->Ln();
        }
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(20, 6, "RECEPCION", 1);
        $pdf->Cell(25, 6, "Fecha y Hora", 1);
        $pdf->Cell(15, 6, "IMPORTE", 1);
        $pdf->Cell(20, 6, "OC", 1);
        $pdf->Cell(25, 6, "FECHA OC ", 1);
        $pdf->Cell(15, 6, "IMPORTE", 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'I', 7);
        $total_oc = 0;
        $total_subtotal = 0;
        $total_iva = 0;
        $total_final = 0;
        foreach ($crSol as $row) {
            /* $total_subtotal += ($row->COST_REC * $row->CANT_REC);
              $total_iva += ($row->COST_REC * $row->CANT_REC)* 0.16;
              $total_final += ($row->COST_REC * $row->CANT_REC) * 1.16;
              $total_oc += $row->TOT_PARTIDA; */
            $pdf->Cell(20, 6, TRIM($row->CVE_DOC), 'L,T,R');
            $pdf->Cell(25, 6, trim($row->FECHAELAB), 'L,T,R');
            $pdf->Cell(15, 6, '$ ' . number_format($row->IMPORTE_REAL, 2), 'L,T,R');
            $pdf->Cell(20, 6, $row->CVE_DOC_OC, 'L,T,R');
            $pdf->Cell(25, 6, $row->FECHAELAB_OC, 'L,T,R');
            $pdf->Cell(15, 6, '$ ' . number_format($row->IMPORTE_OC, 2), 'L,T,R');
            $pdf->Ln();    // Segunda linea descripcion
            $pdf->Cell(20, 6, "", 'L,B,R');
            $pdf->Cell(25, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(20, 6, "", 'L,B,R');
            $pdf->Cell(25, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');

            $pdf->Ln();
        }

        $pdf->Output('SolicitudPago_' . $folio . '_.pdf', 'i');
    }

    function verPagoSolicitudes() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.ver.solicitudes.pagadas.php');
            ob_start();
            $solicitudes = $data->verPagoSolicitudes();
            if (count($solicitudes) > 0) {
                include 'app/views/pages/p.ver.solicitudes.pagadas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON
                	 SOLICITUDES PENDIENTES DE IMPRESIÓN</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function ImpSolPagada($idsol) {
        $data = new Pegaso;
        $dSol = $data->datosSolicitud($idsol);
        $crSol = $data->crSolicitud($idsol);
        $ctrlImp = $data->ctrlImpresiones($idsol);

        if ((int) $ctrlImp == 1) {
            $controlImpresion = '#############  Original  #############';
        } else {
            $controlImpresion = 'Reimpresion No: ' . $ctrlImp . '.';
        }


        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->Image('app/views/images/headerVacio.jpg', 10, 15, 205, 55);
        $pdf->SetFont('Courier', 'B', 25);
        $pdf->SetTextColor(255, 0, 0);
        $pdf->SetXY(110, 28);
        $pdf->Write(10, 'Comprobante');
        $pdf->SetXY(110, 38);
        $pdf->Write(10, utf8_decode('Pago de Crédito'));
        $pdf->Ln(10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(65);
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->SetXY(60, 60);
        $pdf->Write(6, $controlImpresion);
        $pdf->Ln(10);
        foreach ($dSol as $data) {
            $folio = $data->IDSOL;
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Write(6, 'Solicitud No:' . $data->IDSOL . utf8_decode(' Folio Pago Crédito CR-') . strtoupper($data->TP_TES_FINAL) . '-' . $data->FOLIO);
            $pdf->Ln();
            $pdf->Write(6, 'Fecha de Elaboracion: ' . $data->FECHAELAB);
            $pdf->Ln();
            $pdf->Write(6, 'Fecha de Pago: ' . $data->FECHA_REG_PAGO_FINAL);
            $pdf->Ln();
            $pdf->Write(6, 'Usuario Solicitud: ' . $data->USUARIO . '         #################   Usuario Pago: ' . $data->USUARIO_PAGO);
            $pdf->Ln();
            $pdf->Write(6, 'Tipo de pago Solicitado: ' . $data->TIPO . '        #################   Tipo de pago Realizado: ' . $data->TP_TES_FINAL);
            $pdf->Ln();
            $pdf->Write(6, 'Banco Solicitado :' . $data->BANCO . '           #################   Banco Pago: ' . $data->BANCO_FINAL);
            $pdf->Ln();
            $pdf->Write(6, 'Proveedor: ' . $data->NOM_PROV);
            $pdf->Ln();
            $pdf->Write(6, 'Monto Solicitado: $ ' . number_format($data->MONTO, 2) . '       #################  Monto del Pago Realizado: $ ' . number_format($data->MONTO_FINAL, 2));
            $pdf->Ln();
            $pdf->Ln();
        }

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(20, 6, "RECEPCION", 1);
        $pdf->Cell(25, 6, "Fecha y Hora", 1);
        $pdf->Cell(15, 6, "IMPORTE", 1);
        $pdf->Cell(20, 6, "OC", 1);
        $pdf->Cell(25, 6, "FECHA OC ", 1);
        $pdf->Cell(15, 6, "IMPORTE", 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'I', 7);
        $total_oc = 0;
        $total_subtotal = 0;
        $total_iva = 0;
        $total_final = 0;
        foreach ($crSol as $row) {
            /* $total_subtotal += ($row->COST_REC * $row->CANT_REC);
              $total_iva += ($row->COST_REC * $row->CANT_REC)* 0.16;
              $total_final += ($row->COST_REC * $row->CANT_REC) * 1.16;
              $total_oc += $row->TOT_PARTIDA; */
            $pdf->Cell(20, 6, TRIM($row->CVE_DOC), 'L,T,R');
            $pdf->Cell(25, 6, trim($row->FECHAELAB), 'L,T,R');
            $pdf->Cell(15, 6, '$ ' . number_format($row->IMPORTE, 2), 'L,T,R');
            $pdf->Cell(20, 6, $row->CVE_DOC_OC, 'L,T,R');
            $pdf->Cell(25, 6, $row->FECHAELAB_OC, 'L,T,R');
            $pdf->Cell(15, 6, '$ ' . number_format($row->IMPORTE_OC, 2), 'L,T,R');
            $pdf->Ln();    // Segunda linea descripcion
            $pdf->Cell(20, 6, "", 'L,B,R');
            $pdf->Cell(25, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Cell(20, 6, "", 'L,B,R');
            $pdf->Cell(25, 6, "", 'L,B,R');
            $pdf->Cell(15, 6, "", 'L,B,R');
            $pdf->Ln();
        }

        $pdf->Output('SolicitudPago_' . $folio . '_.pdf', 'i');
    }

    function verCompras() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.ver.compras.php');
            ob_start();
            $docr = Null;
            $compras = $data->verCompras($docr);

            if (count($compras) > 0) {
                include 'app/views/pages/p.ver.compras.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON
                	 SOLICITUDES PENDIENTES DE IMPRESIÓN</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function costeoRecepcion($docr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.ver.compra.completa.php');
            ob_start();
            $compras = $data->verCompras($docr);
            $totalPiezas = $data->piezas($docr);
            $partidas = $data->verPartidasCompras($docr);
            if (count($compras) > 0) {
                include 'app/views/pages/p.ver.compra.completa.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON
                	 SOLICITUDES PENDIENTES DE IMPRESIÓN</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function recConta($folio) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.ver.compras.php');
            ob_start();
            $recconta = $data->recConta($folio);
            $compras = $data->verCompras();
            if (count($compras) > 0) {
                include 'app/views/pages/p.ver.compras.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON
                	 SOLICITUDES PENDIENTES DE IMPRESIÓN</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verComprasRecibidas() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.ver.compras.recibidas.php');
            ob_start();
            $compras = $data->verComprasRecibidas();
            if (count($compras) > 0) {
                include 'app/views/pages/p.ver.compras.recibidas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON
                	 SOLICITUDES PENDIENTES DE IMPRESIÓN</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function regCompraEdoCta($folio, $doc, $fecha) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.ver.compras.recibidas.php');
            ob_start();
            $registrar = $data->regCompraEdoCta($folio, $doc, $fecha);
            $compras = $data->verComprasRecibidas();
            if (count($compras) > 0) {
                include 'app/views/pages/p.ver.compras.recibidas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRARON
                	 SOLICITUDES PENDIENTES DE IMPRESIÓN</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function buscaPagos() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $usuario = $_SESSION['user']->NOMBRE;
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.buscarPagos.php');
            if ($usuario = 'Alejandro') {
                include 'app/views/pages/p.buscarPagos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>USTED NO ESTA AUTORIZADO PARA REALIZAR ESTA FUNCION </h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function buscarPagos($campo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $usuario = $_SESSION['user']->NOMBRE;
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.mostrarPagos.php');
            ob_start();
            $res = $data->buscarPagos($campo);
            if (count($res) > 0 and $usuario = 'Alejandro') {
                include 'app/views/pages/p.mostrarPagos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>USTED NO ESTA AUTORIZADO PARA REALIZAR ESTA FUNCION </h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function cancelarPago($idp) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $usuario = $_SESSION['user']->NOMBRE;
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.mostrarPagos.php');
            ob_start();
            $cancelarPago = $data->cancelarPago($idp);
            $campo = '';
            $res = $data->buscarPagos($campo);
            if (count($res) > 0 and $usuario = 'Alejandro') {
                include 'app/views/pages/p.mostrarPagos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>USTED NO ESTA AUTORIZADO PARA REALIZAR ESTA FUNCION </h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function enviarConta($folios, $cuentaBancaria, $medio, $importe) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $dao = new pegaso;
            $misFolios = explode(",", $folios);
            $creaSP = $dao->NewSolicitudPago($cuentaBancaria, $medio, $importe, $misFolios);
            if ($creaSP > 2) {
                foreach ($misFolios as $folio):
                    $asignafolio = $dao->asignaFolioDocumento($folio, $creaSP);
                endforeach;
                $enviaConta = $dao->enviarConta($creaSP);

                $this->listarOCContrarecibos();
            }else {
                echo 'No se puede crear 1 misma solicitud de pago para varios proveedores, favor de seleccionar Recepciones de 1 solo Proveedor...';
                $this->listarOCContrarecibos();
            }
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function pagoFacturas($idp) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.pagoFacturas.php');
            ob_start();
            $total = $data->montoAplicado($idp);
            $pago = $data->infoPago($idp);
            $facturas = $data->pagoFacturas($idp);
            if (count($facturas) > 0) {
                include 'app/views/pages/p.pagoFacturas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRO LA INFORMACION DE LAS FACTURAS CON ESTE PAGO, FAVOR DE REPORTAR A SISTEMAS.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function buscaContrarecibos() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $contrarecibo = 0;
            $html = $this->load_page('app/views/pages/p.buscaContrarecibos.php');
            include 'app/views/pages/p.buscaContrarecibos.php';
            $table = ob_get_clean();
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function buscarContrarecibos($campo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.buscaContrarecibos.php');
            ob_start();
            $contrarecibo = $data->buscarContrarecibos($campo);
            if (count($contrarecibo) > 0) {
                include 'app/views/pages/p.buscaContrarecibos.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $contrarecibo = 0;
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRO LA INFORMACION DE LAS DE LA RECEPCION FAVOR DE REVISAR LOS DATOS.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function editIngresoBodega($idi, $costo, $proveedor, $cant, $unidad) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            ob_start();
            $edit = $data->editIngresoBodega($idi, $costo, $proveedor, $cant, $unidad);
            $ingresos = $data->verIngresoBodega();
            if (count($ingresos) > 0) {
                include 'app/views/pages/p.verIngresoBodega.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                echo "<script>alert('NO se pudo Ingresar el producto a la Bodega, Favor de revisar que la descipcion no incluya comillas simples como  y como');</script>";
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function revAplicaciones() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            ob_start();
            $proceso = $data->procesoAplicaciones();
            $verResultado = $data->verAplicaiones();
            if (count($ingresos) > 0) {
                include 'app/views/pages/p.verIngresoBodega.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                echo "<script>alert('NO se pudo Ingresar el producto a la Bodega, Favor de revisar que la descipcion no incluya comillas simples como  y como');</script>";
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function dirVerFacturas($mes, $vend, $anio) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');
            $html = $this->load_page('app/views/pages/p.dirVerFacturas.php');
            ob_start();
            if (!isset($mes) or empty($mes)) {
                $mes = date("n");
            }
            if (!isset($vend) or empty($vend)) {
                $vend = 'todos';
            }
            if ($anio == 99) {
                $mes = 12;
            }
            if ($mes != 'errorfecha') {
                $facturas = $data->dirVerFacturas($mes, $vend, $anio);
                $ventasMes = $data->ventasMes($mes, $vend, $anio);
                $saldoFacturas = $data->saldoFacturas($mes, $vend, $anio);
                $NotasCreditoMes = $data->NotasCreditoMes($mes, $vend, $anio);
                $pagosDelMes = $data->facturasPagadasMes($mes, $vend, $anio);
                $ventaTotal = $data->ventaTotalMes($mes, $vend, $anio);
                $meses = $data->traeMeses();
                $mesActual = $data->traeMes($mes);
                $facturasFAA = $data->serieFAA($mes, $vend, $anio);
                $facturasG = $data->serieG($mes, $vend, $anio);
                $facturasE = $data->serieE($mes, $vend, $anio);
                $NotasCreditoAplicadas = $data->NCaplicadas($mes, $vend, $anio);
                $vendedores = $data->traeVendedores();
            } else {
                $facturas = 0;
            }
            if (count($facturas) > 1) {
                include 'app/views/pages/p.dirVerFacturas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2> FAVOR DE SELECCIONAR EL MES Y AÑO CORRECTO.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function buscaOC($fechaedo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $fecha = $fechaedo;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.buscaOC.php');
            include 'app/views/pages/p.buscaOC.php';
            $table = ob_get_clean();
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function traeOC($campo, $fechaedo) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verOC.php');
            ob_start();
            $banco = $data->CuentasBancos();
            $oc = $data->traeOC($campo);
            $fechaedo = $fechaedo;
            if (count($oc) > 0) {
                include 'app/views/pages/p.verOC.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRO LA INFORMACION DE LA ORDEN DE COMPRA, FAVOR DE VIERFICAR Y EJECUTAR NUEVAMENTE.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function procesarOC($doco, $idb, $fechaedo, $montof, $factura, $tpf) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verCierreVal.php');
            ob_start();
            $exec = $data->procesarOC($doco, $idb, $fechaedo, $montof, $factura, $tpf);
            $redireccionar = "buscaOC&fechaedo={$fechaedo}";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function deudores($fechaedo, $banco) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.deudores.php');
            ob_start();
            $banco = $data->CuentasBancos();
            $proveedor = $data->verProveedores();
            $deudor = $data->deudores();
            $fechaedo = $fechaedo;
            $banco = $banco;
            include 'app/views/pages/p.deudores.php';
            $table = ob_get_clean();
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function guardaDeudor($fechaedo, $monto, $proveedor, $banco, $tpf, $referencia, $destino) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.deudores.php');
            ob_start();
            $guardar = $data->guardaDeudor($fechaedo, $monto, $proveedor, $banco, $tpf, $referencia, $destino);
            $redireccionar = "deudores&fechaedo={$fechaedo}&banco={$banco}";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
            // $this->deudores($fechaedo, $banco);
        }
    }

    function transfer($fechaedo, $bancoO) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.transfer.php');
            ob_start();
            $banco = $data->CuentasBancos();
            $transfer = $data->transferyprestamo($fechaedo, $bancoO);
            $fechaedo = $fechaedo;
            $banco = $banco;
            include 'app/views/pages/p.transfer.php';
            $table = ob_get_clean();
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function guardaTransPago($fechaedo, $monto, $bancoO, $bancoD, $tpf, $TT, $referencia) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.transfer.php');
            ob_start();
            $guardar = $data->guardaTransPago($fechaedo, $monto, $bancoO, $bancoD, $tpf, $TT, $referencia);

            $redireccionar = "transfer&fechaedo={$fechaedo}&bancoO={$bancoO}";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        }
    }

    function facturapagomaestro($maestro) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.pagoFacturaMaestro.php');
            ob_start();
            $docxmaestro = $data->facturapagomaestro($maestro);

            include 'app/views/pages/p.pagoFacturaMaestro.php';
            $table = ob_get_clean();
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function pagoFacturaMaestro($maestro, $docf) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.pagoFM.php');
            ob_start();
            $factura = $data->factura($docf);
            $pagos = $data->traePagoMaestro($maestro);
            include 'app/views/pages/p.pagoFM.php';
            $table = ob_get_clean();
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function calendarCxC($cartera) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.CalendarioCartera.php');
            ob_start();
            $totales = $data->totalesCanlendar($cartera);
            $totalSemana = $data->totalSemanaCalendar($cartera);
            $calendario = $data->CalendarioCxC($cartera);
            //$oc=$data->traeOC($campo);
            //$fechaedo = $fechaedo;
            if (count($calendario) > 0) {
                include 'app/views/pages/p.CalendarioCartera.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRO LA INFORMACION DE LA ORDEN DE COMPRA, FAVOR DE VIERFICAR Y EJECUTAR NUEVAMENTE.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verMaestros() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $cartera = $_SESSION['user']->CC;
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.verMaestros.php');
            ob_start();
            $maestros = $data->verMaestros($cartera);
            if (count($maestros) > 0) {
                include 'app/views/pages/p.verMaestros.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRO LA INFORMACION DE LA ORDEN DE COMPRA, FAVOR DE VIERFICAR Y EJECUTAR NUEVAMENTE.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function editarMaestro($idm) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.editarMaestro.php');
            ob_start();
            $datosMaestro = $data->editarMaestro($idm);
            if (count($datosMaestro) > 0) {
                include 'app/views/pages/p.editarMaestro.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $html . '<div class="alert-danger"><center><h2>NO SE ENCONTRO LA INFORMACION DE LA ORDEN DE COMPRA, FAVOR DE VIERFICAR Y EJECUTAR NUEVAMENTE.</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function editaMaestro($idm, $cc, $cr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.editarMaestro.php');
            ob_start();
            $datosMaestro = $data->editaMaestro($idm, $cc, $cr);

            $redireccionar = "verMaestros";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function nuevo_maestro() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.nuevo_maestro.php');


            include 'app/views/pages/p.nuevo_maestro.php';
            $table = ob_get_clean();
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);

            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function altaMaestro($nombre, $cc, $cr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.nuevo_maestro.php');
            ob_start();
            $alta = $data->altaMaestro($nombre, $cc, $cr);
            $redireccionar = 'verMaestros';
            include 'app/views/pages/p.redirectform.php';
            $table = ob_get_clean();
            $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);

            $this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function calcularCosto($cimpuesto, $cflete, $cseguro, $caduana, $pedimento, $docr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.nuevo_maestro.php');
            ob_start();
            $alta = $data->calcularCosto($cimpuesto, $cflete, $cseguro, $caduana, $pedimento, $docr);
            //$redireccionar='';
            //include 'app/views/pages/p.redirectform.php';
            //$table = ob_get_clean();
            //$pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            $this->costeoRecepcion($docr);
            //$this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function costoFOB($cfob, $tc, $docr, $par, $pedimento) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.nuevo_maestro.php');
            ob_start();


            $alta = $data->costoFOB($cfob, $tc, $docr, $par, $pedimento);
            //$redireccionar='';
            //include 'app/views/pages/p.redirectform.php';
            //$table = ob_get_clean();
            //$pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            $this->costeoRecepcion($docr);
            //$this->view_page($pagina);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function finalizaCosteo($docr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.nuevo_maestro.php');
            ob_start();
            $finaliza = $data->finalizaCosteo($docr);
            $this->verCompras();
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verFacturas() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verFacturas.php');
            ob_start();
            $facturas = $data->verFacturas();
            if (count($facturas)) {
                include 'app/views/pages/p.verFacturas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function selectFactura($docf, $select) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verFacturas.php');
            ob_start();
            $facturas = $data->selectFactura($docf, $select);
            $this->verFacturas();
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function GeneraReporteSalida() {

        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.generaReporte.php');
            ob_start();
            $datos = $data->datosReporteSalida();
            //$unidades=$data->traeUnidades()
            if (count($datos)) {
                include 'app/views/pages/p.generaReporte.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function generaEmbarque($vehiculo, $cajas, $placas, $operador, $observaciones, $fecha) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $embarque = $data->registraEmbarque($vehiculo, $cajas, $placas, $operador, $observaciones, $fecha);
            $redireccionar = "reporteEmbarque&idr={$embarque}";
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.redirectform.php');
            include 'app/views/pages/p.redirectform.php';
            $this->view_page($pagina);
        }
    }

    function imprimirReporte($vehiculo, $cajas, $placas, $operador, $observaciones, $fecha) {
        ob_start();
        $data = new Pegaso;
        $datos = $data->datosFacturas($embarque);
        $fecha = date("Y-m-d H:i:s");
        $pdf = new FPDF('L', 'mm', 'Letter');
        $pdf->AddPage();
        //$pdf->Image('app/views/images/headerCierreRuta.jpg',10,15,205,55);
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->Write(6, 'IMPORTADORA MIZCO S.A. DE C.V.');
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Write(6, 'REPORTE DE EMBARQUES DE MERCANCIA');
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Write(6, 'MERCANCIA SEGURADA POR ACE SEGUROS S.A. POLIZA NO: 15,600');
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Write(6, 'Folio: ' . $embarque . '                                          Fecha de Recepcion: ' . $fecha);
        $pdf->Ln();
        $pdf->Write(6, 'Operador:' . $operador . '         Vehiculo: ' . $vehiculo . '         Placas:' . $placas);
        $pdf->Ln();
        $pdf->Ln(10);
        $pdf->Write(6, 'Observaciones: ' . $observaciones . '        Cajas:' . $cajas . '.');
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(10, 6, "CAJAS", 1);
        $pdf->Cell(18, 6, "FACTURA / REMISION", 1);
        $pdf->Cell(40, 6, "CLIENTE", 1);
        $pdf->Cell(30, 6, "FECHA", 1);
        $pdf->Cell(18, 6, "IMPORTE", 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'I', 7);
        foreach ($datos as $row) {
            $pdf->Cell(10, 6, $cajas, 1);
            $pdf->Cell(18, 6, trim($row->CVE_DOC), 1);
            $pdf->Cell(40, 6, substr($row->NOMBRE, 0, 23), 1);
            $pdf->Cell(30, 6, $row->FECHAELAB, 1);
            $pdf->Cell(18, 6, '$ ' . number_format($row->OBSERVACION, 2), 1);
        }


        $pdf->Ln(12);
        $pdf->Write(6, '_______________________                    ______________________________________     _____________________               _________________________________');
        $pdf->Ln();
        $pdf->Write(6, 'VERIFICO CARGA                                 FIRMA OPERADOR                               AUTORIZO                                        DEPTO DE COBRANZA ');
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Ln();
        ob_get_clean();
        $pdf->Output('Reporte de Embarque: ' . $embarque . '.pdf', 'i');
    }

    function reimprimirReporte($idr) {
        ob_start();
        $data = new Pegaso;
        //$embarque=$data->registraEmbarque($vehiculo,$cajas,$placas,$operador, $observaciones, $fecha);
        $embarque = $idr;
        $datosEmbarque = $data->reimprimirEmbarque($idr);
        $datos = $data->datosFacturas($embarque);

        foreach ($datosEmbarque as $d) {
            $folio = $d->ID;
        }
        $fecha = date("Y-m-d H:i:s");
        $pdf = new FPDF('L', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetXY(10, 10);
        $pdf->Image('app/views/images/LOGOSELECT.jpg', 10, 15);
        //$pdf->SetXY(30,30);
        //$pdf->Write(0,'FOLIO: ');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(0, 6, "IMPORTADORA MIZCO S.A. DE C.V.", 0, 0, 'C');
        $pdf->Ln();
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 6, 'REPORTE DE EMBARQUES DE MERCANCIA', 0, 0, 'C');
        $pdf->Cell(0, 10, 'Folio: ' . $folio, 0, 0, 'R');
        $pdf->Ln();
        $pdf->Ln(.5);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 6, 'MERCANCIA ASEGURADA POR ACE SEGUROS S.A. POLIZA : 15,600', 0, 0, 'C');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();


        foreach ($datosEmbarque as $data) {
            $cajas = $data->CAJAS;
            $pdf->SetFont('Arial', 'B', 12);
            //$pdf->Write(6,'Folio: '.$data->ID);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(120);
            $pdf->Write(6, 'Fecha de Recepcion: ' . $data->FECHA_REPORTE);
            $pdf->Ln();
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(70, 6, 'Operador: ' . $data->OPERADOR, 0, 0, 'L');
            $pdf->Cell(50, 6, 'Vehiculo:  ' . $data->VEHICULO, 0, 0, 'L');
            $pdf->Cell(50, 6, 'Placas: ' . $data->PLACAS, 0, 0, 'L');
            $pdf->Ln();
            $pdf->Write(6, 'Estatus del Embarque: ' . $data->ESTATUS);
            $pdf->Ln();
            $pdf->Write(6, 'Observaciones: ' . $data->OBSERVACIONES . '        Cajas:' . $data->CAJAS . '.');
            $pdf->Ln();
        }

        $pdf->LN();
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(10, 6, "CAJAS", 1);
        $pdf->Cell(45, 6, "FACTURA / REMISION", 1);
        $pdf->Cell(60, 6, "CLIENTE", 1);
        $pdf->Cell(30, 6, "FECHA FACTURA", 1);
        $pdf->Cell(100, 6, "OBSERVACIONES", 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'I', 7);

        foreach ($datos as $row) {
            if (substr($row->DOCUMENTO, 0, 1) == 'F') {
                $doc = 'Factura Electronica : ';
            } else {
                $doc = 'Remision :';
            }

            $pdf->Cell(10, 6, $row->CAJAS, 'L,T,R');
            $pdf->Cell(45, 6, $doc . trim($row->DOCUMENTO), 'L,T,R');
            $pdf->Cell(60, 6, substr($row->CLIENTE, 0, 60), 'L,T,R');
            $pdf->Cell(30, 6, $row->FECHA_ELABORACION, 'L,T,R');
            $pdf->Cell(100, 6, $row->OBSERVACION, 'L,T,R');
            $pdf->Ln();
            $pdf->Cell(10, 6, '', 'L,B,R');
            $pdf->Cell(45, 6, 'Sucursal : ', 'L,B,R');
            $pdf->Cell(60, 6, substr($row->CALLE, 0, 60) . substr($row->CALLE2, 0, 60), 'L,B,R');
            $pdf->Cell(30, 6, '', 'L,B,R');
            $pdf->Cell(100, 6, '', 'L,B,R');
            $pdf->Ln();
        }

        $pdf->Ln(12);
        $pdf->SetXY(15, 180);
        $pdf->Write(6, '_______________________                    ______________________________________     _____________________               _________________________________');
        $pdf->Ln();
        $pdf->Write(6, '        VERIFICO CARGA                                 FIRMA OPERADOR                                                       AUTORIZO                                        DEPTO DE COBRANZA ');
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Ln();
        ob_get_clean();
        $pdf->Output('Reporte de Embarque' . $embarque . '.pdf', 'D');
    }

    function asociarSKU() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verClientes.php');
            ob_start();
            $cliente = $data->verClientes();
            if (count($cliente)) {
                include 'app/views/pages/p.verClientes.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function traeProductosCliente($cliente, $nombre, $nomdepto, $numdepto) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.asociarProductoCliente.php');
            ob_start();
            $productos = $data->traeProducto($cliente, $numdepto);
            if (count($productos)) {
                include 'app/views/pages/p.asociarProductoCliente.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function asociarelSKU($cliente, $numdepto, $nomdepto, $nombre, $cprod, $sku) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Compra Venta');
            $html = $this->load_page('app/views/pages/p.nuevo_maestro.php');
            ob_start();
            $agrega = $data->asociarSKU($cliente, $numdepto, $cprod, $sku);
            $this->traeProductosCliente($cliente, $nombre, $nomdepto, $numdepto);
        } else {
            $e = "Favor de Iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verReportes() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verReportes.php');
            ob_start();
            $reportes = $data->verReportes();
            if (count($reportes)) {
                include 'app/views/pages/p.verReportes.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function reporteEmbarque($idr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.reporteEmbarque.php');
            ob_start();
            $reporte = $data->reporteEmbarque($idr);
            $facturas = $data->reporteEmbarqueFacturas($idr);
            if (count($reporte)) {
                include 'app/views/pages/p.reporteEmbarque.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function guardaCaja($idr, $docf, $cajas) {
        session_cache_limiter('private_no_expire');
        if ($_SESSION['user']) {
            $data = new pegaso;
            ob_start();
            $setCaja = $data->guardaCaja($idr, $docf, $cajas);
            $this->reporteEmbarque($idr);
        }
    }

    function cancelaEmbarque($idr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            ob_start();
            $cancelar = $data->cancelaEmbarque($idr);
            $this->verReportes();
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function cambiarReporte($vehiculo, $cajas, $placas, $operador, $observaciones, $fecha, $idr) {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            ob_start();
            $cambiar = $data->cambiarReporte($vehiculo, $cajas, $placas, $operador, $observaciones, $fecha, $idr);
            $this->reporteEmbarque($idr);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verFacturasFecha() {
        session_cache_limiter('private_no_expire');
        if (isset($_SESSION['user'])) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verFacturasFecha.php');
            ob_start();
            $facturas = $data->verFacturasFecha();
            if (count($facturas)) {
                include 'app/views/pages/p.verFacturasFecha.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function cambiarFecha($docf, $nuevaFecha, $cliente) {
        session_cache_limiter('private_no_expire');
        if ($_SESSION['user']) {
            $data = new pegaso;
            ob_start();
            $cambiaFecha = $data->cambiaFecha($docf, $nuevaFecha, $cliente);
            $this->verFacturasFecha();
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function cerrarFecha($docf) {
        session_cache_limiter('private_no_expire');
        if ($_SESSION['user']) {
            $data = new pegaso;
            ob_start();
            $cerrar = $data->cerrarFecha($docf);
            $this->verFacturasFecha();
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verCambiosFechas() {
        session_cache_limiter('private_no_expire');
        if ($_SESSION['user']) {
            $data = new pegaso;
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verCambiosFechas.php');
            ob_start();
            $facturas = $data->verCambiosFechas();
            if (count($facturas) > 0) {
                include 'app/views/pages/p.verCambiosFechas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function guardaObsPar($docf, $obs, $idr, $cajas, $nvaFecha) {
        session_cache_limiter('private_no_expire');
        if ($_SESSION['user']) {
            $data = new pegaso;
            ob_start();
            $guardaObsp = $data->GuardaObs($docf, $obs, $idr, $cajas, $nvaFecha);
            $this->reporteEmbarque($idr);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verRecepProcesadas() {
        session_cache_limiter('private_no_expire');
        if ($_SESSION['user']) {
            $data = new pegaso;
            ob_start();
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.ver.compras.finalizadas.php');
            $ocfinalizadas = $data->verRecepProcesadas();
            if (count($ocfinalizadas) > 0) {
                include 'app/views/pages/p.ver.compras.finalizadas.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function liberarRecepcion($docr) {
        session_cache_limiter('private_no_expire');
        if ($_SESSION['user']) {
            $data = new pegaso;
            ob_start();
            $actualizar = $data->liberarRecepcion($docr);
            $this->verRecepProcesadas();
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function verES($fechaini, $fechafin) {
        session_cache_limiter('private_no_expire');
        if ($_SESSION['user']) {
            $data = new pegaso;
            ob_start();
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.verES.php');

            $fi = $fechaini;
            $ff = $fechafin;
            if ($fechaini == '') {
                $es = 1;
            } else {
                //$es = $data->verES($fechaini, $fechafin);
                $refac=$data->refacturaciones();
                $es=$data->inventarioAunaFecha($fi, $ff);
            }

            ///var_dump($es);
            if (count($es) > 0) {
                include 'app/views/pages/p.verES.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function imprimeES($fechaini, $fechafin) {
        ob_start();
        $data = new Pegaso;
        //$datos = $data->verES($fechaini, $fechafin);
        $fi = $fechaini;
        $ff = $fechafin;
        $datos = $data->inventarioAunaFecha($fi, $ff);

        $usuario = $_SESSION['user']->USER_LOGIN;
        $fecha = date("Y-m-d H:i:s");
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();

        $pdf->Image('app/views/images/LOGOSELECT.jpg', 10, 15);
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(0, 6, "IMPORTADORA MIZCO S.A. DE C.V.", 0, 0, 'C');
        $pdf->Ln();
        $pdf->Ln(.5);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 6, 'REPORTE DE MOVIMIENTOS A UNA FECHA', 0, 0, 'C');
        $pdf->Ln();
        $pdf->Ln(20);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Write(6, 'Resumen de movimientos por productos.');
        $pdf->Ln();
        $pdf->Write(6, 'Fecha de Emision: ' . $fecha);
        $pdf->Ln();
        $pdf->Write(6, 'Usuario:' . $usuario);
        $pdf->Ln();
        $pdf->Write(6, 'Fecha de inicio: ' . $fechaini . ' al ' . $fechafin);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Write(6, 'Incluye todos los Conceptos de movimientos al inventario.');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(18, 6, "Clave", 1, 0, 'C');
        $pdf->Cell(60, 6, "Descripcion", 1, 0, 'C');
        $pdf->Cell(14, 6, "Inicial", 1, 0, 'C');
        $pdf->Cell(15, 6, "Entradas", 1, 0, 'C');
        $pdf->Cell(12, 6, "Salidas", 1, 0, 'C');
        $pdf->Cell(14, 6, "Exist. Final", 1, 0, 'C');
        $pdf->Ln();
        $pdf->SetFont('Arial', 'I', 6);
        foreach ($datos as $row) {
            $pdf->Cell(18, 6, trim($row[0]), 1, 0, 'L');
            $pdf->Cell(60, 6, substr($row[1], 0, 35), 1, 0, 'L');
            $pdf->Cell(14, 6, number_format($row[3], 0), 1, 0, 'R');
            $pdf->Cell(15, 6, number_format($row[4], 0), 1, 0, 'R');
            $pdf->Cell(12, 6, number_format($row[5], 0), 1, 0, 'R');
            $pdf->Cell(14, 6, number_format($row[6], 0), 1, 0, 'R');
            $pdf->Ln();
        }
        $pdf->Ln(12);
        $pdf->Write(6, 'Reporte de Entradas y salidas a una fecha.');
        $pdf->Ln();
        $pdf->Write(6, 'Incial: ' . $fechaini . '  Final: ' . $fechafin);
        $pdf->Ln();
        //$pdf->Write(6,'       R E C E P C I O N                                                                                              C O N T A B I L I D A D');
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Ln();
        ob_clean();
        $pdf->Output('Reporte Entradas - Salidas.pdf', 'i');
    }

    function sendXml() {
        session_cache_limiter('private_no_expire');
        if ($_SESSION['user']) {
            $data = new pegaso;
            ob_start();
            $pagina = $this->load_template('Pedidos');
            $html = $this->load_page('app/views/pages/p.listadoXml.php');
            $listado = $data->traeListadoFacturas();
            if (count($listado) > 0) {
                include 'app/views/pages/p.listadoXml.php';
                $table = ob_get_clean();
                $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
            } else {
                $pagina = $this->replace_content('/\CONTENIDO\#/ms', $html . '<div class="alert-info"><center><h2>No hay datos para mostrar</h2><center></div>', $pagina);
            }
            $this->view_page($pagina);
        } else {
            $e = "Favor de iniciar Sesión";
            header('Location: index.php?action=login&e=' . urlencode($e));
            exit;
        }
    }

    function compruebaXml($folio) {
        //$result = array("status" => "OK", "response" => "simon carnal " . $folio);
        //$result = array("status" => "ERROR", "response" => "nelson carnal " . $folio);
        require_once('app/lib/nusoap.php');

        $oSoapClient = new nusoap_client('http://serviciosweb.soriana.com/RecibeCfd/wseDocRecibo.asmx?wsdl', true);
        $xml = file_get_contents('./xml/IMI161007SY7F' . $folio . '.xml'); //colocar bien la ruta de la carpeta con los xml
        //parametros a enviar, deben ser en array
        $param = array('XMLCFD' => " <![CDATA[".$xml."]]>");

        $oSoapClient->loadWSDL();
        //en call colocamos el nombre del metodo a usar
        $respuesta = $oSoapClient->call("RecibeCFD", $param);
        if ($oSoapClient->fault) {
            $result = array("status" => "ERROR", "response" => "No se pudo completar la operación: " . $oSoapClient->getError());
        } else { // No
            $sError = $oSoapClient->getError();
            if ($sError) {
                $result = array("status" => "ERROR", "response" => "Error! " . $sError);
            }
        }
//        echo '<br>';
//        echo '<pre>';
//        print_r($respuesta['RecibeCFDResult']);
//        echo '</pre>';
        if ($respuesta['RecibeCFDResult'] == "ok") { //comprobar contra status de soriana
            $result = array("status" => "OK", "response" => $respuesta['RecibeCFDResult']);
        } else {
            $result = array("status" => "ERROR", "response" => $respuesta['RecibeCFDResult']);
        }
        return $result;
    }


    function utilerias($opcion, $docp, $docd, $docf, $fechaIni, $fechaFin, $maestro){
        session_cache_limiter('private_no_expire');        
        if (isset($_SESSION['user'])) {            
            $data = new pegaso;
            $pagina = $this->load_template('Pagos');                        
            ob_start();            
            
            $usuario = $_SESSION['user']->NOMBRE;
            $resultado =  'No se pudo realizar la operacion, favor de revisar los datos';
            if($opcion == 5) {
                $rec=$data->RecalcularPrecio();
            }elseif ($opcion == 6) {
                $rec=$data->recalcularCosto();
            }
            echo $resultado;
            //$cf = $data->asociaCF();
                 include 'app/views/pages/p.utilerias.php';
                 $table = ob_get_clean();
                 $pagina = $this->replace_content('/\#CONTENIDO\#/ms', $table, $pagina);
             $this->view_page($pagina);
        } else {
             $e = "Favor de Iniciar Sesión";
             header('Location: index.php?action=login&e=' . urlencode($e));
             exit;
        }
    }

}
?>

