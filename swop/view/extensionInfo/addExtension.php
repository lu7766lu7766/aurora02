<?php
$this->partialView($top_view_path);
?>
    <h3 id="title"><?php echo $this->menu->currentName?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

    <?php
    echo Html::form();
    ?>
        <table class="table table-v">
            <tbody>
            <tr>
                <td class="col-md-3 col-xs-5">用戶</td>
                <td class="col-md-9 col-xs-7">
                    <?php echo Html::selector($model->empSelect2);?>
                </td>
            </tr>
            <tr>
                <td>分機號碼</td>
                <td>
                    <div class="form-inline">
                        <input type="text" name="extensionNo" class="form-control">
                        <span>～</span>
                        <input type="text" name="extensionNos" class="form-control">
                    </div>
                </td>
            </tr>
            <tr>
                <td>分機名稱</td>
                <td>
                    <input type="text" name="extName" class="form-control">
                </td>
            </tr>
            <tr>
                <td>手動撥號主叫</td>
                <td>
                    <input type="text" name="offNetCli" class="form-control">
                </td>
            </tr>
            <tr>
                <td>註冊密碼</td>
                <td>
                    <input type="text" name="customerPwd" class="form-control num-only" value="<?php echo $model->tmpPwd?>">
                </td>
            </tr>
            <tr>
                <td>座席</td>
                <td>
                    <select name="calloutGroupId" class="form-control">
                        <option></option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input id="post_btn" class="btn btn-primary form-control" type="submit" value="新增">
                </td>
            </tr>
            </tbody>
        </table>
    <?php
    echo Html::formEnd();
    ?>


<?php
$this->partialView($bottom_view_path);
?>