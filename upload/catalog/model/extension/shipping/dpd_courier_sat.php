<?php
class ModelExtensionShippingDpdCourierSat extends Model {
    public function getQuote($address) {
        $this->load->language('extension/shipping/dpd_courier');

        $quote_data = array();

        $status = $this->config->get('shipping_dpd_courier_sat_status');

        $status_per_day = $this->config->get('shipping_dpd_courier_sat_weekdays');

        $current_day = strtolower(date('D'));

        $mon = $this->config->get('shipping_dpd_courier_sat_mon_pickup');
        $tue = $this->config->get('shipping_dpd_courier_sat_tue_pickup');
        $wed = $this->config->get('shipping_dpd_courier_sat_wed_pickup');
        $thu = $this->config->get('shipping_dpd_courier_sat_thu_pickup');
        $fri = $this->config->get('shipping_dpd_courier_sat_fri_pickup');

        // Shipping method turn of depend of week day
        if (empty($status_per_day)) {
            $status = false;
        } else {
            if (!in_array($current_day, $status_per_day)) {
                $status = false;
            } else {
                $status = false;

                switch ($current_day) {
                    case 'mon':
                        if ($this->is_between_times($mon[1]['from'], $mon[1]['until'])) {
                            $status = true;
                        }

                        if ($this->is_between_times($mon[2]['from'], $mon[2]['until'])) {
                            $status = true;
                        }

                        break;
                    case 'tue':
                        if ($this->is_between_times($tue[1]['from'], $tue[1]['until'])) {
                            $status = true;
                        }

                        if ($this->is_between_times($tue[2]['from'], $tue[2]['until'])) {
                            $status = true;
                        }

                        break;
                    case 'wed':
                        if ($this->is_between_times($wed[1]['from'], $wed[1]['until'])) {
                            $status = true;
                        }

                        if ($this->is_between_times($wed[2]['from'], $wed[2]['until'])) {
                            $status = true;
                        }

                        break;
                    case 'thu':
                        if ($this->is_between_times($thu[1]['from'], $thu[1]['until'])) {
                            $status = true;
                        }

                        if ($this->is_between_times($thu[2]['from'], $thu[2]['until'])) {
                            $status = true;
                        }

                        break;
                    case 'fri':
                        if ($this->is_between_times($fri[1]['from'], $fri[1]['until'])) {
                            $status = true;
                        }

                        if ($this->is_between_times($fri[2]['from'], $fri[2]['until'])) {
                            $status = true;
                        }

                        break;

                    default:

                        break;
                }
            }
        }

        // If status per day is enabled
        if ($status) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name");
        
            foreach ($query->rows as $result) {
                if ($this->config->get('shipping_dpd_courier_sat_' . $result['geo_zone_id'] . '_status')) {
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$result['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

                    if ($query->num_rows) {
                        $geo_status = true;
                    } else {
                        $geo_status = false;
                    }
                } else {
                    $geo_status = false;
                }


                if ($geo_status) {
                    $cost = '';
                    $weight = $this->cart->getWeight();
                    $itemsNo = $this->cart->countProducts();
                    $priceCalculationMethod = $this->config->get('dpd_setting_price_calculation');

                    // If price calculation by weight
                    if ($priceCalculationMethod == 'weight') {
                        // Get rates
                        $rates = explode(',', $this->config->get('shipping_dpd_courier_sat_' . $result['geo_zone_id'] . '_rate'));

                        foreach ($rates as $rate) {
                            $data = explode(':', $rate);

                            if ($data[0] >= $weight) {
                                if (isset($data[1])) {
                                    $cost = str_replace(',', '.', $data[1]);
                                }

                                break;
                            }
                        };
                    } else if ($priceCalculationMethod == 'item') {
                        $cost = $itemsNo * str_replace(',', '.', $this->config->get('shipping_dpd_courier_sat_' . $result['geo_zone_id'] . '_price'));


                    } else {
                        $cost = str_replace(',', '.', $this->config->get('shipping_dpd_courier_sat_' . $result['geo_zone_id'] . '_price'));


                    }



                    // Check can we apply free shipping
                    if ((int)$this->config->get('shipping_dpd_courier_sat_' . $result['geo_zone_id'] . '_free_shipping_from') != '') {
                        if ($this->cart->getSubTotal() > $this->config->get('shipping_dpd_courier_sat_' . $result['geo_zone_id'] . '_free_shipping_from')) {
                            $cost = 0;
                        }
                    }

                    // If it's free shipping, show different data
                    if ($cost == 0) {
                        $title = '. ' . $this->language->get('text_free_shipping');
                        $text = $this->currency->format(0, $this->session->data['currency']);
                    } else {
                        // $title = $result['name'];
                        $title = '';

                        $text = $this->currency->format($this->tax->calculate($cost, $this->config->get('shipping_dpd_courier_sat_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency']);
                    }

                    // Show wheight on shipping title
                    if ($this->config->get('config_cart_weight') == 1) {
                        $weight = '  (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class_id')) . ')';
                    } else {
                        $weight = '';
                    }

                    if ((string)$cost != '') {
                        $quote_data['dpd_courier_sat'] = array(
                            'code'         => 'dpd_courier_sat.dpd_courier_sat',
                            'title'        => $this->language->get('text_additional_shipping_title') . $title . ' '. $result['name'] . $weight,
                            'cost'         => $cost,
                            'tax_class_id' => $this->config->get('shipping_dpd_courier_sat_tax_class_id'),
                            'text'         => $text
                        );
                    }
                }
            }
        }


        // Shipping method
        if (isset($this->session->data['language']) && $this->session->data['language']) {
            $lccode = $this->session->data['language'];
        } else {
            $lccode = $this->config->get('config_language');
        }
        
        // Shipping method title
        $shipping_title = $this->config->get('shipping_dpd_courier_sat_title');
        
        if (isset($shipping_title[$lccode]) && $shipping_title[$lccode] != '') {
            $shipping_method_title = $this->language->get('text_title_logo') . ' ' . $shipping_title[$lccode];
        } else {
            $shipping_method_title = $this->language->get('text_title_sat');
        }

        $method_data = array();

        if ($quote_data) {
            $method_data = array(
                'code'       => 'dpd_courier_sat',
                'title'      => $shipping_method_title,
                'quote'      => $quote_data,
                'sort_order' => $this->config->get('shipping_dpd_courier_sat_sort_order'),
                'error'      => false
            );
        }

        return $method_data;
    }

    private function is_between_times( $start = null, $end = null ) {
        date_default_timezone_set('Europe/Vilnius');

        if ( $start == null ) $start = '00:00';
        if ( $end == null ) $end = '23:59';
        
        return ( $start <= date( 'H:i' ) && date( 'H:i' ) <= $end );
    }
}