<?php

$addon = rex_addon::get('adminer');

/**
 * Addon in die Setup-Addons in der config.yml dieser Installation hinzufügen.
 */
$config_file = rex_path::coreData('config.yml');
$data = rex_file::getConfig($config_file);
if (array_key_exists('setup_addons', $data) && !in_array('adminer', $data['setup_addons'], true)) {
    $data['setup_addons'][] = 'adminer';
    rex_file::putConfig($config_file, $data);
}
