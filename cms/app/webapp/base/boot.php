<!DOCTYPE html>
<html lang="<?php echo SOYCMS_ADMIN_HTML_LANG; ?>">
<head>

<?php soycms_admin_html_head_output(); ?>

<script type="text/javascript" src="<?php echo CMSApplication::getRoot(); ?>js/jquery.js"></script>
<?php CMSApplication::printScript(); ?>
<?php CMSApplication::printLink(); ?>
<title><?php echo CMSApplication::getTitle(); ?></title>

</head>
<body>

<div class="container">
<?php if (!IFRAME_DISPLAY_MODE) {?>
<div class="masthead">
  <h3 class="text-muted"><?php echo CMSApplication::getApplicationName(); ?></h3>

  <nav>
    <?php MaatApplication::printTabs();?>
  </nav>
</div><!-- masthead.end -->

<?php }?>

<section id="content"><?php CMSApplication::printApplication(); ?></section>

<?php if (!IFRAME_DISPLAY_MODE) {?>
<footer class="footer">
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

</footer>
<?php }?>

</div><!-- //container.end -->

</body>
</html>
