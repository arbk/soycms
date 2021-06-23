<?php
define("CMS_APPLICATION_ROOT_DIR", __DIR__ . "/");
define("CMS_COMMON", dirname(__DIR__) . "/common/");

require_once(__DIR__ . "/webapp/base/config.php");

try {
  // アプリケーションの実行
    CMSApplication::run();

  // 表示
    CMSApplication::display();
} catch (Exception $e) {
    $exception = $e;
    include(CMS_COMMON . "error/admin.php");
}
