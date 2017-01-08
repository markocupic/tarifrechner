<?php
/**
 * Created by PhpStorm.
 * User: Marko Cupic, m.cupic@gmx.ch
 * Date: 06.11.2016
 * Time: 22:03
 */

namespace HUF;


class Tarifrechner
{
    /**
     * @return string
     */
    public static function getPrice()
    {
        $table = \Input::get('table');
        $weight = \Input::get('weight') == '' ? 0 : \Input::get('weight');
        $arrPrice = self::getAssignedPrice($weight, $table);
        echo json_encode($arrPrice);
        exit;

    }

    /**
     * @param $strTag
     * @return bool
     */
    public function replaceInsertTags($strTag)
    {
        if (strpos($strTag, 'FORM_DATA::') !== false)
        {
            return self::getFromSession(str_replace('FORM_DATA::', '', $strTag));
        }

        return false;
    }

    /**
     * @param $key
     * @return bool
     */
    public function getFromSession($key)
    {
        if (isset($_SESSION['FORM_DATA'][$key]))
        {
            return $_SESSION['FORM_DATA'][$key];
        }
        return false;
    }

    /**
     * @param $objWidget
     * @param $formId
     * @param $this
     */
    public function validateFormField($objWidget, $formId, $arrData, $objForm)
    {
        if (in_array($objForm->id, $GLOBALS['TARIFRECHNER']['FORM_IDS']))
        {
            $weight = \Input::post('ctrlGewicht');
            $table = \Input::post('ctrlDatentabelle');
            if ($weight != '' && $table != '')
            {
                // Check for valid weight
                if ($objWidget->name == 'ctrlGewicht')
                {
                    $arrPrice = self::getAssignedPrice($weight, $table);
                    if ($arrPrice['validWeight'] != 'true')
                    {
                        $objWidget->addError('Bitte geben Sie einen g&uuml;ltigen Wert ein!');
                    }
                }
                if ($objWidget->name == 'ctrlRollgeldNetto')
                {
                    $arrPrice = self::getAssignedPrice($weight, $table);
                    if ($arrPrice['validWeight'] != 'true')
                    {
                        $objWidget->value = 0;
                    }
                    else
                    {
                        $objWidget->value = $arrPrice['netto'];
                    }
                }
                if ($objWidget->name == 'ctrlRollgeldMwst')
                {
                    $arrPrice = self::getAssignedPrice($weight, $table);
                    if ($arrPrice['serverResponse'] != 'true')
                    {
                        $objWidget->value = 0;
                    }
                    else
                    {
                        $objWidget->value = $arrPrice['mwst'];
                    }
                }
                if ($objWidget->name == 'ctrlRollgeldBrutto')
                {
                    $arrPrice = self::getAssignedPrice($weight, $table);
                    if ($arrPrice['serverResponse'] != 'true')
                    {
                        $objWidget->value = 0;
                    }
                    else
                    {
                        $objWidget->value = $arrPrice['brutto'];
                    }
                }
            }
        }


        return $objWidget;


    }

    /**
     * @param $weight
     * @param $table
     * @return array
     */
    public static function getAssignedPrice($weight, $table)
    {
        $weight = self::floattostr($weight);
        $arrPrice = array(
            'netto' => 0,
            'mwst' => 0,
            'brutto' => 0,
            'serverResponse' => 'true',
            'validWeight' => 'false',
            'minWeight' => 'false',
            'maxWeight' => 'false',
            'minPrice' => 'false',
            'maxPrice' => 'false',
            'table' => $table,
            'weight' => $weight,
        );

        // Get minimum and maximum weight, miniumum and maximum price
        $objDb = \Database::getInstance()->prepare('SELECT * FROM ' . $table . ' ORDER BY gewicht ASC')->limit(1)->execute();
        $minWeight = $objDb->gewicht;
        $minPrice = $objDb->netto_preis;

        $objDb = \Database::getInstance()->prepare('SELECT * FROM ' . $table . ' ORDER BY gewicht DESC')->limit(1)->execute();
        $maxWeight = $objDb->gewicht;
        $maxPrice = $objDb->netto_preis;

        // Store values in array
        $arrPrice['minWeight'] = $minWeight;
        $arrPrice['maxWeight'] = $maxWeight;
        $arrPrice['minPrice'] = $minPrice;
        $arrPrice['maxPrice'] = $maxPrice;

        if ($maxWeight > 0)
        {
            if ($weight <= $maxWeight && $weight > 0)
            {
                $objDb = \Database::getInstance()->prepare('SELECT * FROM ' . $table . ' WHERE gewicht >= ? ORDER BY gewicht ASC')->limit(1)->execute($weight);
                if ($objDb->numRows)
                {

                    $netto = $objDb->netto_preis;
                    $mwst = HUF_MWST * $netto / 100;
                    $netto = number_format((float)$netto, 2, '.', '');
                    $mwst = number_format((float)$mwst, 2, '.', '');
                    $brutto = $netto + $mwst;
                    $arrPrice['netto'] = $netto;
                    $arrPrice['mwst'] = $mwst;
                    $arrPrice['brutto'] = $brutto;
                    $arrPrice['validWeight'] = 'true';
                }
            }
            elseif ($weight > $maxWeight && $weight > 0)
            {
                $arrPrice['validWeight'] = 'invalid weight - weight is too high';
            }
            elseif ($weight < 0)
            {
                $arrPrice['validWeight'] = 'invalid weight - weight is too low';
            }
            else
            {
                $arrPrice['validWeight'] = 'invalid value';
            }
        }

        return $arrPrice;

    }

