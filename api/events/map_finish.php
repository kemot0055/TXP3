<?php

require('../../TXP.php');
error_reporting(0);

if ( array_key_exists("requestInfo", $_GET) ) {
	$requestInfo = explode(':', $_GET["requestInfo"]);
	
	if ( sizeof ( $requestInfo ) != 5 ) {
		die;
	}
	
	$playersFinished = $requestInfo[0];
	$authorTime = $requestInfo[1];
	$authorLogin = $requestInfo[2];
	$serverGamemode = $requestInfo[3];
	$currentMultipiler = $requestInfo[4];
	
	function consumePart( PlayerGarage $playerGarage, ItemStack $itemStack = null ) {
		if ( $itemStack != null ) {
			$item = $itemStack->getItem();
			$itemStack->setItemDurbality( $itemStack->getDurbality() - 1 );
			
			if ( $itemStack->getDurbality() <= 0 ) {
				$itemStack = null;
			}
			
			CarUtils::attachPart($playerGarage, $itemStack, $item->getPartType());
		}
	}
	
	$logPointer = fopen('../../finish_events.log', 'a');
	fwrite($logPointer, date('[Y-m-d, H:i:s]') . ' Przyjęcie requestu z liczbą graczy: ' . $playersFinished . PHP_EOL);
	
	$playersInfo = array();
	for ( $i = 0; $i < $playersFinished; $i++ ) {
		$playerInfo = explode(':', $_GET[("playerData" . $i)]);
		
		$player = TXP::getPlayer($playerInfo[0]);
		$playerTime = $playerInfo[1];
		$playerScore = $playerInfo[2];
		
		$playerGarage = $player->getPlayerGarage( $player->getCurrentGarageId() );
		$playerCar = $playerGarage->getCar();
		
		if ( $playerCar != null ) {		
			$playerCapabilties = new PlayerCapabilites();
			
			$engine = $playerGarage->getEngineItemStack();
			$gearbox = $playerGarage->getGearboxItemStack();
			$suspension = $playerGarage->getSuspensionItemStack();
			$handling = $playerGarage->getHandlingItemStack();
			$tyres = $playerGarage->getTyresItemStack();
			$turbo = $playerGarage->getTurboItemStack();
			$firstAddon = $playerGarage->getFirstAddonItemStack();
			$secondAddon = $playerGarage->getSecondAddonItemStack();
			
			$playerCapabilties->manageItemStack($engine);
			$playerCapabilties->manageItemStack($gearbox);
			$playerCapabilties->manageItemStack($suspension);
			$playerCapabilties->manageItemStack($handling);
			$playerCapabilties->manageItemStack($tyres);
			$playerCapabilties->manageItemStack($turbo);
			$playerCapabilties->manageItemStack($firstAddon);
			$playerCapabilties->manageItemStack($secondAddon);
			
			consumePart($playerGarage, $engine);
			consumePart($playerGarage, $gearbox);
			consumePart($playerGarage, $suspension);
			consumePart($playerGarage, $handling);
			consumePart($playerGarage, $tyres);
			consumePart($playerGarage, $turbo);
			
			$acceleration = $playerCar->getBaseAcceleration() + $playerCapabilties->partAcceleration;
			$maxSpeed = $playerCar->getBaseMaxSpeed() + $playerCapabilties->partMaxSpeed;
			$steering = $playerCar->getBaseSteering() + $playerCapabilties->partSteering;
			
			$average = ( ( $acceleration + $maxSpeed + $steering ) / 3 );
			$playerTime += (CarStats::getCarTime($steering, $acceleration, $maxSpeed) * 1000);
			
			$playersInfo[$i][0] = $player;
			$playersInfo[$i][1] = $playerCapabilties;
			$playersInfo[$i][2] = $playerTime;
			$playersInfo[$i][3] = $average;
			$playersInfo[$i][4] = $playerScore;
		} else {
			$playersInfo[$i][0] = $player->getPlayerNick();
		}
	}
	
	function sortTimes($valueA, $valueB) {
		$timeA = $valueA[2];
		$timeB = $valueB[2];

		if ( $timeA == -1 || $timeB == -666 ) {
			return 0;
		}
		if ( $timeA > $timeB ) {
			return 1;
		}
	}
	usort($playersInfo, 'sortTimes');
	
	fwrite($logPointer, date('[Y-m-d, H:i:s]') . ' Posortowano graczy pomyślnie wg. czasu!' . PHP_EOL);
	
	$trackTXP = (log( ($authorTime / 1000) ) * ($playersFinished * 4.5));
	$sessionState = TXP::getSessionState();
	if ( $sessionState == 0 ) {
		$txpSessionMultipiler = 0.5;
		$vrSessionMultipiler = 0.25;
	} else {
		$txpSessionMultipiler = 1.05;
		$vrSessionMultipiler = 1.25;
	}
	
	for ( $i = 0; $i < $playersFinished; $i++ ) {
		$player = $playersInfo[$i][0];
		$playerCapabilties = $playersInfo[$i][1];
		$playerTime = $playersInfo[$i][2];
		$carAverage = $playersInfo[$i][3];
		
		if ( $player instanceof Player ) {
			$timeDiff = ($authorTime / $playerTime);
			if ( $timeDiff >= 4 ) {
				$timeDiff = 4;
			} else if ( $timeDiff < 0.5 ) {
				$timeDiff = 0.5;
			}
			$averageMult = (1 + ($carAverage / 1000));
			
			$txp = floor ( ( ( $trackTXP * ( ( $playersFinished - $i ) / $playersFinished ) ) * $timeDiff ) ); 
			$txp = floor ( $txp * $averageMult );
			$txp = floor ( $txp * $currentMultipiler );
			$txp = floor ( $txp * $txpSessionMultipiler);
			
			$txpBoost = ( 100 + $playerCapabilties->txpBoost ) / 100;
			$txp = floor ( $txp * $txpBoost);
			
			$txpBonus = $playerCapabilties->txpBonus;
			$txp = floor ( $txp + $txpBonus );
			
			$txpPercBonus = $playerCapabilties->txpPercentageBonus;
			$txp = floor ( $txp + ( ( $txp * $txpPercBonus ) / 100 ) );
			
			$vr = floor( ( ( $txp / 3 ) * ( $player->getLevel() / 1.75 ) ) * $vrSessionMultipiler );
			
			$vrBoost = ( 100 + $playerCapabilties->vrBoost ) / 100;
			$vr = floor ( $vr * $vrBoost );
			
			$vrBonus = $playerCapabilties->vrBonus;
			$vr = floor ( $vr + $vrBonus );
			
			$vrPercBonus = $playerCapabilties->vrPercentageBonus;
			$vr = floor ( $vr + ( ( $vr * $vrPercBonus ) / 100 ) );
			
			$player->addTxp($txp);
			$player->addCash($vr);
			
			if ( $playersFinished > 1 && $sessionState != 0 ) {
				if ( $i == 0 ) {
					$player->addCasualWin();
				} else {
					$player->addCasualLoss();
				}
			}
			
			$playerGarage = $player->getPlayerGarage( $player->getCurrentGarageId() );
			
			$playerNick = '$fff' . $player->getPlayerNick();
			if ( $playerGarage->getCar() != null ) {
				$neonStack = $playerGarage->getNeonItemStack();
			
				if ( $neonStack != null ) {
					$stack = $neonStack->getItem();
					$playerNick = $stack->paintNick( $player->getPlayerNick() );
				}
			}

			fwrite($logPointer, date('[Y-m-d, H:i:s]') . ' Przyznano graczowi ['. $player->getLogin() . ', ' . $player->getPlayerNick() .'] następujące punkty za miejsce [' . ($i + 1) . '] TXP: ' . $txp . ', VR: ' . $vr . PHP_EOL);
			echo '$ff0>> Gracz ' . $playerNick . ' $ff0ukończył wyścig na ' . ($i + 1) . ' pozycji z czasem: $fff' . TXP::formatTime($playerTime, true) . ' $ff0i dostał $fff'. $txp . ' $ff0TXP oraz $fff' . $vr . ' $ff0VR!' . ( ($i == ( $playersFinished - 1 )) ? '' : PHP_EOL );			
		} else {
			echo '$ff0>> Gracz $888' . $player . ' $ff0nie posiada samochodu i nie dostanie TXP oraz VR!' . ( ($i == ( $playersFinished - 1 )) ? '' : PHP_EOL );
		}
	}

	fwrite($logPointer, PHP_EOL);
	fclose($logPointer);
}

?>