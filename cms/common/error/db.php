<?php
  header("HTTP/1.1 404 Not Found");
?>
<!DOCTYPE html>
<html lang="<?php echo SOYCMS_ADMIN_HTML_LANG; ?>">

<head>
<?php soycms_admin_html_head_output(); ?>
<?php SOY2::import("util.CMSUtil"); ?>
<title><?php echo CMSUtil::getCMSName(); ?></title>

<link rel="stylesheet" href="<?php echo SOY2PageController::createRelativeLink("../admin/css/"); ?>style.css"/>
<link rel="stylesheet" href="<?php echo SOY2PageController::createRelativeLink("../admin/css/"); ?>form.css"/>
<link rel="stylesheet" href="<?php echo SOY2PageController::createRelativeLink("../admin/css/"); ?>table.css"/>
<link type="text/css" rel="stylesheet" href="<?php echo SOY2PageController::createRelativeLink("../admin/css/"); ?>global_page/globalpage.css"/>
<script type="text/JavaScript" charset="utf-8" src="<?php echo SOY2PageController::createRelativeLink("../admin/js/"); ?>common.js"></script>

<style type="text/css">

#stack_trace .stacktrace{
  margin:0pt;
  padding:0pt;
  overflow:visible;
}

#stack_list{
  padding-left:20px;
}

#exception_message div{
  padding-left:20px;
}

#resolve_message div{
  padding-left:20px;
}
#resolve_message p{
  padding-left:20px;
}
#resolve_message div p{
  padding-left:0px;
}

h3{
  border-color:red;
  margin-top:10px;
}

h4{
  margin-top:10px;
  margin-bottom:5px;
}

#content{
  margin-top:10px;

}

#stack_trace textarea{
  width:100%;
  height:240px;

}

</style>
</head>

<body>

<div id="wrapper">

  <div id="upperMenu">
    <div style="clear:both;"></div>
  </div>

  <div id="content">

    <h2>致命的なエラーが発生しました / Fatal error</h2>

    <div id="exception_message">
      <h3>エラーの内容 / Error message</h3>
      <div>
        <h4>データベースに接続できません。</h4>
        <p>データベースの設定ファイルが存在しません。</p>

        <hr style="margin: 1em 0;">

        <h4>Can not get a data source.</h4>
        <p>No database configuration file is found.<br/>Check your database configuration in <?php echo CMSUtil::getCMSName(); ?>.</p>
      </div>
    </div>

    <div id="resolve_message">
      <h3>解決策 / Solution</h3>
      <div>
        <h4>設定ファイルを置いてください。</h4>
        <p>データベースの設定ファイルを <strong><?php echo soy2_h(SOY2::RootDir()."config/db/".SOYCMS_DB_TYPE.".php"); ?></strong> に置いてください。<br>
        <p>設定ファイルの例は <strong><?php echo soy2_h(SOY2::RootDir()."config/db/".SOYCMS_DB_TYPE.".sample.php"); ?></strong> にあります。
        <?php if (SOYCMS_DB_TYPE=="mysql") {
            ?>'<p>設定ファイルの書き方は「MySQLの設定 (http://www.soycms.net/man/mysql_configuration.html)」で読むことができます。<?php
        } ?>

        <hr style="margin: 1em 0;">

        <h4>Create a database configuration file.</h4>
        <p>A configuration file should exist at <strong><?php echo soy2_h(SOY2::RootDir()."config/db/".SOYCMS_DB_TYPE.".php"); ?></strong>.<br>
        <p>See <strong><?php echo soy2_h(SOY2::RootDir()."config/db/".SOYCMS_DB_TYPE.".sample.php"); ?></strong> for example.
      </div>
            <?php if (CMSUtil::getCMSName() == "SOY CMS") { ?>
      <p style="margin-top:30px">
        解決策や内容がご不明な場合は フォーラム (http://www.soycms.org/) をご利用ください。
      </p>
            <?php } ?>
    </div>

    <!-- no stack_trace -->

  </div>

  <div>
    <div id="footer">
      <div id="footer_left"></div>
      <div id="footer_right"></div>
      <div id="copyright"><?php echo CMSUtil::getCMSName(); ?>. © <?php echo CMSUtil::getDeveloperName(); ?></div>
    </div>
  </div>

</div>

</body>
</html>
