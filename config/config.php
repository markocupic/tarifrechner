<?php
/**
 * Created by PhpStorm.
 * User: Marko Cupic, m.cupic@gmx.ch
 * Date: 06.11.2016
 * Time: 22:03
 */


// Mwst-Satz 19%
define('HUF_MWST', 19);


if (TL_MODE == 'FE')
{

    $GLOBALS['TARIFRECHNER']['FORM_IDS'] = array(2, 3, 4, 5);

    // Javascript
    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/tarifrechner/assets/tarifrechner.js|static';

    // replaceInsertTags f.ex. 'tarifrechner::ctrlGewicht'
    $GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('HUF\Tarifrechner', 'replaceInsertTags');
    $GLOBALS['TL_HOOKS']['prepareFormData'][] = array('HUF\Tarifrechner', 'prepareFormData');
    $GLOBALS['TL_HOOKS']['processFormData'][] = array('HUF\Tarifrechner', 'processFormData');
    $GLOBALS['TL_HOOKS']['compileFormFields'][] = array('HUF\Tarifrechner', 'compileFormFields');
    $GLOBALS['TL_HOOKS']['validateFormField'][] = array('HUF\Tarifrechner', 'validateFormField');


    // Ajax Request
    if (Environment::get('isAjaxRequest') && Input::get('isAjax') && Input::get('table') && Input::get('act') == 'getPrice')
    {
        HUF\Tarifrechner::getPrice();
    }

    $GLOBALS['TARIFRECHNER']['labels'] = array(
        'ctrlRollgeldNetto' => 'Rollgeld Netto',
        'ctrlRollgeldMwst' => 'Rollgeld Mwst',
        'ctrlRollgeldBrutto' => 'Rollgeld Brutto',
        'ctrlGewicht' => 'Gewicht',
        'ctrlFirmName' => 'Firma',
        'ctrlCustomerName' => 'Kundename',
        'ctrlCustomerStreet' => 'Straße/Nr.',
        'ctrlCustomerCity' => 'PLZ/Ort',
        'ctrlCustomerPhone' => 'Telefon',
        'ctrlCustomerEmail' => 'E-Mail',

        'ctrlPickupAddress' => 'Abholadresse',
        'ctrlPickupAddressName' => 'Firma',
        'ctrlPickupAddressStreet' => 'Straße',
        'ctrlPickupAddressContact' => 'Kontaktperson',
        'ctrlPickupAdressPhone' => 'Telefon',

        'ctrlDeliveryAddress' => 'Lieferadresse',
        'ctrlDeliveryAddressName' => 'Firma',
        'ctrlDeliveryAddressStreet' => 'Strasse',
        'ctrlDeliveryAddressPhone' => 'Telefon',
        'ctrlDeliveryAddressContact' => 'Kontaktperson',

        'ctrlPickupDate' => 'Gew&uuml;nschtes Transportdatum',
        'ctrlMessage' => 'Beschreibung Transportgut'
    );


}