    /**
     * @param $arrSubmitted
     * @param $arrLabels
     * @param $objForm
     * @param $arrFields
     */
    public function prepareFormData($arrSubmitted, $arrLabels, $objForm, $arrFields)
    {
        if (in_array($objForm->id, $GLOBALS['TARIFRECHNER']['FORM_IDS']))
        {
            if (\Input::post('FORM_SUBMIT') != '')
            {
                // Disable sendViaEmail; send E-Mail via processFormDataHook-function
                $objForm->sendViaEmail = '';
            }
        }
    }

    /**
     * @param $arrData
     * @param $arrFiles
     * @param $arrLabels
     * @param $objForm
     */
    public function processFormData($arrSubmitted, $arrData, $arrFiles, $arrLabels, $objForm)
    {


        if (in_array($objForm->id, $GLOBALS['TARIFRECHNER']['FORM_IDS']))
        {
            if ($objForm->id == 2 || $objForm->id == 4)
            {
                if (\Input::post('ctrlPickupAddress') == 'Inselhafen Juist')
                {
                    $arrFieldname = array(
                        // 1. Conditional Form
                        'ctrlPickupAddressName',
                        'ctrlPickupAddressStreet',
                        'ctrlPickupAddressContact',
                        'ctrlPickupAdressPhone',
                    );

                    foreach ($arrFieldname as $k)
                    {
                        unset($_SESSION['FORM_DATA'][$k]);
                        \Input::setPost($k, '');
                    }

                }

                if (\Input::post('ctrlDeliveryAddress') == 'Inselhafen Juist')
                {
                    $arrFieldname = array(
                        // 2. Conditional Form
                        'ctrlDeliveryAddressName',
                        'ctrlDeliveryAddressStreet',
                        'ctrlDeliveryAddressContact',
                        'ctrlDeliveryAddressPhone',
                    );

                    foreach ($arrFieldname as $k)
                    {
                        unset($_SESSION['FORM_DATA'][$k]);
                        \Input::setPost($k, '');
                    }

                }
            }

            if (\Input::post('ctrlFormDescription') == 'zusammenfassung')
            {
                if (\Input::post('FORM_SUBMIT') != '')
                {
                    // Send E-Mail to customer
                    $objEmailTemplate = new \FrontendTemplate('email_to_customer');
                    $objEmailTemplate->fields = $_SESSION['FORM_DATA'];
                    $strEmailBody = \Controller::replaceInsertTags($objEmailTemplate->parse());

                    $email = new \Email();
                    $email->subject = 'Ihre Bestellbestätigung';
                    $email->text = $strEmailBody;
                    $email->from = $GLOBALS['TL_ADMIN_EMAIL'];
                    $email->fromName = $GLOBALS['TL_ADMIN_NAME'];
                    $email->sendTo(self::getFromSession('ctrlCustomerEmail'));

                    // Send E-Mail to webmaster
                    $objEmailTemplate = new \FrontendTemplate('email_to_webmaster');
                    $objEmailTemplate->fields = $_SESSION['FORM_DATA'];
                    $strEmailBody = \Controller::replaceInsertTags($objEmailTemplate->parse());

                    $email = new \Email();
                    $email->subject = 'Eine neue Bestellung ist eingetroffen';
                    $email->text = $strEmailBody;
                    $email->from = $GLOBALS['TL_ADMIN_EMAIL'];
                    $email->fromName = $GLOBALS['TL_ADMIN_NAME'];
                    $email->sendTo($objForm->recipient);


                    unset($_SESSION['FORM_DATA']);

                }
            }
        }
    }

    /**
     * @param $arrFields
     * @param $formId
     * @param $objForm
     * @return mixed
     */
    public function compileFormFields($arrFields, $formId, $objForm)
    {
        if (in_array($objForm->id, $GLOBALS['TARIFRECHNER']['FORM_IDS']))
        {
            foreach ($arrFields as $objField)
            {
                if ($objField->name == 'ctrlFormDescription')
                {
                    if ($objField->value == 'zusammenfassung')
                    {
                        // Das Aufrufen des Formulars nach einem abgeschlossenen Bestellprozess verhindern.
                        if (!isset($_SESSION['FORM_DATA']) || empty($_SESSION['FORM_DATA']))
                        {
                            \Controller::redirect();
                        }
                    }
                }
            }
        }
        return $arrFields;
    }

    /**
     * http://php.net/manual/de/function.floatval.php
     * @param $val
     * @return string
     */
    public static function floattostr($val)
    {
        preg_match("#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o);
        return $o[1] . sprintf('%d', $o[2]) . ($o[3] != '.' ? $o[3] : '');
    }

}