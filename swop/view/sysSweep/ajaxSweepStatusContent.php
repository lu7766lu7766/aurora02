<div class="table-responsive col-md-12">
    <table class="table table-h table-striped table-hover">
        <tbody>
        <tr>
            <th>編號</th>
            <th>掃號名稱</th>
            <th>搜集時間</th>
            <th>號碼區段</th>
            <th>筆數</th>
            <th>執行</th>
            <th>無效</th>
            <th>有效</th>
            <th>接通</th>
            <th>使用金額</th>
            <th style="width:80px">開關</th>
            <th>刪除</th>
        </tr>
        <?php
        $total_calledCount = 0;
        $total_waitCall = 0;
        if (is_array($model->data3)) {
            foreach ($model->data3 as $i => $data) {
                $startCalledNumber = "";
                $waitCall = $data["CalledCount"];
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
                $d_url = "{$base[folder]}sysSweep/";
                $d_params = "?callOutId=$callOutId&startCalledNumber={$data['StartCalledNumber']}";
                $total_calledCount += $waitCall;
                $total_waitCall += $data["CalledCount"] - $data["RunTimeCount"];
                echo "<tr id='data3row{$i}' userId='{$data['UserID']}' callOutId='{$callOutId}'>";
                echo "<td class='index'>" . ($i + 1) . "</td>";
                echo "<td class='planName'>" . $data["PlanName"] . "</td>";
                echo "<td class='startDateTime'>{$data['StartDateTime']}</td>";
                echo "<td class='startCallNumber'>" . $startCalledNumber . "</td>";
                echo "<td>" . $data["CalledCount"] . "</td>";//筆數 //<a target='_blank' href='{$d_url}downloadCalledCount{$d_params}' class='calledCount'> </a>
                //echo "<td>".$waitCall."</td>";//待發 //<a target='_blank' href='{$d_url}downloadWaitCall{$d_params}' class='waitCall'> </a>
                echo "<td>" . $data["RunTimeCount"] . "</td>";//執行 //<a target='_blank' href='{$d_url}downloadRunTimeCount{$d_params}' class='calloutCount'> </a>
                echo "<td><a target='_blank' href='{$d_url}downloadCallUnavailable{$d_params}' class='callUnavailable'>{$data['CallUnavailable']}</a></td>";//無效
                echo "<td><a target='_blank' href='{$d_url}downloadCallAvailable{$d_params}' class='callAvailable'>{$data['CallAvailable']}</a></td>";//有效
                echo "<td><a target='_blank' href='{$d_url}downloadCallConCount{$d_params}' class='callConCount'>" . $data["CallConCount"] . "</a></td>";//接通
                echo "<td class='totalFee'>" . (!empty($data["TotalFee"]) ? $data["TotalFee"] : "0") . "</td>";
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


    $(".ajaxDel_btn").confirm({
        text: "刪除確認",
        confirm: function (button) {
            var id = $(button).parent().parent().attr("id");
            $.post(folder + "sysSweep/ajaxDeleteSearchPlan", {
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
    //communicationSearch

    $(".useState_switch").change(function () {
        var callOutId = $(this).attr("callOutId");
        var state = $(this).is(":checked");
        $.post(folder + "sysSweep/ajaxUseState", {
            userId: $("#choice").val(),
            callOutId: callOutId,
            useState: state ? this.value : "0"
        }, function (data) {

        });
    });

</script>
