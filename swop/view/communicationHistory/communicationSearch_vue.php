<?php
Bundle::addLink("datetime");
Bundle::addLink("loading");
Bundle::addLink("vue2");
Bundle::addLink("vue-component");
Bundle::addLink("lodash");
Bundle::addLink("moment");
Bundle::addLink("math");
Bundle::addLink("alertify");
$this->partialView($top_view_path);
?>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

<div class="table-responsive" v-cloak id="container">

  <table class="table table-v table-condensed">
    <tbody>
    <tr>
      <td class="col-md-1">用戶</td>
      <td class="col-md-3">
        <select class="form-control" v-model="where.userId">
          <option v-for="select in empSelector" :value="select.value">{{ select.name }}</option>
        </select>
      </td>
      <td class="col-md-1">分機號碼</td>
      <td class="col-md-3">
        <input type="text" name="extensionNo" class="form-control num-only rigorous"
               v-model="where.extensionNo" @keyup.prevent.83="where.extensionNo = 'system'">
      </td>
      <td class="col-md-1">目的端號碼</td>
      <td class="col-md-3">
        <input type="text" name="orgCalledId" class="form-control num-only rigorous"
               v-model="where.orgCalledId">
      </td>
    </tr>
    <tr>
      <td>開始時間</td>
      <td>
        <date-time-picker :date.sync="where.callStartBillingDate" :time.sync="where.callStartBillingTime"
                          type="datetimepicker"/>
      </td>
      <td>結束時間</td>
      <td>
        <date-time-picker :date.sync="where.callStopBillingDate" :time.sync="where.callStopBillingTime"
                          type="datetimepicker"/>
      </td>
      <td>等級</td>
      <td>
        <input type="text" name="customerLevel" class="form-control"
               v-model="where.customerLevel">
      </td>
    </tr>
    <tr>
      <td>秒數</span></td>
      <td>
        <div class="input-group">
          <input type="text" name="searchSec" class="form-control" v-model="where.searchSec">
          <span class="input-group-addon">~</span>
          <input type="text" name="searchSec2" class="form-control" v-model="where.searchSec2">
        </div>
      </td>
      <td>撥號類型</td>
      <td>
        <select class="form-control" v-model="where.callType">
          <option v-for="select in callTypeSelector" :value="select.value">{{ select.name }}</option>
        </select>
      </td>
      <td></td>
      <td>
        <input type="button" class="form-control btn btn-primary" @click="search()" value="列表"/>
      </td>
    </tr>
    </tbody>
  </table>

  <div class="data-container">
    <ul class="pager responsive">
      <li><a href="#" @click="pageChange(1)">第一頁</a></li>
      <li><a href="#" v-if="paginator.page != 1" @click="pageChange(paginator.page - 1)">上一頁</a></li>
      <li><a href="#" v-if="lastPage > 0 && paginator.page != lastPage" @click="pageChange(paginator.page + 1)">下一頁</a>
      </li>
      <li><a href="#" @click="pageChange(lastPage)">最後一頁</a></li>
      <li><a href="#">第
          <select :value="paginator.page" @change="pageChange($event.target.value)">
            <option v-for="page in pageSelector" :value="page">{{ page }}</option>
          </select>
          頁</a></li>
      <li><a href="#">(共{{ lastPage }}頁，{{ paginator.total }}筆資料)</a></li>
    </ul>

    <input type="button" class="btn btn-danger" value="Delete" ref="del_btn">
    <input type="button" class="btn btn-default" @click="openDownloadWindow()" value="下載">
    總計：{{ datas.length }}筆
    <table class="table table-h table-striped table-hover">
      <tbody>
      <tr>
        <th>
          <input type="checkbox" v-model="isCheckedAll">
        </th>
        <th>編號</th>
        <th>用戶</th>
        <th>分機</th>
        <th>目的端號碼</th>
        <th>開始時間</th>
        <th>時間</th>
        <th>費用</th>
        <th>等級</th>
        <th>錄音下載</th>
      </tr>
      <tr v-for="(data, index) in datas"
          :style="{ 'background-color': data.CallType == 0 ? '#dddddd' : '#ffbbbb' }">
        <td>
          <input type="checkbox" :value="data.LogID" v-model="delContainer">
        </td>
        <td>{{ (paginator.page - 1) * paginator.per_page + 1 + index }}</td>
        <td>{{ data.UserID }}</td>
        <td>{{ data.ExtensionNo }}</td>
        <td>{{ data.OrgCalledId }}</td>
        <td>{{ data.CallStartBillingDate + ' ' + data.CallStartBillingTime }}</td>
        <td>{{ data.CallDuration }}</td>
        <td>{{ data.BillValue }}</td>
        <td>{{ data.CustomerLevel }}</td>
        <td><a :href="getVoiceUrl(data)" :target="data.RecordFile ? '_blank' : ''"
               :class="['label', {
							[data.RecordFile ? 'label-info' : 'label-default'] : true
						}]">下載</a></td>
      </tr>
      <tr>
        <td colspan="3">合計</td>
        <!--                    <td></td>-->
        <td></td>
        <td></td>
        <td></td>
        <td>{{ totalData.totalTime }}</td>
        <td>{{ totalData.totalMoney }}</td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td colspan="3">總合計</td>
        <!--                    <td></td>-->
        <td></td>
        <td></td>
        <td></td>
        <td>{{ allData.totalTime }}</td>
        <td>{{ allData.totalMoney }}</td>
        <td></td>
        <td></td>
      </tr>
      </tbody>
    </table>
  </div>
