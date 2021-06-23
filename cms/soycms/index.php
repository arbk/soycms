<?php
require_once(dirname(__DIR__) . "/common/common.inc.php");
require_once(__DIR__ . '/webapp/config.inc.php');

try {
    SOY2PageController::run();
} catch (Exception $e) {
    $exception = $e;
    include(SOY2::RootDir() . "error/admin.php");
}
