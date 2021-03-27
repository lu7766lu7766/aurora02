<?php
Bundle::addLink("datetime");
$this->partialView($top_view_path);
?>
    <h3 id="title"><?php echo $this->menu->currentName?></h3>
    <!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

    <div class="table-responsive">
        <?php
        echo Html::form();
        ?>
        <table class="table table-v table-condensed">
            <tbody>
            <tr>
                <td class="col-md-2">用戶:</td>
                <td class="col-md-4">
                    <?php echo Html::selector($model->empSelect2);?>
                </td>
                <td class="col-md-2">座席代號:</td>
                <td class="col-md-3">
                    <input type="text" name="calloutGroupId" class="form-control num-only" value="<?php echo $model->calloutGroupId=="*"?"":$model->calloutGroupId?>">
                </td>
                <td class="non col-md-1">
                    <input type="button" class="form-control btn btn-primary get_btn" value="列表"/>
                </td>
            </tr>
            </tbody>
        </table>
            <table class="table table-h table-striped table-hover">
                <tbody>
                <tr>
                    <th>編號</th>
                    <th>用戶</th>
                    <th>座席代號</th>
                    <th>分機數</th>
                </tr>
                <?php
                if(is_array($model->data))
                {
                    $i = 1;
                    foreach($model->data as $data)
                    {
                        $userId = $data["UserID"];
                        $calloutGroupId = $data["CalloutGroupID"];
                        //redirect='extensionInfo/seatTacticsModify/userId/{$userId}/calloutGroupId/{$calloutGroupId}
                        echo "<tr>";
                        echo "<td>".$i++."</td>";
                        echo "<td>".$data["UserID"]."</td>";
                        echo "<td>".$data["CalloutGroupID"]."</td>";
                        echo "<td>".$data["ExtensionRows"]."</td>";
                        echo "</tr>";
                    }
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