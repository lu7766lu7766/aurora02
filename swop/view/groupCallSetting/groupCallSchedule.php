<?php
//echo "<pre>";print_r($this);
Bundle::addLink("vue2");
Bundle::addLink("alertify");
$this->partialView($top_view_path);

?>
    <h3 id="title"><?php echo $this->menu->currentName?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->
<?php
echo Html::form();
?>
    <div class="col-md-12 form-inline">
        <label class="col-md-2">
            <input type="radio" v-model="add_type" class="radio add_type" name="numberMode" value="0" />區段增加</label>
        <label class="col-md-2">
            <input type="radio" v-model="add_type" class="radio add_type" name="numberMode" value="1" />名單上傳</label>
        <label class="col-md-2">
            <input type="radio" v-model="add_type" class="radio add_type" name="numberMode" value="2" />同號測試</label>
        <label class="col-md-2">
            <input type="radio" v-model="add_type" class="radio add_type" name="numberMode" value="3" />有效號新增</label>
        <div class="col-md-4"></div>
<!--        <input id="post_btn" class="col-md-2 btn btn-primary post_btn" type="button" value="新增">-->
    </div>

    <div class="table-responsive col-md-12">

        <table class="table table-v">

            <tbody>
            <tr>
                <td class="col-md-2">群呼名稱</td>
                <td class="col-md-2">
                    <input type="text" name="planName" class="form-control">
                </td>
                <template v-if="['0', '2', '3'].indexOf(add_type) > -1">
                    <td class="col-md-2 type1" >起始電話</td>
                    <td class="col-md-2 type1" >
                        <input v-model="start_number" type="text" name="startCalledNumber" class="form-control">
                    </td>
                    <td class="col-md-2 type1" >筆數</td>
                    <td class="col-md-2 type1" >
                        <input type="text" name="calledCount" class="form-control">
                    </td>
                </template>
                <template v-if="['1'].indexOf(add_type) > -1">
                    <td class="col-md-2 type2" >上傳名單</td>
                    <td class="col-md-6 type2" colspan="3">
                        <input type="file" name="list" @change="onFileChange($event)"/>
                    </td>
                </template>
            </tr>
            <tr>
<!--                <td>顯示主叫</td>-->
<!--                <td>-->
<!--                    <select name="callerPresent" class="form-control">-->
<!--                        <option value="0">不顯示</option>-->
<!--                        <option value="1">顯示</option>-->
<!--                    </select>-->
<!--                </td>-->
<!--                <td>主叫號</td>-->
<!--                <td>-->
<!--                    <input type="text" name="callerID" class="form-control">-->
<!--                </td>-->
                <td>座席</td>
                <td>
                    <select name="calloutGroupID" class="form-control">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                </td>
                <td>響鈴方式</td>
                <td>
                    <select name="calldistribution" class="form-control">
                        <option value="0">分號少的開始配號</option>
                        <option value="1" selected>自動平均分配</option>
                    </select>
                </td>
                <td>撥出電話等待秒數</td>
                <td>
                    <input type="text" name="callProgressTime" class="form-control" value="30">
                </td>
            </tr>
            <tr>

                <td>轉分機等待秒數</td>
                <td>
                    <input type="text" name="extProgressTime" class="form-control" value="15">
                </td>
                <td>自動撥號速度</td>
                <td>
                    <!--<input type="text" name="concurrentCalls" class="form-control">-->
                    <?php echo Html::selector($model->concurrentCallsSelect)?>
                </td>
                <td></td>
                <td><input id="post_btn" class="form-control btn btn-primary post_btn" type="button" value="新增"></td>

            </tr>
<!--            <tr>-->

<!--                <td>啟用</td>-->
<!--                <td>-->
<!--                    <input type="checkbox" name="useState" value="1" checked class="bootstrap-switch" data-size="mini" data-off-color="danger">-->
<!--                </td>-->
                <!--<td>搜集模式<br>(接通即掛)</td>
                <td>
                    <input type="checkbox" name="collageMode" value="1" class="bootstrap-switch" data-size="mini" data-off-color="danger">
                </td>-->
<!--            </tr>-->
            </tbody>
        </table>

        <input type="button" class="btn btn-danger delete_btn" value="Delete">
        <table class="table table-h table-striped table-hover table-pointer">
            <tbody>
            <tr>
                <th>
                    <input type='checkbox' class="checkAll" for="delete[]"/>
                </th>
                <th>用戶</th>
                <th>群呼名稱</th>
                <th>起始電話</th>
                <th>筆數</th>
                <th>響鈴方式</th>
                <th>啟用</th>
            </tr>
            <?php
            foreach($model->data as $data)
            {
                $userId = $data["UserID"];
                $callOutId = $data["CallOutID"];
                echo "<tr redirect='groupCallSetting/groupCallScheduleModify?userId={$userId}&callOutId={$callOutId}'>";
                echo "<td><input type='checkbox' name='delete[]' value='{$userId},{$callOutId}'/></td>";
                echo "<td>".$data["UserID"]."</td>";
                echo "<td>".$data["PlanName"]."</td>";
                echo "<td>".$data["StartCalledNumber"]."</td>";
                echo "<td>".$data["CalledCount"]."</td>";
                echo "<td>".($data["Calldistribution"]=="1"?"自動平均分配":"分號少的開始配號")."</td>";
                echo "<td><input id='switch' class='bootstrap-switch' readonly type='checkbox' ".
                    ($data["UseState"]==1?"checked":"").
                    " data-size='mini' data-off-color='danger'></td>";
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>

    </div>
<?php
echo Html::formEnd();
?>
<script>
    var vm = new Vue({
        el: "#wrapper",
        data: {
            add_type: "0",
            start_number: ''
        },
        watch: {
            add_type() {
                if (this.add_type === '3') {
                    this.start_number = '09'
                }
            },
            start_number() {
                if (this.add_type === '3' && this.start_number.indexOf('09') !== 0)
                {
                    alertify.alert('只接受09開頭的號碼');
                    this.start_number = '09'
                }
            }
        },
        methods: {
            onFileChange(evt) {
                for(var i=0, f; f=evt.target.files[i]; i++) {
                    if (!f.type.match('text/plain')) {
                        evt.target.value = "";
                        alertify.alert('檔案格式不符，請上傳txt檔');
                    }
                }
            }
        }
    })


    // $(document).ready(function(){
    //     $(".add_type").change(function(){
    //         var $this = $(this), type = $this.val();
    //         switch(type){
    //             case "0":
    //             case "2":
    //             case "3":
    //                 $(".type1").show();
    //                 $(".type2").hide();
    //                 break;
    //             case "1":
    //                 $(".type1").hide();
    //                 $(".type2").show();
    //                 break;
    //         };
    //     })
    //     $("input[name='list']").change(function(evt) {
    //         for(var i=0, f; f=evt.target.files[i]; i++) {
    //             if (!f.type.match('text/plain')) {
    //                 $("input[name='list']").val('');
    //                 alert("檔案格式不符，請上傳txt檔");
    //             }
    //         }
    //     })
    // })
</script>

<?php
$this->partialView($bottom_view_path);
?>
