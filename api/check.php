<?php

error_reporting(0);
$playerLogin = $_GET["playerLogin"];

if ( $playerLogin == 'tomek0055' ) {
	echo 2;
} else if ( $playerLogin == 'wojtek281195' ) {
	echo 1;
} else {
	echo 0;
}

?>