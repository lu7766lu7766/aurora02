<?php
Bundle::addLink("datetime");
Bundle::addLink("alertify");
//echo "<pre>";print_r($this);
$this->partialView($top_view_path);
$choice_id = $model->session["choice"];
if (isset($model->userId)) {
    $user = $model->user;
    $status = 1;
    $btn_name = $choice_id == "root" ? "更新" : "權限更新";
    $id = "update_btn";
} else {
    $status = 0;
    $btn_name = "新增";
    $id = "post_btn";
}
?>
<h3 id="title"><?php echo $this->menu->currentName; ?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

<?php
echo Html::form();
?>
<table class="table table-v">
  <tbody>
  <tr>
    <td class="col-md-3 col-xs-5">登入帳號</td>
    <td class="col-md-9 col-xs-7">
        <?php
        echo $status ? "<span>" . $model->userId . "</span>" :
            '<input type="text" name="userID1" id="userID1" class="form-control" />'
        ?>
    </td>
  </tr>
  <tr>
    <td class="col-md-3 col-xs-5">
      登入帳號2
    </td>
    <td class="col-md-9 col-xs-7">
      <input type="text" name="userID2" class="form-control" value="<?php echo $user["UserID2"] ?>"
             placeholder="僅登入使用">
    </td>
  </tr>
  <tr>
    <td>密碼</td>
    <td>
        <?php echo \lib\Hash::decode($user["UserPassword"]) ?>
    </td>
  </tr>
  <tr>
    <td>帳號狀態</td>
    <td>
      <input id='useState' name='useState' class='bootstrap-switch' type='checkbox' value="1"
          <?php echo $status ? ($user["UseState"] ? "checked" : "") : "checked" ?>
             data-size='mini' data-off-color='danger'>
    </td>
  </tr>
  <tr>
    <td>用戶名稱</td>
    <td>
      <input type="text" name="userName" class="form-control" value="<?php echo $user["UserName"] ?>">
    </td>
  </tr>
  <tr>
    <td>用戶備註</td>
    <td>
      <input type="text" name="noteText" class="form-control" value="<?php echo $user["NoteText"] ?>">
    </td>
  </tr>
  <tr>
    <td>用戶資訊</td>
    <td>
      <input type="text" name="userInfo" class="form-control" value="<?php echo $user["UserInfo"] ?>">
    </td>
  </tr>
  <?php if ($choice_id == "root") { ?>
    <tr>
      <td>經銷商</td>
      <td>
        <input type="text" name="distributor" class="form-control" value="<?php echo $user["Distributor"] ?>">
      </td>
    </tr>
    <tr>
      <td>透支額度</td>
      <td>
        <input type="text" name="overdraft" class="form-control" value="<?php echo $user["Overdraft"] ?>">
      </td>
    </tr>
  <?php } ?>
  <tr>
    <td>費率表</td>
    <td>
        <?php
        echo Html::selector($model->rateGroupID);
        if ($choice_id != "root") {
            ?>
          <script>
            var rateGroupIDText = $('#rateGroupID :selected').text()
            $('#rateGroupID').after(rateGroupIDText)
            $('#rateGroupID').remove()
          </script>
            <?php
        }
        ?>
    </td>
  </tr>
  <tr>
    <td>剩餘點數</td>
    <td>
      <div class="form-inline">
        <div class="form-group">
          <span>現有</span><span><?php echo isset($user["Balance"]) ? $user["Balance"] : 0 ?></span><span>點</span>
        </div>
      </div>
      <div class="input-group">
        <span class="input-group-addon">新增</span><input type="text" name="balance"
                                                        class="form-control num-only"/><span
            class="input-group-addon">點</span>
      </div>
      <!--            <div class="input-group">-->
      <!--                <span class="input-group-addon">-->
        <?php //echo isset($user["Balance"])?$user["Balance"]:0?><!--</span>-->
      <!--                <span class="input-group-addon">(新增</span><input type="text" name="balance" class="form-control num-only"/><span class="input-group-addon"> )</span>-->
      <!--            </div>-->

    </td>
  </tr>
  <tr>
    <td>*每日可使用時間</td>
    <td>
      <input type="text" name="startTime" class="form-control time-picker"
             value="<?php echo $user["StartTime"] ?>">
    </td>
  </tr>
  <tr>
    <td>*每日自動啟用使用時間</td>
    <td>
      <input type="text" name="autoStartTime" class="form-control time-picker"
             value="<?php echo $user["AutoStartTime"] ?>">
    </td>
  </tr>
  <tr>
    <td>*每日自動關閉時間</td>
    <td>
      <input type="text" name="autoStopTime" class="form-control time-picker"
             value="<?php echo $user["AutoStopTime"] ?>">
    </td>
  </tr>
  <tr>
    <td>*每日結束時間</td>
    <td>
      <input type="text" name="stopTime" class="form-control time-picker" value="<?php echo $user["StopTime"] ?>">
    </td>
  </tr>
  <tr>
    <td>[ 自動 ]停止秒數</td>
    <td>
      <div class="form-inline">
        <input type="text" name="callWaitingTime" class="form-control num-only"
               value="<?php echo $user["CallWaitingTime"] ?>"/>
        <span>(0 ~ 300) 秒，自動撥號在一段座席滿線後的暫停秒數</span>
      </div>
    </td>
  </tr>
  <tr>
    <td>*掃號每日可使用時間</td>
    <td>
      <input type="text" name="searchStartTime" class="form-control time-picker"
             value="<?php echo $user["SearchStartTime"] ?>">
    </td>
  </tr>
  <tr>
    <td>*掃號每日自動啟用使用時間</td>
    <td>
      <input type="text" name="searchAutoStartTime" class="form-control time-picker"
             value="<?php echo $user["SearchAutoStartTime"] ?>">
    </td>
  </tr>
  <tr>
    <td>*掃號每日自動關閉時間</td>
    <td>
      <input type="text" name="searchAutoStopTime" class="form-control time-picker"
             value="<?php echo $user["SearchAutoStopTime"] ?>">
    </td>
  </tr>
  <tr>
    <td>*掃號每日結束時間</td>
    <td>
      <input type="text" name="searchStopTime" class="form-control time-picker"
             value="<?php echo $user["SearchStopTime"] ?>">
    </td>
  </tr>
  <tr>
    <td>總路由線路數</td>
    <td>
      <input type="text" name="maxRoutingCalls" class="form-control"
             value="<?php echo $user["MaxRoutingCalls"] ?>">
    </td>
  </tr>
  <tr>
    <td>掃號路由線數</td>
    <td>
      <input type="text" name="maxSearchCalls" class="form-control" value="<?php echo $user["MaxSearchCalls"] ?>">
    </td>
  </tr>
  <tr>
    <td>節費路由線數</td>
    <td>
      <input type="text" name="maxRegularCalls" class="form-control"
             value="<?php echo $user["MaxRegularCalls"] ?>">
    </td>
  </tr>
  <tr>
    <td>自動撥號路由線數</td>
    <td>
      <input type="text" name="maxCalls" class="form-control" value="<?php echo $user["MaxCalls"] ?>">
    </td>
  </tr>
  <?php if ($model->userId != "root") { ?>
    <tr>
      <td>直屬上司</td>
      <td>
          <?php
          echo Html::selector($model->empSelect2);
          ?>
      </td>
    </tr>
  <?php } ?>
  <?php if ($choice_id == "root") { ?>
    <tr>
      <td>分機管理權限</td>
      <td>
        <input id='canSwitchExtension' name='canSwitchExtension' class='bootstrap-switch' type='checkbox'
               value="1"
            <?php echo $user["CanSwitchExtension"] ? "checked" : "" ?>
               data-size='mini' data-off-color='danger'>
      </td>
    </tr>
  <?php } ?>
  <?php if ($choice_id == "root") { ?>
    <tr>
      <td>權限設定</td>
      <td>
        <input id='permissionControl' name='permissionControl' class='bootstrap-switch' type='checkbox'
               value="1"
            <?php echo $user["PermissionControl"] ? "checked" : "" ?>
               data-size='mini' data-off-color='danger'>
      </td>
    </tr>
  <?php } ?>
  <?php
  if ($choice_id == "root" || ($choice_id != $model->userId && $model->session["permission_control"])) {
      ?>
    <tr>
      <td>選單設定</td>
      <td>
          <?php
          $choice_MenuList =
              $choice_id == $model->session["login"]["UserID"] ? $model->session["login"]["MenuList"] :
                  EmpHelper::getMenuList($model->session["sub_emp"], $this->model->session["choice"]);
          echo $this->menu->CreateMenuManage($choice_MenuList, $user["MenuList"], $model->userId);
          ?>
      </td>
    </tr>
  <?php } ?>

  <tr>
    <td></td>
    <td>
      <input id="<?php echo $id ?>" <?php echo //一般用戶不可以編輯自己，不可以新增
      ($choice_id != "root" && !$status) ||//新增時的條件
      ($choice_id != "root" && $status && $choice_id == $model->userId) ? //更新時的條件
          "disabled" : "" ?>
             class="btn btn-primary form-control" type="submit" value="<?php echo $btn_name ?>">
    </td>
  </tr>
  </tbody>
