<style>
    .tip {
        padding: 0 20px 0 0;
    }
</style>
<div class="col-md-4 col-xs-12">
    <div class="panel panel-info">
        <div class="panel-heading text-center">
            執行中(<?php echo is_array($model->data1) ? count($model->data1) : 0 ?>)
        </div>
        <table class="table table-h table-striped panel-footer">
            <tbody>
            <tr>
                <th>號</th>
                <th>目的端號碼</th>
                <!--                    <th>狀態</th>-->
                <!--                    <th>時間</th>-->
                <!--                    <th>強</th>-->
                <th>掛</th>
            </tr>
            <?php
            if (count($model->data1) > 0) {
                foreach ($model->data1 as $i => $data) {
                    echo "<tr id='data1row{$i}'>";
                    echo "<td class='index'>" . ($i + 1) . "</td>";
                    echo "<td class='calledId'>{$data["CalledId"]}  " .
                        ($data["NormalCall"] ? "<span class='label label-danger'>節費</span>" : "") .
                        "</td>";
                    //echo "<td></td>";
                    //echo "<td>".$data["CallDuration"]."</td>";
                    //echo "<td></td>";
                    echo "<td class='ajaxHangUp'><input type='button' value='掛' seat='" . $data["Seat"] . "' calledId='" . $data["CalledId"] . "' class='btn btn-info ajaxHangUp_btn'/></td>";
                    echo "</tr>";
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-8 col-xs-12">
    <div class="panel panel-info">
        <div class="panel-heading text-center">
            等待分機(<span id="waitExtensionNoCount"><?php echo $model->waitExtensionNoCount ?></span>)
            分機(<span id="extensionNoCount"><?php echo $model->extensionNoCount ?></span>)
        </div>
        <table class="table table-h table-striped panel-footer">
            <tbody>
            <tr>
                <th>號</th>
                <th>分機</th>
                <th>目的端號碼</th>
                <th>座</th>
                <th>時間</th>
                <th>Ping</th>
                <th>掛</th>
            </tr>
            <?php
            if (is_array($model->data2)) {
                foreach ($model->data2 as $i => $data) {
                    echo "<tr id='data2row{$i}'>";
                    echo "<td class='index'>" . ($i + 1) . "</td>";
                    echo "<td class='extensionNo'>{$data["ExtensionNo"]}  " .
                        ($data["NormalCall"] ? "<sapn class='label label-danger'>節費</sapn>" : "") .
                        "</td>";
                    echo "<td class='calledId'>{$data["CalledId"]}  " .
                        ($data["OnMonitor"] ? "<span class='label label-danger'>監聽</span>" : "") .
                        "</td>";
                    echo "<td class='calloutGroupID'>" . $data["CalloutGroupID"] . "</td>";
                    echo "<td class='callDuration'>" . $data["CallDuration"] . "</td>";
                    echo "<td class='pintTime'>" . $data["PingTime"] . "</td>";
                    echo "<td class='ajaxHangUp'><input type='button' value='掛' seat='" . $data["Seat"] . "' calledId='" . $data["CalledId"] . "' class='btn btn-info ajaxHangUp_btn'/></td>";
                    echo "</tr>";
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<div class="table-responsive col-md-12">
    <table class="table table-h table-striped table-hover">
        <tbody>
        <tr>
            <th>編號</th>
            <th>群呼名稱</th>
            <th>號碼區段</th>
            <th>筆數</th>
            <th>待發</th>
            <th>執行</th>
            <!--                <th>振鈴</th>-->
            <th>接聽數</th>
            <th>接通率</th>
            <th>失敗下載</th>
            <th>未接</th>
            <!--                <th>座席</th>-->
            <th>撥號速度</th>
            <th>座席</th>
            <!--                <th>接通</th>-->
            <!--                <th>回撥</th>-->
            <th style="width:80px">開關</th>
            <!--                <th>重撥</th>-->
            <th>刪除</th>
        </tr>
        <?php
        $total_calledCount = 0;
        $total_waitCall = 0;
        if (is_array($model->data3)) {
            foreach ($model->data3 as $i => $data) {
                $startCalledNumber = "";
                $waitCall = $data["CalledCount"] - $data["CalloutCount"];
                if ($waitCall < 0) {
                    $waitCall = 0;
                }
                if (!empty($data["StartCalledNumber"])) {
                    $startCalledNumber = $data["StartCalledNumber"];
                    if ($data["CalledCount"] > 1 && $data["NumberMode"] == "0") {
                        $startCalledNumber = $data["StartCalledNumber"] . "~" .
                            str_pad($data["StartCalledNumber"] + $data["CalledCount"] - 1,
                                strlen($data["StartCalledNumber"]), '0', STR_PAD_LEFT);
                    }
                }
                $callOutId = $data["CallOutID"];
                $d_url = "{$base[folder]}sysLookOut/";
                $d_params = "?callOutId=$callOutId&startCalledNumber={$data['StartCalledNumber']}";
                $total_calledCount += $data["CalledCount"];
                $total_waitCall += $waitCall;
                echo "<tr id='data3row{$i}' userId='{$data['UserID']}' callOutId='{$callOutId}'>";
                echo "<td class='index'>" . ($i + 1) . "</td>";
                echo "<td class='planName'>" . $data["'PlanName'"] . "</td>";
                echo "<td class='startCallNumber'>" . $startCalledNumber . "</td>";
                echo "<td><a target='_blank' href='{$d_url}downloadCalledCount{$d_params}' class='calledCount'>" . $data["CalledCount"] . "</a></td>";//筆數
                echo "<td><a target='_blank' href='{$d_url}downloadWaitCall{$d_params}' class='waitCall'>" . $waitCall . "</a></td>";//待發
                echo "<td><a target='_blank' href='{$d_url}downloadCalloutCount{$d_params}' class='calloutCount'>" . $data["CalloutCount"] . "</a></td>";//執行
                echo "<td><a target='_blank' href='{$d_url}downloadCallConCount{$d_params}' class='callConCount'>" . $data["CallConCount"] . "</a></td>";//接通數
                //echo "<td>".$data["CallSwitchCount"]."</td>";//座席數//<a target='_blank' href='{$d_url}downloadCallSwitchCount{$d_params}'></a>
                echo "<td class='callConCountRate'>" . ($data["CallConCount"] == 0 ? 0 : number_format($data["CallSwitchCount"] / $data["CalloutCount"] * 100,
                        2)) . "%</td>";//接通率  //接聽數/執行
                echo "<td><a target='_blank' href='{$d_url}downloadFaild{$d_params}' class='btn btn-info'>下載</a></td>";//失敗下載
                echo "<td><a target='_blank' href='{$d_url}downloadMissed{$d_params}' class='btn btn-info'>未接</a></td>";//未接
                echo "<td class='concurrentCalls'>" . Html::selector($model->concurrentCallsSelect,
                        $data["ConcurrentCalls"]) . "</td>";//撥號速度
                echo "<td class='calloutGroupID'>" . Html::selector($model->calloutGroupIDSelect,
                        $data["CalloutGroupID"]) . "</td>";//座席
                echo "<td class='useState'><label class='switch'>";
                echo "<input class=' useState_switch' type='checkbox' value='1' " .
                    ($data["UseState"] == 1 ? "checked" : "") .
                    " data-size='mini' data-off-color='danger' callOutId='{$callOutId}'>";//bootstrap-switch
                echo "<div class='slider round'></div>";
                echo "</label></td>";
                //echo "<td><input type='button' value='重' userId='".$data["UserID"]."' calloutId='".$data["CallOutID"]."' class='btn btn-info ajaxRecall_btn'/></td>";
                echo "<td class='ajaxDel'><input type='button' value='刪' userId='" . $data["UserID"] . "' callOutId='{$callOutId}' class='btn btn-danger ajaxDel_btn'/></td>";
                echo "</tr>";
            }
        }
        ?>
        </tbody>
    </table>
</div>
<script>
    var total_calledCount = '<?php echo $total_calledCount;?>';
    var total_waitCall = '<?php echo $total_waitCall;?>';
    var balance = '<?php echo $model->balance;?>';
    $("#total_calledCount").text(total_calledCount);
    $("#total_waitCall").text(total_waitCall);
    $("#balance").text(balance);

    //        var bootstrap_switch = {
    //            ready : function(e){
    //                var max_times = 5,times=0;
    //                var timer = setInterval(function(){
    //                    if( (times++) >max_times || $(".bootstrap-switch").length){
    //                        e();
    //                        clearInterval(timer);
    //                    }
    //                },500);
    //            }
    //        }

    $(".ajaxHangUp_btn").confirm({
        text: "刪除掛斷",
        confirm: function (button) {
            var id = $(button).parent().parent().attr("id");
//                var host = location.hostname+":60/CallRelease.atp";
            $.post(folder + "sysLookout/ajaxCallRelease", {
                Seat: $(button).attr("seat"),
                CalledID: $(button).attr("calledId")
                //url:"http://127.0.0.1:60/CallRelease.atp?Seat="+$(button).attr("seat")+"&CalledID="+$(button).attr("calledId")
            }, function (data) {
                console.log(data)
                if ($.trim(data).indexOf("OK") > -1) {
                    $("#" + id).remove();
                }
            })
//                $("#ifrm").remove();
//                var host = "http://"+location.hostname+":60/CallRelease.atp";
//                var params = "?Seat="+$(button).attr("seat")+"&CalledID="+$(button).attr("calledId");
//                var ifrm = document.createElement('iframe');
//                ifrm.setAttribute('id', 'ifrm');
//                ifrm.setAttribute('src', host+params);
//                $(ifrm).hide().appendTo($("body"));
        },
        post: true,
        confirmButton: "確定",
        cancelButton: "取消",
        confirmButtonClass: "btn-danger",
        cancelButtonClass: "btn-default"
    });
    $(".ajaxDel_btn").confirm({
        text: "刪除確認",
        confirm: function (button) {
            var id = $(button).parent().parent().attr("id");
            $.post(folder + "sysLookout/ajaxDeleteCallPlan", {
                userId: $(button).attr("userId"),
                callOutId: $(button).attr("callOutId")
            }, function (data) {
                if ($.trim(data) == "success") {
                    $("#" + id).remove();
                }
            })
        },
        post: true,
        confirmButton: "確定",
        cancelButton: "取消",
        confirmButtonClass: "btn-danger",
        cancelButtonClass: "btn-default"
    });
    $(".ajaxRecall_btn").confirm({
        text: "該筆群呼資料將重頭開始撥號",
        confirm: function (button) {
            $.post(folder + "sysLookout/ajaxRecall", {
                userId: $(button).attr("userId"),
                calloutId: $(button).attr("calloutId")
            }, function (data) {
                if ($.trim(data) == "success") {
                    alertify.alert('已開始重新撥號!');
                }
            })
        },
        post: true,
        confirmButton: "確定",
        cancelButton: "取消",
        confirmButtonClass: "btn-danger",
        cancelButtonClass: "btn-default"
    });
    //communicationSearch
    $(".ajaxChgConcurrentCalls").change(function () {//變更連線速度
        $this = $(this);
        $data = $this.parent().parent();
        value = this.value;
        $.post(folder + "sysLookout/ajaxChgConcurrentCalls", {
            concurrentCalls: value,
            userId: $data.attr("userId"),
            calloutId: $data.attr("calloutId")
        }, function (data) {
            if ($.trim(data) == "success") {
                $this.blur();
                alertify.alert('已成功修改!');
                //$(".ajaxChgCalloutGroupID").focus();
            }
        })
    }).focusin(function () {
        clearInterval(timer);
    }).focusout(function () {
        timer = setInterval(ajaxCallStatusContent, update_time);
    });

    $(".ajaxChgCalloutGroupID").change(function () {//變更座席
        $this = $(this);
        $data = $this.parent().parent();
        console.log($data)
        console.log($data.attr("calloutId"))
        value = this.value;
        $.post(folder + "sysLookout/ajaxChgCalloutGroupID", {
            calloutGroupID: value,
            userId: $data.attr("userId"),
            calloutId: $data.attr("calloutId")
        }, function (data) {
            if ($.trim(data) == "success") {
                $this.blur();
                alertify.alert('已成功修改!');
            }

        })
    }).focusin(function () {
        clearInterval(timer);
    }).focusout(function () {
        timer = setInterval(ajaxCallStatusContent, update_time);
    });


    $(".useState_switch").change(function () {
        var callOutId = $(this).attr("callOutId");
        var state = $(this).is(":checked");
        $.post(folder + "sysLookout/ajaxUseState", {
            userId: $("#choice").val(),
            callOutId: callOutId,
            useState: state ? this.value : "0"
        }, function (data) {

        });
    })

    //        bootstrap_switch.ready(function(){
    //            $(".bootstrap-switch").on('switchChange.bootstrapSwitch', function(event, state) {
    //                var callOutId = $(this).attr("callOutId");
    //                $.post(folder+"sysLookout/ajaxUseState",{
    //                    userId:$("#choice").val(),
    //                    callOutId:callOutId,
    //                    useState:state? this.value: "0"
    //                },function( data ) {
    //
    //                });
    //
    //            });
    //
    //
    //            $(".bootstrap-switch").children().click(function(e) {
    //                e.stopPropagation();
    //            });
    //        });

    ////////////////////////////////////////////////////


</script>
