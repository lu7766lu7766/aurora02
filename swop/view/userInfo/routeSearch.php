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
                <td class="col-md-2">被叫號:</td>
                <td class="col-md-3">
                    <input type="text" name="extensionNo" class="form-control num-only" value="<?php echo $model->extensionNo?>">
                </td>
                <td class="non col-md-1">
                    <input type="button" class="form-control btn btn-primary get_btn" value="列表"/>
                </td>
            </tr>
            </tbody>
        </table>
        <?php if($model->data)
        { ?>
            <table class="table table-h table-striped table-hover">
                <tbody>
                <tr>
                    <th>費率前置碼</th>
                    <th>路由前置碼</th>
                    <th>顯示號碼</th>
                    <th>TrunkIP</th>
                    <th>實際被叫號</th>
                </tr>
                <?php

                echo "<td>".$model->prefixCode."</td>";
                echo "<td>".$model->routeAddPrefix."</td>";
                echo "<td>".$model->mainCall."</td>";
                echo "<td>".$model->trunkIp."</td>";
                echo "<td>".$model->lastCalled."</td>";
                echo "</tr>";
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