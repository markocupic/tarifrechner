<?php
/**
 * Created by PhpStorm.
 * User: Marko Cupic, m.cupic@gmx.ch
 * Date: 06.11.2016
 * Time: 22:03
 */


$GLOBALS['TL_DCA']['tl_huf_1'] = array(

    // Config
    'config' => array(
        'dataContainer' => 'Table',

        'sql' => array(
            'keys' => array(
                'id' => 'primary',
            ),
        ),
    ),
    // Fields
    'fields' => array(
        'id' => array(
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'gewicht' => array(
            'sql' => "int(5) unsigned NOT NULL default '0'",
        ),
        'netto_preis' => array(
            'sql' => "float(10,2) unsigned NOT NULL default '0.00'"
        )
    )
);
