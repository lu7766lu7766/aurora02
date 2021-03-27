<?php
//echo "<pre>";print_r($this);
$this->partialView($top_view_path);
$choice_id = $model->session["choice"];
?>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

<div class="table-responsive">
    <?php
    echo Html::form();
    ?>
    <input type="button" class="btn btn-danger delete_btn" value="Delete">
    <a href="javascript:redirect('userInfo/userAdd')" class="btn btn-primary" style="color:white">新增
    </a>
    <table class="table table-h table-pointer table-striped table-hover">
        <tbody>
        <tr>
            <th>
                <input type='checkbox' class="checkAll" for="delete[]"/>
            </th>
            <th>登入帳號(編輯)</th>
            <th>狀態</th>
            <th>經銷商</th>
            <th>分機數</th>
            <th>費率</th>
            <th>剩餘點數</th>
            <th>用戶名稱</th>
            <th>用戶備註</th>
        </tr>
        <?php
        foreach ($model->sysUser as $user) {
            $userId = $user["UserID"];
            echo "<tr redirect='userInfo/userModify?userId={$userId}'>";
            echo "<td>";
            if ($choice_id != $userId) {
                echo "<input type='checkbox' name='delete[]' value='{$userId}'/>";
            }
            echo "</td>";
            echo "<td>{$userId}</td>";
            //echo "<td><input id='switch' class='bootstrap-switch' readonly type='checkbox' ".($user["UseState"]?"checked":"")." data-size='mini' data-off-color='danger'></td>";
            echo "<td><label class='switch'><input id='switch' disabled type='checkbox' " .
                ($user["UseState"] ? "checked" : "") .
                "><div class='slider round'></div></label></td>";
            echo "<td>{$user["Distributor"]}</td>";
            echo "<td>{$user["ExtensionCount"]}</td>";
            echo "<td>" . $user["RateGroupID"] . "</td>";
            echo "<td class='balance'>" . (floor($user["Balance"] * 100) / 100) . "</td>";
            echo "<td>" . $user["UserName"] . "</td>";
            echo "<td>" . $user["NoteText"] . "</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
    <?php
    echo Html::formEnd();
    ?>
</div>
<script>
    var model = <?php echo json_encode($model->sysUser)?>;
    var users = [];
    model.forEach(function (x) {
        users.push(x.UserID);
    })
    var timer = setInterval(function () {
        $.getJSON(folder + "userInfo/getBalance/users/" + users.join(","), function (data) {
            $.each($(".balance"), function (i) {
                this.innerHTML = Number(data[i]).toFixed(2)//Math.floor(data[i]*100)/100;
            })
        })
    }, 10000);

</script>

<?php
$this->partialView($bottom_view_path);
?>