</table>

<script>
  $startTime = $('input[name=\'startTime\']')
  $autoStartTime = $('input[name=\'autoStartTime\']')
  $stopTime = $('input[name=\'stopTime\']')
  $autoStopTime = $('input[name=\'autoStopTime\']')

  $startTime.change(function () {
    if ($autoStartTime.val() != '' && $startTime.val() != '' && $autoStartTime.val() < $startTime.val()) {
      alertify.alert('自動啟動時間不得早於可用時間')
      $autoStartTime.val($startTime.val())
    }
  })

  $autoStartTime.change(function () {
    if ($startTime.val() != '' && $autoStartTime.val() != '' && $autoStartTime.val() < $startTime.val()) {
      alertify.alert('自動啟動時間不得早於可用時間')
      $autoStartTime.val($startTime.val())
    }
  })

  $stopTime.change(function () {
    if ($autoStopTime.val() != '' && $startTime.val() != '' && $autoStopTime.val() > $stopTime.val()) {
      alertify.alert('自動停止時間不得晚於停用時間')
      $autoStopTime.val($stopTime.val())
    }
  })

  $autoStopTime.change(function () {
    if ($stopTime.val() != '' && $autoStartTime.val() != '' && $autoStopTime.val() > $stopTime.val()) {
      alertify.alert('自動停止時間不得晚於停用時間')
      $autoStopTime.val($stopTime.val())
    }
  })


  //        $searchStartTime = $("input[name='searchStartTime']");
  //        $searchAutoStartTime = $("input[name='searchAutoStartTime']");
  //        $searchStopTime = $("input[name='searchStopTime']");
  //        $searchAutoStopTime = $("input[name='searchAutoStopTime']");
  //
  //        $searchStartTime.change(function(){
  //            if($searchAutoStartTime.val()!="" && $searchStartTime.val()!="" && $searchAutoStartTime.val()<$searchStartTime.val())
  //            {
  //                alertify.alert("掃號自動啟動時間不得早於可用時間");
  //                $searchAutoStartTime.val($searchStartTime.val());
  //            }
  //        });
  //
  //        $searchAutoStartTime.change(function(){
  //            if($searchStartTime.val()!="" && $searchAutoStartTime.val()!="" && $searchAutoStartTime.val()<$searchStartTime.val())
  //            {
  //                alertify.alert("掃號自動啟動時間不得早於可用時間");
  //                $searchAutoStartTime.val($searchStartTime.val());
  //            }
  //        })
  //
  //        $searchStopTime.change(function(){
  //            if($searchAutoStopTime.val()!="" && $searchStopTime.val()!="" && $searchAutoStopTime.val()>$searchStopTime.val())
  //            {
  //                alertify.alert("掃號自動停止時間不得晚於停用時間");
  //                $searchAutoStopTime.val($searchStopTime.val());
  //            }
  //        });
  //
  //        $searchAutoStopTime.change(function(){
  //            if($searchStopTime.val()!="" && $searchAutoStopTime.val()!="" && $searchAutoStopTime.val()>$searchStopTime.val())
  //            {
  //                alertify.alert("掃號自動停止時間不得晚於停用時間");
  //                $searchAutoStopTime.val($searchStopTime.val());
  //            }
  //        })

  $maxRoutingCalls = $('input[name=\'maxRoutingCalls\']')
  $maxSearchCalls = $('input[name=\'maxSearchCalls\']')
  $maxRegularCalls = $('input[name=\'maxRegularCalls\']')
  $maxCalls = $('input[name=\'maxCalls\']')
  $.each([$maxRoutingCalls, $maxSearchCalls, $maxRegularCalls, $maxCalls], function () {
    $this = $(this)

    $this.data('value', $this.val()).on('change', function () {
      $this = $(this)
      if (!countCondition()) {
        alert('總路由線路數必須 >= 掃號路由線數 + 節費路由線數 +自動撥號路由線數')
        $this.val($this.data('value'))
      }
      else {
        $this.data('value', $this.val())
      }
    })
  })


  var countCondition = function () {
    return parseInt($maxRoutingCalls.val() || 0) >=
      (parseInt($maxSearchCalls.val() || 0) + parseInt($maxRegularCalls.val() || 0) + parseInt($maxCalls.val() || 0))
  }

</script>
<?php
echo Html::formEnd();
?>


<?php
$this->partialView($bottom_view_path);
?>
