<?php

require('../../TXP.php');
$playerLogin = explode(':', $_GET["playerLogin"]);

$player = TXP::getPlayer($playerLogin[0]);
$sessionState = TXP::getSessionState();

if ( $player != null ) {
	
	$playerCar = $player->getPlayerGarage( $player->getCurrentGarageId() )->getCar();
	$playerHaveElectricEngine =  ( $playerCar != null ? ( $playerCar->getType() == Car::TYPE_ELECTRIC ) : false );
	
	$itemType = mt_rand(0, 2);
	if ( $itemType == 0 ) {
		$orbType = mt_rand(0, 6);
		if ( $orbType < 2 ) {
			$orbType = 0;
		} else if ( $orbType < 4 ) {
			$orbType = 2;
		} else if ( $orbType == 5 ) {
			$orbType = 3;
		} else {
			$orbType = 1;
		}
		
		$itemId = intval('200' . $orbType);
		$item = TXP::getItem($itemId);
		$durbality = 0;
	} else {
		$partId = mt_rand(3, 9);
		if ( $partId == 4 && !$playerHaveElectricEngine ) {
			$partId = 3;
		}
		
		$manufId = mt_rand(0, 3);
		$cBonusId = mt_rand(0, 10000);
		if ( $cBonusId < 10 ) {
			$cBonusId = 8;
		} else if ( $cBonusId < 50 ) {
			$cBonusId = 7;
		} else if ( $cBonusId < 200 ) {
			$cBonusId = 6;
		} else if ( $cBonusId < 400 ) {
			$cBonusId = 5;
		} else if ( $cBonusId < 800 ) {
			$cBonusId = 4;
		} else if ( $cBonusId < 1600 ) {
			$cBonusId = 3;
		} else if ( $cBonusId < 3000 ) {
			$cBonusId = 2;
		} else if ( $cBonusId < 6000 ) {
			$cBonusId = 1;
		} else {
			$cBonusId = 0;
		}
		
		$tier = mt_rand(0, 2500);
		if ( $tier < 50 && $sessionState == 2 ) {
			$tier = 3;
		} else if ( $tier < 200 && $sessionState >= 1 ) {
			$tier = 2;
		} else if ( $tier < 750 ) {
			$tier = 1;
		} else {
			$tier = 0;
		}

		$itemId = intval($partId . $manufId . $cBonusId . $tier);
		$item = TXP::getItem($itemId);
		
		if ( $sessionState != 0 ) {
			$durbality = floor( ( mt_rand(4, 10) / 10 ) * $item->getMaxDamage() );
		} else {
			$durbality = floor( ( mt_rand(3, 6) / 10 ) * $item->getMaxDamage() );
		}
	}
	
	$player->getInventory()->addItemStack( new ItemStack( $item, null, null, $durbality ) );
	$playerGarage = $player->getPlayerGarage( $player->getCurrentGarageId() );
	
	$playerNick = '$fff' . $player->getPlayerNick();
	if ( $playerGarage->getCar() != null ) {
		$neonStack = $playerGarage->getNeonItemStack();
		
		if ( $neonStack != null ) {
			$stack = $neonStack->getItem();
			$playerNick = $stack->paintNick( $player->getPlayerNick() );
		}
	}
	
	echo '$ff0>> Gracz '. $playerNick . '$ff0 dostał ' . TXP::$RARITES[$item->getRarityId()] . $item->getName() . '!';
}

?>