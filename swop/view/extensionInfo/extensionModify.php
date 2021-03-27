<?php
Bundle::addLink("datetime");
//echo "<pre>";print_r($this);
$this->partialView($top_view_path);
$data = $model->data;

$choice_id = $model->session["choice"];
$choice_root_display = $choice_id!="root"?" style='display:none' ":"";
?>
    <h3 id="title"><?php echo $this->menu->currentName;?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

    <div class="table-responsive">
    <?php
    echo Html::form();
    ?>
        <input type="hidden" name="search_userID" value="<?php echo $model->search_userID?>"/>
        <input type="hidden" name="search_content" value="<?php echo $model->search_content?>"/>
    <table class="table table-v">
    <tbody><tr>
        <td class="col-md-3">用戶:</td>
        <td class="col-md-9">
            <span><?php echo $model->userId ?></span>
        </td>
    </tr>
    <tr>
        <td>分機號碼:</td>
        <td>
            <span><?php echo $model->extensionNo ?></span>
        </td>
    </tr>
    <tr>
        <td>分機名稱:</td>
        <td>
            <input type="text" name="extName" class="form-control" value="<?php echo $data["ExtName"]?>">
        </td>
    </tr>
    <?php if($choice_id == "root"){ ?>
    <tr>
        <td>手動撥號主叫:</td>
        <td>
            <input type="text" name="offNetCli" class="form-control" value="<?php echo $data["OffNetCli"]?>">
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td>註冊密碼:</td>
        <td>
            <input type="text" name="customerPwd" class="form-control" value="<?php echo $data["CustomerPwd"]?>">
        </td>
    </tr>
    <tr>
        <td>錄音:</td>
        <td>
            <input id='startRecorder' name='startRecorder' class='bootstrap-switch' type='checkbox' value="1"
                <?php echo ($data["StartRecorder"]?"checked":"") ?>
                   data-size='mini' data-off-color='danger'>
        </td>
    </tr>
    <tr>
        <td>狀態:</td>
        <td>
            <?php //echo $data["Suspend"]?>
            <input id='suspend' name='suspend' class='bootstrap-switch' type='checkbox' value="0"
                <?php echo ($data["Suspend"]==0?"checked":"") ?>
                   data-size='mini' data-off-color='danger'>
        </td>
    </tr>

    <tr <?php echo $choice_root_display?>>
        <td>是否啟用:</td>
        <td>
            <input id='useState' name='useState' class='bootstrap-switch' type='checkbox' value="1"
                <?php echo ($data["UseState"]?"checked":"") ?>
                   data-size='mini' data-off-color='danger'>
        </td>
    </tr>

    <tr>
        <td>座席:</td>
        <td>
            <?php echo Html::selector($model->calloutGroupIdSelect) ?>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <input class="btn btn-primary form-control update_btn" type="button" value="更新">
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