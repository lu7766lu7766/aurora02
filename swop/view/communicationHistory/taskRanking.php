<?php
Bundle::addLink("datetime");
$this->partialView($top_view_path);

$choice_id = $model->session[ "choice" ];
?>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->
<script>
	$(document).ready(function () {

		if (document.getElementsByName("callStartBillingDate")[0].value == "") {
			var today = new Date().Format("Y-m-d"), a_today = today.split("-");
			a_today[1] = a_today[2] == "01" ? parseInt(a_today[1]) - 1 : a_today[1];
			today = a_today[0] + "-" + a_today[1].toString().padLeft("0", 2) + "-" + "01";
			document.getElementsByName("callStartBillingDate")[0].value = today;
		}
	})
</script>
<div class="table-responsive">
	<?php
	echo Html::form();
	?>
	<table class="table table-v table-condensed">
		<tbody>
		<tr>
			<td class="col-md-2">用戶:</td>
			<td class="col-md-2">
				<?php echo Html::selector($model->empSelect2); ?>
			</td>
			<td class="col-md-2">開始日期:</td>
			<td class="col-md-2">
				<input type="text" name="callStartBillingDate" class="form-control date-picker"
				       value="<?php echo $model->callStartBillingDate ?>">
			</td>
			<td class="col-md-2">結束日期:</td>
			<td class="col-md-2">
				<input type="text" name="callStopBillingDate" class="form-control date-picker yesterday-date"
				       value="<?php echo $model->callStopBillingDate ?>">
			</td>
		</tr>
		<tr>
			<?php if ($choice_id == "root") { ?>
				<td>顯示模式</td>
				<td>
					<select name="display_mode" class="form-control">
						<option value="0" <?php echo $model->display_mode == "0" ? "selected" : "" ?>>分機排序</option>
						<option value="1" <?php echo $model->display_mode == "1" ? "selected" : "" ?>>用戶排序</option>
					</select>
				</td>
			<?php } ?>
			<td></td>
			<td colspan="<?php echo $choice_id == "root" ? "3" : "5" ?>">
				<input type="button" class="form-control btn btn-primary get_btn" value="列表"/>
			</td>
		</tr>
		</tbody>
	</table>
	<?php if (is_array($model->data)) { ?>
		<table class="table table-h table-striped table-hover">
			<tbody>
			<tr>
				<th>編號</th>
				<th>帳號</th>
				<?php if ($model->display_mode == "0") { ?>
					<th>分機號</th>
				<?php } ?>
				<th>用戶名稱</th>
				<th>時間</th>
				<th>通數</th>
				<th>費用</th>
				<?php if ($choice_id == "root") { ?>
					<th>成本</th>
				<?php } ?>
			</tr>
			<?php
			$totalTime = 0;
			$totalCount = 0;
			$totalMoney = 0;
			$totalCost = 0;
			foreach ($model->data as $i => $data) {
				$extensionNo = !empty( $data[ "ExtensionNo" ] ) ? $data[ "ExtensionNo" ] : "Wait";
				$totalTime += $data[ "CallDuration" ];
				$totalCount += $data[ "Count" ];
				$totalMoney += $data[ "BillValue" ];
				$totalCost += $data[ "BillCost" ];
				echo "<tr>";
				echo "<td>" . ( $i + 1 ) . "</td>";
				echo "<td>" . $data[ "UserID" ] . "</td>";
				if ($model->display_mode == "0") {
					echo "<td>" . $extensionNo . "</td>";
				}
				echo "<td>" . $data[ "UserName" ] . "</td>";
				echo "<td>" . $data[ "CallDuration" ] . "</td>";
				echo "<td>" . $data[ "Count" ] . "</td>";
				echo "<td>" . $data[ "BillValue" ] . "</td>";
				if ($choice_id == "root") {
					echo "<td>" . $data[ "BillCost" ] . "</td>";
				}
				echo "</tr>";
			}
			?>
			<tr>
				<td colspan="2">合計</td>
				<td></td>
				<?php if ($model->display_mode == "0") { ?>
					<td></td>
				<?php } ?>
				<td><?php echo $totalTime ?></td>
				<td><?php echo $totalCount ?></td>
				<td><?php echo $totalMoney ?></td>
				<?php if ($choice_id == "root") { ?>
					<td><?php echo $totalCost; ?></td>
				<?php } ?>
			</tr>
			</tbody>
		</table>
		<?php
	}
	echo Html::formEnd();
	?>
</div>
<?php
$this->partialView($bottom_view_path);
?>
