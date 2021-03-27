<?php
Bundle::addLink("datetime");
$this->partialView($top_view_path);
?>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->
<script>
    $(document).ready(function () {
        $("input[download]").click(function () {
            window.open(folder + "communicationHistory/communicationSearchDownload");
        })
        $("input[name='extensionNo']").keyup(function (e) {
            e.preventDefault();
            if (e.keyCode == 83) {
                this.value = "system";
            }
        })
    })
</script>
<div class="table-responsive">
    <?php
    echo Html::form();
    echo Html::pageInput($model->page, $model->last_page, $model->per_page);
    ?>

    <table class="table table-v table-condensed">
        <tbody>
        <tr>
            <td class="col-md-1">用戶</td>
            <td class="col-md-3">
                <?php echo Html::selector($model->empSelect2); ?>
            </td>
            <td class="col-md-1">分機號碼</td>
            <td class="col-md-3">
                <input type="text" name="extensionNo" class="form-control num-only rigorous"
                       value="<?php echo $model->extensionNo ?>">
            </td>
            <td class="col-md-1">目的端號碼</td>
            <td class="col-md-3">
                <input type="text" name="orgCalledId" class="form-control num-only rigorous"
                       value="<?php echo $model->orgCalledId ?>">
            </td>
        </tr>
        <tr>
            <td>開始時間</td>
            <td>
                <div class="input-group">
                    <input type="text" name="callStartBillingDate" class="form-control date-picker today-date"
                           value="<?php echo $model->callStartBillingDate ?>">
                    <span class="input-group-addon"> </span>
                    <input type="text" name="callStartBillingTime" class="form-control time-picker"
                           value="<?php echo $model->callStartBillingTime ?? "00:00:00" ?>">
                </div>
            </td>
            <td>結束時間</td>
            <td>
                <div class="input-group">
                    <input type="text" name="callStopBillingDate" class="form-control date-picker tomorrow-date"
                           value="<?php echo $model->callStopBillingDate ?>">
                    <span class="input-group-addon"> </span>
                    <input type="text" name="callStopBillingTime" class="form-control time-picker"
                           value="<?php echo $model->callStopBillingTime ?? "00:00:00" ?>">
                </div>
            </td>
            <td>等級</td>
            <td>
                <input type="text" name="customerLevel" class="form-control"
                       value="<?php echo $model->customerLevel ?>">
            </td>
        </tr>
        <tr>
            <td>秒數<span style="font-size: 8pt">(以上)</span></td>
            <td>
                <input type="text" name="searchSec" class="form-control" value="<?php echo $model->searchSec ?>">
            </td>
            <td>撥號類型</td>
            <td>
                <?php echo Html::selector($model->callTypeSelect); ?>
            </td>
            <td></td>
            <td>
                <input type="button" class="form-control btn btn-primary get_btn" value="列表"/>
            </td>
        </tr>
        </tbody>
    </table>

    <?php if (is_array($model->data)) { ?>
        <ul class="pager responsive">
            <li class="first-page"><a href="#">第一頁</a></li>
            <li class="prev-page"><a href="#">上一頁</a></li>
            <li class="next-page"><a href="#">下一頁</a></li>
            <li class="last-page"><a href="#">最後一頁</a></li>
            <li><a href="#">第<?php echo Html::selector($model->pageSelect) ?>頁</a></li>
            <li><a href="#">(共<?php echo $model->last_page ?>頁，<?php echo $model->rows ?>筆資料)</a></li>
        </ul>
        <?php /*<input type="button" class="btn btn-danger delete_btn" value="Delete">*/
        ?>
        <input type="button" class="btn btn-default" download value="下載"><!-- -->
        總計：<?php echo count($model->data) ?>筆
        <table class="table table-h table-striped table-hover">
            <tbody>
            <tr>
                <?php /*<th>
                        <input type='checkbox' class="checkAll" for="delete[]"/>
                    </th>*/
                ?>
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
            <?php
            $totalTime = 0;
            $totalMoney = 0;
            $i = $model->page * $model->per_page + 1;
            foreach ($model->data as $data) {
                $logId = $data["LogID"];
                $recordFilePath = empty($data["RecordFile"]) ? "#" :
                    getApiUrl(
                        "main/recordFile/userId/" .
                        $data["UserID"] . "/connectDate/" .
                        date("Ymd", strtotime($data["CallStartBillingDate"])) .
                        "/fileName/" .
                        $data["RecordFile"]);
                $hrefAttr = empty($data["RecordFile"]) ? "" : "href='$recordFilePath'";
                $targetAttr = empty($data["RecordFile"]) ? "" : "target='_blank'";
                $btnClass = empty($data["RecordFile"]) ? "label-default" : "label-info";
                $totalTime += $data["CallDuration"];
                $totalMoney += $data["BillValue"];
                $callType = $data["CallType"];
                $background = $callType == 0 ? '#dddddd' : '#ffbbbb';
                echo "<tr style='background-color: {$background}'>";
                //echo "<td><input type='checkbox' name='delete[]' value='{$logId}'/></td>";
                echo "<td>" . ($i++) . "</td>";
                echo "<td>" . $data["UserID"] . "</td>";
                echo "<td>" . $data["ExtensionNo"] . "</td>";
                echo "<td>" . $data["OrgCalledId"] . "</td>";
                echo "<td>" . $data["CallStartBillingDate"] . " " . $data["CallStartBillingTime"] . "</td>";
                echo "<td>" . $data["CallDuration"] . "</td>";
                echo "<td>" . $data["BillValue"] . "</td>";
                echo "<td>" . $data["CustomerLevel"] . "</td>";
                echo "<td><a $hrefAttr $targetAttr class='label $btnClass'/>下載</a></td>";
                echo "</tr>";
            }
            ?>
            <tr>
                <td colspan="2">合計</td>
                <!--                    <td></td>-->
                <td></td>
                <td></td>
                <td></td>
                <td><?php echo $totalTime ?></td>
                <td><?php echo $totalMoney ?></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">總合計</td>
                <!--                    <td></td>-->
                <td></td>
                <td></td>
                <td></td>
                <td><?php echo $model->totalTime ?></td>
                <td><?php echo $model->totalMoney ?></td>
                <td></td>
                <td></td>
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
