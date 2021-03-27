<?php
Bundle::addLink("alertify");
Bundle::addLink("vue");
$this->partialView($top_view_path);
?>
<style xmlns:v-bind="http://www.w3.org/1999/xhtml">
    .tip {
        padding: 0 20px 0 0;
    }
</style>

<div id="container">

    <h3 id="title"><?php echo $this->menu->currentName ?></h3>

    <div class="form-inline">

        <div class="form-group">
            <!--            <label>總路由線路數:</label>-->
            <!--            <input type="text" size="5"-->
            <!--                   :class="{'form-control':true, 'num-only':true, 'readonly':!isRoot}"-->
            <!--                   v-model="maxRoutingCalls"-->
            <!--                   @focus="save_value"-->
            <!--                   @change="chg_maxRoutingCalls">-->
            <label>自動撥號線數上限:</label>
            <input type="text" size="5"
                   :class="{'form-control':true, 'num-only':true, 'readonly':!isRoot}"
                   v-model="maxCallsLimit"
                   @focus="save_value"
                   @change="chg_maxCallsLimit">
            <!--            {{maxRoutingCalls}} >= {{maxSearchCalls}} + {{maxRegularCalls}} + {{maxCalls}}-->
        </div>
        <div class="form-group">
            <label>自動撥號當前線數:</label>
            <input type="text" size="5" class="form-control num-only"
                   v-model="maxCalls"
                   @focus="save_value"
                   @change="chg_maxCalls">
        </div>
        <div class="form-group">
            <label>自動停止秒數:</label>
            <input type="text" size="5" class="form-control num-only"
                   v-model="callWaitingTime"
                   @change="chg_callWaitingTime">
        </div>
        <div class="form-group">
            <label>撥號分配模式:</label>
            <select class="form-control" v-model="planDistribution" @change="chg_planDistribution">
                <option value="0">輪詢</option>
                <option value="1">待發數多優先</option>
                <option value="2">第一筆開始優先分配</option>
                <option value="3">最後一筆開始優先分</option>
                <option value="4">待發數少的優先</option>
            </select>
        </div>

        <div class="form-group">
            <input type="button"
                   :class="{'btn':true, 'ajaxSuspend_switch':true, 'btn-success':is_suspend, 'btn-danger':!is_suspend}"
                   :value="is_suspend?'啟動':'停止'"
                   @click="ajaxSuspend">
        </div>
    </div>
    <div class="form-inline">
        <div class="form-group">
            <label>總筆數:</label>
            <label id="total_calledCount" v-text="subData.total_calledCount"></label>
        </div>
        <div class="form-group">
            <label>總待發數:</label>
            <label id="total_waitCall" v-text="subData.total_waitCall"></label>
        </div>
        <div class="form-group">
            <label>剩餘點數:</label>
            <label id="balance" v-text="subData.balance"></label>
        </div>
    </div>
    <br>

    <div class="col-md-4 col-xs-12">
        <div class="panel panel-info">
            <div class="panel-heading text-center">
                執行中(<span v-text="subData.data1.length"></span>)
            </div>
            <div style="height:300px; overflow: auto">
                <table class="table table-h table-striped panel-footer">
                    <tbody>
                    <tr>
                        <th>號</th>
                        <th>目的端號碼</th>
                        <th>掛</th>
                    </tr>
                    <tr v-for="data in subData.data1">
                        <td v-text="$index+1"></td>
                        <td>
                          <span v-text="data.CalledId"></span>
                          <span v-if="data.NormalCall" class="label label-danger">節費</span></td>
                        <td><input type='button' value='掛' class='btn btn-info' @click="ajaxHangUp(1,$index,$event)"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="col-md-8 col-xs-12">
        <div class="panel panel-info">
            <div class="panel-heading text-center">
                等待分機(<span id="waitExtensionNoCount" v-text="subData.waitExtensionNoCount"></span>)
                分機(<span id="extensionNoCount" v-text="subData.extensionNoCount"></span>)
            </div>
            <div style="height:300px; overflow: auto">
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
                    <tr v-for="data in subData.data2">
                        <td v-text="$index+1"></td>
                        <td>
                          <span v-text="data.ExtensionNo"></span>
                          <span v-if="data.NormalCall" class="label label-danger">節費</span>
                        </td>
                        <td>
                          <span v-text="data.CalledId"></span>
                          <span v-if="data.OnMonitor" class="label label-danger">監聽</span></td>
                        <td v-text="data.CalloutGroupID"></td>
                        <td v-text="data.CallDuration"></td>
                        <td v-text="data.PingTime"></td>
                        <td><input type='button' value='掛' class='btn btn-info' @click="ajaxHangUp(2,$index,$event)"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
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
                <th>接聽數</th>
                <th>接通率</th>
                <th>失敗下載</th>
                <th>未接</th>
                <th>撥號速度</th>
                <th>座席</th>
                <th style="width:80px">開關</th>
                <th>刪除</th>
            </tr>
            <tr v-for="data in subData.data3">
                <td v-text="$index+1"></td><!--編號-->
                <td v-text="data.PlanName"></td><!--群呼名稱-->
                <td v-text="data.StartCalledNumber_txt"></td><!--號碼區段-->
                <td><a target='_blank' :href="data.downloadCalledCount" v-text="data.CalledCount"></a></td><!--筆數-->
                <td><a target='_blank' :href="data.downloadWaitCall" v-text="data.waitCall"></a></td><!--待發-->
                <td><a target='_blank' :href="data.downloadCalloutCount" v-text="data.CalloutCount"></a></td><!--執行-->
                <td><a target='_blank' :href="data.downloadCallConCount" v-text="data.CallConCount"></a></td><!--接聽數-->
                <td v-text="data.CallConCount_txt"></td><!--接通率-->
                <td><a target='_blank' :href="data.downloadFaild" class='btn btn-info'>下載</a></td>
                <td><a target='_blank' :href="data.downloadMissed" class='btn btn-info'>未接</a></td>
                <td>
                    <select v-model="data.ConcurrentCalls"
                            @change="chg_ConcurrentCalls(data.ConcurrentCalls,data.CallOutID)"
                            @focus="stop_update"
                            @blur="start_update">
                        <option v-for="option in concurrentCallsSelect" :value="option.value"
                                v-text="option.name"></option>
                    </select>
                </td>
                <td>
                    <select v-model="data.CalloutGroupID"
                            @change="chg_CalloutGroupID(data.CalloutGroupID,data.CallOutID)"
                            @focus="stop_update"
                            @blur="start_update">
                        <option v-for="option in calloutGroupIDSelect" :value="option.value"
                                v-text="option.name"></option>
                    </select>
                </td>
                <td>
                    <label class='switch'>
                        <input class='useState_switch' type='checkbox' value="1" v-model='data.UseState'
                               @change="chg_useState($index)"/>

                        <div class='slider round'></div>
                    </label>
                </td>
                <td><input type='button' value='刪' class='btn btn-danger ajaxDel_btn' @click="ajaxDel($index,$event)"/>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</div>
