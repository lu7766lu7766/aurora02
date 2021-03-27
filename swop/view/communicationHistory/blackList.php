<?php
Bundle::addLink("vue");
$this->partialView($top_view_path);
$choice_id = $model->session["choice"];
?>
    <h3 id="title"><?php echo $this->menu->currentName?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->
<?php
echo Html::form();
echo Html::pageInput($model->page,$model->last_page,$model->per_page);
?>
<script>
    function file_upload(){
        $('#status').val('file');
        $btn = $("<button></button>").hide();
        $('#file').after($btn);
        $btn.trigger("click");
    }
</script>
<div>
    <input type="file" name="list" />
    <button type="button" id="file" class="btn btn-default" onclick="file_upload()">上傳</button>
    <span class="text-danger" style="font-size:9px">上傳限制一次最多一萬筆</span>
    <div class="form-inline">
        <div class="input-group">
            <input type="text" name="calledNumber" class="form-control" placeholder="輸入多筆請用 , 分開!" v-model="number">
        </div>
        <input id="add" type="submit" value="新增" onclick="$('#status').val('add')" class="btn btn-primary"/>
        <input type="button" value="查詢" @click="checkNumberExists(number)" class="btn btn-info"/>
        <span :style='checkStyle' v-text="checkResult"></span>
    </div>
</div>
<?php
if ($model->result && count($model->result)){
    echo "<span style='color:red'>號碼已存在：<br>".join(", ",$model->result)."</span>";
}
?>
<?php if (count($model->data)) {?>
    <ul class="pager responsive">
        <li class="first-page"><a href="#">第一頁</a></li>
        <li class="prev-page"><a href="#">上一頁</a></li>
        <li class="next-page"><a href="#">下一頁</a></li>
        <li class="last-page"><a href="#">最後一頁</a></li>
        <li><a href="#">第<?php echo Html::selector($model->pageSelect) ?>頁</a></li>
        <li><a href="#">(共<?php echo $model->last_page ?>頁，<?php echo $model->rows ?>筆資料)</a></li>
    </ul>



    <input type="button" class="btn btn-danger delete_btn" value="Delete">
    <a href="<?php echo $base["folder"]?>communicationHistory/downloadBlackList" target="_blank" class="btn btn-default">下載</a>

    <div class="table-responsive">
        <table class="table table-h table-pointer table-striped table-hover">
            <tbody>
            <tr>
                <th>
                    <input type='checkbox' class="checkAll" for="delete[]"/>
                </th>
                <th>編號</th>
                <th>號碼</th>
            </tr>
            <?php
            $i = $model->page*$model->per_page+1;
            foreach ($model->data as $data) {
                $number = $data["CalledNumber"];
                echo "<td><input type='checkbox' name='delete[]' value='{$number}'/></td>";
                echo "<td>".($i++)."</td>";
                echo "<td>" . $number . "</td>";
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
<?php
}else{
    echo "目前查無任何資料！！";
}

echo Html::formEnd();
?>
<script>
    var vm = new Vue({
        el:"#main-content",
        data:{
            number:"",
            is_exists:false,
            checkResult: ""
        },
        methods:{
            checkNumberExists:function(number){
                $.post(folder+"communicationHistory/checkCalledNumber",{
                    number:number
                },function(data){
                    vm.is_exists = data.status > 0
                    vm.checkResult = data.status>0? "此號碼已存在": "此號碼不存在";
                },"json")
            }
        },
        computed:{
            checkStyle: function(){
                return {color: this.is_exists? 'red': 'green'};
            }
        }
    });
</script>
<?php
$this->partialView($bottom_view_path);
?>
