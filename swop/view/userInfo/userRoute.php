<?php
Bundle::addLink("validate");
$this->partialView($top_view_path);

?>
<script>
    $(document).ready(function(){
        $("form").validate({
            rules:{
                trunkPort: {
                    required: true,
                    range: [1, 65535]
                }
            },
            messages: {
                trunkPort: {
                    required: "這欄位必填",
                    range: "請輸入介於1~65535中間的值"
                }
            }
        });
    });
</script>

    <h3 id="title"><?php echo $this->menu->currentName?></h3>
    <!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

    <div class="table-responsive">
        <?php
        echo Html::form();
        ?>
        <table class="table table-v table-condensed">
            <tbody>
            <tr>
                <td>用戶:</td>
                <td>
                    <?php echo Html::selector($model->empSelect2);?>
                </td>
                <td>顯示號碼:</td>
                <td>
                    <input type="text" name="routeCLI" class="form-control">
                </td>
                <td>Trunk IP:</td>
                <td>
                    <input type="text" name="trunkIp" class="form-control">
                </td>
            </tr>
            <tr>
                <td>前置碼:</td>
                <td>
                    <input type="text" name="prefixCode" class="form-control">
                </td>
                <td>新增前置碼:</td>
                <td>
                    <input type="text" name="addPrefix" class="form-control">
                </td>
                <td>Trunk port:</td>
                <td>
                    <input type="text" name="trunkPort" class="form-control" value="5060" placeholder="1~65535" >
                </td>

            </tr>
            <tr>
                <td>路由名稱:</td>
                <td>
                    <input type="text" name="routeName" class="form-control">
                </td>
                <td></td>
                <td colspan="3">
                    <input type="submit" class="form-control btn btn-primary" name="postUserRoute" value="新增"/>
                </td>
            </tr>
            </tbody>
        </table>
        <input type="button" class="btn btn-danger delete_btn" value="Delete">
        <table class="table table-h table-striped table-hover">
            <tbody>
            <tr>
                <th>
                    <input type='checkbox' class="checkAll" for="delete[]"/>
                </th>
                <th>編號</th>
                <th>用戶</th>
                <th>前置碼</th>
                <th>新增前置碼</th>
                <th>顯示號碼</th>
                <th>Trunk IP</th>
                <th>Trunk Port</th>
                <th>路由名稱</th>
            </tr>
            <?php
            $i=0;
            foreach($model->userRoute as $data)
            {
                $userId = $data["UserID"];
                $prefixCode = $data["PrefixCode"];
                $i++;
                echo "<tr class='delete'>";
                echo "<td><input type='checkbox' name='delete[]' value='{$userId},{$prefixCode}'/></td>";
                echo "<td>{$i}</td>";
                echo "<td>{$userId}</td>";
                echo "<td>{$prefixCode}</td>";
                echo "<td>".$data["AddPrefix"]."</td>";
                echo "<td>".$data["RouteCLI"]."</td>";
                echo "<td>".$data["TrunkIP"]."</td>";
                echo "<td>".$data["TrunkPort"]."</td>";
                echo "<td>".$data["RouteName"]."</td>";
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
        <?php
        echo Html::formEnd();
        ?>
    </div>
<?php
$this->partialView($bottom_view_path);
?>