<?php
Bundle::addLink("validate");
$this->partialView($top_view_path);

?>
    <script>
        $(document).ready(function(){
            $("form").validate({
                rules:{
                    prefixCode: {
                        required: true
                    }
                },
                messages: {
                    prefixCode: "這欄位必填"

                }
            });
        });
    </script>
    <h3 id="title">(<?php echo " ".$model->rateGroupId." <=> ".$model->rateGroupName." ";?>)用戶費率表更改</h3>
    <!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

    <div class="table-responsive">
        <?php
        echo Html::form();
        ?>
        <table class="table table-v table-condensed">
            <tbody>
            <tr>

                <td>時間1:</td>
                <td>
                    <input type="text" name="time1" class="form-control" value="0">
                </td>
                <td>費用1:</td>
                <td>
                    <input type="text" name="rateValue1" class="form-control" value="0.0001">
                </td>
                <td>成本1:</td>
                <td>
                    <input type="text" name="rateCost1" class="form-control" value="0">
                </td>

            </tr>
            <tr>

                <td>時間2:</td>
                <td>
                    <input type="text" name="time2" class="form-control" value="0">
                </td>
                <td>費用2:</td>
                <td>
                    <input type="text" name="rateValue2" class="form-control" value="0.0001">
                </td>
                <td>成本2:</td>
                <td>
                    <input type="text" name="rateCost1" class="form-control" value="0">
                </td>

            </tr>
            <tr>
                <td>前置碼:</td>
                <td>
                    <input type="text" name="prefixCode" class="form-control">
                </td>
                <td>刪除幾碼:</td>
                <td>
                    <?php echo Html::selector($model->subNumSelect);?>
                </td>
                <td>新增前置碼:</td>
                <td>
                    <input type="text" name="addPrefix" class="form-control">
                </td>
            </tr>
            <tr>
                <td></td>
                <td colspan="5">
                    <input type="submit" class="btn btn-primary form-control post_btn" value="新增">
                </td>
            </tr>
            </tbody>
        </table>
        <input type="button" class="btn btn-danger delete_btn" value="Delete">
        <input type="button" class="btn btn-danger delete_all_btn" value="Delete All">
        <table class="table table-h table-striped table-hover">
            <tbody>
            <tr>
                <th>
                    <input type='checkbox' class="checkAll" for="delete[]"/>
                </th>
                <th>前置碼</th>
                <th>時間1</th>
                <th>費用1</th>
                <th>時間2</th>
                <th>費用2</th>
                <th>成本1</th>
                <th>成本2</th>
                <th>刪除幾碼</th>
                <th>新增前置碼</th>
            </tr>
            <?php
            foreach($model->rateDetail as $detail)
            {
                $prefixCode = $detail["PrefixCode"];
                echo "<tr data-update>";
                echo "<td><input type='checkbox' name='delete[]' value='{$prefixCode}'/></td>";
                echo "<td data-bind='prefixCode'>{$prefixCode}</td>";
                echo "<td data-bind='time1'>".$detail["Time1"]."</td>";
                echo "<td data-bind='rateValue1'>".$detail["RateValue1"]."</td>";
                echo "<td data-bind='time2'>".$detail["Time2"]."</td>";
                echo "<td data-bind='rateValue2'>".$detail["RateValue2"]."</td>";
                echo "<td data-bind='rateCost1'>".$detail["RateCost1"]."</td>";
                echo "<td data-bind='rateCost2'>".$detail["RateCost2"]."</td>";
                echo "<td data-bind='subNum'>".$detail["SubNum"]."</td>";
                echo "<td data-bind='addPrefix'>".$detail["AddPrefix"]."</td>";
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