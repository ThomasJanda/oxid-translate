<?php
$sMetadataVersion = '2.0';

$aModule = array(
    'id' => 'rs-translate',
    'title' => '*RS Translate',
    'description' => 'Edit translation files in oxid admin',
    'thumbnail' => '',
    'version' => '0.0.5',
    'author' => '',
    'url' => '',
    'email' => '',
    'extend' => array(
        \OxidEsales\Eshop\Core\Language::class => \rs\translate\Core\Language::class,
    ),
    'controllers' => array(
        'rs_translate_manager' => rs\translate\Application\Controller\Admin\rs_translate_manager::class,
        'rs_translate' => rs\translate\Model\rs_translate::class,
        'rs_translate_list' => rs\translate\Model\rs_translate_list::class,
    ),
    'templates' => array(
        'rs_translate_manager.tpl' => 'rs/translate/views/admin/tpl/rs_translate_manager.tpl',
    ),
    'settings' => array(
    ),
);
