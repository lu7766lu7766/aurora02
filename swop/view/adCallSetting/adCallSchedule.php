<?php
//echo "<pre>";print_r($this);
Bundle::addLink("validate");
Bundle::addLink("bootstrap-datetime");
Bundle::addLink("vue2");
Bundle::addLink("vue-component");
Bundle::addLink("lodash");
Bundle::addlink("alertify");
$this->partialView($top_view_path);
?>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->
<?php
echo Html::form();
?>
<div class="col-md-12 form-inline">
    <label class="col-md-2"><input type="radio" class="radio add_type" name="numberMode" value="1" checked>名單上傳</label>
    <label class="col-md-2"><input type="radio" class="radio add_type" name="numberMode" value="0">區段增加</label>

    <div class="col-md-4"></div>
    <!--        <input id="post_btn" class="col-md-2 btn btn-primary post_btn" type="button" value="新增">-->
</div>
<div class="table-responsive col-md-12" v-cloak id="container">
    <table class="table table-v">
        <tbody>
        <tr>
            <td class="col-md-2">廣告名稱</td>
            <td class="col-md-2">
                <input type="text" name="planName" class="form-control">
            </td>
            <td class="col-md-2 type1" style="display: none">起始電話</td>
            <td class="col-md-2 type1" style="display: none">
                <div class="dropdown">
                    <input type="text" name="startCalledNumber" id="startCalledNumber"
                           class="form-control num-only rigorous">
                </div>
            </td>
            <td class="col-md-2 type1" style="display: none">筆數</td>
            <td class="col-md-2 type1" style="display: none">
                <input type="text" name="calledCount" class="form-control">
            </td>
            <td class="col-md-2 type2">上傳名單</td>
            <td class="col-md-6 type2" colspan="3">
                <input type="file" name="list"/>
            </td>
        </tr>
        <tr>
            <td>撥出電話等待秒數</td>
            <td>
                <input type="text" name="callProgressTime" class="form-control" v-model.lazy="callProgressTime">
            </td>
            <td>自動撥號速度</td>
            <td>
                <!--                    <select id="concurrentCalls" name="concurrentCalls" class="form-control">-->
                <!--                        <option v-for="value in _.range(10,101)" :value="value">每１秒{{value}}通</option>-->
                <!--                    </select>-->
                <concurrent-calls :values="_.concat([3, 5, 7], _.range(10,101), [125, 150, 175, 200])"
                                  id="concurrentCalls"
                                  name="concurrentCalls"
                                  v-model="concurrentCalls"
                                  class="form-control"></concurrent-calls>
            </td>
            <td class="col-md-2">開始日期</td>
            <td class="col-md-2">
                <input type="text" name="startDate" class="form-control bootstrap-date-picker" placeholder="2017/01/01"
                       readonly>
            </td>
        </tr>
        <tr>
            <td>開始時間1</td>
            <td>
                <input type="text" name="startTime1" class="form-control bootstrap-time-picker" placeholder="00:00:00"
                       readonly step="2">
            </td>
            <td>開始時間2</td>
            <td>
                <input type="text" name="startTime2" class="form-control bootstrap-time-picker" placeholder="00:00:00"
                       readonly step="2">
            </td>
            <td>開始時間3</td>
            <td>
                <input type="text" name="startTime3" class="form-control bootstrap-time-picker" placeholder="00:00:00"
                       readonly step="2">
            </td>
        </tr>
        <tr>
            <td>結束日期</td>
            <td>
                <input type="text" name="stopDate" class="form-control bootstrap-date-picker" placeholder="2017/01/01"
                       readonly>
            </td>
            <td>結束時間1</td>
            <td>
                <input type="text" name="stopTime1" class="form-control bootstrap-time-picker" placeholder="00:00:00"
                       readonly step="2">
            </td>
            <td>結束時間2</td>
            <td>
                <input type="text" name="stopTime2" class="form-control bootstrap-time-picker" placeholder="00:00:00"
                       readonly step="2">
            </td>
        </tr>
        <tr>
            <td>結束時間3</td>
            <td>
                <input type="text" name="stopTime3" class="form-control bootstrap-time-picker" placeholder="00:00:00"
                       readonly step="2">
            </td>
            <td>語音檔名</td>
            <td>
                <!--                    <input type="text" name="fileName1" class="form-control">-->
                <select name="fileName1" class="form-control" v-if="voiceFileList.length">
                    <option v-for="voiceFile in voiceFileList" :value="voiceFile">{{ voiceFile }}</option>
                </select>
                <span v-else>查無語音檔</span>
            </td>
            <td>結束語音檔名</td>
            <td>
                <!--                    <input type="text" name="fileName1" class="form-control">-->
                <select name="fileName2" class="form-control" v-if="voiceFileList.length">
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
            <td>重撥間隔秒數</td>
            <td>
                <select class="form-control" name="retryTime" v-model="retryTime">
                    <option v-for="val in [180, 300, 600, 1800]" :value="val">{{val}}</option>
                </select>
            </td>
            <td>停撥接通數</td>
            <td>
                <input type="text" class="form-control" name="stopOnConCount" v-model="stopOnConCount">
            </td>

        </tr>
        <tr>
            <td>紀錄按鍵</td>
            <td>
                <label class='switch'>
                    <input id='switch' name="waitDTMF" type='checkbox' value="1" :checked="waitDTMF"
                           @click="waitDTMF = Math.abs(waitDTMF-1)"/>

                    <div class='slider round'></div>
                </label>
            </td>
            <td class="col-md-2"></td>
            <td colspan="3">
                <input id="post_btn" class="form-control btn btn-primary post_btn" type="button" value="新增">
            </td>
        </tr>
        </tbody>
    </table>

    <input type="button" class="btn btn-danger delete_btn" value="Delete">
    <table class="table table-h table-striped table-hover table-pointer">
        <tbody>
        <tr>
            <th>
                <input type='checkbox' class="checkAll" for="delete[]"/>
            </th>
            <th>用戶</th>
            <th>掃號名稱</th>
            <th>起始電話</th>
            <th>筆數</th>
            <th>啟用</th>
        </tr>
        <?php
        foreach ($model->data as $data) {
            $userId = $data["UserID"];
            $callOutId = $data["CallOutID"];
            echo "<tr redirect='adCallSetting/adCallScheduleModify?userId={$userId}&callOutId={$callOutId}'>";
            echo "<td><input type='checkbox' name='delete[]' value='{$userId},{$callOutId}'/></td>";
            echo "<td>" . $data["UserID"] . "</td>";
            echo "<td>" . $data["PlanName"] . "</td>";
            echo "<td>" . $data["StartCalledNumber"] . "</td>";
            echo "<td>" . $data["CalledCount"] . "</td>";
            echo "<td><label class='switch'><input id='switch' disabled type='checkbox' " .
                ($data["UseState"] ? "checked" : "") .
                "><div class='slider round'></div></label></td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>

</div>
<?php
echo Html::formEnd();
?>
<script>

    var vm = new Vue({
        el: "#container",
        data: {
            callRetry: 0,
            retryTime: 600,
            stopOnConCount: 0,
            callProgressTime: 20,
            concurrentCalls: 10,
            waitDTMF: 0,
            voiceFileList: <?php echo json_encode($this->voiceFileList)?>
        },
        watch: {
            callProgressTime: function (newVal) {
                if (newVal < 15 || newVal > 300) {
                    alertify.alert("撥出電話等待秒數必須大於15小於300")
                    setTimeout(function () {
                        vm.callProgressTime = 15
                    }, 50)
                }
            }
        }
    })

    $(document).ready(function () {

        $(".add_type").change(function () {
            var $this = $(this), type = $this.val();
            switch (type) {
                case "0":
                    $(".type1").show();
                    $(".type2").hide();
                    break;
                case "1":
                    $(".type1").hide();
                    $(".type2").show();
                    break;
                case "2":
                    $(".type1").show();
                    $(".type2").hide();
                    break;
            }
        });

        $("input[name='list']").change(function (evt) {
            for (var i = 0, f; f = evt.target.files[i]; i++) {
                if (!f.type.match('text/plain')) {
                    $("input[name='list']").val('');
                    alert("檔案格式不符，請上傳txt檔");
                }
            }
        })

    })
</script>

<?php
$this->partialView($bottom_view_path);
?>
