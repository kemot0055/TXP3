<?php

final class CarStats {
	
	public static function getCarTime($carSteering, $carAcceleration, $carMaxSpeed) {
		$timeSteering = ( ( 9 * $carSteering ) / 1000 );
		$timeAcceleration = ( ( 6 * $carAcceleration ) / 1000 );
		$timeMaxSpeed = ( ( 3 * $carMaxSpeed ) / 1000 );
		return (12 - $timeSteering - $timeAcceleration - $timeMaxSpeed);
	}
	
}

final class CarUtils {
	
	public static function attachPart(PlayerGarage $playerGarage, ItemStack $itemStack = null, $partType) {
		switch ( $partType ) {
			case CarPart::TYPE_ENGINE : $playerGarage->attachEngine($itemStack); break;
			case CarPart::TYPE_GEARBOX : $playerGarage->attachGearbox($itemStack); break;
			case CarPart::TYPE_SUSPENSION : $playerGarage->attachSuspension($itemStack); break;
			case CarPart::TYPE_HANDLING : $playerGarage->attachHandling($itemStack); break;
			case CarPart::TYPE_TYRES : $playerGarage->attachTyres($itemStack); break;
			case CarPart::TYPE_TURBO : $playerGarage->attachTurbo($itemStack); break;
			case CarPart::TYPE_FIRST_ADDITIONAL : $playerGarage->attachFirstAdditional($itemStack); break;
			case CarPart::TYPE_SECOND_ADDITIONAL : $playerGarage->attachSecondAdditional($itemStack); break;
			case CarPart::TYPE_NEON : $playerGarage->attachNeon($itemStack); break;
		}
	}
	
}

?>