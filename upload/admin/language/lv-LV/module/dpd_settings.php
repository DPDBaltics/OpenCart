<?php
// Heading
$_['heading_title']    = '<img src="view/image/dpd_logo.png" title="DPD Piegādes spraudnis" alt="DPD Piegādes spraudnis" style="height: 22px; margin-right: 15px; vertical-align: bottom;" /> DPD Piegādes spraudnis';
$_['text_module']      = 'Moduļi';
$_['text_success']     = 'DPD modulis ir veiksmīgi iestatīts!';
$_['text_success_labels_no_changed'] = 'Pavadlapu skaits sūtījumā ir veiksmīgi izmainīts!';
$_['text_courier_success'] = 'Kurjera pieteikums ir veiksmīgi saglabāts. Kurjers ieradīsies: <strong>%s - %s</strong>';
$_['text_error_response'] = 'DPD atbilde:';
$_['error_courier_shipment'] = 'Kurjera pieteikums netika saglabāts! Jūs esat ievadījis neatbilstošu datumu vai laiku. Lūdzu, izmainiet ievadītos datus un saglabājiet kurjera pieteikumu!';
$_['text_edit']        = '<img src="view/image/dpd_logo.png" title="DPD Piegādes spraudnis" alt="DPD Piegādes spraudnis" style="height: 22px; margin-right: 15px; vertical-align: middle;" /> Mainīt iestatījumus DPD modulī';
$_['text_success_dpd_canceled'] = 'Izvēlētie DPD sūtījumi ir veiksmīgi atcelti!';
$_['text_dpd_order_successfully_canceled'] = 'Izvēlētais DPD sūtījums ir veiksmīgi atcelts!';
$_['text_order_error'] = 'Pasūtījuma kļūda:';
$_['text_one_shipment'] = 'Visas preces no pasūtījuma tiks iepakotas vienā pakā';
$_['text_separate_shipment'] = 'Katra prece pasūtījumā tiks iepakota atsevišķā pakā';
$_['text_separate_quantity_shipment'] = 'Atsevišķās pakās tiks iepakots katras preces veida kopējais daudzums no pasūtījuma';

// Tabs strings
$_['tab_general'] = 'Vispārēji iestatījumi';
$_['tab_collection_request'] = 'Paņemšanas pieprasījums';
$_['tab_company'] = 'Noliktavas iestatījumi';
$_['tab_parcel_configuration'] = 'Sūtījumu iestatījumi';

// Entries
$_['entry_request'] = 'Saglabāt paņemšanas pieprasījumu';
$_['entry_pickup_title'] = 'Ievadiet informāciju par nosūtītāju!';
$_['entry_pickup_name'] = 'Nosūtītājs: <span data-toggle="tooltip" title="" data-original-title="Nosaukums vai vārds, no kā ir jāpaņem sūtījumi. Maksimālais simbolu skaits - 140." class="custom-tooltip"></span>';
$_['entry_pickup_address'] = 'Nosūtītāja adrese: <span data-toggle="tooltip" title="" data-original-title="Nosūtītāja iela, ēkas un dz. nr. vai mājas, ciema nosaukums. Maksimālais simbolu skaits - 35." class="custom-tooltip"></span>';
$_['entry_pickup_postcode'] = 'Nosūtītāja pasta indekss: <span data-toggle="tooltip" title="" data-original-title="Ievadiet pasta indeksu bez valsts koda un atstarpēm! Piem. 1005. Maksimālais simbolu skaits - 8." class="custom-tooltip"></span>';
$_['entry_pickup_city'] = 'Nosūtītāja pilsēta:';
$_['entry_pickup_country'] = 'Nosūtītāja valsts:';
$_['entry_pickup_contact'] = 'Kontakttālrunis:';
$_['entry_pickup_contact_email'] = 'E-pasts:';
$_['entry_pickup_recipient_title'] = 'Ievadiet informāciju par saņēmēju!';
$_['entry_placeholder_weight'] = 'Kopējais svars, kg';
$_['entry_pickup_recipient_name'] = 'Saņēmējs: <span data-toggle="tooltip" title="" data-original-title="Nosaukums vai vārds, kam jāpiegādā sūtījumi. Maksimālais simbolu skaits - 70." class="custom-tooltip"></span>';
$_['entry_pickup_recipient_address'] = 'Saņēmēja adrese: <span data-toggle="tooltip" title="" data-original-title="Saņēmēja iela, ēkas un dz. nr. vai mājas, ciema nosaukums. Maksimālais simbolu skaits - 35." class="custom-tooltip"></span>';
$_['entry_pickup_recipient_postcode'] = 'Saņēmēja pasta indekss: <span data-toggle="tooltip" title="" data-original-title="Ievadiet pasta indeksu bez valsts koda un atstarpēm! Piem. 1005. Maksimālais simbolu skaits - 8." class="custom-tooltip"></span>';
$_['entry_pickup_recipient_city'] = 'Saņēmēja pilsēta:';
$_['entry_pickup_recipient_country'] = 'Saņēmēja valsts:';
$_['entry_parcels_title'] = 'Ievadiet informāciju par sūtījuma vienībām!';
$_['entry_pickup_parcels_information'] = 'Ievadiet paku/ palešu skaitu:';
$_['entry_placeholder_parcels'] = 'Ievadiet paku skaitu';
$_['entry_placeholder_pallets'] = 'Ievadiet palešu skaitu';
$_['entry_pickup_parcels_additional_information'] = 'Papildus informācija <span data-toggle="tooltip" data-html="true" title="" data-original-title="(order number)"></span>';

