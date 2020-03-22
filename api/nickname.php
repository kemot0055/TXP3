<?php

require('../TXP.php');

error_reporting(0);
$playerLogin = $_GET["playerLogin"];
$playerNick = $_GET["playerNick"];

if ( !empty ($playerLogin) ) {
	$player = TXP::getPlayer($playerLogin);
	if ( $player != null ) {
		if ( $player->getCash() >= 1000 ) {
			$player->addCash(-1000);
			$player->setPlayerNickname($playerNick);
			
			echo '$ff0> Zmieniono pomyślnie nick na:$fff ' . $playerNick;
		} else {
			echo '$ff0> $f00Brak funduszy na zmiane nicku (brakuje: ' . (1000 - $player->getCash()) . ' VR)!';
		}
	} else {
		echo '$ff0> $f00Błąd podczas przetwarzania loginu!';
	}
} else {
	echo '$ff0> $f00Błąd podczas przetwarzania!';
}

?>