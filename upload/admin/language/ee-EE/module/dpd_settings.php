<?php
// Heading
$_['heading_title']    = '<img src="view/image/dpd_logo.png" title="DPD Moodul" alt="DPD Moodul" style="height: 22px; margin-right: 15px; vertical-align: bottom;" /> DPD Moodul';
$_['text_module']      = 'Moodulid';
$_['text_success']     = 'DPD mooduli muutmine oli edukas!';
$_['text_success_labels_no_changed'] = 'Trükitavate siltide kogus on edukalt muudetud!';
$_['text_courier_success'] = 'Kuller on edukalt tellitud. Kuller saabub vahemikus: <strong>%s - %s</strong>';
$_['text_error_response'] = 'DPD vastus:';
$_['error_courier_shipment'] = 'Kullerit ei tellitud! Olete sisestanud vale kuupäeva või kellaaja. Palun parandage sisestatud andmed, et tellimust salvestada!';
$_['text_edit']        = '<img src="view/image/dpd_logo.png" title="DPD Extension" alt="DPD Extension" style="height: 22px; margin-right: 15px; vertical-align: middle;" /> DPD mooduli seadistamine';
$_['text_success_dpd_canceled'] = 'Valitud tellimused on edukalt tühistatud!';
$_['text_dpd_order_successfully_canceled'] = 'DPD saadetis antud tellimusele on tühistatud!';
$_['text_order_error'] = 'Viga tellimuses:';
$_['text_one_shipment'] = 'Kõik tooted samas saadetises';
$_['text_separate_shipment'] = 'Iga toode eraldi saadetises';
$_['text_separate_quantity_shipment'] = 'Iga tootekogus eraldi saadetises';

// Tabs strings
$_['tab_general'] = 'Üldine seadistus';
$_['tab_collection_request'] = 'Korje kolmanda osapoole juurest';
$_['tab_company'] = 'Lao seadistus';
$_['tab_parcel_configuration'] = 'Saadetiste seadistus';

// Entries
$_['entry_request'] = 'Päring';
$_['entry_pickup_title'] = 'Kustkohast me teie pakid peale korjame?';
$_['entry_pickup_name'] = 'Saatja nimi: <span data-toggle="tooltip" title="" data-original-title="Pealekorje nimi. Maksimaalselt 140 sümbolit" class="custom-tooltip"></span>';
$_['entry_pickup_address'] = 'Saatja aadress: <span data-toggle="tooltip" title="" data-original-title="Pealekorje aadress. Maksimaalselt 35 sümbolit" class="custom-tooltip"></span>';
$_['entry_pickup_postcode'] = 'Saatja postiindeks: <span data-toggle="tooltip" title="" data-original-title="Pealekorje postiindeks ilma riigikoodita. Maksimaalselt 8 numbrit" class="custom-tooltip"></span>';
$_['entry_pickup_city'] = 'Saatja linn:';
$_['entry_pickup_country'] = 'Saatja riik:';
$_['entry_pickup_contact'] = 'Kontaktisiku telefoninumber:';
$_['entry_pickup_contact_email'] = 'Kontaktisiku e-maili aadress:';
$_['entry_pickup_recipient_title'] = 'Kuhu me teie pakid viima peaksime?';
$_['entry_placeholder_weight'] = 'Kogukaal kilogrammides';
$_['entry_pickup_recipient_name'] = 'Saaja nimi: <span data-toggle="tooltip" title="" data-original-title="Sisestage saaja nimi. Maksimaalselt 70 sümbolit" class="custom-tooltip"></span>';
$_['entry_pickup_recipient_address'] = 'Saaja aadress: <span data-toggle="tooltip" title="" data-original-title="Sisestage saaja aadress. Maksimaalselt 35 sümbolit" class="custom-tooltip"></span>';
$_['entry_pickup_recipient_postcode'] = 'Saaja postiindeks: <span data-toggle="tooltip" title="" data-original-title="Sisestage saaja postiindeks ilma riigikoodita. Maksimaalselt 8 numbrit" class="custom-tooltip"></span>';
$_['entry_pickup_recipient_city'] = 'Saaja linn:';
$_['entry_pickup_recipient_country'] = 'Saaja riik:';
$_['entry_parcels_title'] = 'Täpsem info teie pakkide / aluste kohta';
$_['entry_pickup_parcels_information'] = 'Sisestage pakkide / aluste arv:';
$_['entry_placeholder_parcels'] = 'Pakkide arv';
$_['entry_placeholder_pallets'] = 'Aluste arv';
$_['entry_pickup_parcels_additional_information'] = 'Lisainformatsioon <span data-toggle="tooltip" data-html="true" title="" data-original-title="(tellimuse number)"></span>';

