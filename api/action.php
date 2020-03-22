<?php

require ('../TXP.php');
require ('../interface/groups.php');
require ('../interface/dialogs.php');
require ('../manialink/manialink.php');

$playerLogin = $_GET["playerLogin"];
$actionId = $_GET["actionId"];

if( empty ($playerLogin) || empty ($actionId) ) {
	return;
}

$groupId = intval($actionId[0] . $actionId[1]);
if ( strlen($actionId) < 5 || $groupId <= 40 || $groupId >= 60 ) {
	$actionId = Groups::TOOLBAR . '000';
	$groupId = intval($actionId[0] . $actionId[1]);
}

$player = TXP::getPlayer($playerLogin);

if( $player != null ) {
	error_reporting(0);
	$manialink = new Manialink(30000);
	
	if( $groupId == Groups::TOOLBAR ) {
		include('../interface/menu/toolbar.php');
	} else {
		include('../interface/menu/main.php');
	}
	
	if( $groupId == Groups::INVENTORY ) {
		include ('../interface/inventory.php');
	} else if ( $groupId == Groups::GARAGE ) {
		include ('../interface/garage.php');
	} else if ( $groupId == Groups::CAR_DEALER ) {
		include ('../interface/dealer.php');
	} else if ( $groupId == Groups::MARKET ) {
		include ('../interface/market.php');
	} else if ( $groupId == Groups::RANKINGS ) {
		include ('../interface/rankings.php');
	}
	
	if( $groupId == Groups::TOOLBAR ) {
		$manialink->append($toolbar);
	} else {
		$manialink->append($menu);
	}
	
	$manialink->buildManialink();
}

?>