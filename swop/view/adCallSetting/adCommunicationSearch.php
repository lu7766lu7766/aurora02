<?php
Bundle::addLink("datetime");
Bundle::addLink("vue2");
Bundle::addLink("lodash");
Bundle::addLink("file-saver");
$this->partialView($top_view_path);
?>
<div id="container" v-cloak>
    <h3 id="title"><?php echo $this->menu->currentName ?></h3>
    <!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->
    <script>
        $(document).ready(function () {
        })
    </script>

    <div class="table-responsive">

        <table class="table table-v table-condensed">
            <tbody>
            <tr>
                <td>開始時間</td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" v-model="StartDate" ref="StartDate">
                        <span class="input-group-addon"> </span>
                        <input type="text" class="form-control time-picker" v-model="StartTime" ref="StartTime">
                    </div>
                </td>
                <td>結束時間</td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" v-model="StopDate" ref="StopDate">
                        <span class="input-group-addon"> </span>
                        <input type="text" class="form-control time-picker" v-model="StopTime" ref="StopTime">
                    </div>
                </td>
                <td>任務名稱</td>
                <td>
                    <input type="text" class="form-control" v-model="PlanName">
                </td>
            </tr>
            <tr>
                <td>是否計費</td>
                <td>
                    <div class="form-inline">
                        <label>
                            <input type="radio" value="1" v-model="CallBill">
                            是
                        </label>
                        <label>
                            <input type="radio" value="0" v-model="CallBill">
                            否
                        </label>
                        <label>
                            <input type="radio" value="-1" v-model="CallBill">
                            全部
                        </label>
                    </div>
                </td>
                <td></td>
                <td colspan="3">
                    <input type="button" class="form-control btn btn-primary" value="列表" @click="getData()"/>
                </td>
            </tr>
            </tbody>
        </table>
        <div v-if="data.length">
            <ul class="pager responsive" v-if="Math.ceil(data.length/ pageRows) > 1">
                <li><a href="#" @click="page = 0">第一頁</a></li>
                <li><a href="#" v-show="page > 0" @click="page--">上一頁</a></li>
                <li><a href="#" v-show="page < (Math.ceil(data.length/ pageRows)-1)" @click="page++">下一頁</a></li>
                <li><a href="#" @click="page = Math.ceil(data.length/ pageRows)-1">最後一頁</a></li>
                <li><a href="javascript:void(0)">第{{page+1}}頁</a></li>
                <li><a href="javascript:void(0)">(共{{Math.ceil(data.length/ pageRows)}}頁，{{data.length}}筆資料)</a></li>
            </ul>
            總計：{{data.length}}筆
            <button type="button" @click="exportXls" class="btn">Excel</button>
