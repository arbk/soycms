<!DOCTYPE html>
<html lang="<?php echo SOYCMS_ADMIN_HTML_LANG; ?>">
<head>

<?php soycms_admin_html_head_output(); ?>

<!-- Framework CSS -->
<link rel="stylesheet" href="<?php echo CMSApplication::getRoot(); ?>css/blueprint/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="<?php echo CMSApplication::getRoot(); ?>css/blueprint/print.css" type="text/css" media="print">
<!--[if IE]><link rel="stylesheet" href="<?php echo CMSApplication::getRoot(); ?>css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->

<link rel="stylesheet" href="<?php echo CMSApplication::getRoot(); ?>css/styles.css" />
<link rel="stylesheet" href="<?php echo CMSApplication::getRoot(); ?>css/layer/layer.css" />

<script type="text/javascript" src="<?php echo CMSApplication::getRoot(); ?>js/jquery.js"></script>
<script type="text/javascript" src="<?php echo CMSApplication::getRoot(); ?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo CMSApplication::getRoot(); ?>js/soycms_widget.js"></script>
<script type="text/javascript" src="<?php echo CMSApplication::getRoot(); ?>js/soy2js/soy2js.js"></script>
<script type="text/javascript" src="<?php echo CMSApplication::getRoot(); ?>js/tools/advanced_textarea.js"></script>
<script type="text/javascript" src="<?php echo CMSApplication::getRoot(); ?>js/main.js"></script>

<?php CMSApplication::printScript(); ?>
<?php CMSApplication::printLink(); ?>

<title><?php echo CMSApplication::getTitle(); ?></title>

</head>
<body>

<div id="wrapper" class="container">

<div id="logo" class="span-12">
<h1><a href="<?php echo CMSApplication::getApplicationRoot(); ?>"><?php echo CMSApplication::getApplicationName(); ?></a></h1>
</div>

<?php if (CMSApplication::hasUpperMenu()) { ?>
<div id="upperMenu" class="span-8 last" style="text-align:right;">
    <?php CMSApplication::printUpperMenu(); ?>
</div>
<?php } ?>

<div id="tabs" class="content-wrapper">
  <?php CMSApplication::printTabs(); ?>
</div>

<div id="content" class="content-wrapper last"><?php CMSApplication::printApplication(); ?></div>

<div id="bottomMenu" class="content-wrapper" style="text-align:center;margin-top:10px;">
<?php if (CMSApplication::isDirectLogin()) { ?>
  <a href="<?php echo SOY2PageController::createRelativeLink("../admin/" . F_FRCTRLER . "/Login/Logout"); ?>">ログアウト</a>
<?php } else { ?>
  <a href="<?php echo SOY2PageController::createRelativeLink("../admin/"); ?>">CMS管理</a>
  &nbsp;
    <?php if (CMSApplication::checkUseSiteDb()) { ?>
  <a href="<?php echo SOY2PageController::createRelativeLink("../admin/" . F_FRCTRLER . "/Site/Login/") . CMSApplication::getLoginedSiteId(); ?>">ログイン中のサイトへ</a>
    <?php } else { ?>
  <a href="<?php echo SOY2PageController::createRelativeLink("../admin/" . F_FRCTRLER . "/Site"); ?>">サイト一覧</a>
    <?php }?>
  &nbsp;
  <a href="<?php echo SOY2PageController::createRelativeLink("../admin/" . F_FRCTRLER . "/Application"); ?>">アプリケーション一覧</a>
<?php } ?>
</div>

<div id="footer" class="content-wrapper">
  <div id="footer_left"></div>
  <div id="footer_right"></div>
  <div id="copyright"><?php echo CMSUtil::getCMSName(); ?>. © <?php echo CMSUtil::getDeveloperName(); ?></div>
</div>

</div>

</body>
</html>
