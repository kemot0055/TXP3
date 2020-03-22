<?php

require('../TXP.php');

TXP::initalize();

$id = 1;
$login = TXP::getLoginViaUID($id); 
$player = TXP::getPlayer($login);

function dupga(PlayerGarage $garage, ItemStack $stack = null) {
	if ( $stack != null ) {
		$item = $stack->getItem();
		$type = $item->getPartType();
		$maxDmg = $item->getMaxDamage();
		
		$itemRealDmg = floor ( $maxDmg * $stack->getDurbality() );
		$stack->setItemDurbality( $itemRealDmg );
		
		CarUtils::attachPart($garage, $stack, $type);
	}
}

while ( $player != null ) {
	echo $login . '</br>';
	/* $inventory = $player->getInventory();
	echo $login . '</br>';
	
	for ( $i = 1; $i <= 100; $i++ ) {
		$itemStack = $inventory->getItemStack($i);
		if ( $itemStack != null ) {		
			$item = $itemStack->getItem();
			$itemDamage = $itemStack->getDurbality();
			
			$itemRealDamage = floor( $item->getMaxDamage() * $itemDamage );
			$itemStack->setItemDurbality( $itemRealDamage );
			
			$inventory->setItemStack($itemStack, $i);
		}
	} */
	
	$garage = $player->getPlayerGarage($player->getCurrentGarageId());
	$carId = $garage->getCarId();
	
	if ( $carId != 0 ) {
		
		dupga($garage, $garage->getEngineItemStack());
		dupga($garage, $garage->getGearboxItemStack());
		dupga($garage, $garage->getSuspensionItemStack());
		dupga($garage, $garage->getHandlingItemStack());
		dupga($garage, $garage->getTyresItemStack());
		dupga($garage, $garage->getTurboItemStack());
		
	}
	
	$id++;
	$login = TXP::getLoginViaUID($id);
	$player = TXP::getPlayer($login);
}

?>