$_['entry_dpd_setting_api_username'] = 'Lietotājvārds:';
$_['entry_dpd_setting_api_password'] = 'Parole:';
$_['entry_dpd_setting_api_url'] = 'API Url:';
$_['entry_dpd_setting_price_calculation'] = 'Piegādes cenas aprēķina metode (kurjerpiegādēm)';
$_['entry_dpd_setting_price_calculation_parcels'] = 'Piegādes cenas aprēķina metode (Pickup tīklam)';
$_['entry_dpd_setting_google_map_api_key'] = 'Google Map API key:<br /><a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank"><small>Get your key</small></a><span data-toggle="tooltip" title="" data-original-title="Google kartes tiks izmantotas pasta indeksa aizpildīšanai no saņēmēju ievadītās adreses, kā arī Pickup punktu kartes atspoguļošanai." class="custom-tooltip"></span>';
$_['entry_dpd_tracking_number'] = 'DPD Pakas numurs:';
$_['entry_dpd_label_size'] = 'Pavadlapas izdrukas izmērs';
$_['entry_dpd_parcel_distribution'] = 'Pavadlapu drukāšanas veids:';
$_['entry_help_manifest'] = 'Manifests ir aizvērts. Ja vēlaties to izdrukāt, dodieties uz <a href="%s" target="_blank">here</a> un atveriet manifestu sadaļu!';
$_['entry_dpd_rod_services'] = 'Aktivizēt ROD / Dokumentu atgriešanas pakalpojumu:';
$_['entry_dpd_return_services'] = 'Vai aktivizēt atgriešanas pavadlapu drukāšanu?  <span data-toggle="tooltip" title="" data-original-title="Ja atgriešanas pavadlapu drukāšana tiks aktivizēta, drukājot DPD sūtījuma piegādes pavadlapu, automātiski tiks izdrukāta arī pavadlapa priekš Jūsu klienta, ja tas vēlēsies atgriezt preci, nododot to Pickup punktos." class="custom-tooltip"></span>';
$_['info_warehouses'] = 'Visiem laukiem ir jābūt aizpildītiem! Šeit ir jāievada noliktavas adrese un kontaktpersona, no kuras būs jāpaņem sūtījumi. Jūs varat pievienot vairākas nosūtīšanas adreses.';
$_['entry_pickup_parcels_date'] = 'Ievadiet datumu, kurā kurjeram ir jāierodas pakaļ sūtījumiem:';

$_['text_action'] = 'Iespējas';
$_['text_print'] = 'Drukāt';
$_['text_current_manifest_day'] = 'Manifests aizvērts';
$_['text_select'] = '-- Izvēlieties valsti --';
$_['text_per_item'] = 'Par katru preci';
$_['text_per_order'] = 'Par kopējo pasūtījumu';
$_['text_per_weight'] = 'Pēc svara';
$_['entry_name'] = 'Noliktavas nosaukums';
$_['entry_address'] = 'Adrese';
$_['entry_postcode'] = 'Pasta indekss';
$_['entry_city'] = 'Pilsēta';
$_['entry_contact_person_name'] = 'Kontaktpersona';
$_['entry_warehouse_phone'] = 'Tālrunis';
$_['entry_warehouse_working_hours'] = '17:00';
$_['entry_warehouse_pickup_time'] = '12:30';
$_['entry_select_warehouse'] = 'Izvēlieties noliktavu: <span data-toggle="tooltip" title="" data-original-title="Jūs varat izvēlēties dažādas adreses, no kurām paņemt sūtījumus." class="custom-tooltip"></span>';
$_['entry_select_warehouse_return'] = 'Izvēlieties noliktavu: <span data-toggle="tooltip" title="" data-original-title="Jūs varat izvēlēties vienu noliktavu, uz kuru kurjeram piegādāt atgrieztās preces." class="custom-tooltip"></span>';
$_['entry_pallets_no'] = 'Palešu skaits:';
$_['entry_parcels_no'] = 'Paku skaits:';
$_['entry_comment_for_courier'] = 'Informācija:';
$_['entry_pickuptime'] = 'Paņemšanas laiks:';

