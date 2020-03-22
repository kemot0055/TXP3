<?php

require('varinfo.php');

class Database {
	
	private $mysqli;
	
	public function __construct() {
		if ( TXP::HOME_URL == "http://localhost/") {
			$this->mysqli = new mysqli('localhost', 'root', '', 'txp');
		} else {
			$this->mysqli = new mysqli('txp.boo.pl', 'kemot0055_txp', 'OaHSe2rNRo', 'kemot0055_txp');
		}
		$this->mysqli->set_charset('utf8');
	}
	
	public function getLoginViaUId($uid) {
		$result = $this->mysqli->query('
			SELECT player_login FROM txp_players
			WHERE player_id = ' . $uid . '
		');
		
		if ( $result == null ) {
			return null;
		}
		
		return $result->fetch_row()[0];
	}
	
	public function getVarInfoFromPlayer($playerLogin, $varName) {
		$result = $this->mysqli->query('
			SELECT ' . $varName . '
			FROM txp_players
			WHERE player_login = "' . $playerLogin . '"
		');
		
		if( $result == null ) {
			return null;
		}
		
		return new VarInfo($varName, $result->fetch_row()[0]);
	}
	
	public function getVarInfoFromItem($itemId, $varName) {
		$result = $this->mysqli->query('
			SELECT ' . $varName . '
			FROM txp_items
			WHERE item_id = ' . $itemId . '
		');
		
		if( $result == null ) {
			return null;
		}
		
		return new VarInfo($varName, $result->fetch_row()[0]);
	}
	
	public function getVarInfoFromCar($carId, $varName) {
		$result = $this->mysqli->query('
			SELECT ' . $varName . '
			FROM txp_cars
			WHERE car_id = ' . $carId . '
		');
		
		if( $result == null ) {
			return null;
		}
		
		return new VarInfo($varName, $result->fetch_row()[0]);		
	}
	
	public function getVarInfoFromModification($modId, $varName) {
		$result = $this->mysqli->query('
			SELECT ' . $varName . '
			FROM txp_modificators
			WHERE mod_id = ' . $modId . '
		');
		
		if( $result == null ) {
			return null;
		}
		
		return new VarInfo($varName, $result->fetch_row()[0]);		
	}
	
	public function updatePlayerVarInfo($playerId, VarInfo $varInfo = null) {
		if( $varInfo == null ) {
			return false;
		}
		
		$result = $this->mysqli->query('
			UPDATE txp_players
			SET ' . $varInfo->getName() . ' = ' . ($varInfo->isString() ? ( '"' .  $varInfo->getValue() . '"' ) : $varInfo->getValue()) . '
			WHERE player_id = ' . $playerId . '
		');
	}
	
	public function getVarInfoFromGarage($playerId, $garageId, $varName) {
		$result = $this->mysqli->query('
			SELECT ' . $varName . '
			FROM txp_garage
			WHERE garage_id = ' . $garageId . '
			AND player_id = ' . $playerId . '
		');
		
		if ( $result == null ) {
			return null;
		}
		
		return new VarInfo($varName, $result->fetch_row()[0]);
	}
	
	public function updateGarageVarInfo($playerId, $garageId, VarInfo $varInfo = null) {
		if( $varInfo == null ) {
			return false;
		}
		
		$result = $this->mysqli->query('
			UPDATE txp_garage
			SET ' . $varInfo->getName() . ' = ' . ($varInfo->isString() ? ( '"' .  $varInfo->getValue() . '"' ) : $varInfo->getValue()) . '
			WHERE garage_id = ' . $garageId . '
			AND player_id = ' . $playerId . '
		');
	}
	
	public function getPlayerGaragesCount($playerId) {
		$result = $this->mysqli->query('
			SELECT COUNT(1)
			FROM txp_garage
			WHERE player_id = ' . $playerId . '
		');
		
		if( $result == null ) {
			return false;
		}
		
		return $result->fetch_row()[0];
	}
	
	public function playerExists($playerLogin) {
		$result = $this->mysqli->query('
			SELECT COUNT(1)
			FROM txp_players
			WHERE player_login = "' . $playerLogin . '"
		');
		
		if( $result === null ) {
			return false;
		}
		
		return $result->fetch_row()[0];
	}
	
	public function registerPlayerInfo($playerLogin) {
		$this->mysqli->query('
			INSERT INTO txp_players		
			(player_login, player_name, player_cash)
			VALUES ("' . $playerLogin . '", "' . $playerLogin . '", 500)
		');
	}
	
	public function registerInventory($playerId) {
		for($slotId = 1; $slotId < 101; $slotId++) {
			$this->mysqli->query('
				INSERT INTO txp_inventory
				(player_id, slot_id, item_id, item_damage)
				VALUES (' . $playerId . ', ' . $slotId . ', 0, 0)
			');
		}
	}
	
	public function registerGarage($playerId, $garageId) {
		$this->mysqli->query('
			INSERT INTO txp_garage
			(garage_id, player_id)
			VALUES (' . $garageId . ', ' . $playerId . ')
		');
	}
	
	public function getItemIdFromPlayerInventory($playerId, $slotId) {
		$result = $this->mysqli->query('
			SELECT item_id FROM txp_inventory
			WHERE player_id = ' . $playerId . '
			AND slot_id = ' . $slotId . '
		');
		
		if( $result == null ) {
			return null;
		}
		
		return $result->fetch_row()[0];
	}
	
	public function getItemDamageFromPlayerInventory($playerId, $slotId) {
		$result = $this->mysqli->query('
			SELECT item_damage FROM txp_inventory
			WHERE player_id = ' . $playerId . '
			AND slot_id = ' . $slotId . '
		');
		
		if( $result == null ) {
			return null;
		}
		
		return $result->fetch_row()[0];
	}
	
	public function getItemModFromPlayerInventory($playerId, $slotId, $itemModSlot) {
		$result = $this->mysqli->query('
			SELECT item_mod' . ( $itemModSlot + 1 ) . ' FROM txp_inventory
			WHERE player_id = ' . $playerId . '
			AND slot_id = ' . $slotId . '
		');
		
		if( $result == null ) {
			return null;
		}
		
		return $result->fetch_row()[0];
	}
	
	public function getItemModValueFromPlayerInventory($playerId, $slotId, $itemModSlot) {
		$result = $this->mysqli->query('
			SELECT item_mod_value' . ( $itemModSlot + 1 ) . ' FROM txp_inventory
			WHERE player_id = ' . $playerId . '
			AND slot_id = ' . $slotId . '
		');
	
		if( $result == null ) {
			return null;
		}
	
		return $result->fetch_row()[0];
	}
	
	public function setItemInPlayerInventory($playerId, $slotId, $itemId) {
		$this->mysqli->query('
			UPDATE txp_inventory
			SET item_id = ' . $itemId . '
			WHERE player_id = ' . $playerId . '
			AND slot_id = ' . $slotId . '
		');
	}
	
	public function setItemDamageInPlayerInventory($playerId, $slotId, $itemDamage) {
		$this->mysqli->query('
			UPDATE txp_inventory
			SET item_damage = ' . $itemDamage . '
			WHERE player_id = ' . $playerId . '
			AND slot_id = ' . $slotId . '
		');
	}
	
	public function setItemModInPlayerInventory($playerId, $slotId, $itemModSlot, $modId, $modValue) {
		$this->mysqli->query('
			UPDATE txp_inventory SET 
			item_mod' . ( $itemModSlot + 1 ) . ' = ' . $modId . ',
			item_mod_value' . ( $itemModSlot + 1 ) . ' = ' . $modValue . '
			WHERE player_id = ' . $playerId . '
			AND slot_id = ' . $slotId . '
		');
	}
	
	public function getFreeSlotInPlayerInventory($playerId) {
		$result = $this->mysqli->query('
			SELECT slot_id FROM txp_inventory
			WHERE player_id = ' . $playerId . ' AND item_id = 0
			LIMIT 1
		');
		
		if( $result == null ) {
			return -1;
		}
		
		return $result->fetch_row()[0];
	}
	
	public function getCarsSize() {
		$result = $this->mysqli->query('
			SELECT COUNT(*) FROM txp_cars
		');
		
		if ( $result == null ) {
			return -1;
		}
		
		return $result->fetch_row()[0];
	}
	
	public function getPlayerCarsCount($playerId) {
		$result = $this->mysqli->query('
			SELECT COUNT(*) FROM txp_garage WHERE player_id = ' . $playerId . ' AND car_id != 0
		');
		
		if ( $result == null ) {
			return -1;
		}
		
		return $result->fetch_row()[0];
	}
	
	public function getPlayerEmptyGarages($playerId) {
		$result = $this->mysqli->query('
			SELECT garage_id FROM `txp_garage` WHERE player_id = ' . $playerId . ' AND car_id = 0 
		');
		
		if ( $result == null ) {
			return -1;
		}
		
		return $result->fetch_row()[0];
	}
	
	public function getItemClass($itemId) {
		$result = $this->mysqli->query('
			SELECT item_class FROM txp_items
			WHERE item_id = ' . $itemId . '
		');
		
		if( $result == null ) {
			return null;
		}
		
		return $result->fetch_row()[0];
	}
	
	public function changeSession($sessionState) {
		$result = $this->mysqli->query('
			UPDATE txp_global
			SET is_session = ' . $sessionState . '
		');
	}
	
	public function getSessionState() {
		$result = $this->mysqli->query('
			SELECT is_session FROM txp_global
		');
		
		if ( $result == null ) {
			return null;
		}
		
		return $result->fetch_row()[0];
	}
	
	public function getFirstNews() {
		$result = $this->mysqli->query('
			SELECT news_content1 FROM txp_global
		');
		
		if ( $result == null ) {
			return '$f00Błąd przy wczytywaniu pierwszej wiadomości!';
		}
		
		return $result->fetch_row()[0];
	}
	
	public function getFirstNewsTitle() {
		$result = $this->mysqli->query('
			SELECT news_head1 FROM txp_global
		');
		
		if ( $result == null ) {
			return '$f00Błąd przy wczytywaniu pierwszego nagłówka!';
		}
		
		return $result->fetch_row()[0];
	}
	
	public function getSecondNews() {
		$result = $this->mysqli->query('
			SELECT news_content2 FROM txp_global
		');
		
		if ( $result == null ) {
			return '$f00Błąd przy wczytywaniu drugiej wiadomości!';
		}
		
		return $result->fetch_row()[0];
	}
	
	public function getSecondNewsTitle() {
		$result = $this->mysqli->query('
			SELECT news_head2 FROM txp_global
		');
		
		if ( $result == null ) {
			return '$f00Błąd przy wczytywaniu drugiego nagłówka!';
		}
		
		return $result->fetch_row()[0];
	}
	
	public function getRankings() {
		$result = $this->mysqli->query('
			SELECT player_login FROM txp_players ORDER BY player_txp DESC LIMIT 18
		');
		
		if ( $result == null ) {
			return null;
		}
				
		return $result->fetch_all( MYSQLI_NUM );
	}
	
	public function getModificationRange($rangeType, $modificationId) {
		$result = $this->mysqli->query('
			SELECT ' . ( $rangeType == ModificationRanges::RANGE_LOW ? 'mod_range_low' : 'mod_range_high' ) . ' 
			FROM txp_modificators WHERE mod_id = ' . $modificationId . '
		');
		
		if ( $result == null ) {
			return null;
		}
		
		return $result->fetch_row()[0];
	}
	
}

?>