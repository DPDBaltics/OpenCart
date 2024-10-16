<?php
class ModelExtensionShippingDpdLivehandler extends Model {
    const PARCELCODE = 'dpd_parcel';
    const COURIERCODE = 'dpd_courier';
    const COURIERCODESAT = 'dpd_courier_sat';
    const COURIERCODESAME = 'dpd_courier_same';

    private function install() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dpd_barcodes` (
            `dpd_barcode_id` int(11) NOT NULL AUTO_INCREMENT,
            `order_id` int(11) NOT NULL,
            `dpd_barcode` VARCHAR(50) NOT NULL,
            PRIMARY KEY (`dpd_barcode_id`)
        )");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "terminals_list` (
            `terminal_id` int(11) NOT NULL AUTO_INCREMENT,
            `code` VARCHAR(256) NOT NULL,
            `company` varchar(256) NOT NULL,
            `country` varchar(5) NOT NULL,
            `city` varchar(100) NOT NULL,
            `street` varchar(128) NOT NULL,
            `pcode` varchar(10) NOT NULL,
            `email` varchar(96) NOT NULL,
            `phone` varchar(32) NOT NULL,
            `mon` TEXT NOT NULL,
            `tue` TEXT NOT NULL,
            `wed` TEXT NOT NULL,
            `thu` TEXT NOT NULL,
            `fri` TEXT NOT NULL,
            `sat` TEXT NOT NULL,
            `sun` TEXT NOT NULL,
            `working_hours` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            `distance` int(30) NOT NULL,
            `longitude` varchar(100) NOT NULL,
            `latitude` varchar(100) NOT NULL,
            `type` varchar(50) NOT NULL,
            `cod` TINYINT(1) NOT NULL DEFAULT '0',
            `date_updated` DATE NOT NULL,
            PRIMARY KEY (`terminal_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        // $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dpd_manifest` (
        //  `id` int(11) NOT NULL AUTO_INCREMENT,
        //  `pdf_content` varchar(256) NOT NULL,
        //  `date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        //  PRIMARY KEY (`id`)
        // )"); // ENGINE=MyISAM DEFAULT CHARSET=utf8

        $shipping_parcel_id_result = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'shipping_parcel_id'");

        if($shipping_parcel_id_result->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD `shipping_parcel_id` VARCHAR(256) NOT NULL DEFAULT '0' AFTER `shipping_code`");
        }

        // 
        $shipping_labels_result = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'shipping_labels'");

        if($shipping_labels_result->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD `shipping_labels` VARCHAR(256) NOT NULL DEFAULT '0' AFTER `shipping_code`");
        }

        // 
        $shipping_services_results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'shipping_services'");

        if($shipping_services_results->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD `shipping_services` TINYINT(1) NOT NULL AFTER `shipping_labels`");
        }

        $shipping_comments_results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'shipping_note'");

        if($shipping_comments_results->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD `shipping_note` VARCHAR(35) NOT NULL AFTER `shipping_services`");
        }

        // 
        $shipping_timeframe_result = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'shipping_timeframe'");

        if($shipping_timeframe_result->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD `shipping_timeframe` VARCHAR(256) NOT NULL DEFAULT '0' AFTER `shipping_code`");
        }
    }

    public function getManifest() {
        $this->install();

        date_default_timezone_set('Europe/Vilnius');

        $valid = array(
            'pdf' => 'PDF',
        );

        $files = array();

        $dir = new DirectoryIterator(DIR_DOWNLOAD);

        foreach($dir as $file) {
            // filter out directories
            if($file->isDot() || !$file->isFile()) continue;

            // Use pathinfo to get the file extension
            $info = pathinfo($file->getPathname());

            // Check there is an extension and it is in the whitelist
            if(isset($info['extension']) && isset($valid[$info['extension']])) {
                $file_date = explode('|', $file->getFilename());

                if (count($file_date) > 0) {
                    $manifest_date = $file_date[0];
                } else {
                    $manifest_date = '';
                }

                $files[] = array(
                    'filename' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'type' => $valid[$info['extension']], // 'PDF' or 'Word'
                    'created' => date('Y-m-d H:i:s', $file->getMTime()),
                    'date' => date('Y-m-d H:i:s', $file->getMTime()),

                    'preview' => HTTP_CATALOG . 'index.php?route=extension/module/dpdpdf&pdf='.$file->getFilename()
                );
            }
        }

        // Delete manifest older than 5 days
        $now = time();
        if ($files) {
            foreach ($files as $file) {
                if ((time() - filemtime(DIR_DOWNLOAD . '/' . $file['filename'])) > (5 *86400)) {
                    unlink(DIR_DOWNLOAD . '/' . $file['filename']);
                }
            }
        }

        return $files;
    }

    public function getOrderBarcode($orderId) {
        $this->install();

        if ($orderId) {
            $barcode_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "dpd_barcodes WHERE order_id = '" . (int)$orderId . "'");

            return $barcode_query->rows;
        }
    }

    public function deleteBarcode($order_id) {
        if (isset($order_id)) {
            $this->db->query("DELETE FROM `" . DB_PREFIX . "dpd_barcodes` WHERE `order_id` = '" . $order_id . "'");
        }
    }

    public function getOrderIdByBarcode($barcode) {
        $this->install();

        if ($barcode) {
            $id_query = $this->db->query("SELECT order_id FROM " . DB_PREFIX . "dpd_barcodes WHERE dpd_barcode = '" . $barcode . "'");

            return $id_query->row['order_id'];
        }
    }

    public function getTerminalName($terminal_id) {
        $this->install();

        if ($terminal_id) {

            $terminal_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "terminals_list WHERE code = '" . $terminal_id . "'");

            return $terminal_query->row;
        }
    }

