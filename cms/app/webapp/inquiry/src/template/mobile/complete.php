<table class="inquiry_message" id="inquiry_message_complete">
<tr>
  <td colspan="2">
  <?php $message = $config->getMessage();
  echo $message["complete"]; ?>
  </td>
</tr>
<tr>
  <td><b>お問い合わせ番号</b></td>
</tr>
<tr>
  <td><?php echo $inquiry->getTrackingNumber(); ?></td>
</tr>
<tr>
  <td><b>お問い合わせ内容</b></td>
</tr>
<tr>
  <td><pre><?php echo $inquiry->getContent() ?></pre></td>
</tr>
<tr>
  <td><b>お問い合わせ日時</b></td>
</tr>
<tr>
  <td><?php echo date("Y-m-d H:i:s", $inquiry->getCreateDate()); ?></td>
</tr>
</table>

<div class="inquiry_ctrl">
  <a href="<?php echo $page_link; ?>">戻る</a>
</div>
