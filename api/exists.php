<?php

require('../TXP.php');
error_reporting(0);

$playerLogin = $_GET["playerLogin"];
$player = TXP::getPlayer($playerLogin);
echo ($player != null ? 1 : 0);

?>