$_['entry_dpd_setting_api_username'] = 'Kasutajanimi:';
$_['entry_dpd_setting_api_password'] = 'Parool:';
$_['entry_dpd_setting_api_url'] = 'API Url:';
$_['entry_dpd_setting_price_calculation'] = 'Saatmise hinna arvutamine (Kuller)';
$_['entry_dpd_setting_price_calculation_parcels'] = 'Saatmise hinna arvutamine (Pickup punktid)';
$_['entry_dpd_setting_google_map_api_key'] = 'Google Maps API võti:<br /><a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank"><small>Saa võti</small></a><span data-toggle="tooltip" title="" data-original-title="Google Mapsi kasutatakse postiindeksite leidmiseks ning Pickup punktide kaardi kuvamiseks" class="custom-tooltip"></span>';
$_['entry_dpd_tracking_number'] = 'DPD jälgimisnumber:';
$_['entry_dpd_label_size'] = 'Vaikimisi sildiformaat';
$_['entry_dpd_parcel_distribution'] = 'Pakkide jaotus:';
$_['entry_help_manifest'] = 'Manifest on suletud. DPD ei nõua manifesti välja printimist, aga printimise soovi korral minge <a href="%s" target="_blank">siia</a> ja valige Manifesti sektsioon';
$_['entry_dpd_rod_services'] = 'Luba ROD / Dokumenditagastus:';
$_['entry_dpd_return_services'] = 'Luba tagastussiltide printimine?  <span data-toggle="tooltip" title="" data-original-title="Lubamise korral saate DPD silti printides kaks silti: üks paki jaoks ning teine kaasa panekuks, mida klient saab hiljem kauba tagastamiseks kasutada" class="custom-tooltip"></span>';
$_['info_warehouses'] = 'Kõik väljad peavad olema täidetud. Siin saab sisestada andmed oma lao kohta. Lisada saab rohkem kui ühe lao millest DPD korjeid teeb.';
$_['entry_pickup_parcels_date'] = 'Sisestage kuupäev millal korje toimuma peaks:';

$_['text_action'] = 'Tegevus';
$_['text_print'] = 'Prindi';
$_['text_current_manifest_day'] = 'Manifest on suletud';
$_['text_select'] = '-- Vali riik --';
$_['text_per_item'] = 'Ühiku kohta';
$_['text_per_order'] = 'Tellimuse kohta';
$_['text_per_weight'] = 'Kaalupõhine';
$_['entry_name'] = 'Nimi';
$_['entry_address'] = 'Aadress';
$_['entry_postcode'] = 'Postiindeks';
$_['entry_city'] = 'Linn';
$_['entry_contact_person_name'] = 'Kontaktisik';
$_['entry_warehouse_phone'] = 'Lao telefoninumber';
$_['entry_warehouse_working_hours'] = '17:00';
$_['entry_warehouse_pickup_time'] = '12:30';
$_['entry_select_warehouse'] = 'Vali ladu: <span data-toggle="tooltip" title="" data-original-title="Pakkide pealekorjeks on võimalik erinevaid aadresse valida" class="custom-tooltip"></span>';
$_['entry_select_warehouse_return'] = 'Vali ladu: <span data-toggle="tooltip" title="" data-original-title="Tagastuse jaoks saab valida aadressi kuhu kuller tooted tagastama peaks" class="custom-tooltip"></span>';
$_['entry_pallets_no'] = 'Aluste arv:';
$_['entry_parcels_no'] = 'Pakkide arv:';
$_['entry_comment_for_courier'] = 'Kommentaar kullerile:';
$_['entry_pickuptime'] = 'Pealekorje aeg:';

