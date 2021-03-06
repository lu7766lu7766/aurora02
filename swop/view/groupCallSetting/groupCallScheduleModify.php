<?php
//echo "<pre>";print_r($this);
$this->partialView($top_view_path);
?>
    <h3 id="title"><?php echo $this->menu->currentName?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

    <div class="table-responsive">
    <?php
    echo Html::form();
    ?>
        <input type="hidden" name="calledCount_source" value="<?php echo $model->data["CalledCount"];?>"/>
        <input type="hidden" name="numberMode" value="<?php echo $model->data["NumberMode"];?>"/>
        <table class="table table-v">
            <tbody>
            <tr>
                <td class="col-md-3">網頁登入帳號</td>
                <td class="col-md-9">
                    <?php echo $model->userId?>
                </td>
            </tr>
            <tr>
                <td>用戶名稱</td>
                <td>
                    <?php echo $model->data["UserName"]?>
                </td>
            </tr>
            <tr>
                <td>剩餘點數</td>
                <td>
                    <?php echo $model->data["Balance"]?>
                </td>
            </tr>
            <tr>
                <td>群呼名稱</td>
                <td>
                    <input type="text" name="planName" class="form-control" value="<?php echo $model->data["PlanName"]?>">
                </td>
            </tr>
            <tr>
                <td>新增模式</td>
                <td>
                    <?php
                    $tmp_word = "";
                    switch($model->data["NumberMode"])
                    {
                        case "0":
                            $tmp_word = "區段增加";
                            break;
                        case "1":
                            $tmp_word = "名單上傳";
                            break;
                        case "2":
                            $tmp_word = "同號測試";
                            break;
                    }
                    echo $tmp_word;
                    ?>
                </td>
            </tr>
            <tr style="<?php echo ($model->data["NumberMode"]=="1") ? "display:none;" : ""?>">
                <td>起始電話</td>
                <td>
                    <div class="input-group">
                        <input type="text" name="startCalledNumber" class="form-control" value="<?php echo $model->data["StartCalledNumber"]?>">
                        <span class="input-group-addon">筆數</span>
                        <input type="text" name="calledCount" class="form-control num-only" value="<?php echo $model->data["CalledCount"]?>">
                    </div>
                </td>
            </tr>
            <tr>
                <td>顯示主叫</td>
                <td>
                    <?php echo Html::selector($model->callerPresentSelect)?>
                </td>
            </tr>
            <tr>
                <td>主叫號</td>
                <td>
                    <input type="text" name="callerID" class="form-control" value="<?php echo $model->data["CallerID"]?>">
                </td>
            </tr>
            <tr>
                <td>座席</td>
                <td>
                    <?php echo Html::selector($model->calloutGroupIDSelect)?>
                </td>
            </tr>
            <tr>
                <td>響鈴方式</td>
                <td>
                    <?php echo Html::selector($model->calldistributionSelect)?>
                </td>
            </tr>
            <tr>
                <td>撥出電話等待秒數</td>
                <td>
                    <div class="input-group">
                        <input type="text" name="callProgressTime" class="form-control num-only" value="<?php echo $model->data["CallProgressTime"]?>">
                        <span class="input-group-addon">(5~300)秒，等待接通時間</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>轉分機等待秒數</td>
                <td>
                    <input type="text" name="extProgressTime" class="form-control num-only" value="<?php echo $model->data["ExtProgressTime"]?>">
                </td>
            </tr>
            <tr>
                <td>[自動]停止秒數</td>
                <td>
                    <div class="input-group">
                        <input type="text" name="callWaitingTime" class="form-control" value="<?php echo $model->data["CallWaitingTime"]?>">
                        <span class="input-group-addon">(0~300)秒，自動撥號在一段時間座席滿線後的暫停秒數</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>自動撥號速度</td>
                <td>
<!--                    <input type="text" name="concurrentCalls" class="form-control" value="--><?php //echo $model->data["ConcurrentCalls"]?><!--">-->
                    <?php echo Html::selector($model->concurrentCallsSelect)?>
                </td>
            </tr>
            <tr>
                <td>啟用</td>
                <td>
                    <input type="checkbox" name="useState" value="1" class="bootstrap-switch"
                        <?php echo $model->data["UseState"]=="1"?"checked":"";?>
                           data-size="mini" data-off-color="danger">
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
<!--                    <div class="col-md-6">-->
<!--                        <input id="sound_btn" class="btn btn-info form-control" type="button" value="音檔更新">-->
<!--                    </div>-->
<!--                    <div class="col-md-6">-->
                        <input id="update_btn" class="btn btn-primary form-control update_btn" type="button" value="修改">
<!--                    </div>-->
                </td>
            </tr>
            </tbody>
        </table>
    <?php
    echo Html::formEnd();
    ?>
    </div>


<?php
$this->partialView($bottom_view_path);
?>