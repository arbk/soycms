<?php
if (!isset($exception)) {
    $exception = new Exception("Unknown Error");
}
include(__DIR__."/error.func.php");
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
   <h3>エラーメッセージ / Error message</h3>
   <div><?php echo soy2_h($exception->getMessage()); ?></div>
  </div>

  <div id="resolve_message">
   <h3>詳細・解決策 / Solution</h3>
   <div><?php echo get_resolve_message($exception); ?></div>
   <p style="margin-top:30px">
    解決策や内容がご不明な場合は フォーラム (http://www.soycms.org) をご利用ください。<br>
    その際、以下のレポートをご利用いただけると解決の役に立つ場合がございます。
   </p>
  </div>

  <div id="stack_trace">
   <h3>レポート</h3>
   <div id="stack_list">
    <textarea class="wrap_off" readonly="readonly"><?php echo soy2_h(get_report($exception)); ?></textarea>
   </div>
  </div>

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
