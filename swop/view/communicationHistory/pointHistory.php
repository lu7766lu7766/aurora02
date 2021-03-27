<?php
Bundle::addLink("datetime");
Bundle::addLink("alertify");
$this->partialView($top_view_path);
?>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->
<script>
    $(document).ready(function () {

        if (document.getElementsByName("startDate")[0].value == "") {
            var today = new Date().Format("Y-m-d"), a_today = today.split("-");
            today = a_today[0] + "-" + a_today[1] + "-" + "01";
            document.getElementsByName("startDate")[0].value = today;
        }

        $(".memo").on('change', function (e) {
            $.post(ctrl_uri + "ajaxEditRechargeLogMemo", {
                id: e.target.id,
                value: e.target.value
            }, function (res) {
                if (res.code == 0) {
                    alertify.alert("更新成功");
                } else {
                    alertify.alert("更新失敗，請重新輸入");
                }
            }.bind(this), 'json');
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
            <td class="col-md-2">開始日期:</td>
            <td class="col-md-2">
                <input type="text" name="startDate" class="form-control date-picker"
                       value="<?php echo $model->startDate ?>">
            </td>
            <td class="col-md-2">結束日期:</td>
            <td class="col-md-2">
                <input type="text" name="endDate" class="form-control date-picker today-date"
                       value="<?php echo $model->endDate ?>">
            </td>
            <td class="col-md-2">備註:</td>
            <td class="col-md-2">
                <input type="text" name="memo" class="form-control" value="<?php echo $model->memo ?>">
            </td>
        </tr>
        <tr>
            <td>用戶:</td>
            <td>
                <?php echo Html::selector($model->empSelect2); ?>
            </td>
            <td></td>
            <td colspan="3">
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

        <table class="table table-h table-striped table-hover">
            <tbody>
            <tr>
                <th>編號</th>
                <th>儲值者</th>
                <th>儲值對象</th>
                <th>儲值金額</th>
                <th>儲值時間</th>
                <th>備註</th>
            </tr>
            <?php
            $i = 1;
            foreach ($model->data as $data) {
                echo "<tr>";
                echo "<td>" . $i++ . "</td>";
                echo "<td>" . $data["SaveUserID"] . "</td>";
                echo "<td>" . $data["UserID"] . "</td>";
                echo "<td>" . $data["AddValue"] . "</td>";
                echo "<td>" . $data["AddTime"] . "</td>";
                echo "<td>";
                if ($model->session["choice"] == "root") {
                    echo "<input type='text' class='form-control memo' id='{$data['LogID']}' value='{$data['Memo']}' />";
                } else {
                    echo $data['Memo'];
                }
                echo "</td>";
                echo "</tr>";
            }
            ?>
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