$_['column_name'] = 'Noliktavas nosaukums';
$_['column_address'] = 'Adrese';
$_['column_postcode'] = 'pasta indekss';
$_['column_city'] = 'Pilsēta';
$_['column_country'] = 'Valsts';
$_['column_contact_person'] = 'Kontaktpersona';
$_['column_phone'] = 'Tālrunis';

// Buttons
$_['button_warehouse_add'] = 'Pievienot noliktavu';
$_['button_request_dpd'] = 'Izveidot paņemšanas pieprasījumu';
$_['button_request_dpd_courier'] = 'Pieteikt kurjeru';

// Errors
$_['error_dpd_setting_google_map_api_key'] = 'Google Map API key ir obligāts!';
$_['error_dpd_setting_api_username'] = 'DPD lietotājvārds ir obligāts!';
$_['error_dpd_setting_api_password'] = 'DPD parole ir obligāta!';
$_['error_dpd_setting_api_url'] = 'API URL ir obligāts!';
$_['error_dpd_setting_parcel_distribution'] = 'Izvēlieties metodi!';
$_['error_non_dpd_orders_selected'] = 'Lūdzu, atzīmējiet pasūtījumus, kuriem vēlaties izdrukāt DPD pavadlapas!';
$_['error_non_dpd_orders_canceled'] = 'Lūdzu, atzīmējiet sūtījumus, kuriem vēlaties atcelt izdrukātās DPD pavadlapas!';
$_['error_non_dpd_orders'] = 'Pasūtījumos Nr. <strong>%s</strong> nav izvēlēta DPD piegāde, pavadlapas nevar izdrukāt!';
$_['error_non_tracking_numbers'] = 'Atzīmētajiem pasūtījumiem nav izdrukātas pavadlapas, tādēļ tās nevar atcelt.';
$_['error_shipping_method_no_dpd'] = 'Nav izvēlēta DPD piegāde';
$_['error_labels_number_empty'] = 'Kļūda: Lūdzu, norādiet pavadlapu skaitu, cik Jūs vēlaties izdrukāt! Tam ir jābūt lielākam par 0!';
$_['error_weight'] = 'Ievadiet svaru (kg)';
$_['error_parcels'] = 'Ievadiet paku skaitu';
$_['text_from'] = 'No';
$_['text_until'] = 'Līdz';
$_['text_success_note_added'] = 'Dokumentu atgriešanas pakalpojums (ROD) ir noformēts!';
$_['text_success_collection_requested'] = 'Kurjera pieteikums ir izveidots! Kurjers ierdīsies Jūsu norādītājā laikā.';
$_['error_pickup_name'] = 'Lauks ir obligāts! Nosaukumam ir jābūt 1-140 simbolu garam';
$_['error_pickup_address'] = 'Lauks ir obligāts! Adresei ir jābūt 1-35 simbolu garam';
$_['error_pickup_postcode'] = 'Lauks ir obligāts! Pasta ir indeksam jāsastāv no 1-8 cipariem';
$_['error_pickup_city'] = 'Lauks ir obligāts! Pilsētas nosaukumam ir jāsastāv no 1-25 simboliem';
$_['error_pickup_country'] = 'Lauks ir obligāts!';
$_['error_recipient_name'] = 'Lauks ir obligāts! Saņēmēja vārdam ir jābūt 1-70 simbolu garam';
$_['error_recipient_pickup_address'] = 'Lauks ir obligāts! Saņēmēja adresei ir jābūt 1-35 simbolu garai';
$_['error_recipient_pickup_postcode'] = 'Lauks ir obligāts! Pasta ir indeksam jāsastāv no 1-8 cipariem';
$_['error_recipient_pickup_city'] = 'Lauks ir obligāts! Pilsētas nosaukumam ir jāsastāv no 1-25 simboliem';
$_['error_recipient_pickup_country'] = 'Lauks ir obligāts!';