    private function setOrderBarcode($orderId, $barcode) {
        $this->install();

        // Get order data
        $this->load->model('sale/order');
        $order_data = $this->model_sale_order->getOrder($orderId);


        $this->load->language('extension/module/dpd_settings');

        $comment_barcode = $this->language->get('entry_dpd_tracking_number') . ' ' . $barcode;


        if ($orderId && $barcode) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "dpd_barcodes` SET order_id = '" . (int)$orderId . "', dpd_barcode = '" . $barcode . "'");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order_history` SET order_id = '" . (int)$orderId . "', order_status_id = '" . (int)$order_data['order_status_id'] . "', comment = '" . $this->db->escape($comment_barcode) . "', date_added = '" . date('Y-m-d H:m:s') . "'");
        }
    }

    public function setLabelsNumber($orderId, $labels_no) {
        $this->install();

        // Get order data
        $this->load->model('sale/order');

        if ($orderId && $labels_no) {
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `shipping_labels` = '" . (int)$labels_no . "' WHERE `order_id` = '" . (int)$orderId . "'");
        }
    }

    public function updateRodServices($orderId, $rod) {
        $this->install();

        // Get order data
        $this->load->model('sale/order');

        if ($orderId && $rod) {
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `shipping_services` = '" . $rod . "' WHERE `order_id` = '" . (int)$orderId . "'");
        } else if ($orderId) {
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `shipping_services` = '0' WHERE `order_id` = '" . (int)$orderId . "'");
        }
    }

    public function updateRodNote($orderId, $note) {
        $this->install();

        // Get order data
        $this->load->model('sale/order');

        if ($orderId && $note) {
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `shipping_note` = '" . $this->db->escape($note) . "' WHERE `order_id` = '" . (int)$orderId . "'");
        }
    }

    public function setOrderHistory($orderId, $comment) {
        // Get order data
        $this->load->model('sale/order');
        $order_data = $this->model_sale_order->getOrder($orderId);

        if ($orderId) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order_history` SET order_id = '" . (int)$orderId . "', order_status_id = '" . (int)$order_data['order_status_id'] . "', comment = '" . $this->db->escape($comment) . "', date_added = '" . date('Y-m-d H:m:s') . "'");
        }
    }

    private function addOrderHistory($orderId, $comment) {
        // Get order data
        $this->load->model('sale/order');
        $order_data = $this->model_sale_order->getOrder($orderId);

        if ($orderId && $comment) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order_history` SET order_id = '" . (int)$orderId . "', order_status_id = '" . (int)$order_data['order_status_id'] . "', comment = '" . $this->db->escape($comment) . "', date_added = '" . date('Y-m-d H:m:s') . "'");
        }
    }

    // Force PDF document download
    private function getLabelsOutput($pdf, $file_name = 'dpdLabels') {

        ob_get_clean();

        $today = date('Y-m-d');
        $name = $file_name . '-'.$today. '.pdf';

        if (ob_get_contents()) {
            $this->session->data['error_dpd'] = 'Some data has already been output, can\'t send PDF file';
            $this->response->redirect($this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true));
        }

        header('Content-Description: File Transfer');

        if (headers_sent()) {
            $this->session->data['error_dpd'] = 'Some data has already been output to browser, can\'t send PDF file';
            $this->response->redirect($this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true));
        }

        header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header( "refresh:1;url=".(isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:''."" );
        header('Content-Type: application/force-download');
        header('Content-Type: application/octet-stream', false);
        header('Content-Type: application/download', false);
        header('Content-Type: application/pdf', false);
        header('Content-Disposition: attachment; filename="'.$name.'";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.strlen($pdf));

        echo $pdf;

        return '';
    }

    // 4.1 Shipment creation
    public function shipment_creation($orders_id = array()) {
        $this->load->model('sale/order');
        $this->load->model('catalog/product');
        $this->load->model('extension/shipping/dialcodehelper');

        $tracking_barcodes = array();

        if ($this->config->get('dpd_setting_return_service') == 1) {
            $pickup_parcel_type = 'PS-RETURN';
            $pickup_cod_parcel_type = 'PS-COD-RETURN';

            $courier_parcel_type = 'D-B2C-RETURN';
            $courier_cod_parcel_type = 'D-B2C-COD-RETURN';

            $courier_parcel_rod_type = 'D-B2C-DOCRET-RETURN';
            $courier_cod_parcel_rod_type = 'D-COD-B2C-DOCRET-RETURN';

            // Saturday services
            $courier_sat_parcel_type = 'D-B2C-SAT-RETURN';
            $courier_sat_cod_parcel_type = 'D-B2C-SAT-COD-RETURN';

            $courier_sat_parcel_rod_type = 'D-B2C-SAT-DOCRET-RETURN';
            $courier_cod_sat_parcel_rod_type = 'D-COD-B2C-SAT-DOCRET-RETURN';

            $courier_same_parcel_type = 'SD-RETURN';
            $courier_same_cod_parcel_type = 'SD-COD-RETURN';
        } else {
            $pickup_parcel_type = 'PS';
            $pickup_cod_parcel_type = 'PS-COD';

            $courier_parcel_type = 'D-B2C';
            $courier_cod_parcel_type = 'D-B2C-COD';

            $courier_parcel_rod_type = 'D-B2C-DOCRET';
            $courier_cod_parcel_rod_type = 'D-COD-B2C-DOCRET';

            // Saturday services
            $courier_sat_parcel_type = 'D-B2C-SAT';
            $courier_sat_cod_parcel_type = 'D-B2C-SAT-COD';

            $courier_sat_parcel_rod_type = 'D-B2C-SAT-DOCRET';
            $courier_cod_sat_parcel_rod_type = 'D-COD-B2C-SAT-DOCRET';

            $courier_same_parcel_type = 'SD';
            $courier_same_cod_parcel_type = 'SD-COD';
        }

        foreach ($orders_id as $order_id) {
            $order_data = $this->model_sale_order->getOrder($order_id);
            $products = $this->model_sale_order->getOrderProducts($order_id);

            // Fixing params for DPD
            $name1 = $this->custom_length($order_data['shipping_firstname'] . ' ' . $order_data['shipping_lastname'], 40); // required 1, max length 40
            $name2 = $this->custom_length($order_data['shipping_company'], 40); // required 1, max length 40
            $street = $this->custom_length($order_data['shipping_address_1'], 40); // required 1, max length 40
            $city = $this->custom_length($order_data['shipping_city'], 40); // required 1, max length 40

            $country_code = $order_data['shipping_iso_code_2'];
            if (strtoupper($country_code) == 'LT' or strtoupper($country_code) == 'LV' or strtoupper($country_code) == 'EE') {
                $pcode = preg_replace('/[^0-9,.]/', '', $order_data['shipping_postcode']);
            } else {
                $pcode = preg_replace('/[^a-zA-Z0-9]+/', '', $order_data['shipping_postcode']);
            }

            $correct_phone = $this->model_extension_shipping_dialcodehelper->separatePhoneNumberFromCountryCode($order_data['telephone'], $country_code);
            $phone = $correct_phone['dial_code'] . $correct_phone['phone_number'];
            $email = $order_data['email'];
            $shipping_code = explode('.', $order_data['shipping_code']);
            $order_comment = $this->custom_length($order_data['comment'], 40); // required 0, max length 40
            $num_of_parcel = $order_data['shipping_labels'];
            $shipping_parcel_id = $order_data['shipping_parcel_id'];

            // If documents should be return
            $shipping_return = $order_data['shipping_return'];
            $shipping_note = $order_data['shipping_note'];

            if (strpos($shipping_code[0], self::COURIERCODE) !== FALSE or
                strpos($shipping_code[0], self::COURIERCODESAT) !== FALSE or
                strpos($shipping_code[0], self::COURIERCODESAME) !== FALSE or
                strpos($shipping_code[0], self::PARCELCODE) !== FALSE) {

                if (!$this->getOrderBarcode($order_id)) {
                    $product_weight = 0;
                    $total_order_quantity = 0;
                    $total_different_products = 0;

                    foreach ($products as $product) {
                        $product_data = $this->model_catalog_product->getProduct($product['product_id']);
                        $product_weight += $product_data['weight'] * $product['quantity'];
                        $total_order_quantity += $product['quantity'];
                        $total_different_products += 1;
                    }

                    // How many labels print
                    $labels_setting = $this->config->get('dpd_setting_parcel_distribution');

                    if ($num_of_parcel == 0) { // was 1
                        // All products in same shipment
                        if ($labels_setting == 1) {
                            $num_of_parcel = 1;
                            // Each product in seperate shipment
                        } else if ($labels_setting == 2) {
                            $num_of_parcel = $total_different_products;
                            // Each product wuantity in separate shipment
                        } else if ($labels_setting == 3) {
                            $num_of_parcel = $total_order_quantity;
                        } else {
                            $num_of_parcel = 1;
                        }
                    } else if ($num_of_parcel > 1) {
                        $num_of_parcel = $num_of_parcel;
                    } else {
                        $num_of_parcel = 1;
                    }

                    // Params which we send
                    $params = array();

                    $params = array(
                        'action' => 'createShipment_',
                        'name1' => $name1,
                        'name2' => $name2,
                        'street' => $street,
                        'city' => $city,
                        'country' => $country_code,
                        'pcode' => $pcode,
                        'num_of_parcel' => $num_of_parcel,
                        'phone' => $phone,
                        'idm_sms_number' => $phone,
                        'email' => $email,
                        'weight' => $product_weight,
                        'order_number' => $order_id . '#' . $this->config->get('config_invoice_prefix'),
                        // DPD IC mod start
                        'order_number3' => 'OC' . VERSION . ';' . $this->config->get('dpd_setting_module_version'),
                        // DPD IC mod end
                        'idm_sms_number' => $phone
                    );

                    // Courier services
                    if (strpos($shipping_code[0], self::COURIERCODE) !== FALSE) {
                        $params['remark'] = $order_comment;

                        // If shipping_services field have 1, it means thats its ROD services used
                        if ($shipping_return == 1) {
                            $params['parcel_type'] = $courier_parcel_rod_type;
                        } else {
                            $params['parcel_type'] = $courier_parcel_type;
                        }

                        // If order is COD
                        if ($order_data['payment_code'] == 'cod') {
                            $params['cod_amount'] = number_format($order_data['total'], 2, '.', '');

                            if ($shipping_return == 1) {
                                $params['parcel_type'] = $courier_cod_parcel_rod_type;
                            } else {
                                $params['parcel_type'] = $courier_cod_parcel_type;
                            }
                        }

                        if ($shipping_return == 1) {
                            $params['dnote_reference'] = $shipping_note;
                        }

                        // Timefrae
                        if ($order_data['shipping_timeframe']) {
                            $shipping_timeframe = explode('-', $order_data['shipping_timeframe']);

                            $params['timeframe_to'] = $shipping_timeframe[1];
                            $params['timeframe_from'] = $shipping_timeframe[0];
                        }
                    }

                    // Courier SAT services
                    if (strpos($shipping_code[0], self::COURIERCODESAT) !== FALSE) {
                        $params['remark'] = $order_comment;

                        // If shipping_services field have 1, it means thats its ROD services used
                        if ($shipping_return == 1) {
                            $params['parcel_type'] = $courier_sat_parcel_rod_type;
                        } else {
                            $params['parcel_type'] = $courier_sat_parcel_type;
                        }

                        // If order is COD
                        if ($order_data['payment_code'] == 'cod') {
                            $params['cod_amount'] = number_format($order_data['total'], 2, '.', '');

                            if ($shipping_return == 1) {
                                $params['parcel_type'] = $courier_cod_sat_parcel_rod_type;
                            } else {
                                $params['parcel_type'] = $courier_sat_cod_parcel_type;
                            }
                        }

                        if ($shipping_return == 1) {
                            $params['dnote_reference'] = $shipping_note;
                        }
                    }

                    // Courier SAME DAY services
                    if (strpos($shipping_code[0], self::COURIERCODESAME) !== FALSE) {
                        $params['remark'] = $order_comment;

                        $params['parcel_type'] = $courier_same_parcel_type;

                        // If order is COD
                        if ($order_data['payment_code'] == 'cod') {
                            $params['cod_amount'] = number_format($order_data['total'], 2, '.', '');

                            $params['parcel_type'] = $courier_same_cod_parcel_type;
                        }
                    }

                    // Parcelshop services
                    if (strpos($shipping_code[0], self::PARCELCODE) !== FALSE) {
                        $params['parcel_type'] = $pickup_parcel_type;
                        $params['parcelshop_id'] = $shipping_parcel_id;

                        // If order is COD
                        if ($order_data['payment_code'] == 'cod') {
                            $params['cod_amount'] = number_format($order_data['total'], 2, '.', '');

                            $params['parcel_type'] = $pickup_cod_parcel_type;
                        }

                        $params['fetchGsPUDOpoint'] = 1;
                    }

                    if ($params) {
                        $requestResults = $this->getRequest($params);

                        if ($requestResults && isset($requestResults['status']) && $requestResults['status'] == "ok") {
                            $tracking_barcodes[$order_id]['status'] = 'ok';
                            $tracking_barcodes[$order_id]['barcodes'] = $requestResults['pl_number'];

                            if ($requestResults['pl_number']) {
                                foreach ($requestResults['pl_number'] as $number) {
                                    $this->setOrderBarcode($order_id, $number);
                                }

                                $tracking_barcodes[$order_id]['status'] = 'ok';
                                $tracking_barcodes[$order_id]['barcodes'] = $this->getOrderBarcode($order_id);
                            }
                        } else if ($requestResults && isset($requestResults['status']) && $requestResults['status'] == "err") {
                            $tracking_barcodes[$order_id]['status'] = 'err';
                            $tracking_barcodes[$order_id]['errlog'] = $requestResults['errlog'];

                            // Add error message to order history
                            $this->addOrderHistory($order_id, $requestResults['errlog']);
                        }
                    }
                } else {
                    $tracking_barcodes[$order_id]['status'] = 'ok';
                    $tracking_barcodes[$order_id]['barcodes'] = $this->getOrderBarcode($order_id);
                }
            } else {
                $tracking_barcodes[$order_id]['status'] = 'err';
                $tracking_barcodes[$order_id]['errlog'] = $this->language->get('error_shipping_method_no_dpd');
            }
        }

        return $tracking_barcodes;
    }

    // 4.2 Parcel shops per destination | Get all terminals
    public function getTerminalList() {
        // LV91 Post Station – COD not allowed
        // LV90 Parcel Locker – COD not allowed
        // LV10 Parcel Shops – COD not allowed

        //LT1 (COD is not available)
        //LT900 (COD will be available in near future, bot not available now)

        // EE91.. – Robots (No COD)
        // EE90.. - Lockers (COD available)
        // EE10.. - Pickup Stores (COD available)

        $this->install();

        // Load all countries which are in Opencart
        $this->load->model('localisation/country');
        // $countries = $this->model_localisation_country->getCountries();

        $countries = [
            'LT',
            'LV',
            'EE',
            // 'AT',
            // 'BE',
            // 'BG',
            // 'CY',
            // 'CZ',
            // 'DK',
            // 'FI',
            // 'FR',
            // 'DE',
            // 'GR',
            // 'HU',
            // 'IE',
            // 'IT',
            // 'LU',
            // 'MT',
            // 'NL',
            // 'PL',
            // 'PT',
            // 'RO',
            // 'SK',
            // 'SI',
            // 'ES',
            // 'SE',
            // 'GB'
        ];

        foreach ($countries as $country) {
            // Define list of COD for pudo
            switch ($country) {
                case 'LT':
                    $cod_pudo = ['LT900'];
                    $lenght = 5;
                    break;

                case 'LV':
                    $cod_pudo = [];
                    break;

                case 'EE':
                    $cod_pudo = ['EE90', 'EE10'];
                    $lenght = 4;
                    break;

                default:
                    $cod_pudo = ['LT900'];
                    $lenght = 5;
                    break;
            }

            // Make API call
            $params = array(
                'action' => 'parcelShopSearch_',
                'fetchGsPUDOpoint' => 1,
                'country' => strtoupper($country),
                'retrieveOpeningHours' => 1
            );

            $result = $this->getRequest($params);

            if (isset($result['status']) == 'ok') {
                if (count($result['parcelshops']) > 0) {
                    foreach ($result['parcelshops'] as $parcel) {
                        $parcel_id = substr($parcel['parcelshop_id'], 0, $lenght);

                        if (in_array($parcel_id, $cod_pudo)) {
                            $cod_available = '1';
                        } else {
                            $cod_available = '0';
                        }

                        $sql = $this->db->query("SELECT code FROM `" . DB_PREFIX . "terminals_list` WHERE code = '" . $this->db->escape($parcel['parcelshop_id']) . "'");

                        if ($sql->num_rows == 0) {
                            $this->db->query("INSERT INTO `" . DB_PREFIX . "terminals_list` SET terminal_id = '" . (int)$parcel['parcelshop_id'] . "', code = '" . $this->db->escape($parcel['parcelshop_id']) . "', company = '" . $this->db->escape($parcel['company']) . "', country = '" . $this->db->escape($parcel['country']) . "', city = '" . $this->db->escape($parcel['city']) . "', street = '" . $this->db->escape($parcel['street']) . "', pcode = '" . $this->db->escape($parcel['pcode']) . "', email = '" . $this->db->escape($parcel['email']) . "', phone = '" . $this->db->escape($parcel['phone']) . "', distance = '" . $this->db->escape($parcel['distance']) . "', longitude = '" . $this->db->escape($parcel['longitude']) . "', latitude = '" . $this->db->escape($parcel['latitude']) . "', type = 'DPD', cod = '" . (int)$cod_available . "', date_updated = '" . date("Y-m-d") . "'");

                            foreach ($parcel['openingHours'] as $day) {
                                $morning = $day['openMorning'].'-'.$day['closeMorning'];
                                $afternoon = $day['openAfternoon'].'-'.$day['closeAfternoon'];

                                $working_hours = $morning.'|'.$afternoon;

                                $this->db->query("UPDATE `" . DB_PREFIX . "terminals_list` SET ". strtolower($day['weekday']) ." = '" . $working_hours . "' WHERE code = '" . $this->db->escape($parcel['parcelshop_id']) . "'");

                            }
                        }
                    }
                }
            } else {
                $result['status'] = 'err';
            }
        }

        return $result;
    }

    // 4.3 Creating shipment returns
    // 4.4 Creating parcel labels
    public function print_parcel_label($tracking_number = null) {
        $label_size = $this->config->get('dpd_setting_label_size');

        if (isset($label_size)) {
            $label_size = $this->config->get('dpd_setting_label_size');
        } else {
            $label_size = 'A4';
        }

        $params = array(
            'action' => 'parcelPrint_',
            'parcels' => $tracking_number,
            'printType' => 'PDF',
            'printFormat' => $label_size
        );

        $result = $this->getRequest($params);

        $responseResult = @json_decode($result, true);

        if (!is_null($responseResult) && $responseResult['status'] == 'err') {
            return $responseResult;
        } else {
            return $this->getLabelsOutput($result);
        }
    }

    // 4.5 Closing the manifest
    public function close_manifest() {
        $api_url_by_country = $this->config->get("dpd_setting_api_url");

        $dates = [
            date('Y-m-d'),
            date("Y-m-d", strtotime("+ 1 day")),
            date("Y-m-d", strtotime("+ 2 days")),
            date("Y-m-d", strtotime("+ 3 days"))
        ];

        foreach ($dates as $date) {
            $params = array(
                'action' => 'parcelManifestPrint_',
                'type' => 'manifest',
                'date' => $date
            );

            // Pachekinti koks name buna kai isaugojam PDF
            $manifest_name =  'dpd_manifest_' . date("Y_m_d_H_i_s") . '.pdf';

            $result = $this->getRequest($params);
            $responseResult = @json_decode($result, true);


            if ($api_url_by_country == 'lt') {
                if (isset($responseResult['status']) && $responseResult['status'] == 'err') {
                    return $responseResult['errlog'];
                } else {
                    file_put_contents(DIR_DOWNLOAD . $manifest_name, $result);
                }
            } else {
                if (isset($responseResult['status']) && $responseResult['status'] == 'ok') {
                    if (isset($responseResult['pdf'])) {
                        // $this->db->query("INSERT INTO `" . DB_PREFIX . "dpd_manifest` SET pdf_content = '" . $responseResult['pdf'] . "'");
                        file_put_contents(DIR_DOWNLOAD . $manifest_name, base64_decode($responseResult['pdf']));
                    }
                } else {
                    return $responseResult['errlog'];
                }
            }

        }

        // return $responseResult;
        return 'ok';
    }

    public function printManifest($pdf_content) {
        return $this->getLabelsOutput(base64_decode($pdf_content), 'dpd_manifest');
    }

    // 4.6 Courier request
    public function requestCourier($data = array()) {
        $this->load->model('localisation/country');
        $this->load->model('extension/shipping/dialcodehelper');
        $this->load->model('setting/setting');

        $orderNo = $this->config->get('dpd_setting_request_order_no');

        if ($orderNo == '') {
            $this->model_setting_setting->editSetting('dpd_setting_request_order_no', array('dpd_setting_request_order_no' => 1));

            $orderNo = $this->config->get('dpd_setting_request_order_no');
        } else {
            $this->model_setting_setting->editSetting('dpd_setting_request_order_no', array('dpd_setting_request_order_no' => (int)$orderNo + 1));

            $orderNo = '21' . $this->config->get('dpd_setting_request_order_no');
        }

        // Get info about warehouse
        $warehouse_info = $this->getWarehouse($data['warehouse_id']);

        $result = '';

        if ($warehouse_info) {
            $payerId = $this->config->get('dpd_setting_api_username');
            $senderAddress = $this->custom_length($warehouse_info['address'], 100);
            $senderCity = $this->custom_length($warehouse_info['city'], 100);
            $senderCountry = $this->model_localisation_country->getCountry($warehouse_info['country_id']);
            $senderPostalCode = preg_replace('/[^0-9,.]/', '', $warehouse_info['postcode']);
            $senderContact = $this->custom_length($warehouse_info['contact_person_name'], 100);
            $palletsCount = $data['palletsCount'];
            $parcelsCount = $data['parcelsCount'];


            // Get total weight of order
            $this->load->model('catalog/product');
            $this->load->model('sale/order');

            $weight_total = 0.1;

            $selected = explode (',', $data['selected']);

            // unset($array[1]);
            foreach ($selected as $order_id) {
                // Get order data
                $order_data = $this->model_sale_order->getOrder($order_id);

                // Get info of order products
                $products = $this->model_sale_order->getOrderProducts($order_id);

                // Get order weight
                foreach ($products as $product) {
                    $product_data = $this->model_catalog_product->getProduct($product['product_id']);
                    $weight_total += $product_data['weight'];
                }
            }

            // Correct phone
            $country_code = $senderCountry['iso_code_2'];
            $correct_phone = $this->model_extension_shipping_dialcodehelper->separatePhoneNumberFromCountryCode($warehouse_info['phone'], $country_code);
            $phone = $correct_phone['dial_code'] . $correct_phone['phone_number'];

            // Working hours
            $dayofweek = date('w', strtotime(date('Y-m-d')));

            $pickup_until = '17:00:00';
            $pickup_from = '10:00:00';

            $time_cut_off = strtotime('15:00:00');

            if ($dayofweek == 6) {
                // If its saturday
                $date = date("Y-m-d", strtotime("+ 2 days"));
            } else if ($dayofweek == 7) {
                // If its sunday
                $date = date("Y-m-d", strtotime("+ 1 day"));
            } else if ($dayofweek == 5) {
                // If its more or equal 15, request go for tommorow
                if (strtotime(date('H:m:s')) >= $time_cut_off or date('H:m:s', strtotime($data['pickup_from'])) >= $time_cut_off) {
                    $date = date("Y-m-d", strtotime("+ 3 days"));
                } else {
                    $date = date("Y-m-d");

                    $pickup_from = $data['pickup_from'] . ':00';
                    $pickup_until = $data['pickup_until'] . ':00';
                }
            } else {
                if (strtotime(date('H:m:s')) >= $time_cut_off or date('H:m:s', strtotime($data['pickup_from'])) >= $time_cut_off) {
                    $date = date("Y-m-d", strtotime("+ 1 days"));
                } else {
                    $date = date("Y-m-d");

                    $pickup_from = $data['pickup_from'] . ':00';
                    $pickup_until = $data['pickup_until'] . ':00';
                }
            }

            $until = $date . ' ' . $pickup_until;
            $from = $date . ' ' . $pickup_from;

            // Comment
            $comment = $this->custom_length($this->removeSpecials($data['nonStandard']), 100);

            $params = array(
                'action' => 'pickupOrderSave_',
                'orderNr' => $orderNo,
                'payerId' => $payerId,
                'senderAddress' => $senderAddress,
                'senderCity' => $senderCity,
                'senderCountry' => $senderCountry['iso_code_2'],
                'senderPostalCode' => $senderPostalCode,
                'senderContact' => $senderContact,
                'senderPhone' => $phone,
                'senderWorkUntil' => $until,
                'pickupTime' => $from,
                'weight' => $weight_total,
                'parcelsCount' => $parcelsCount,
                'palletsCount' => $palletsCount,
                'nonStandard' => isset($comment) ? $comment : '' // Comment for courier
            );

            $result = $this->getRequest($params);

            $result .= '#from#' . $from;
            $result .= '#until#' . $pickup_until;
        }

        return $result;
    }

    // 6 Collections request
    public function requestCollection($data = array()) {
        $this->load->model('localisation/country');

        $total_amount = $data['parcels'] + (int)$data['pallets'];

        if (isset($data['weight']) && $data['weight']) {
            $weight = '#kg' . round(($data['weight'] / $total_amount), 2);
        } else {
            $weight = '';
        }

        if ($total_amount > 0) {
            for ($i = 1; ; $i++) {
                $pallets_amount = $data['pallets'];

                if ($data['parcels'] >= 0) {
                    $parcels_amount = $data['parcels']--;

                    if ($parcels_amount != 0) {
                        $parcels_no = '#1cl|';
                        $pallets_no = '#0pl|';
                    }
                }

                if ($parcels_amount == 0) {
                    if ($data['pallets'] >= 0) {
                        $pallets_amount = $data['pallets']--;

                        if ($pallets_amount != 0) {
                            $parcels_no = '#0cl|';
                            $pallets_no = '#1pl|';
                        }
                    }
                }

                //$parcels_no = (isset($data['parcels']) && $data['parcels'] > 0) ? '#' . $data['parcels'] . 'cll ' : ' ';
                //$pallets_no = (isset($data['pallets']) && $data['pallets'] > 0) ? ', ' . $data['pallets'] . 'pll ' : ' ';

                $cname = $data['pickup_name'];
                $cname0 = substr($cname, 0, 35);
                $cname1 = substr($cname, 35, 35);
                $cname2 = substr($cname, 70, 35);
                $cname3 = substr($cname, 105, 35);

                $cstreet = $data['pickup_address'];
                $cpostal = $data['pickup_postcode'];
                $ccity = $data['pickup_city'];

                if (is_numeric($data['pickup_country'])) {
                    $countries = $this->model_localisation_country->getCountry($data['pickup_country']);
                    $ccountry = $countries['iso_code_2'];
                } else {
                    $ccountry = $data['pickup_country'];
                }

                $cphone = $data['pickup_contact'];
                $cemail = $data['pickup_contact_email'];

                $rname = $data['recipient_name'];
                $rname0 = substr($rname, 0, 35);
                $rname1 = substr($rname, 35, 35);

                $rstreet = $data['recipient_pickup_address'];
                $rpostal = $data['recipient_pickup_postcode'];
                $rcity = $data['recipient_pickup_city'];

                if (is_numeric($data['recipient_pickup_country'])) {
                    $rcountries = $this->model_localisation_country->getCountry($data['recipient_pickup_country']);
                    $rcountry = $rcountries['iso_code_2'];
                } else {
                    $rcountry = $data['recipient_pickup_country'];
                }

                $rphone = $data['recipient_pickup_contact'];
                $remail = $data['recipient_pickup_contact_email'];

                if (isset($data['pickup_date']) && $data['pickup_date']) {
                    $pickup_date = $data['pickup_date'];
                } else {
                    $pickup_date = isset($data['pickup_parcels_date']) ? $data['pickup_parcels_date'] : date('Y-m-d', strtotime('+1 day'));
                }

                $info1 = $parcels_no . $pallets_no . $pickup_date . $weight;

                $info2 = $data['pickup_parcels_additional_information'];

                $params = array(
                    'action' => 'crImport_',
                    'cstreet' => $cstreet,
                    'ccountry' => strtoupper($ccountry),
                    'cpostal' => $cpostal,
                    'ccity' => $ccity,
                    'info1' => $info1,
                    'rstreet' => $rstreet,
                    'rpostal' => $rpostal,
                    'rcountry' => strtoupper($rcountry),
                    'rcity' => $rcity
                );

                if (isset($cphone) && $cphone) {
                    $params['cphone'] = $cphone;
                }

                if (isset($cemail) && $cemail) {
                    $params['cemail'] = $cemail;
                }

                if (isset($rphone) && $rphone) {
                    $params['rphone'] = $rphone;
                }

                if (isset($remail) && $remail) {
                    $params['remail'] = $remail;
                }

                if (isset($info2) && $info2) {
                    $params['info2'] = $info2;
                }

                if (isset($cname0) && $cname0) {
                    $params['cname'] = $cname0;
                }

                if (isset($cname1) && $cname1) {
                    $params['cname1'] = $cname1;
                }

                if (isset($cname2) && $cname2) {
                    $params['cname2'] = $cname2;
                }

                if (isset($cname3) && $cname3) {
                    $params['cname3'] = $cname3;
                }

                if (isset($rname0) && $rname0) {
                    $params['rname'] = $rname0;
                }

                if (isset($rname1) && $rname1) {
                    $params['rname2'] = $rname1;
                }

                $result = $this->getRequest($params);

                if ($i >= $total_amount) {
                    break;
                }
            }
        }

        return $result;
    }

    // 7.0 Shipment cancelation
    public function cancel_Shipment($tracking_number = null) {
        $new_tracking_numbers = explode('|', $tracking_number);
        $params = array(
            'action' => 'parcelDelete_',
            'parcels' => $tracking_number
        );

        $result = $this->getRequest($params);

        return $result;
    }

    // 9.0 Get parcel status
    public function getParcelStatus($order_id = null) {
        $barcodes = $this->getOrderBarcode($order_id);

        $results = array();

        if ($barcodes) {
            foreach ($barcodes as $key => $barcode) {
                $params = array(
                    'action' => 'parcelStatus_',
                    'parcel_number' => $barcode['dpd_barcode']
                );

                $result = $this->getRequest($params);
                $result['barcode'] = $barcode['dpd_barcode'];

                array_push($results, $result);
            }

        } else {
            $results = [
                'status' => 'err',
                'errlog' => 'Barcode not found'
            ];
        }

        return $results;
    }

    public function reverseCollectionRequest($order_id, $warehouse_id) {
        $this->load->model('extension/shipping/dialcodehelper');

        // Get order data
        $this->load->model('sale/order');

        $this->load->model('catalog/product');

        $order_data = $this->model_sale_order->getOrder($order_id);

        // Get info of order products
        $products = $this->model_sale_order->getOrderProducts($order_id);
        $weight_total = 0;

        // Get order weight
        foreach ($products as $product) {
            $product_data = $this->model_catalog_product->getProduct($product['product_id']);
            $weight_total += $product_data['weight'];
        }

        $country_code = $order_data['shipping_iso_code_2'];
        if (strtoupper($country_code) == 'LT' or strtoupper($country_code) == 'LV' or strtoupper($country_code) == 'EE') {
            $pcode = preg_replace('/[^0-9,.]/', '', $order_data['shipping_postcode']);
        } else {
            $pcode = $order_data['shipping_postcode'];
        }

        $correct_phone = $this->model_extension_shipping_dialcodehelper->separatePhoneNumberFromCountryCode($order_data['telephone'], $country_code);

        $data['pickup_name'] = $order_data['shipping_firstname'] . ' ' . $order_data['shipping_lastname'];
        $data['pickup_address'] = $this->custom_length($order_data['shipping_address_1'], 35);
        $data['pickup_postcode'] = $pcode;
        $data['pickup_city'] =  $this->custom_length($order_data['shipping_city'], 25);
        $data['pickup_contact'] = $correct_phone['dial_code'] . $correct_phone['phone_number'];
        $data['pickup_contact_email'] = $order_data['email'];
        $data['pickup_country'] = $country_code;
        $data['parcels'] = '1';
        $data['pallets'] = '0';

        $data['weight'] = $weight_total;
        $data['pickup_date'] = date('Y-m-d', strtotime('+1 day'));

        $data['pickup_parcels_additional_information'] = 'product return';

        $warehouse_info = $this->getWarehouse($warehouse_id);

        $data['recipient_name'] = $warehouse_info['name'];
        $data['recipient_pickup_address'] = $warehouse_info['address'];
        $data['recipient_pickup_postcode'] = $warehouse_info['postcode'];
        $data['recipient_pickup_city'] = $warehouse_info['city'];
        $data['recipient_pickup_country'] = $warehouse_info['country_id'];
        $data['recipient_pickup_contact'] = $warehouse_info['contact_person_name'];
        $data['recipient_pickup_contact_email'] = $this->config->get('config_email');

        $result = $this->requestCollection($data);

        return $result;
    }

    // Update terminal list
    public function updateTerminalList() {
        $this->install();

        $test_login = $this->checkConnection();

        // Check is parcels filled
        $total_terminals = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "terminals_list");
        $date_updated = $this->db->query("SELECT `date_updated` FROM " . DB_PREFIX . "terminals_list");

        // if (count($date_updated->num_rows) <= 0) {
        if ($date_updated->num_rows <= 0) {
            $status = $this->getTerminalList();
        } else {
            // Data is older than 1 days, so delete all date, and update with new one
            if (isset($date_updated->row['date_updated'])) {
                $date = $date_updated->row['date_updated'];
            } else {
                $date = '';
            }

            if (strtotime($date) < strtotime(date('Y-m-d', strtotime('-1 days', time())))) {
                $this->db->query("TRUNCATE `" . DB_PREFIX . "terminals_list`");
                $status = $this->getTerminalList();
            }
        }

        return $test_login;
    }

    private function checkConnection() {
        $params = array(
            'action' => 'parcelStatus_',
            'parcel_number' => ''
        );

        $result = $this->getRequest($params);

        if (isset($result['status']) && $result['status'] == 'err') {
            if ($result['errlog'] == 'Unable to find user to authenticate!') {
                $status = 'Unable to find user to authenticate!';
            } else {
                $status = '';
            }
        } else {
            $status = '';
        }

        return $status;
    }

    private function getWarehouse($warehouse_id) {
        $warehouses = $this->config->get('dpd_setting_warehouse');

        foreach ($warehouses as $key => $warehouse) {
            if ($key == $warehouse_id) {
                return $warehouse;
            }
        }

        return '';
    }

    private function custom_length($x, $length) {
        if(strlen($x) <= $length) {

            return $x;
        } else {
            $y = substr($x, 0, $length);
            return $y;
        }
    }

    private function removeSpecials($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // // trim
        // $text = trim($text, '-');

        // // remove duplicate -
        // $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    function getRequest($params = array('action' => 'parcelshop_info'), $url = null) {
        $api_url_by_country = $this->config->get("dpd_setting_api_url");

        switch ($api_url_by_country) {
            case 'lt':
                $api_url = 'https://integracijos.dpd.lt/';
                $params['PluginVersion'] = '1';
                $params['EshopVersion'] = 'Opencart ' . VERSION;

                break;
            case 'lv':
                $api_url = 'https://integration.dpd.lv/';
                break;
            case 'ee':
                $api_url = 'https://integration.dpd.ee/';
                break;
            default:
                $api_url = 'https://integracijos.dpd.lt/';
        }

        // if (!$url) {
        $url = $api_url . 'ws-mapper-rest/' . $params['action'];
        // }


        if ($params['action'] != 'crImport_') {
            $url .= '?';
        }

        $auth = '';

        $params['username'] = $this->config->get("dpd_setting_api_username");
        $params['password'] = $this->config->get("dpd_setting_api_password");

        $logRequest = array(
            'url' => $url,
            'request' => $params,
            'response' => '',
        );

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => $auth . "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($params),
                'timeout' => 60
            ),
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            )
        );

        $context = stream_context_create($options);

        $postRequestResult = null;

        $currenttime = time();

        set_error_handler(
            function($errno, $errstr, $errfile, $errline)
            {
                /*echo $errstr . " in file " . $errfile . " on line " . $errline . ". Debug backtrace: <pre>" . print_r(debug_backtrace(), true) . "</pre>";*/
            }
        );

        try {

            $postRequestResult = file_get_contents($url, false, $context);
        }

        catch (Exception $e) {
            echo $e->getMessage();
        }

        restore_error_handler();

        if($params['action']=='parcelPrint_' || $params['action'] == 'parcelManifestPrint_' || $params['action'] == 'crImport_'){
            return $postRequestResult;
        }
        $body = @json_decode($postRequestResult, true);

        if ($params['action'] == 'pickupOrderSave_') {
            if(strcmp(substr($postRequestResult, 3, 4), "DONE") == 0){
                return "DONE|";
            } else {
                return $postRequestResult;
            }
        }

        // if (!is_array($body) || !isset($body['errlog']) || $body['errlog'] !== '') {
        //     //$translatedText = sprintf($this->l('DPD request failed with response: %s'), print_r($postRequestResult, true));
        //     $logRequest['response'] = $body;
        //     //$this->_addLogRequest($logRequest);

        //     //throw new Exception($translatedText);
        // }

        $logRequest['response'] = $body;

        //$this->_addLogRequest($logRequest);

        return $logRequest['response'];
    }
}