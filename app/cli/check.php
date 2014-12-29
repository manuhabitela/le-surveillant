#!/usr/bin/env php
<?php
define('USING_CLI', true);
require_once __DIR__.'/../config/init.php';
require_once LIB_PATH.'/common.php';

$webpages = documentsList('webpage');
foreach ($webpages as $website) {
    checkWebpage($website);
}
