<?php
/*
 * Generate use-list for Trait files in current directory and add the list to the appropriate file(s).
 *
 * When run inside a directory called `Plugin/`, with a PHP file inside `Plugin/` called `ActionUtils.php`,
 * this script will generate `use Traits\Plugin\ActionUtils;`.
 */
$dest_files = [dirname(dirname(dirname(__FILE__))).'/classes/AbsBaseAp.php'];

if ($_handle = opendir(__DIR__)) {
    while (false !== ($_file = readdir($_handle))) {
        if ($_file !== '.' && $_file !== '..' && $_file !== '.build.php' && stristr($_file, '.php') !== false) {
            $use_traits_list .= 'use Traits\\'.basename(__DIR__).'\\'.basename($_file, '.php').';'.PHP_EOL.'    ';
        }
    }
    closedir($_handle);
    unset($_file, $_files);
}

$use_traits_list = PHP_EOL.'    '.$use_traits_list; // Prepare formatting.

foreach ($dest_files as $file) { // Update file(s) with use Traits list
    echo 'Updating '.$file.PHP_EOL;
    echo $use_traits_list;
    $file_contents = file_get_contents($file);
    $file_contents = preg_replace('/(\/\*\[\.build\.php\-auto\-generate\-use\-Traits\]\*\/)(?:.*?)(\/\*\[\/\.build\.php\-auto\-generate\-use\-Traits\]\*\/)/us', '${1}'.$use_traits_list.'${2}', $file_contents);
    file_put_contents($file, $file_contents);
}