<script>

    var model = {
        choice: choice,
        isRoot: isRoot,
        maxRoutingCalls: "<?php echo $model->maxRoutingCalls?>",
        maxCalls: "<?php echo $model->maxCalls?>",
        callWaitingTime: "<?php echo $model->callWaitingTime?>",
        maxRegularCalls: "<?php echo $model->maxRegularCalls?>",
        maxSearchCalls: "<?php echo $model->maxSearchCalls?>",
        planDistribution: "<?php echo $model->planDistribution?>",
        is_suspend: <?php echo $model->is_suspend ? "true" : "false"?>,
        concurrentCallsSelect: <?php echo json_encode($model->concurrentCallsSelect["option"])?>,
        calloutGroupIDSelect: <?php echo json_encode($model->calloutGroupIDSelect["option"])?>,
        alert_txt: "總路由線路數必須 >= 掃號路由線數 + 節費路由線數 +自動撥號當前線數",
        subData: {},
    };
    var vm = new Vue({
        el: '#container',
        data: model,
        methods: {
            stop_update: function () {
                clearInterval(timer);
            },
            start_update: function () {
                timer = setInterval(ajaxCallStatusContent, update_time);
            },
            save_value: function (e) {
                this.tmp = e.target.value;
            },

            ajax_request: function (uri, formData) {
                $.post(uri, formData, function (data) {
                    if ($.trim(data) == "success") {
                        alertify.alert('已成功修改!');
                    }
                })
            },
            chg_maxCallsLimit: function (e) {
                if (!this.countCondition) {
                    this.maxCallsLimit = this.tmp;
                    alert(this.alert_txt);
                }
                else {
                    this.ajax_request(ctrl_uri + "ajaxMaxRoutingCalls", {
                        userId: this.choice,
                        maxRoutingCalls: this.maxRoutingCalls//e.target.value
                    });
                }
            },
            chg_maxCalls: function (e) {
                if (!this.countCondition) {
                    this.maxCalls = this.tmp;
                    alert(this.alert_txt);
                }
                else {
                    this.ajax_request(ctrl_uri + "ajaxMaxCalls", {
                        userId: this.choice,
                        maxCalls: e.target.value
                    });
                }
            },
            chg_callWaitingTime: function (e) {
                this.ajax_request(ctrl_uri + "ajaxcCallWaitingTime", {
                    userId: this.choice,
                    callWaitingTime: e.target.value
                });
            },
            chg_planDistribution: function (e) {
                this.ajax_request(ctrl_uri + "ajaxcPlanDistribution", {
                    userId: this.choice,
                    planDistribution: e.target.value
                });
            },

            chg_ConcurrentCalls: function (value, calloutID) {
                this.ajax_request(ctrl_uri + "ajaxChgConcurrentCalls", {
                    concurrentCalls: value,
                    userId: this.choice,
                    calloutId: calloutID
                });
            },
            chg_CalloutGroupID: function (value, calloutID) {
                this.ajax_request(ctrl_uri + "ajaxChgCalloutGroupID", {
                    calloutGroupID: value,
                    userId: this.choice,
                    calloutId: calloutID
                });
            },

            ajaxSuspend: function (e) {
                $(e.target).confirm({
                    text: "確定要變更狀態?",
                    confirm: function (button) {
                        $.post(ctrl_uri + "ajaxSuspendSwitch", {
                            userId: vm.choice,
                        }, function (data) {
                            if ($(button).is(".btn-danger")) {
                                $(button).removeClass("btn-danger").addClass("btn-success").val("啟動");
                            } else {
                                $(button).removeClass("btn-success").addClass("btn-danger").val("停止");
                            }
                        })
                    },
                    post: true,
                    confirmButton: "確定",
                    cancelButton: "取消",
                    confirmButtonClass: "btn-danger",
                    cancelButtonClass: "btn-default"
                }).trigger("click");
            },

            chg_useState: function (index) {
                this.stop_update();
                this.start_update();
                var item = vm.subData.data3[index];
                $.post(ctrl_uri + "ajaxUseState", {
                    userId: this.choice,
                    callOutId: item.CallOutID,
                    useState: item.UseState
                }, function (data) {

                });
            },

            ajaxHangUp: function (pos, index, e) {
                var datas = vm.$eval('subData.data' + pos);
                var item = datas[index];
                vm.stop_update();
                $(e.target).confirm({
                    text: "刪除掛斷",
                    confirm: function (button) {
                        //var id = $(button).parent().parent().attr("id");
                        vm.start_update();
                        $.post(ctrl_uri + "ajaxCallRelease", {
                            Seat: item.Seat,//$(button).attr("seat"),
                            CalledID: item.CalledId//$(button).attr("calledId")
                        }, function (result) {
                            //console.log(data)
                            if ($.trim(result).indexOf("OK") > -1) {
                                //$("#"+id).remove();
                                //datas.splice(index,1);
                                datas.$remove(vm.$eval('subData.data' + pos)[index]);
                            }
                        });
                    },
                    cancel: function (button) {

                        vm.start_update();
                    },
                    post: true,
                    confirmButton: "確定",
                    cancelButton: "取消",
                    confirmButtonClass: "btn-danger",
                    cancelButtonClass: "btn-default"
                }).trigger("click");
            },
            ajaxDel: function (index, e) {
                vm.stop_update();
                var item = this.subData.data3[index];
                $(e.target).confirm({
                    text: "刪除確認",
                    confirm: function (button) {
                        vm.start_update();
                        $.post(ctrl_uri + "ajaxDeleteCallPlan", {
                            userId: vm.choice,
                            callOutId: item.CallOutID
                        }, function (data) {
                            if ($.trim(data) == "success") {
                                //vm.subData.data3.splice(index,1);
                                vm.subData.data3.$remove(vm.subData.data3[index]);
                            }
                        })
                    },
                    cancel: function () {
                        vm.start_update();
                    },
                    post: true,
                    confirmButton: "確定",
                    cancelButton: "取消",
                    confirmButtonClass: "btn-danger",
                    cancelButtonClass: "btn-default"
                }).trigger("click");
            }
        },
        computed: {
            countCondition: function () {
                return parseInt(this.maxRoutingCalls) >=
                    parseInt(this.maxSearchCalls) + parseInt(this.maxRegularCalls) + parseInt(this.maxCalls);
            },
            maxCallsLimit: {
                get: function () {
                    return parseInt(this.maxRoutingCalls) - parseInt(this.maxSearchCalls) - parseInt(this.maxRegularCalls);
                },
                set: function (newValue) {
                    this.maxRoutingCalls = parseInt(newValue) + parseInt(this.maxSearchCalls) + parseInt(this.maxRegularCalls);
                }
            }
        }
    });

    var update_time = 4 * 1000, timer, call_status_data, reload_times = 0, max_times = 2000;

    var ajaxCallStatusContent = function () {
        if (++reload_times > max_times) {
            setCookie("scrollTop", $(window).scrollTop());
            window.history.go(0);
            return;
        }

        $.ajax({
          type: "POST",
          url: apiUrl + controller + "/ajaxCallStatusContent2",
          // contentType: "application/json",
          dataType: 'json',
          data:{userId: vm.choice},
          success: function(data) {
            data.total_calledCount = data.total_waitCall = 0;

            data.data3.forEach(function (x) {

              data.total_calledCount += +x.CalledCount;

              var waitCall = x.CalledCount - x.CalloutCount;

              x.waitCall = waitCall > 0 ? waitCall : 0;

              data.total_waitCall += waitCall;

              x.StartCalledNumber_txt = x.StartCalledNumber;

              if (x.NumberMode == 0 && x.CalledCount > 1) {
                x.StartCalledNumber_txt += ("~" + (parseInt(x.StartCalledNumber) + x.CalledCount).toString().padLeft("0", x.StartCalledNumber.length));
              }
              if (x.NumberMode === 3 && x.EndCalledNumber) {
                if (x.EndCalledNumber) {
                  x.StartCalledNumber_txt += ("~" + x.EndCalledNumber + "(有效)")
                } else {
                  x.StartCalledNumber_txt += "(有效)"
                }
              }

              x.d_params = "?callOutId=" + x.CallOutID + "&startCalledNumber=" + x.StartCalledNumber;

              x.downloadCalledCount = ctrl_uri + 'downloadCalledCount' + x.d_params;
              x.downloadWaitCall = ctrl_uri + 'downloadWaitCall' + x.d_params;
              x.downloadCalloutCount = ctrl_uri + 'downloadCalloutCount' + x.d_params;
              x.downloadCallConCount = ctrl_uri + 'downloadCallConCount' + x.d_params;
              x.downloadFaild = ctrl_uri + 'downloadFaild' + x.d_params;
              x.downloadMissed = ctrl_uri + 'downloadMissed' + x.d_params;

              x.CallConCount_txt = x.CallConCount == 0 ? "0%" : ((x.CallConCount / x.CalloutCount) * 100).toFixed(2) + "%";

            });
            model.is_suspend = data['suspend'] ? true : false;
            delete data['suspend']
            model.subData = data;

            data = null;

            var st = getCookie("scrollTop");
            if (st) {
              $("html,body").scrollTop(st);
              delCookie("scrollTop");
            }

            st = null;
          }
        });
    }
    ajaxCallStatusContent();

    timer = setInterval(ajaxCallStatusContent, update_time);

    //////////////// jQ View


</script>
<?php
$this->partialView($bottom_view_path);
?>


