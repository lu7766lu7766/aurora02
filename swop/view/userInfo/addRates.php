<?php
//echo "<pre>";print_r($this);
$this->partialView($top_view_path);
?>
    <h3 id="title"><?php echo $this->menu->currentName?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

    <div class="table-responsive">
    <?php
    echo Html::form();
    ?>
        <table class="table table-v">
            <tbody>
            <tr>
                <td class="col-md-3">費率表代號</td>
                <td class="col-md-9">
                    <div class="input-group">
                        <input type="text" name="rateGroupId" class="form-control">
                        <span class="input-group-addon">( 1 ~ 99999 )</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>費率表名稱</td>
                <td>
                    <input type="text" name="rateGroupName" class="form-control">
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
    </div>


<?php
$this->partialView($bottom_view_path);
?>