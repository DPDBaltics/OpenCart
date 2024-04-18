<?php
class ModelExtensionhippingDpdParcel extends Model {
	
	public function getTerminals($country_id, $city) {
    // public function getTerminals($country_id) {
		$country_code = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE country_id = ". $country_id);
		$country_code = $country_code->row['iso_code_2'];
		
    	$terminals = $this->db->query("SELECT * FROM " . DB_PREFIX . "terminals_list WHERE country = '". $country_code ."' AND LOWER(city) LIKE '%" . mb_strtolower($city) ."%'");
        // $terminals = $this->db->query("SELECT * FROM " . DB_PREFIX . "terminals_list WHERE country = '". $country_code ."'");

    	return $terminals;
    }

    public function setOrderTerminal($order_id, $code) {
        $query = "UPDATE `" . DB_PREFIX . "order` SET shipping_parcel_id = '" . $this->db->escape($code) . "' WHERE order_id = '" . (int)$order_id . "'";
        
    	return $this->db->query($query);
    }
}