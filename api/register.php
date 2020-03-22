<?php

error_reporting(0);

include('../TXP.php');
$playerLogin = $_GET["playerLogin"];

if( !empty($playerLogin) ) {
	$isRegistered = TXP::registerPlayer($playerLogin);
	echo ($isRegistered ? '$ff0> Zarejestrowano konto w TXP!' : '$ff0> $f00Jesteś już zarejestrowany w TXP!2');	
} else {
	echo '$ff0> $f00Błąd w przetwarzaniu danych!';
}

?>