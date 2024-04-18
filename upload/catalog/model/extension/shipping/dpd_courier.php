<?php
class ModelExtensionShippingDpdCourier extends Model {
    function getQuote($address) {
        $this->load->language('extension/shipping/dpd_courier');

        $quote_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name");

        foreach ($query->rows as $result) {
            if ($this->config->get('shipping_dpd_courier_' . $result['geo_zone_id'] . '_status')) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$result['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

                if ($query->num_rows) {
                    $status = true;
                } else {
                    $status = false;
                }
            } else {
                $status = false;
            }

            if ($status) {
                $cost = '';
                $weight = $this->cart->getWeight();
                $itemsNo = $this->cart->countProducts();
                $priceCalculationMethod = $this->config->get('dpd_setting_price_calculation');


                // If price calculation by weight
                if ($priceCalculationMethod == 'weight') {
                    // Get rates
                    $rates = explode(',', $this->config->get('shipping_dpd_courier_' . $result['geo_zone_id'] . '_rate'));
                    
                    foreach ($rates as $rate) {
                        $data = explode(':', $rate);

                        if ($data[0] >= $weight) {
                            if (isset($data[1])) {
                                $cost = $data[1];
                            }

                            break;
                        }
                    };
                } else if ($priceCalculationMethod == 'item') {
                    $cost = $itemsNo * $this->config->get('shipping_dpd_courier_' . $result['geo_zone_id'] . '_price');
                } else {
                    $cost = $this->config->get('shipping_dpd_courier_' . $result['geo_zone_id'] . '_price');
                }

                // Show wheight on shipping title
                if ($this->config->get('config_cart_weight') == 1) {
                    $weight = '  (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class_id')) . ')';
                } else {
                    $weight = '';
                }

                // Check can we apply free shipping
                if ($this->config->get('shipping_dpd_courier_' . $result['geo_zone_id'] . '_free_shipping_from') != '') {
                    if ($this->cart->getSubTotal() > $this->config->get('shipping_dpd_courier_' . $result['geo_zone_id'] . '_free_shipping_from')) {
                        $cost = 0;
                    }
                }               

                // If it's free shipping, show different data
                if ($cost == 0) {
                    $title = '. ' . $this->language->get('text_free_shipping');
                    $text = $this->currency->format(0, $this->session->data['currency']);
                } else {
                    // $title = $result['name']; // geo zone name
                    $title = '';
                    $text = $this->currency->format($this->tax->calculate($cost, $this->config->get('shipping_dpd_courier_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency']);
                }

                // Timeframes allow
                $shipping_city = isset($address['city']) ? $address['city'] : '';

                if (in_array($this->slugify($shipping_city), $this->timeframes_cities())) {
                    if ($this->config->get('dpd_setting_api_url') != 'ee') {
                        $timeframe = $this->timeframe_select($address['iso_code_2'], $result['geo_zone_id']);
                    } else {
                        $timeframe = '';
                    }
                } else {
                    $timeframe = '';
                }

                if ((string)$cost != '') {
                    $quote_data['dpd_courier_' . $result['geo_zone_id']] = array(
                        'code'         => 'dpd_courier.dpd_courier_' . $result['geo_zone_id'],
                        'title'        => $this->language->get('text_additional_shipping_title') . $title . ' '. $result['name'] . $weight,
                        'cost'         => $cost,
                        'timeframe'    => $timeframe,
                        'tax_class_id' => $this->config->get('shipping_dpd_courier_tax_class_id'),
                        'text'         => $text
                    );
                }
            }
        }

        $method_data = array();

        if ($quote_data) {
            $method_data = array(
                'code'       => 'dpd_courier',
                'title'      => $this->language->get('text_title'),
                'quote'      => $quote_data,
                'sort_order' => $this->config->get('shipping_dpd_courier_sort_order'),
                'error'      => false
            );
        }

        return $method_data;
    }

    private function timeframes_cities() {
        $timeframes_available = [
            // LT
            'vilnius',
            'kaunas',
            'klaipeda',
            'siauliai',
            'panevezys',
            'alytus',
            'marijampole',
            'telsiai',
            'taurage',
            'utena',
            // LV
            'riga',
            'ryga',
            'rīga',
            'talsi',
            'liepaja',
            'jelgava',
            'jekabpils',
            'daugavpils',
            'rezekne',
            'valmiera',
            'gulbene',
            'cesis',
            'saldus',
            'ventspils'
        ];

        return $timeframes_available;
    }

    private function timeframe_select($shipping_country, $geo_zone) {
        $this->load->language('shipping/dpd_courier');

        $output_html = '';

        switch ($shipping_country) {
            case 'LT':
                $output_html = '
                    <div class="row shipping-timeframe dpd_courier_' . $geo_zone .'" style="display: none; margin-top: 10px; margin-bottom: 30px;">
                        <div class="col-md-12">
                            <strong>' . $this->language->get('text_pickup_time') . '</strong>
                        </div>

                        <div class="col-md-12">
                            <select class="form-control" name="shipping_timeframe" class="timeframe">
                                <option value="08:00-18:00">08:00-18:00</option>
                                <option value="08:00-14:00">08:00-14:00</option>
                                <option value="14:00-18:00">14:00-18:00</option>
                                <option value="18:00-22:00">18:00-22:00</option>
                            </select>
                        </div>
                    </div>';
                break;

            case 'LV':
                $output_html = '
                    <div class="row shipping-timeframe dpd_courier_' . $geo_zone .'" style="display: none; margin-top: 10px; margin-bottom: 30px;">
                        <div class="col-md-12">
                            <strong>' . $this->language->get('text_pickup_time') . '</strong>
                        </div>

                        <div class="col-md-12">
                            <select class="form-control" name="shipping_timeframe" class="timeframe">
                                <option value="08:00-18:00">08:00-18:00</option>
                                <option value="18:00-22:00">18:00-22:00</option>
                            </select>
                        </div>
                    </div>';
            default:
                break;
        }

        return $output_html;
    }

    private function slugify($text) {
        // // replace non letter or digits by -
        // $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // // transliterate
        // $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // // remove unwanted characters
        // $text = preg_replace('~[^-\w]+~', '', $text);

        // // trim
        // $text = trim($text, '-');

        // // remove duplicate -
        // $text = preg_replace('~-+~', '-', $text);

        // // lowercase
        // $text = strtolower($text);

        // if (empty($text)) {
        //     return 'n-a';
        // }

        // return $text;

        // Convert to lowercase and remove whitespace
        $str = strtolower(trim($text));

        // Replace high ascii characters
        $chars = array("ä", "ö", "ü", "ß", "ā", "ē", "ī", "ū", "š", "ģ", "ķ", "ļ", "ž", "č", "ņ", "ą", "č", "ę", "ė", "į", "ų");
        $replacements = array("ae", "oe", "ue", "ss", "a", "e", "i", "u", "s", "g", "k", "l", "z", "c", "n", "a", "c", "e", "e", "i", "u");
        $str = str_replace($chars, $replacements, $str);
        $pattern = array("/(é|è|ë|ê)/", "/(ó|ò|ö|ô)/", "/(ú|ù|ü|û)/");
        $replacements = array("e", "o", "u");
        $str = preg_replace($pattern, $replacements, $str);

        // Remove puncuation
        $pattern = array(":", "!", "?", ".", "/", "'");
        $str = str_replace($pattern, "", $str);

        // Hyphenate any non alphanumeric characters
        $pattern = array("/[^a-z0-9-]/", "/-+/");
        $str = preg_replace($pattern, "-", $str);

        return $str;
    }
}