<?php
class ModelExtensionShippingDpdParcel extends Model {
    public function getNoParcelWarningText() {
        $this->load->language('extension/shipping/dpd_parcel');

        return $this->language->get('text_no_parcel_selected');
    }

    public function getMethodTitleText() {
        $this->load->language('extension/shipping/dpd_parcel');

        return $this->language->get('text_title');
    }

    public function getQuote($address) {
        $google_map_api = $this->config->get('dpd_setting_google_map_api_key');

        $this->load->language('extension/shipping/dpd_parcel');

        $quote_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name");

        $shipping_country_code = $this->session->data['shipping_address']['iso_code_2'];
        $shipping_city = $this->session->data['shipping_address']['city'];

        foreach ($query->rows as $result) {
            if ($this->config->get('shipping_dpd_parcel_' . $result['geo_zone_id'] . '_status')) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$result['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

                if ($query->num_rows) {
                    $status = true;
                } else {
                    $status = false;
                }
            } else {
                $status = false;
            }

            // Just for select dropdown if no API Key found
            if ($google_map_api == '') {
                $shipping_country_code = strtolower($shipping_country_code);

                if ($shipping_country_code == 'lt') {
                    $parcels = $this->getParcelSelect(strtolower($shipping_country_code), $shipping_city, $result['geo_zone_id']);
                } else if ($shipping_country_code == 'lv') {
                    $parcels = $this->getParcelSelect(strtolower($shipping_country_code), $shipping_city, $result['geo_zone_id']);
                } else if ($shipping_country_code == 'ee') {
                    $parcels = $this->getParcelSelect(strtolower($shipping_country_code), $shipping_city, $result['geo_zone_id']);
                } else {
                    $parcels = $this->getParcelSelect(strtolower($shipping_country_code), $shipping_city, $result['geo_zone_id']);
                    $status = false;
                }

                $js = "
		            <script type='text/javascript'>
		                $(document).ready(function() {
		                    var selector = 'input[value=\"dpd_parcel.dpd_parcel_" . $result['geo_zone_id'] . "\"]';
		                    var el = $(selector);
		                    var orig_value = el.val();
		                    var dropdown = el.parent().parent().find('.select-parcel').first();
		                    el.val(orig_value + '|' + $(dropdown).val() + '|' + $(dropdown).find('option:selected').text().replace('|', ','));
		                    $(dropdown).change(function(e) {
		                        var value = $(this).val();
		                        var text = $(this).find('option:selected').text();
		                        el.prop('checked', true);
		                        el.val(orig_value + '|' + value + '|' + text.replace('|', ','));
		                        el.prop('checked', 'checked');
		                    })
		                })
		            </script>
		        ";
            } else {
                $terminals = $this->getParcel($shipping_country_code, $shipping_city, $result['geo_zone_id']);

                $parcels = '';
                $js = '';
            }
            //


            $cost = '';
            $max_weight = $this->config->get('shipping_dpd_parcel_weight');
            $max_length = $this->config->get('shipping_dpd_parcel_length');
            $max_width = $this->config->get('shipping_dpd_parcel_width');
            $max_height = $this->config->get('shipping_dpd_parcel_height');
            $dimensions_dpd_parcel = array($max_length, $max_width, $max_height);
            sort($dimensions_dpd_parcel);
            $cartProducts = $this->cart->getProducts();
            $total_cart_weight = 0;
            foreach ($cartProducts as $product) {
                $dimensions = array();
                $weight = $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
                $length = $this->length->convert($product['length'], $product['length_class_id'], $this->config->get('config_length_class_id'));
                $width = $this->length->convert($product['width'], $product['length_class_id'], $this->config->get('config_length_class_id'));
                $height = $this->length->convert($product['height'], $product['length_class_id'], $this->config->get('config_length_class_id'));

                $dimensions = array($length, $width, $height);
                sort($dimensions);

                if (($dimensions[0] <= $dimensions_dpd_parcel[0])&&($dimensions[1] <= $dimensions_dpd_parcel[1])&&($dimensions[2] <= $dimensions_dpd_parcel[2])) {
                    $total_cart_weight = $total_cart_weight + $weight;
                } else {
                    $status = false;
                    break;
                }
            }

            if ($total_cart_weight > $max_weight) {
                $status = false;
            }
            if ($status) {
                $priceCalculationMethod = $this->config->get('dpd_setting_price_calculation_parcels');

                // If price calculation by weight
                if ($priceCalculationMethod == 'weight') {
                    // Get rates
                    $rates = explode(',', $this->config->get('shipping_dpd_parcel_' . $result['geo_zone_id'] . '_rate'));

                    foreach ($rates as $rate) {
                        $data = explode(':', $rate);

                        if ($data[0] >= $weight) {
                            if (isset($data[1])) {
                                $cost = $data[1];
                            }

                            break;
                        }
                    };
                } else {
                    $cost = str_replace(',', '.', $this->config->get('shipping_dpd_parcel_' . $result['geo_zone_id'] . '_price'));
                }


                // Check can we apply free shipping
                if ($this->config->get('shipping_dpd_parcel_' . $result['geo_zone_id'] . '_free_shipping_from') != '') {
                    if ($this->cart->getSubTotal() > str_replace(',', '.', $this->config->get('shipping_dpd_parcel_' . $result['geo_zone_id'] . '_free_shipping_from'))) {
                        $cost = 0;
                    }
                }

                // If it's free shipping, show different data
                if ($cost == 0) {
                    $title = $this->language->get('text_free_shipping') . ' | ';
                    $text = $this->currency->format(0, $this->session->data['currency']);
                } else {
                    $title = $this->language->get('text_additional_shipping_title') . ' | ';
                    $text = $this->currency->format($this->tax->calculate($cost, $this->config->get('shipping_dpd_parcel_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency']);
                }

                // Just for select dropdown if no API Key found
                if ($google_map_api != '') {
                    $extension_title = $title . $terminals;
                } else {
                    $extension_title = $parcels . $js;
                }

                if ((string)$cost != '') {
                    $quote_data['dpd_parcel_' . $result['geo_zone_id']] = array(
                        'code'         => 'dpd_parcel.dpd_parcel_' . $result['geo_zone_id'],
                        'title'        => $extension_title,
                        'timeframe'	   => '',
                        'cost'         => $cost,
                        'tax_class_id' => $this->config->get('shipping_dpd_parcel_tax_class_id'),
                        'text'         => $text
                    );
                }
            }
        }

        $method_data = array();

        if ($quote_data) {
            $method_data = array(
                'code'       => 'dpd_parcel',
                'title'      => $this->language->get('text_title'),
                'quote'      => $quote_data,
                'sort_order' => $this->config->get('shipping_dpd_parcel_sort_order'),
                'error'      => false
            );
        }

        return $method_data;
    }

    /**
     * Returns <select> element with terminal locations as <option> values.
     *
     * @param string $city
     * @param string $state
     *
     * @return string
     */
    public function getParcel($country, $city, $geo_id) {
        // Return only label if admin side
        if(strpos($_SERVER['HTTP_REFERER'], 'sale/order') > -1 || isset($_GET['route']) && strpos($_GET['route'], 'sale/order') > -1) {
            return $this->getAdminParcel($country, $city, $geo_id);
        } else {
            return $this->getFrontendParcel($country, $city, $geo_id);
        }
    }

    public function getAdminParcel($country, $city, $geo_id) {
        return preg_replace("/<img[^>]+\>/i", "", $this->language->get('text_title'));
    }

    public function getFrontendParcel($country, $city, $geo_id) {
        $html = '';

        if ($country) {
            //$terminals = $this->getTerminals($country, $city);
            $terminals = $this->getTerminals($country);

            // Modalo reikės tik admin daly. Modal begins
            /*
            // Parcel dropdown ID structure: fixed part 'parcel-' + shipping methop radio ID
            $html = '<select id="dpd_parcel.dpd_parcel_'. $geo_id .'" name="dpd_parcel" class="select-parcel full-width">';
            // Build dropdown options
            $html .= '<option value=""></option>';
            foreach ($terminals->rows as $terminal) {
                $html .= '<option value="'. $terminal['code'] .'">';
                $html .= $terminal['company'] . ', ' . $terminal['street'];
                $html .= '</option>';
            }
            $html .= '</select>';
            */
            // Modal ends

            $modal = $this->getModal($terminals);
            $html .= $modal;

        }

        return $html;
    }

    public function getTerminals($country) {
        $terminals = $this->db->query("SELECT * FROM " . DB_PREFIX . "terminals_list WHERE country = '". $country ."'");

        return $terminals;
    }

    private function getTerminalName($terminals, $code) {
        foreach($terminals as $terminal) {
            if($terminal['code'] == $code) {
                return $terminal['company'] . ', ' . $terminal['street'];
            }
        }

        return null;
    }

    public function getTerminalCodStatus($code) {
        $cod_status = $this->db->query("SELECT cod FROM " . DB_PREFIX . "terminals_list WHERE code = '". $code ."'");

        return $cod_status->row['cod'];
    }

    public function getModal($terminals) {
        $this->load->language('extension/shipping/dpd_parcel');

        $html = '
			<!-- Modal trigger -->
			<span id="dpd-selected-parcel">' . ( isset($this->session->data['shipping_parcel_id']) && $this->session->data['shipping_parcel_id'] ? $this->getTerminalName($terminals->rows, $this->session->data['shipping_parcel_id']) : '' ) . '</span>
			<a href="javascript:void(0)" id="dpd-show-parcel-modal">' . $this->language->get('text_modal_show') . '</a>

			<!-- Modal styles -->
			<style>
				#dpd-parcel-modal {
				    display: none;
				    position: fixed;
				    z-index: 9999;
				    padding-top: 100px;
				    left: 0;
				    top: 0;
				    width: 100%;
				    height: 100%;
				    overflow: auto;
				    background-color: rgb(0,0,0);
				    background-color: rgba(0,0,0,0.4);
				}

				#dpd-parcel-modal .modal-content {
				    background-color: #fefefe;
				    margin: auto;
				    padding: 20px;
				    border: 1px solid #888;
				    width: 80%;
				    max-width: 900px;
				}