</div>

<script>
  new Vue({
    el: '#container',
    data: {
      isRoot: isRoot,
      datas: [],
      delContainer: [],
      allData: {
        totalMoney: 0,
        totalTime: 0,
      },
      callTypeSelector: [
        {'value': '', 'name': '全部'},
        {'value': '0', 'name': '自動撥號'},
        {'value': '1', 'name': '手動撥號'},
      ],
      empSelector: <?php echo json_encode(array_map(function ($x) {
            return ["value" => $x["value"], "name" => $x["name"]];
        }, $this->model->empSelect["option"])); ?>,
      where: {
        userId: '',
        extensionNo: '',
        orgCalledId: '',
        callStartBillingDate: moment().format('YYYY/MM/DD'),
        callStartBillingTime: '00:00:00',
        callStopBillingDate: moment().add(1, 'days').format('YYYY/MM/DD'),
        callStopBillingTime: '00:00:00',
        customerLevel: '',
        searchSec: '',
        searchSec2: '',
        callType: '',
      },
      paginator: {
        page: 1,
        total: 0,
        per_page: 100,
      },
    },
    computed: {
      totalData: function () {
        var money = 0
        _.forEach(this.datas, function (x) {
          money = math.eval(money + '+' + (x.BillValue || 0))
        })
        return {
          totalMoney: money,
          totalTime: _.sumBy(this.datas, 'CallDuration'),
        }
      },
      lastPage: function () {
        return Math.ceil(this.paginator.total / this.paginator.per_page)
      },
      pageSelector: function () {
        return _.range(1, this.lastPage)
      },
      transBody: function () {
        return Object.assign({
          page: this.paginator.page,
          per_page: this.paginator.per_page,
        }, this.where)
      },
      isCheckedAll: {
        set: function (val) {
          this.delContainer = val ? _.map(this.datas, 'LogID') : []
        },
        get: function () {
          return this.datas.length > 0 && this.datas.length === this.delContainer.length
        },
      },
    },
    methods: {
      getVoiceUrl: function (data) {
        return data.RecordFile
          ? downloaderUrl + 'downloadFile/recordFile?userId=' + data.UserID + '&connectDate=' + moment(data.CallStartBillingDate).format('YYYYMMDD') + '&fileName=' + data.RecordFile
          : '#'
      },
      openDownloadWindow: function () {
        window.open(ctrl_uri + 'communicationSearchDownload')
      },
      search: function () {
        this.paginator.page = 1
        $.post(ctrl_uri + 'ajax_getCommunicationSearchCommonData', this.transBody,
          function (res) {
            if (res.code == 0) {
              var data = res.data
              this.allData.totalMoney = data.totalMoney
              this.allData.totalTime = data.totalTime
              this.paginator.total = data.count
            }
          }.bind(this), 'json')

        this.pageChange(this.paginator.page)
      },
      doDelete() {
        $.post(ctrl_uri + 'ajax_delCommunicationSearchCommonData', {id: this.delContainer},
          function (res) {
            if (res.code == 0 && res.data) {
              this.search()
              alertify.alert('刪除成功!')
            }
            else {
              alertify.alert('刪除失敗!')
            }
          }.bind(this), 'json')
      },
      pageChange: function (page) {
        this.paginator.page = page
        $('body').loading()
        $.post(ctrl_uri + 'ajax_getCommunicationSearchPageDatas', this.transBody,
          function (res) {
            $('body').loading('stop')
            if (res.code == 0) {
              this.datas = res.data
            }
          }.bind(this), 'json')
      },
    },
    mounted: function () {
      $(this.$refs.del_btn).confirm({
        text: '刪除確認',
        confirm: function (button) {
          this.doDelete()
        }.bind(this),
        post: true,
        confirmButton: '確定',
        cancelButton: '取消',
        confirmButtonClass: 'btn-danger',
        cancelButtonClass: 'btn-default',
      })
    },
  })
</script>

<?php
$this->partialView($bottom_view_path);
?>
