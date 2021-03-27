<?php
Bundle::addLink("datetime");
Bundle::addLink("vue2");
Bundle::addLink("lodash");
Bundle::addLink("file-saver");
Bundle::addLink("vue-component");
$this->partialView($top_view_path);
?>
<div id="container" v-cloak>
	<h3 id="title"><?php echo $this->menu->currentName ?></h3>
	<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

	<div class="table-responsive">
		<?php
		echo Html::form();
		?>
		<input type="hidden" :value="CalledCalloutDateStart" name="CalledCalloutDateStart"/>
		<input type="hidden" :value="CalledCalloutDateStop" name="CalledCalloutDateStop"/>
		<table class="table table-v table-condensed">
			<tbody>
			<tr>
				<td>開始時間</td>
				<td>
					<date-time-picker :date.sync="StartDate" :time.sync="StartTime" type="datetimepicker"/>
				</td>
				<td>結束時間</td>
				<td>
					<date-time-picker :date.sync="StopDate" :time.sync="StopTime" type="datetimepicker"/>
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
				<td>秒數<span style="font-size: 8pt">(以上)</span></td>
				<td>
					<input type="text" class="form-control" v-model="SearchSec">
				</td>
				<td>按鍵</td>
				<td>
					<input type="text" class="form-control" v-model="RecvDTMF">
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="5">
					<input type="button" class="form-control btn btn-primary" value="列表" @click="getData()"/>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
		echo Html::formEnd();
		?>
		<div v-if="data.length">
			<ul class="pager responsive" v-if="Math.ceil(count/ pageRows) > 1">
				<li><a href="#" @click="page = 0;getData()">第一頁</a></li>
				<li><a href="#" v-show="page > 0" @click="--page;getData()">上一頁</a></li>
				<li><a href="#" v-show="page < (Math.ceil(count/ pageRows)-1)" @click="++page;getData()">下一頁</a></li>
				<li><a href="#" @click="page = Math.ceil(count/ pageRows)-1;getData()">最後一頁</a></li>
				<li><a href="javascript:void(0)">第{{page+1}}頁</a></li>
				<li><a href="javascript:void(0)">(共{{Math.ceil(count/ pageRows)}}頁，{{count}}筆資料)</a></li>
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

				<tr v-for="(communication, index) in data" :style="{backgroundColor: index%2==0? '#dddddd': '#ffbbbb'}">
					<td>{{page * pageRows + index+1}}</td>
					<td><span style="display: none">'</span>{{communication.OrgCalledId}}</td>
					<td>{{communication.CalledCalloutDate}}</td>
					<td>{{communication.CallStartBillingDateTime}}</td>
					<td>{{communication.CallLeaveDateTime}}</td>
					<td>{{communication.CallDuration}}</td>
				</tr>
				<tr>
					<td colspan="6">總時間：{{totalTime}}</td>
				</tr>
				<tr>
					<td colspan="6">總費用：{{totalMoney}}</td>
				</tr>
				<tr>
					<td colspan="6">總通數：{{count}}</td>
				</tr>
				<tr>
					<td colspan="6">總接通數：{{totalConnected}}</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	var vm = new Vue({
		el: "#container",
		data: {
			StartDate: new Date().AddDay(-1).Format("Y/m/d"),
			StartTime: new Date().Format("H:i:s"),
			StopDate: new Date().AddDay(1).Format("Y/m/d"),
			StopTime: new Date().Format("H:i:s"),
			PlanName: "",
			CallBill: 1,
			SearchSec: 0,
			RecvDTMF: '',
			data: [],
			allData: [],
			page: 0,
			pageRows: 30,
			count: 0,
			totalTime: 0,
			totalConnected: 0
		},
		methods: {
			getData: function () {
//				this.StartDate = this.$refs.StartDate.value
//				this.StartTime = this.$refs.StartTime.value
//				this.StopDate = this.$refs.StopDate.value
//				this.StopTime = this.$refs.StopTime.value
				$.post(ctrl_uri + "ajaxAdCommunicationSearch2", {
					CalledCalloutDateStart: this.CalledCalloutDateStart,
					CalledCalloutDateStop: this.CalledCalloutDateStop,
					PlanName: this.PlanName,
					CallBill: this.CallBill,
					SearchSec: this.SearchSec,
					RecvDTMF: this.RecvDTMF,
					offset: this.page * this.pageRows + 1,
					limit: this.pageRows
				}, function (result) {
					vm.count = result.count
					vm.data = result.data
					vm.totalTime = result.totalTime
					vm.totalMoney = result.totalMoney
					vm.totalConnected = result.totalConnected
				}, "json")
			},
			exportXls: function () {
//				this.StartDate = this.$refs.StartDate.value
//				this.StartTime = this.$refs.StartTime.value
//				this.StopDate = this.$refs.StopDate.value
//				this.StopTime = this.$refs.StopTime.value
//                $('form').attr('action',ctrl_uri + "downloadAdCommunicationSearch").submit()
				var uri = encodeURI(ctrl_uri + "downloadAdCommunicationSearch?" +
					"CalledCalloutDateStart=" + this.CalledCalloutDateStart +
					"&CalledCalloutDateStop=" + this.CalledCalloutDateStop +
					"&PlanName=" + this.PlanName +
					"&CallBill=" + this.CallBill +
					"&SearchSec=" + this.SearchSec +
					"&RecvDTMF=" + this.RecvDTMF +
					"&totalTime=" + this.totalTime +
					"&count=" + this.count +
					"&totalConnected=" + this.totalConnected)

				window.open(uri)
			}
		},
		computed: {
			CalledCalloutDateStart: function () {
				return this.StartDate + " " + this.StartTime
			},
			CalledCalloutDateStop: function () {
				return this.StopDate + " " + this.StopTime
			}
		},
//		mounted: function () {
//			$(this.$refs.StartDate, this.$refs.StartTime, this.$refs.StopDate, this.$refs.StopTime).on('change', function () {
//				setTimeout(function () {
//					this.StartDate = this.$refs.StartDate.value
//					this.StartTime = this.$refs.StartTime.value
//					this.StopDate = this.$refs.StopDate.value
//					this.StopTime = this.$refs.StopTime.value
//				}.bind(this), 50)
//			}.bind(this))
//		}
	})


</script>
<?php
$this->partialView($bottom_view_path);
?>
