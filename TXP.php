<?php

require('car.php');
require('carstats.php');
require('player.php');
require('modification.php');

require('items/item.php');
require('database/database.php');
require('effects.php');

final class TXP {
	
	const HOME_URL = 'http://txp.boo.pl/';
	
	public static $RARITES = array('$666', '$cdd', '$3b1', '$36f', '$73f', '$e2f', '$f44', '$fc2', '$f80');
	public static $CAR_RARITIES = array('$555', '$cc8', '$fa0', '$d12', '$0c0', '$0ef', '$f0f', '$a0f');
	
	private static $isInitalized;
	private static $database;
	
	public static function registerPlayer($playerLogin) {
		$player = self::getPlayer($playerLogin);
		
		if($player === null) {
			
			self::$database->registerPlayerInfo($playerLogin);
			$player = self::getPlayer($playerLogin);
			
			if($player === null) {
				return false;	
			}	
					
			self::$database->registerInventory($player->getPlayerId());
			self::$database->registerGarage($player->getPlayerId(), 0);
			
			return true;
		}
		
		return false;
	}
	
	public static function getCar($carId) {
		if ( self::$isInitalized && $carId != 0 ) {
			return new Car($carId, self::$database);
		}
		return null;
	}
	
	public static function getItem($itemId) {
		if( self::$isInitalized && $itemId != 0 ) {
			return Item::createItem($itemId, self::$database);
		}
		return null;
	}
	
	public static function getPlayer($playerLogin) {
		if( self::$isInitalized && self::$database->playerExists ( $playerLogin ) ) {
			return new Player($playerLogin, self::$database);
		}
		return null;
	}
	
	public static function getModification($modificationId, $modificationValue) {
		if ( self::$isInitalized && $modificationId != 0 ) {
			return new Modification($modificationId, $modificationValue, self::$database);
		}
		return null;
	}
	
	public static function initalize() {
		self::$database = new Database();
		self::$isInitalized = true;
	}
	
	public static function isInitalized() {
		return self::$isInitalized;
	}
	
	public static function formatCash($cash) {
		$result = null;
		$cash = strval($cash);
		
		$dotCount = floor((strlen($cash) - 1) / 3);
		$dotPlaced = 0;
		
		if($dotCount < 1) {
			return $cash;
		}
		
		for($i = 0; $i < strlen($cash); $i++) {
			$placeDotAt = (strlen($cash) - ((3 * ($dotCount - $dotPlaced))));
			if($i == $placeDotAt) {
				$result .= '.';
				$dotPlaced += 1;
			}
			$result .= $cash[$i];
		}
		
		return $result;
	}
	
	public static function formatTime($time, $timeFormattingMode = false) {
		if ( $timeFormattingMode ) {
			/** Kod lekko zmodyfikowany i zapożyczony z biblioteki ASECO! */
			$milis = substr($time, strlen($time)-3, 2);
			$time = substr($time, 0, strlen($time)-3);
			$hours = floor($time / 3600);
			$time = $time - ($hours * 3600);
			$minutes = floor($time / 60);
			$time = $time - ($minutes * 60);
			$seconds = floor($time);
			return sprintf('%d:%02d.%02d', $minutes, $seconds, $milis);
		} else {
			$time = strval($time);
			$altTime = '';
			
			for ( $i = 0; $i < strlen($time); $i++ ) {
				if ( $time[$i] == '.' ) {
					$altTime .= $time[$i] . $time[$i + 1];
					break;
				}
				$altTime .= $time[$i];
			}
			
			return $altTime;
		}
		return null;
	}
	
	public static function changeSession($sessionState) {
		if ( self::$isInitalized ) {
			self::$database->changeSession($sessionState);
		}
	}
	
	public static function getSessionState() {
		if ( self::$isInitalized ) {
			return self::$database->getSessionState();
		}
		return -1;
	}
	
	public static function getFirstNewsTitle() {
		if ( self::$isInitalized ) {
			return self::$database->getFirstNewsTitle();
		}
		return '$f00Bład przy wczytywaniu pierwszego nagłówka!';
	}
	
	public static function getFirstNews() {
		if ( self::$isInitalized ) {
			return self::$database->getFirstNews();
		}
		return '$f00Bład przy wczytywaniu pierwszej wiadomości!';		
	}
	
	public static function getSecondNewsTitle() {
		if ( self::$isInitalized ) {
			return self::$database->getSecondNewsTitle();
		}
		return '$f00Bład przy wczytywaniu drugiego nagłówka!';
	}
	
	public static function getSecondNews() {
		if ( self::$isInitalized ) {
			return self::$database->getSecondNews();
		}
		return '$f00Bład przy wczytywaniu drugiej wiadomości!';
	}
	
	public static function getRankings() {
		if ( self::$isInitalized ) {
			return self::$database->getRankings();
		}
		return null;
	}
	
	public static function getModificationRanges( $modificationId ) {
		if ( self::$isInitalized ) {
			return new ModificationRanges($modificationId, self::$database);
		}
		return null;
	}
	
	public static function getLoginViaUID($uid) {
		if ( self::$isInitalized ) {
			return self::$database->getLoginViaUId($uid);
		}
		return null;
	}

}

TXP::initalize();

?>