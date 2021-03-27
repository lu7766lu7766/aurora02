<?php
Bundle::addLink("alertify");
Bundle::addLink("vue2");
Bundle::addLink("vue-component");
Bundle::addLink("lodash");
$this->partialView($top_view_path);
?>
<style xmlns:v-bind="http://www.w3.org/1999/xhtml">
    .tip {
        padding: 0 20px 0 0;
    }

    .note {
        font-size: 1.02em;
    }
</style>

<div id="container" v-cloak>

    <h3 id="title">
        <?php echo $this->menu->currentName ?>
        <textarea class="form-control note"
                  rows="5"
                  placeholder="備註"
                  v-model="adNote"
                  @focus="save_value"
                  @change="chg_adNote">
        </textarea>
    </h3>


    <div class="form-inline">

        <div class="form-group">
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
                <th>未接通</th>
                <th>未接通待撥</th>
                <th>撥號速度</th>
                <th>使用金額</th>
                <th style="width:80px">開關</th>
                <th>刪除</th>
            </tr>
            <tr v-for="(data,index) in subData.data3">
                <td>{{index+1}}</td>
                <td>{{data.PlanName}}</td>
                <td>{{data.StartCalledNumber_txt}}</td>
                <td><a target='_blank' :href="data.downloadCalledCount">{{data.CalledCount}}</a></td><!--筆數-->
                <td><a target='_blank' :href="data.downloadWaitCall">{{data.waitCall}}</a></td>
                <!--待發 @click="ajax_getWaitCall(data.CallOutID)"-->
                <td><a target='_blank' :href="data.downloadCalloutCount">{{data.CalloutCount}}</a></td><!--執行-->
                <td><a target='_blank' :href="data.downloadCallConCount">{{data.CallConCount}}</a></td><!--接聽數-->
                <td>{{data.CallConCount_txt}}</td><!--接通率-->
                <td><a target='_blank' :href="data.downloadCallUnavailable">{{data.CallUnavailable}}</a></td><!--未接通-->
                <td><a target='_blank' :href="data.downloadUnRecieveWaitCall">{{data.UnRecieveWaitCall}}</a></td>
                <!--未接通-->
                <td>
                    <!--                    <select v-model="data.ConcurrentCalls"-->
                    <!--                            @change="chg_concurrentCalls(data.ConcurrentCalls,data.CallOutID)"-->
                    <!--                            @focus="stop_update"-->
                    <!--                            @blur="start_update">-->
                    <!--                        <option v-for="value in _.range(10,101)" :value="value">每１秒{{value}}通</option>-->
                    <!--                    </select>-->
                    <concurrent-calls :values="_.concat([3, 5, 7], _.range(10,101), [125, 150, 175, 200])"
                                      :calloutid="data.CallOutID"
                                      @change="chg_concurrentCalls"
                                      @focus="stop_update"
                                      @blur="start_update"
                                      v-model="data.ConcurrentCalls">

                    </concurrent-calls>
                </td>
                <td>
                    {{ data.TotalFee}}
                </td>
                <td>
                    <label class='switch'>
                        <input class='useState_switch' type='checkbox' value="1" v-model='data.UseState'
                               @change="chg_useState(index)"/>

                        <div class='slider round'></div>
                    </label>
                </td>
                <td><input type='button' value='刪' class='btn btn-danger ajaxDel_btn' @click="ajaxDel(index,$event)"/>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <script>

        var model = {
            choice: choice,
            isRoot: isRoot,
            maxRoutingCalls: "<?php echo $model->maxRoutingCalls?>",
            maxCalls: "<?php echo $model->maxCalls?>",
            maxRegularCalls: "<?php echo $model->maxRegularCalls?>",
            maxSearchCalls: "<?php echo $model->maxSearchCalls?>",
            planDistribution: "<?php echo $model->planDistribution?>",
            adNote: "<?php echo $model->adNote ?>",
            is_suspend: <?php echo $model->is_suspend ? "true" : "false"?>,
            alert_txt: "總路由線路數必須 >= 掃號路由線數 + 節費路由線數 +自動撥號當前線數",
            subData: {},
        };
        model.adNote = model.adNote.br2nl()
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
                ajax_getWaitCall: function (CallOutID) {
//                $.post(ctrl_uri + "ajaxGetDownloadWaitCall", {callOutID: CallOutID}, function (data) {
//                    download("waitCalls_" + choice + ".txt", data.join(",").replace(/\,/g, "\r\n"))
//                }, "json")

                },

                chg_maxCallsLimit: function (e) {
                    if (!this.countCondition) {
                        this.maxCallsLimit = this.tmp;
                        alert(this.alert_txt);
                    }
                    else {
                        this.ajax_request(folder + "sysLookout/ajaxMaxRoutingCalls", {
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
                        this.ajax_request(folder + "sysLookout/ajaxMaxCalls", {
                            userId: this.choice,
                            maxCalls: e.target.value
                        });
                    }
                },
                chg_planDistribution: function (e) {
                    this.ajax_request(folder + "sysLookout/ajaxcPlanDistribution", {
                        userId: this.choice,
                        planDistribution: e.target.value
                    });
                },
                chg_adNote: function (e) {
                    this.ajax_request(folder + "adCallSetting/ajaxAdNote", {
                        userId: this.choice,
                        adNote: e.target.value
                    });
                },
                ajaxSuspend: function (e) {
                    $(e.target).confirm({
                        text: "確定要變更狀態?",
                        confirm: function (button) {
                            $.post(ctrl_uri + "ajaxAdSuspendSwitch", {
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

                chg_concurrentCalls: function (value, calloutID) {
                    this.ajax_request(ctrl_uri + "ajaxChgConcurrentCalls", {
                        concurrentCalls: value,
                        userId: this.choice,
                        calloutId: calloutID
                    });
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
                ajaxDel: function (index, e) {
                    vm.stop_update();
                    var item = this.subData.data3[index];
                    $(e.target).confirm({
                        text: "刪除確認",
                        confirm: function (button) {
                            vm.start_update();
                            $.post(ctrl_uri + "ajaxDeleteAdPlan", {
                                userId: vm.choice,
                                callOutId: item.CallOutID
                            }, function (data) {
                                if ($.trim(data) == "success") {
                                    vm.subData.data3.splice(index, 1);
                                    //vm.subData.data3.$remove(vm.subData.data3[index]);
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

            $.post(ctrl_uri + "ajaxAdCallStatusContent", {
                userId: vm.choice
            }, function (data) {

                data.total_calledCount = data.total_waitCall = 0;

                data.data3.forEach(function (x) {

                    data.total_calledCount += x.CalledCount;

                    var waitCall = x.CalledCount - x.CalloutCount;

                    x.waitCall = waitCall > 0 ? waitCall : 0;

                    data.total_waitCall += waitCall;

                    x.StartCalledNumber_txt = x.StartCalledNumber;

                    if (x.NumberMode == 0 && x.CalledCount > 1) {
                        x.StartCalledNumber_txt += ("~" + (parseInt(x.StartCalledNumber) + x.CalledCount).toString().padLeft("0", x.StartCalledNumber.length));
                    }

                    x.CallConCount_txt = x.CallConCount == 0 ? "0%" : ((x.CallConCount / x.CalloutCount) * 100).toFixed(2) + "%";

                    x.d_params = "?callOutID=" + x.CallOutID;
                    x.downloadCalledCount = ctrl_uri + 'downloadCalledCount' + x.d_params;
                    x.downloadWaitCall = ctrl_uri + 'downloadWaitCall' + x.d_params;
                    x.downloadCalloutCount = ctrl_uri + 'downloadCalloutCount' + x.d_params;
                    x.downloadCallConCount = ctrl_uri + 'downloadCallConCount' + x.d_params;
                    x.downloadUnRecieveWaitCall = ctrl_uri + 'downloadUnRecieveWaitCall' + x.d_params;
                    x.downloadCallUnavailable = ctrl_uri + 'downloadCallUnavailable' + x.d_params;
                });

                model.subData = data;

                data = null;

                var st = getCookie("scrollTop");
                if (st) {
                    $("html,body").scrollTop(st);
                    delCookie("scrollTop");
                }

                st = null;

            }, "json");
        }
        ajaxCallStatusContent();

        timer = setInterval(ajaxCallStatusContent, update_time);

        //////////////// jQ View


    </script>
</div>
<?php
$this->partialView($bottom_view_path);
?>


