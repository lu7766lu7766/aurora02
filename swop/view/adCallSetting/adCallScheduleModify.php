<?php
//echo "<pre>";print_r($this);
Bundle::addLink("datetime");
Bundle::addLink("vue2");
Bundle::addLink("vue-component");
Bundle::addLink("lodash");
$this->partialView($top_view_path);
?>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

<div class="table-responsive" v-cloak id="container">
    <?php
    echo Html::form();
    ?>
    <input type="hidden" name="calledCount_source" value="<?php echo $model->data["CalledCount"]; ?>"/>
    <input type="hidden" name="numberMode" value="<?php echo $model->data["NumberMode"]; ?>"/>
    <table class="table table-v">
        <tbody>
        <tr>
            <td class="col-md-3">網頁登入帳號</td>
            <td class="col-md-9">
                <?php echo $model->userId ?>
            </td>
        </tr>
        <tr>
            <td>用戶名稱</td>
            <td>
                <?php echo $model->data["UserName"] ?>
            </td>
        </tr>
        <tr>
            <td>剩餘點數</td>
            <td>
                <?php echo $model->data["Balance"] ?>
            </td>
        </tr>
        <tr>
            <td>使用金額</td>
            <td>
                <?php echo $model->data["TotalFee"] ?>
            </td>
        </tr>
        <tr>
            <td>掃號名稱</td>
            <td>
                <input type="text" name="planName" class="form-control"
                       value="<?php echo $model->data["PlanName"] ?>">
            </td>
        </tr>
        <tr>
            <td>起始電話</td>
            <td>
                <div class="input-group">
                    <input type="text" name="startCalledNumber" class="form-control"
                           value="<?php echo $model->data["StartCalledNumber"] ?>">
                    <span class="input-group-addon">筆數</span>
                    <input type="text" name="calledCount" class="form-control num-only"
                           value="<?php echo $model->data["CalledCount"] ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>撥出電話等待秒數</td>
            <td>
                <div class="input-group">
                    <input type="text" name="callProgressTime" class="form-control num-only"
                           value="<?php echo $model->data["CallProgressTime"] ?>">
                    <span class="input-group-addon">(5~300)秒，等待接通時間</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>自動撥號速度</td>
            <td>
                <!--                    <input type="text" name="concurrentCalls" class="form-control" value="-->
                <?php //echo $model->data["ConcurrentCalls"]?><!--">-->
                <?php //echo Html::selector($model->concurrentCallsSelect)?>
                <!--                    <select id="concurrentCalls" name="concurrentCalls" class="form-control"  v-model="concurrentCalls">-->
                <!--                        <option v-for="value in _.range(10,101)" :value="value">每１秒{{value}}通</option>-->
                <!--                    </select>-->
                <concurrent-calls :values="_.concat([3, 5, 7], _.range(10,101), [125, 150, 175, 200])"
                                  id="concurrentCalls"
                                  name="concurrentCalls"
                                  class="form-control"
                                  v-model="concurrentCalls"></concurrent-calls>
            </td>
        </tr>
        <tr>
            <td>開始日期</td>
            <td>
                <input type="text" name="startDate" class="form-control date-picker"
                       value="<?php echo $model->data["StartDate"] ?>">
            </td>
        </tr>
        <tr>
            <td>開始時間1</td>
            <td>
                <input type="text" name="startTime1" class="form-control time-picker"
                       value="<?php echo $model->data["StartTime1"] ?>">
            </td>
        </tr>
        <tr>
            <td>開始時間2</td>
            <td>
                <input type="text" name="startTime2" class="form-control time-picker"
                       value="<?php echo $model->data["StartTime2"] ?>">
            </td>
        </tr>
        <tr>
            <td>開始時間3</td>
            <td>
                <input type="text" name="startTime3" class="form-control time-picker"
                       value="<?php echo $model->data["StartTime3"] ?>">
            </td>
        </tr>
        <tr>
            <td>結束日期</td>
            <td>
                <input type="text" name="stopDate" class="form-control date-picker"
                       value="<?php echo $model->data["StopDate"] ?>">
            </td>
        </tr>
        <tr>
            <td>結束時間1</td>
            <td>
                <input type="text" name="stopTime1" class="form-control time-picker"
                       value="<?php echo $model->data["StopTime1"] ?>">
            </td>
        </tr>
        <tr>
            <td>結束時間2</td>
            <td>
                <input type="text" name="stopTime2" class="form-control time-picker"
                       value="<?php echo $model->data["StopTime2"] ?>">
            </td>
        </tr>
        <tr>
            <td>結束時間3</td>
            <td>
                <input type="text" name="stopTime3" class="form-control time-picker"
                       value="<?php echo $model->data["StopTime3"] ?>">
            </td>
        </tr>
        <tr>
            <td>語音檔名</td>
            <td>
                <!--                    <input type="text" name="fileName1" class="form-control" value="-->
                <?php //echo $model->data["FileName1"]?><!--">-->
                <select name="fileName1" id="fileName1" v-model="fileName1" v-if="voiceFileList.length">
                    <option v-for="voiceFile in voiceFileList" :value="voiceFile">{{ voiceFile }}</option>
                </select>
                <span v-else>查無語音檔</span>
            </td>
        </tr>
        <tr>
            <td>結束語音檔名</td>
            <td>
                <!--                    <input type="text" name="fileName1" class="form-control" value="-->
                <?php //echo $model->data["FileName1"]?><!--">-->
                <select name="fileName2" id="fileName2" v-model="fileName2" v-if="voiceFileList.length">
                    <option value=""></option>
                    <option v-for="voiceFile in voiceFileList" :value="voiceFile">{{ voiceFile }}</option>
                </select>
                <span v-else>查無語音檔</span>
            </td>
        </tr>
        <tr>
            <td>失敗重撥次數</td>
            <td>
                <select class="form-control" name="callRetry" v-model="callRetry">
                    <option v-for="val in [0, 1, 2, 3]" :value="val">{{val}}</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>重撥間隔秒數</td>
            <td>
                <select class="form-control" name="retryTime" v-model="retryTime">
                    <option v-for="val in [180, 300, 600, 1800]" :value="val">{{val}}</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>停撥接通數</td>
            <td>
                <input type="text" name="stopOnConCount" class="form-control"
                       value="<?php echo $model->data["StopOnConCount"] ?>">
            </td>
        </tr>
        <tr>
            <td>啟用</td>
            <td>
                <input type="checkbox" name="useState" value="1" class="bootstrap-switch"
                    <?php echo $model->data["UseState"] == "1" ? "checked" : ""; ?>
                       data-size="mini" data-off-color="danger">
            </td>
        </tr>
        <tr>
            <td>紀錄按鍵</td>
            <td>
                <input type="checkbox" name="waitDTMF" value="1" class="bootstrap-switch"
                       :checked="!!waitDTMF"
                       data-size="mini" data-off-color="danger">

            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input id="update_btn" class="btn btn-primary form-control update_btn" type="button" value="修改">
            </td>
        </tr>
        </tbody>
    </table>
    <?php
    echo Html::formEnd();
    ?>
</div>

<script>
    var vm = new Vue({
        el: "#container",
        data: {
            concurrentCalls: "<?php echo $model->data["ConcurrentCalls"]?>",
            callRetry: "<?php echo $model->data["CallRetry"]?>",
            retryTime: "<?php echo $model->data["RetryTime"]?>",
            voiceFileList: <?php echo json_encode($this->voiceFileList) ?>,
            fileName1: "<?php echo $model->data["FileName1"]?>",
            fileName2: "<?php echo $model->data["FileName2"]?>",
            waitDTMF: <?php echo $model->data["WaitDTMF"] ?? 0?>
        }
    })
</script>
<?php
$this->partialView($bottom_view_path);
?>
