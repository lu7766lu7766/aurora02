<?php
Bundle::addLink("alertify");
$this->partialView($top_view_path);
?>

<input type="hidden" name="choice" id="choice" value="<?php echo $model->session["choice"] ?>"/>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->
<div class="form-inline">

    <input type="hidden" id="maxRegularCalls" value="<?php echo $model->maxRegularCalls ?>"/>
    <input type="hidden" id="maxSearchCalls" value="<?php echo $model->maxSearchCalls ?>"/>

    <div class="form-group">
        <label>總路由線路數:</label>
        <input type="text" size="5"
               class="form-control num-only <?php echo !$model->session["isRoot"] ? "readonly" : "" ?>"
               id="maxRoutingCalls"
               value="<?php echo $model->maxRoutingCalls ?>">
    </div>
    <div class="form-group">
        <label>自動撥號路由線數:</label>
        <input type="text" size="5" class="form-control num-only" id="maxCalls"
               value="<?php echo $model->maxCalls ?>">
    </div>
    <div class="form-group">
        <label>自動停止秒數:</label>
        <input type="text" size="5" class="form-control num-only" id="callWaitingTime"
               value="<?php echo $model->callWaitingTime ?>">
    </div>
    <div class="form-group">
        <label>撥號分配模式:</label>
        <select class="form-control" id="planDistribution">
            <option value="0">輪詢</option>
            <option value="1">待發數多優先</option>
            <option value="2">第一筆開始優先分配</option>
            <option value="3">最後一筆開始優先分</option>
            <option value="4">待發數少的優先</option>
        </select>
        <script>
            var planDistribution = "<?php echo $model->planDistribution?>";
            $("#planDistribution").val(planDistribution);
        </script>
    </div>

    <div class="form-group">
        <input type="button"
               class="btn <?php echo $model->is_suspend ? "btn-success" : "btn-danger" ?> ajaxSuspend_switch"
               value="<?php echo $model->is_suspend ? "啟動" : "停止" ?>">
    </div>

    <div class="form-group">
        <label>總筆數:</label>
        <label id="total_calledCount"></label>
    </div>
    <div class="form-group">
        <label>總待發數:</label>
        <label id="total_waitCall"></label>
    </div>
    <div class="form-group">
        <label>剩餘點數:</label>
        <label id="balance"></label>
    </div>
</div>
<br>
<div id="ajaxContent">

</div>
<script>

    $(".ajaxSuspend_switch").confirm({
        text: "確定要變更狀態?",
        confirm: function (button) {
            $.post(folder + "sysLookout/ajaxSuspendSwitch", {
                userId: $("#choice").val(),
            }, function (data) {
                if ($(button).is(".btn-danger")) {
                    $(button).removeClass("btn-danger").addClass("btn-success").val("啟動");
                } else {
                    $(button).removeClass("btn-success").addClass("btn-danger").val("停止");
                }
//                    location.reload();
            })
        },
        post: true,
        confirmButton: "確定",
        cancelButton: "取消",
        confirmButtonClass: "btn-danger",
        cancelButtonClass: "btn-default"
    });

    var update_time = 4 * 1000, timer, call_status_data, reload_times = 0, max_times = 100;

    var ajaxCallStatusContent = function () {
        console.log(reload_times);
        if (++reload_times > max_times) {
            //location.reload();
            setCookie("scrollTop", $(window).scrollTop());
            window.history.go(0);
            return;
        }

        $.post(folder + "sysLookout/ajaxCallStatusContent", {
            userId: $("#choice").val()
        }, function (data) {

            $("#ajaxContent").html(data);

            var st = getCookie("scrollTop");
            if (st) {
                $("html,body").scrollTop(st);
                delCookie("scrollTop");
            }
            //document.getElementById("ajaxContent").innerHTML = data;
//            call_status_data = JSON.parse(data);
//            render();

        })
    }
    ajaxCallStatusContent();
    //    $.post(folder + "sysLookout/ajaxCallStatusContent", {
    //        userId: $("#choice").val()
    //    }, function (data) {
    //        $("#ajaxContent").html(data);
    //    })
    timer = setInterval(ajaxCallStatusContent, update_time);

    //    var render = function(){
    //        var data = call_status_data;
    //        var $table = $("#ajaxContent").find("table");
    //        var $data1_tr = $table.eq(0).find("tr:not(:first)");
    //        var $data2_tr = $table.eq(1).find("tr:not(:first)");
    //        var $data3_tr = $table.eq(2).find("tr:not(:first)");
    //
    //        $.each(data.data1,function(i){
    //
    //        })
    //
    ////        console.log($data1_tr)
    ////        console.log($data2_tr)
    ////        console.log($data3_tr)
    //        //console.log(data);
    //    }

    $maxRoutingCalls = $("#maxRoutingCalls");
    $maxSearchCalls = $("#maxSearchCalls");
    $maxRegularCalls = $("#maxRegularCalls");
    $maxCalls = $("#maxCalls");

    $maxRoutingCalls.data("value", $maxRoutingCalls.val()).on("change", function () {

        if (!countCondition()) {
            alert("總路由線路數必須 >= 掃號路由線數 + 節費路由線數 +自動撥號路由線數");
            $maxRoutingCalls.val($maxRoutingCalls.data("value"));
        } else {
            $.post(folder + "sysLookout/ajaxMaxRoutingCalls", {
                userId: $("#choice").val(),
                maxRoutingCalls: $maxRoutingCalls.val()
            }, function (data) {
                if ($.trim(data) == "success") {
                    alertify.alert('已成功修改!');
                    $maxRoutingCalls.data("value", $maxRoutingCalls.val());
                }
            })
        }
    })


    $maxCalls.data("value", $maxCalls.val()).on("change", function () {

        if (!countCondition()) {
            alert("總路由線路數必須 >= 掃號路由線數 + 節費路由線數 +自動撥號路由線數");
            $maxCalls.val($maxCalls.data("value"));
        } else {
            $.post(folder + "sysLookout/ajaxMaxCalls", {
                userId: $("#choice").val(),
                maxCalls: $maxCalls.val()
            }, function (data) {
                if ($.trim(data) == "success") {
                    alertify.alert('已成功修改!');
                    $maxCalls.data("value", $maxCalls.val());
                }
            })
        }
    })

    $("#callWaitingTime").change(function () {
        $.post(folder + "sysLookout/ajaxcCallWaitingTime", {
            userId: $("#choice").val(),
            callWaitingTime: $("#callWaitingTime").val()
        }, function (data) {
            if ($.trim(data) == "success")
                alertify.alert('已成功修改!');
        })
    })

    $("#planDistribution").change(function () {
        $.post(folder + "sysLookout/ajaxcPlanDistribution", {
            userId: $("#choice").val(),
            planDistribution: $("#planDistribution").val()
        }, function (data) {
            if ($.trim(data) == "success")
                alertify.alert('已成功修改!');
        })
    })

    var countCondition = function () {
        return parseInt($maxRoutingCalls.val() || 0) >=
            (parseInt($maxSearchCalls.val() || 0) + parseInt($maxRegularCalls.val() || 0) + parseInt($maxCalls.val() || 0));
    }

</script>
<?php
$this->partialView($bottom_view_path);
?>


