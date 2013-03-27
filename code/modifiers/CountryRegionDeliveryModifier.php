<?php

class CountryRegionDeliveryModifier extends PickUpOrDeliveryModifier {

	protected static $check_options_count_to_show_form = true;
	static function set_check_options_count_to_show_form($value) {self::$check_options_count_to_show_form = $value;}

	public function ShowForm() {
		$show = parent::ShowForm();
		if(self::$check_options_count_to_show_form) {
			$options = $this->LiveOptions();
			$show = $show && $options->Count() > 1;
		}
		return $show;
	}

	/**
	 * Returns the available delivery options based on the current order country and region settings.
	 * @return DataObjectSet
	 */
	protected function LiveOptions() {
		$countryID = EcommerceCountry::get_country_id();
		$regionID = EcommerceRegion::get_region();

		$options = DataObject::get('PickUpOrDeliveryModifierOptions');
		if($options) {
			foreach($options as $option) {

				if($countryID) {
					$optionCountries = $option->AvailableInCountries();
					if(! $optionCountries->find('ID', $countryID)) { // Invalid
						continue;
					}
				}

				if($regionID) {
					$optionRegions = $option->AvailableInRegions();
					if(! $optionRegions->find('ID', $regionID)) { // Invalid
						continue;
					}
				}

				$result[] = $option;
			}
		}

		if(! isset($result)) {
			$result[] = PickUpOrDeliveryModifierOptions::default_object();
		}
		return new DataObjectSet($result);
	}


	function TableSubTitle() {return $this->getTableSubTitle();}
	function getTableSubTitle() {
		return $this->RegionAndCountry;
	}

}
