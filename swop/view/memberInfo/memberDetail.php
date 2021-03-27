<?php
Bundle::addLink("alertify");
Bundle::addLink("vue2");
Bundle::addLink("vue-component");
Bundle::addLink("lodash");

//echo "<pre>";print_r($this);
$this->partialView($top_view_path);

$choice_id = $model->session["choice"];

if(isset($model->ID)){
    $btn_name = "更新";
    $id = "update_btn";
}else{
    $btn_name = "新增";
    $id = "post_btn";
}
$user = empty($model->data)? []: $model->data;
$subData = empty($model->subData)? []: $model->subData;

?>
    <h3 id="title"><?php echo $this->menu->currentName;?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

    <?php
    echo Html::form();
    ?>
    <table class="table table-v" id="app">
        <tbody>
        <tr>
            <td>新增者</td>
            <td>
                <label><?php echo $user["UserID"]?></label>
            </td>
        </tr>
        <tr>
            <td>用戶名稱</td>
            <td>
                <div class="input-group">
                    <div class="input-group-addon">姓</div>
                    <input type="text" name="LastName" class="form-control" value="<?php echo $user["LastName"]?>">
                    <div class="input-group-addon">名</div>
                    <input type="text" name="FirstName" class="form-control" value="<?php echo $user["FirstName"]?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>用戶電話</td>
            <td>
                <div class="input-group">
                    <div class="input-group-addon">手機</div>
                    <input type="text" name="Cellphone" class="form-control" value="<?php echo $user["Cellphone"]?>">
                    <div class="input-group-addon">市話</div>
                    <input type="text" name="Telephone" class="form-control" value="<?php echo $user["Telephone"]?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>地址</td>
            <td>
                <div class="input-group">
                    <div class="input-group-addon">縣市</div>
                    <input type="text" name="City" class="form-control" value="<?php echo $user["City"]?>">
                    <div class="input-group-addon">街道</div>
                    <input type="text" name="Address" class="form-control" value="<?php echo $user["Address"]?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                自訂欄位
                <a class="pointer" @click="addSubData">
                    <span class="glyphicon glyphicon-plus"></span>
                </a>
            </td>
            <td>
                <div class="input-group" v-for="(data, index) in subData">
                    <div class="input-group-addon">
                        <a class="pointer" @click="delSubData(index)">
                            <span class="glyphicon glyphicon-minus"></span>
                        </a>
                    </div>
                    <div class="input-group-addon">欄位{{ index + 1 }}</div>
                    <input type="text" name="CustomName[]" class="form-control" v-model="data.Name">
                    <div class="input-group-addon">值</div>
                    <input type="text" name="CustomValue[]" class="form-control"  v-model="data.Value">
                </div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input id="<?php echo $id?>" class="btn btn-primary form-control" type="submit" value="<?php echo $btn_name?>">
            </td>
        </tr>
        </tbody>
    </table>
    <?php
    echo Html::formEnd();
    ?>
    <script>
        var vm = new Vue({
            el: '#app',
            data: {
                subData: <?php echo json_encode($subData)?>
            },
            methods: {
                delSubData: function (i) {
                    this.subData.splice(i, 1)
                },
                addSubData: function (i) {
                    this.subData.push({ Name:'', Value:'' })
                }
            }
        })
    </script>

<?php
$this->partialView($bottom_view_path);
?>