$_['column_name'] = 'Lao nimi';
$_['column_address'] = 'Aadress';
$_['column_postcode'] = 'Postiindeks';
$_['column_city'] = 'Linn';
$_['column_country'] = 'Riik';
$_['column_contact_person'] = 'Kontaktisik';
$_['column_phone'] = 'Telefon';

// Buttons
$_['button_warehouse_add'] = 'Lisa ladu';
$_['button_request_dpd'] = 'Loo pealekorje kolmanda osapoole juurest';
$_['button_request_dpd_courier'] = 'Kutsu kuller';

// Errors
$_['error_dpd_setting_google_map_api_key'] = 'Google Map API võti on kohustuslik!';
$_['error_dpd_setting_api_username'] = 'DPD kasutajanimi on kohustuslik!';
$_['error_dpd_setting_api_password'] = 'DPD parool on kohustuslik!';
$_['error_dpd_setting_api_url'] = 'API URL on kohustuslik!';
$_['error_dpd_setting_parcel_distribution'] = 'Valige tüüp';
$_['error_non_dpd_orders_selected'] = 'Palun valige tellimused millele the DPD silte printida soovite!';
$_['error_non_dpd_orders_canceled'] = 'Palun vallige tellimused mida soovite tühistada!';
$_['error_non_dpd_orders'] = 'Silte pole võimalik printida, järgnevad tellimused pole DPD saatmismeetodiga tehtud: ID <strong>%s</strong>';
$_['error_non_tracking_numbers'] = 'Ei saa tühistada, valitud tellimustel pole veel DPD pakinumbrit.';
$_['error_shipping_method_no_dpd'] = 'Saatmismeetod pole DPD';
$_['error_labels_number_empty'] = 'Error: Palun sisestage soovitud siltide arv. Number peab olema suurem kui 0!';
$_['error_weight'] = 'Sisestage kaal';
$_['error_parcels'] = 'Sisestage pakkide arv';
$_['text_from'] = 'Alates';
$_['text_until'] = 'Kuni';
$_['text_success_note_added'] = 'Dokumenditagastuse viitenumber on lisatud!';
$_['text_success_collection_requested'] = 'Teie andmed on DPD-le edastatud ning kuller on tellitud. Lisainfo saamiseks võtke DPD-ga ühendust';
$_['error_pickup_name'] = 'Kohustuslik väli! Nimi peab olema 1 kuni 140 sümbolit pikk';
$_['error_pickup_address'] = 'Kohustuslik väli! Aadress peab olema 1 kuni 35 sümbolit pikk';
$_['error_pickup_postcode'] = 'Kohustuslik väli! Postiindeks peab olema 1 kuni 8 numbrit pikk';
$_['error_pickup_city'] = 'Kohustuslik väli! Linn peab olema 1 kuni 25 sümbolit';
$_['error_pickup_country'] = 'Kohustuslik väli!';
$_['error_recipient_name'] = 'Kohustuslik väli! Saaja nimi peab olema 1 kuni 70 sümbolit pikk';
$_['error_recipient_pickup_address'] = 'Kohustuslik väli! Saaja aadress peab olema 1 kuni 35 sümbolit pikk';
$_['error_recipient_pickup_postcode'] = 'Kohustuslik väli! Saaja postiindeks peab olema 1 kuni 8 numbrit pikk';
$_['error_recipient_pickup_city'] = 'Kohustuslik väli! Saaja linn peab olema 1 kuni 25 sümbolit pikk';
$_['error_recipient_pickup_country'] = 'Kohustuslik väli!';