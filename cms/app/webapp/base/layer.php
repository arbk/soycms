<!DOCTYPE html>
<html lang="<?php echo SOYCMS_ADMIN_HTML_LANG; ?>">
<head>

<?php soycms_admin_html_head_output(); ?>

<!-- Framework CSS -->
<link rel="stylesheet" href="<?php echo CMSApplication::getRoot(); ?>css/blueprint/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="<?php echo CMSApplication::getRoot(); ?>css/blueprint/print.css" type="text/css" media="print">
<!--[if IE]><link rel="stylesheet" href="<?php echo CMSApplication::getRoot(); ?>css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->

<link rel="stylesheet" href="<?php echo CMSApplication::getRoot(); ?>css/styles.css" />

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

<div><?php CMSApplication::printApplication(); ?></div>

</body>
</html>
