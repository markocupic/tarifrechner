<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
    'HUF',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    // Classes
    'HUF\Tarifrechner' => 'system/modules/tarifrechner/classes/Tarifrechner.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'email_to_webmaster' => 'system/modules/tarifrechner/templates',
    'email_to_customer' => 'system/modules/tarifrechner/templates',
));
