<HTML>
<HEAD>
<meta http-equiv="Expires" content="Mon, 26 Jul 1997 05:00:00 GMT">
<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<TITLE>Validacion de documentos electronicos (XML) CFD/CFDI/Retenciones</TITLE>
</HEAD>
<BODY>
<div align=center>
<H1>Validacion de Documentos Electronicos XML</H1>
<H2>CFD/CFDI/Retenciones</H2>
<br><hr><br>
<form method='post' enctype='multipart/form-data'>
 Archivo <input type='file' name='arch' size='60'>
 <INPUT TYPE="submit" VALUE="Valida" >
 <br><br><hr>
</FORM>
<p>
<?php
error_reporting(E_ALL & ~(E_STRICT|E_NOTICE|E_WARNING|E_DEPRECATED));
if (trim($_FILES['arch']['name'])=="") die("no arch");
if ($_FILES['arch']['error']==1 || $_FILES['arch']['size']==0) {
    echo "<h1><red>NO SUBIO archivo, demasiado grande</red></h1>";
    die();
} 
$arch = $_FILES['arch']['tmp_name'];
$texto = file_get_contents($arch);
unlink($arch);
if( substr($texto, 0,3) == pack("CCC",0xef,0xbb,0xbf) ) { 
    $texto = substr($texto, 3); 
    echo "<h3>Tenia BOM, Eliminado</h3>";
} 
if (!mb_check_encoding($texto,"utf-8")) {
    echo "<h3>Error en XML, no esta en UTF-8!</h3>";
}
$nuevo = utf8_decode($texto);
if (mb_check_encoding($nuevo,"utf-8") && $nuevo != $texto) {
    echo "<h3>Sigue siendo utf8, usa decode</h3>";
    $texto = $nuevo;
}
require_once('./lib/nusoap.php');

$oSoapClient = new nusoap_client('http://serviciosweb.soriana.com/RecibeCfd/wseDocRecibo.asmx?wsdl', true);
//$xml = file_get_contents('./IMI161007SY7F' . $folio . '.xml'); //colocar bien la ruta de la carpeta con los xml
print("Texto ===> <p>");
print_r($texto);
print("</p>");

        //parametros a enviar, deben ser en array
        $param = array('XMLCFD' => $texto);
        print_r($param);
        $oSoapClient->loadWSDL();
        //en call colocamos el nombre del metodo a usar
        $respuesta = $oSoapClient->call("RecibeCFD", $param);
        print('<br />==========     Respuesta     ==========<p>');
        print_r($respuesta);
        print('</p>==========     Respuesta     ==========');

        print('</br>==========     Resultado     ==========<p>');
        $result = "";
        if ($oSoapClient->fault) {
            $result = array("status" => "ERROR", "response" => "No se pudo completar la operación: " . $oSoapClient->getError());
        } else { // No
            $sError = $oSoapClient->getError();
            if ($sError) {
                $result = array("status" => "ERROR", "response" => "Error! " . $sError);
            }
        }
        if ($respuesta['RecibeCFDResult'] == "ok") { //comprobar contra status de soriana
            $result = array("status" => "OK", "response" => $respuesta['RecibeCFDResult']);
        } else {
            $result = array("status" => "ERROR", "response" => $respuesta['RecibeCFDResult']);
        }
        
        print_r($result);
        print("</p>");
       
?>
</p>
</BODY>
</HTML>