				#dpd-parcel-modal .dpd-city-label {
				    padding-right: 10px;
    				padding-left: 5px;
    				text-transform: capitalize;
				}

				#dpd-parcel-modal .close {
				    color: #aaaaaa;
				    float: right;
				    font-size: 28px;
				    font-weight: bold;
				}

				#dpd-parcel-modal .close:hover,
				#dpd-parcel-modal .close:focus {
				    color: #000;
				    text-decoration: none;
				    cursor: pointer;
				}

				#dpd-parcel-modal .modal-map{
					height: 400px;
					margin-top: 20px;
					position: relative;
				}

				#dpd-parcel-modal-map {
					height: 100%;
				}

				#dpd-parcel-modal-info {
					position: absolute;
					top: 10px;
					bottom: 10px;
					width: 300px;
					right: 10px;
					background-color: #ffffff;
					// background-color: rgba(255, 255, 255, 0.9);
					display: none;
				}

				#dpd-parcel-modal-info .working-hours {
					padding: 0;
					margin: 0;
					list-style: none inside;
					font-size: 11px;
				}
				#dpd-parcel-modal-info .working-hours span {
					width: 80px;
					margin-right: 5px;
					display: inline-block;
				}

				#dpd-parcel-modal-info .info-wrap {
					position: relative;
					padding: 10px;
					height: 100%;
				}

				#dpd-parcel-modal-info .select-terminal {
					position: absolute;
					bottom: 10px;
					left: 10px;
					right: 10px;
				}
			</style>

			<!-- Parcel modal -->
			<div id="dpd-parcel-modal">

			  <div class="modal-content">
			    <span class="close" id="dpd-close-parcel-modal">&times;</span>
			    
				<form class="form-inline">
				  <div class="form-group">
				    <input name="dpd-modal-address" value="' . $this->session->data['shipping_address']['address_1'] . '" type="text" class="form-control" placeholder="' . $this->language->get('text_modal_address') . '">
				  </div>
				  <label class="dpd-city-label">' . $this->session->data['shipping_address']['city'] . '</label>
				  <input type="hidden" name="dpd-modal-city" value="' . $this->session->data['shipping_address']['city'] . '">
				  <a href="#" class="btn btn-default search-location">' . $this->language->get('text_modal_search') . '</a>
				</form>

				<div class="modal-map">
					<!-- Map -->
					<div id="dpd-parcel-modal-map"></div>

					<!-- Info block -->
					<div id="dpd-parcel-modal-info">
						<div class="info-wrap">
							<h3></h3>

							<p>
								<strong>' . $this->language->get('text_modal_info_address') . '</strong>
								<br/>
								<span class="info-address"></span>
							</p>

							<p>
								<strong>' . $this->language->get('text_modal_info_hours') . '</strong>
								<br/>
								<span class="info-hours"></span>
								<ul class="working-hours">
									<li class="mon"><span>' . $this->language->get('text_day_mon') . '</span> <span class="morning"></span> <span class="afternoon"></span></li>
									<li class="tue"><span>' . $this->language->get('text_day_tue') . '</span> <span class="morning"></span> <span class="afternoon"></span></li>
									<li class="wed"><span>' . $this->language->get('text_day_wed') . '</span> <span class="morning"></span> <span class="afternoon"></span></li>
									<li class="thu"><span>' . $this->language->get('text_day_thu') . '</span> <span class="morning"></span> <span class="afternoon"></span></li>
									<li class="fri"><span>' . $this->language->get('text_day_fri') . '</span> <span class="morning"></span> <span class="afternoon"></span></li>
									<li class="sat"><span>' . $this->language->get('text_day_sat') . '</span> <span class="morning"></span> <span class="afternoon"></span></li>
									<li class="sun"><span>' . $this->language->get('text_day_sun') . '</span> <span class="morning"></span> <span class="afternoon"></span></li>
								</ul>
							</p>

							<p style="display: none;">
								<strong>' . $this->language->get('text_modal_info_contact') . '</strong>
								<br/>
								<span class="info-email"></span>
								<br/>
								<span class="info-phone"></span>
							</p>

							<a href="#" class="btn btn-primary select-terminal">' . $this->language->get('text_modal_info_select') . '</a>
						</div>
					</div>
				</div>

			  </div>

			</div>
			<!-- /Parcel modal -->
		';

        return $html;
    }

    /**
     * Returns <select> element with terminal locations as <option> values.
     *
     * @param string $city
     * @param string $state
     *
     * @return string
     */
    public function getParcelSelect($country, $city, $geoZone) {
        $terminals_list = $this->getTerminals($country);

        // Collect data
        $parcel_terminals = [];

        foreach ($terminals_list->rows as $terminal) {
            $sort_order = $this->getGroupSort($terminal['city']);

            $parcel_terminals[$terminal['city']]['sort_order'] = $sort_order;
            $parcel_terminals[$terminal['city']]['terminals'][] = [
                'name' => $terminal['company'] . ' ' . $terminal['street'] . ' ' . $terminal['pcode'],
                'code' => $terminal['code']
            ];
        }

        // Sort data
        foreach ($parcel_terminals as $city) {
            uasort($parcel_terminals, $this->build_sorter('sort_order'));
        }

        // If you would like to have sorting by cities size, comment 437 line
        ksort($parcel_terminals);
        // Format dropdown
        $parcel_select = '<select name="dpd_parcel" class="select-parcel full-width form-control" onchange="ChangeSelect(this)">';

        $parcel_select .= '<option value="0">'. $this->language->get('text_select') .'</option>';

        foreach ($parcel_terminals as $city => $terminals) {
            $parcel_select .= '<optgroup label="' . $city . '">';

            // If you would like to have sorting by cities size, comment 445 line
            sort($terminals['terminals']);

            foreach ($terminals['terminals'] as $terminal) {
                if (isset($this->session->data['shipping_parcel_id']) && $this->session->data['shipping_parcel_id'] == $terminal['code']){
                    $parcel_select .= '<option selected="selected" value="'. $terminal['code'] .'">';
                }else{
                    $parcel_select .= '<option value="'. $terminal['code'] .'">';
                }
                $parcel_select .= $terminal['name'];
                $parcel_select .= '</option>';

            }
            $parcel_select .= '</optgroup>';
        }
        $parcel_select .= '</select>';

        return $parcel_select;
    }

    private function build_sorter($key) {
        return function ($a, $b) use ($key) {
            return strnatcmp($a[$key], $b[$key]);
        };
    }

    private function getGroupSort($city = '') {
        $city = $this->slugify($city);

        $sorts = array(
            'vilnius' => 1,
            'kaunas' => 2,
            'klaipeda' => 3,
            'siauliai' => 4,
            'panevezys' => 5,
            'alytus' => 6,
            'marijampole' => 7,
        );

        if (isset($sorts[$city])) return $sorts[$city];
        return 99;
    }

    private function slugify($text) {
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