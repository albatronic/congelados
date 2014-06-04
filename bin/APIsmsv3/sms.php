<?php
// FUNCIONES DE ENVIO DE MENSAJES SMS
//
//SE APOYA EN LA LIBRERIA SMSSend.inc

//SE PUEDEN INDICAR VARIOS DESTINATARIOS INDICANDO SUS NUMEROS SEPARADOS POR ';' EN EL PARAMETRO $to


require "SMSSend.inc";

function EnviaSMS($cuenta,$pwd,$to,$text,$form){

$testsms=new smsItem;

//Defino las propiedades
$testsms->setAccount($cuenta);
$testsms->setPwd($pwd);
$testsms->setTo($to);
$testsms->setText($text);
if($from!='') $testsms->setFrom($from);      // Par�metro opcional

//Recupero los valores establecidos
$Account = $testsms->getAccount();
$Pwd = $testsms->getPwd();
$To = $testsms->getTo();
$Text = $testsms->getText();
$Remite = $testsms->getFrom();

//Env�o del mensaje
$resultado = $testsms->Send();

//Resultado de la operaci�n
$getResult = $testsms->getResult();
$getDescription = $testsms->getDescription();
$getCredit = $testsms->getCredit();

return array($getResult,$getDescription,$getCredit);
}
?>