<!--            <button type="button" @click="exportCSV" class="btn">CSV</button>-->
                <table class="table table-h table-striped table-hover">
                    <tbody>
                    <tr>
                        <th>ID</th>
                        <th>目的碼</th>
                        <th>撥打時間</th>
                        <th>接通時間</th>
                        <th>掛斷時間</th>
                        <th>通話秒數</th>
                    </tr>

                    <tr v-for="(communication, index) in CurrentPageData" :style="{backgroundColor: index%2==0? '#dddddd': '#ffbbbb'}">
                        <td>{{page * pageRows + index+1}}</td>
                        <td><span style="display: none">'</span>{{communication.OrgCalledId}}</td>
                        <td>{{communication.CalledCalloutDate}}</td>
                        <td>{{communication.CallStartBillingDateTime}}</td>
                        <td>{{communication.CallLeaveDateTime}}</td>
                        <td>{{communication.CallDuration}}</td>
                    </tr>
                    <tr>
                        <td colspan="6">總時間：{{TotalTime}}</td>
                    </tr>
                    <tr>
                        <td colspan="6">總通數：{{data.length}}</td>
                    </tr>
                    <tr>
                        <td colspan="6">總接通數：{{TotalConnected}}</td>
                    </tr>
                    </tbody>
                </table>
            <!-- for excel start -->
            <div id="tbData" style="display:none">
                <table class="table table-h table-striped table-hover">
                    <tbody>
                    <tr>
                        <th>ID</th>
                        <th>目的碼</th>
                        <th>撥打時間</th>
                        <th>接通時間</th>
                        <th>掛斷時間</th>
                        <th>通話秒數</th>
                    </tr>
                    <tr v-for="(communication, index) in data" :style="{backgroundColor: index%2==0? '#dddddd': '#ffbbbb'}">
                        <td>{{page * pageRows + index+1}}</td>
                        <td><span style="display: none">'</span>{{communication.OrgCalledId}}</td>
                        <td>{{communication.CalledCalloutDate}}</td>
                        <td>{{communication.CallStartBillingDateTime}}</td>
                        <td>{{communication.CallLeaveDateTime}}</td>
                        <td>{{communication.CallDuration}}</td>
                    </tr>
                    <tr>
                        <td colspan="6">總時間：{{TotalTime}}</td>
                    </tr>
                    <tr>
                        <td colspan="6">總通數：{{data.length}}</td>
                    </tr>
                    <tr>
                        <td colspan="6">總接通數：{{TotalConnected}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <!-- for excel end -->
        </div>
    </div>
</div>
<script>
    var vm = new Vue({
        el:"#container",
        data:{
            StartDate: new Date().AddDay(-1).Format("Y/m/d"),
            StartTime: new Date().Format("H:i:s"),
            StopDate: new Date().AddDay(1).Format("Y/m/d"),
            StopTime: new Date().Format("H:i:s"),
            PlanName: "",
            CallBill: 1,
            data: [],
            page: 0,
            pageRows: 30
        },
        methods: {
            getData: function() {
                this.StartDate = this.$refs.StartDate.value
                this.StartTime = this.$refs.StartTime.value
                this.StopDate = this.$refs.StopDate.value
                this.StopTime = this.$refs.StopTime.value
                $.post(ctrl_uri + "ajaxAdCommunicationSearch", {
                    CalledCalloutDateStart: this.CalledCalloutDateStart,
                    CalledCalloutDateStop: this.CalledCalloutDateStop,
                    PlanName: this.PlanName,
                    CallBill: this.CallBill
                }, function (result) {
                    vm.data = result
                    // console.log(result.sql)
                }, "json")
            },
            exportXls: function () {
                var blob = new Blob([document.getElementById('tbData').innerHTML], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
                });
                var strFile = "AdCommunication_"+choice+".xls";
                saveAs(blob, strFile);
                return false;
            }
//            ,
//            exportCSV: function () {
//                var data = []
//                data.push(["目的碼", "撥打時間", "接通時間", "掛斷時間", "通話秒數"])
//                _.forEach(this.data, function (communication) {
//                    data.push([
//                        "'"+communication.OrgCalledId,
//                        communication.CalledCalloutDate,
//                        communication.CallStartBillingDateTime,
//                        communication.CallLeaveDateTime,
//                        communication.CallDuration])
//                })
//
//                data.push(["總時間",this.TotalTime])
//                data.push(["總通數",this.data.length])
//                data.push(["總接通數",this.TotalConnected])
//
//                var csvContent = ""
//                _.forEach(data, function (infoArray, index) {
//                    dataString = infoArray.join(",")
//                    csvContent += index < data.length ? dataString+ "\n" : dataString
//                });
//                var textEncoder = new CustomTextEncoder('windows-1252', {NONSTANDARD_allowLegacyEncoding: true})
//                var blob = new Blob(["\uFEFF"+csvContent], {
//                    type: "data:text/csv;charset=utf-8;"
//                });
//                var strFile = "AdCommunication_" + choice + ".csv";
//                saveAs(blob, strFile);
//                return false;
//            }
        },
        computed: {
            CalledCalloutDateStart: function () {
                return this.StartDate + " " + this.StartTime
            },
            CalledCalloutDateStop: function () {
                return this.StopDate + " " + this.StopTime
            },
            TotalTime: function () {
                var sum = 0
                _.forEach(vm.data, function (x) {
                    sum += x.CallDuration
                })
                return sum
            },
            TotalConnected: function () {
                return _.filter(vm.data, function (x) {
                    return x.CallDuration && x.CallDuration > 0
                }).length
            },
            CurrentPageData: function () {
                return this.data.slice(this.page*this.pageRows, this.page*this.pageRows+this.pageRows)
            }
        }
    })


</script>
<?php
$this->partialView($bottom_view_path);
?>