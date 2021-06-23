<form method="post">
<div class="table-responsive">
<table class="table" style="width:80%;">
  <tr>
    <th>文字列認証</th>
    <td>
      <input type="hidden" name="useKeyword" value="0" />
      <input id="use_keyword_input" onclick="if(this.checked){$('#keyword_input_box').show();}else{$('#keyword_input_box').hide();}" type="checkbox" name="useKeyword" value="1" <?php if ($this->useKeyword) { ?>checked="checked"<?php } ?> />
      <label for="use_keyword_input">文字列認証を使用する</label>

      <div id="keyword_input_box" style="<?php if (!$this->useKeyword) { ?>display:none;<?php } ?>">
        パスワード文字列：
        <input type="text" name="keyword" value="<?php echo soy2_h($this->keyword); ?>"/>
        パスワードキー：
        <input type="text" name="name" value="<?php echo soy2_h($this->name); ?>"/>
      </div>
    </td>
  </tr>

  <tr>
    <th>禁止ワード</th>
    <td>
      <textarea name="prohibitionWords" rows="10" cols="50" class="wrap_off"><?php
        echo implode("\n", $this->prohibitionWords); ?></textarea>
    </td>
  </tr>


  <tr>
    <td colspan="2" style="text-align:center;">
      <input type="submit" name="save" value="保存" />
    </td>
  </tr>

</table>
</div>
</form>

<h3>使い方</h3>

<div>
文字列認証を使う場合は、テンプレートに以下のように記述して下さい。
</div>


<textarea style="width:500px;height:100px;">
<p>お手数ですが、下記入力項目に「<?php echo soy2_h($this->keyword); ?>」と入力して下さい。</p>
<input type="text" name="<?php echo soy2_h($this->name); ?>">
</textarea>
