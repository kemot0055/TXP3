<?php 

final class Effects {
	
	const PART_ACCELERATION = 0;
	const PART_MAX_SPEED = 1;
	const PART_STEERING = 2;
	
	const TXP_BOOST = 3;
	const VR_BOOST = 4;
	
	const TXP_BONUS = 5;
	const TXP_PERCENTAGE_BONUS = 6;
	const TXP_DESC_POSITION_BONUS = 7;
	
	const VR_BONUS = 8;
	const VR_PERCENTAGE_BONUS = 9;
	const VR_DESC_POSITION_BONUS = 10;
	
	const ITEM_DROP_BETTER_QUALITY_CHANCE = 11;
	const FINISH_EVENT_ITEM_DROP_CHANCE = 12;
	const FINISH_EVENT_ITEM_DROP_BETTER_QUALITY_CHANCE = 13;

	const PU_STRENGTH = 14;
	const PU_DURBALITY = 15;
	
	public static function getEffectDescription( $effectID, $effectValue ) {
		switch ( $effectID ) {
			case Effects::TXP_BOOST : return 'Zwiększony boost TXP o ' . $effectValue . '%';
			case Effects::VR_BOOST : return 'Zwiększony boost VR o ' . $effectValue . '%';
			
			case Effects::TXP_BONUS : return '+' . $effectValue . ' do TXP';
			case Effects::TXP_PERCENTAGE_BONUS : return '+' . $effectValue . '% do TXP';
			case Effects::TXP_DESC_POSITION_BONUS : return '';
			
			case Effects::VR_BONUS : return '+' . $effectValue . ' do VR';
			case Effects::VR_PERCENTAGE_BONUS : return '+' . $effectValue . '% do VR';
			case Effects::VR_DESC_POSITION_BONUS : return '';
			
			case Effects::FINISH_EVENT_ITEM_DROP_CHANCE : return '';
			case Effects::FINISH_EVENT_ITEM_DROP_BETTER_QUALITY_CHANCE : return '';
			case Effects::ITEM_DROP_BETTER_QUALITY_CHANCE : return '';
			
			case Effects::PU_STRENGTH : return '';
			case Effects::PU_DURBALITY : return '';
		}
	}
	
}

?>