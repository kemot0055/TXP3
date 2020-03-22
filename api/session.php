<?php

require('../TXP.php');
$sessionState = $_GET["sessionState"];

if ( $sessionState == -1 ) {
	$currentState = TXP::getSessionState();
	switch ( $currentState ) {
		case -1: echo '$ff0> $f00Błąd przy wczytywaniu stanu sesji!'; break;
		case 0: echo '$ff0> Aktualnie sesja jest $fffwyłączona'; break;
		case 1: echo '$ff0> Aktualnie sesja jest $fffwłączona bez CTXP'; break;
		case 2: echo '$ff0> Aktualnie sesja jest $fffwłączona wraz z CTXP'; break;
	}	
} else {
	TXP::changeSession($sessionState);
	
	$currentState = TXP::getSessionState();
	if ( $sessionState == $currentState ) {
		switch($sessionState) {
			case 0: echo '$ff0> Wyłączono sesje!'; break;
			case 1: echo '$ff0> Włączono sesje bez CTXP'; break;
			case 2: echo '$ff0> Włączono sesje wraz z CTXP'; break;
		}
	} else {
		echo '$ff0> $f00Błąd poczas zmiany sesji!';
	}
}

?>