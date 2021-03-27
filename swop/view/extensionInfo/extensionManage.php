<?php
Bundle::addLink("datetime");
$this->partialView($top_view_path);
$choice_id = $model->session["choice"];
$login_id = $model->session["login"]["UserID"];
$can_switch_extension = $choice_id === "root" || $model->session["choicer"]["CanSwitchExtension"];
?>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

<div class="table-responsive">
    <?php
    echo Html::form();
    echo Html::pageInput($model->page, $model->last_page, $model->per_page);
    ?>
  <table class="table table-v table-condensed">
    <tbody>
    <tr>
      <td class="col-md-2">用戶:</td>
      <td class="col-md-4">
          <?php echo Html::selector($model->empSelect2); ?>
      </td>
      <td class="col-md-2">查詢內容:</td>
      <td class="col-md-3">
        <input type="text" name="search_content" class="form-control num-only"
               value="<?php echo ($model->search_content == "*") ? "" : ($model->search_content) ?>">
      </td>
      <td class="non col-md-1">
        <input type="button" class="form-control btn btn-primary get_btn" value="列表"/>
      </td>
    </tr>
    </tbody>
  </table>

  <ul class="pager responsive">
    <li class="first-page"><a href="#">第一頁</a></li>
    <li class="prev-page"><a href="#">上一頁</a></li>
    <li class="next-page"><a href="#">下一頁</a></li>
    <li class="last-page"><a href="#">最後一頁</a></li>
    <li><a href="#">第<?php echo Html::selector($model->pageSelect) ?>頁</a></li>
    <li><a href="#">(共<?php echo $model->last_page ?>頁，<?php echo $model->rows ?>筆資料)</a></li>
  </ul>

    <?php if (count($model->data)) { ?>
      <input type="button" class="btn btn-danger delete_btn" value="Delete">
      <table class="table table-h table-striped table-hover table-pointer">
        <tbody>
        <tr>
            <?php if ($choice_id == "root") { ?>
              <th>
                <input type='checkbox' class="checkAll" for="delete[]"/>
              </th>
            <?php } ?>
          <th>編號</th>
          <th>用戶</th>
          <th>分機</th>
          <th>名稱</th>
          <th>手動撥號主叫</th>
          <th>來源IP</th>
          <th>錄音</th>
          <th>座席</th>
          <th>狀態</th>
          <th>註冊</th>
            <?php if ($can_switch_extension) { ?>
              <th>啟用</th>
            <?php } ?>
          <th>Ping</th>
        </tr>
        <?php
        $i = $model->page * $model->per_page + 1;
        foreach ($model->data as $index => $data) {

            $userId = $data["UserID"];
            $extensionNo = $data["ExtensionNo"];
            $ip = (!empty($data["Received"])) ? $data["Received"] : $data["HostInfo"];
            $url = "extensionInfo/extensionModify?userId={$userId}&extensionNo={$extensionNo}";
            $url .= (!empty($model->search_userID) ? "&search_userID={$model->search_userID}" : "");
            $url .= (!empty($model->search_content) ? "&search_content={$model->search_content}" : "");
            if ($userId != $choice_id && $tmp_user_id != $userId) {
                $tmp_user_id = $userId;
                echo "
					<tr style='background-color: #111'>
						<td colspan='20' style='padding:0px; height:5px'>
						</td>
					</tr>";
            }
            echo "<tr redirect='{$url}'>";//
            if ($choice_id == "root") {
                echo "<td><input type='checkbox' name='delete[]' value='{$userId},{$extensionNo}'/></td>";
            }
            echo "<td>" . $i++ . "</td>";
            echo "<td>{$userId}</td>";//用戶
            echo "<td>{$extensionNo}</td>";//分機
            echo "<td>{$data["ExtName"]}</td>";//分機名稱
            echo "<td>{$data["OffNetCli"]}</td>";//手動撥號主叫
            echo "<td>$ip</td>";
            echo "<td><label class='switch'><input id='switch' disabled type='checkbox' " .//class='bootstrap-switch' readonly
                ($data["StartRecorder"] ? "checked" : "") .
                " data-size='mini' data-off-color='danger'><div class='slider round'></div></label></td>";
            echo "<td>" . $data["CalloutGroupID"] . "</td>";
            echo "<td>";//.$data["Suspend"].";
            echo "<label class='switch'><input id='switch'  disabled type='checkbox' " .//class='bootstrap-switch'
                ($data["Suspend"] == 0 ? "checked" : "") .
                " data-size='mini' data-off-color='danger'><div class='slider round'></div></label></td>";
            echo "<td><label class='switch'><input id='switch'  disabled type='checkbox' " .//class='bootstrap-switch'
                ($data["ETime"] && date_format($data["ETime"], 'Y-m-d H:i:s') > date("Y-m-d H:i:s",
                    time()) ? "checked" : "") .
                " data-size='mini' data-off-color='danger'><div class='slider round'></div></label></td>";
            if ($can_switch_extension) {
                echo "<td><label class='switch'><input id='switch'  disabled type='checkbox' " .//class='bootstrap-switch'
                    ($data["UseState"] == 1 ? "checked" : "") .
                    " data-size='mini' data-off-color='danger'><div class='slider round'></div></label></td>";
            }
            echo "<td>" . $data["PingTime"] . "</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
      </table>
        <?php
    }
    echo Html::formEnd();
    ?>
</div>
<?php
$this->partialView($bottom_view_path);
?>
