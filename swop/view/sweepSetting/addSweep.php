<?php
//echo "<pre>";print_r($this);
Bundle::addLink("validate");
Bundle::addLink("datetime");
Bundle::addLink("lodash");
$this->partialView($top_view_path);

?>
    <script>
        $(document).ready(function(){
            $("form").validate({
                rules:{
                    startCalledNumber: {
                        required: true,
                        minlength: 13,
                        maxlength: 13
                    }
                },
                messages: {
                    startCalledNumber: {
                        required: "這欄位必填",
                        minlength: "請輸入滿13碼",
                        maxlength: "請勿超過13碼"
                    }
                }
            });
            $( "#startCalledNumber").trigger("focus").trigger("blur");
//            $( "#startCalledNumber" ).rules( "add", { //same update
//                minlength: 5,
//                messages: {
//                    minlength: "請輸入滿5碼"
//                }
//            });
        });
    </script>
    <h3 id="title"><?php echo $this->menu->currentName?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->
<?php
echo Html::form();
?>
    <div class="col-md-12 form-inline">
        <label class="col-md-2"><input type="radio" class="radio add_type" name="numberMode" value="0" checked>區段增加</label>
        <label class="col-md-2"><input type="radio" class="radio add_type" name="numberMode" value="1">名單上傳</label>
        <div class="col-md-4"></div>
        <!--        <input id="post_btn" class="col-md-2 btn btn-primary post_btn" type="button" value="新增">-->
    </div>
    <div class="table-responsive col-md-12">
        <table class="table table-v">
            <tbody>
            <tr>
                <td class="col-md-2">掃號名稱</td>
                <td class="col-md-2">
                    <input type="text" name="planName" class="form-control">
                </td>
                <td class="col-md-2 type1">起始電話</td>
                <td class="col-md-2 type1">
                    <div class="dropdown">
                        <input type="text" name="startCalledNumber" id="startCalledNumber" class="form-control num-only rigorous" value="86">
                    </div>
                </td>
                <td class="col-md-2 type1">筆數</td>
                <td class="col-md-2 type1">
                    <input type="text" name="calledCount" class="form-control">
                </td>
                <td class="col-md-2 type2" style="display: none">上傳名單</td>
                <td class="col-md-6 type2" style="display: none" colspan="3">
                    <input type="file" name="list"/>
                </td>
            </tr>
            <tr>
                <td>撥出電話等待秒數</td>
                <td>
                    <input type="text" name="callProgressTime" class="form-control" value="15">
                </td>
                <td>自動撥號速度</td>
                <td>
                    <?php echo Html::selector($model->concurrentCallsSelect)?>
                </td>
                <td>收集時間</td>
                <td>
                    <input type="text" name="startDateTime" class="form-control datetime-picker">
                </td>
            </tr>
            <tr>
<!--                <td>有效號判斷模式</td>-->
<!--                <td>-->
<!--<!--                    <select class="form-control" name="inspectMode">-->
<!--<!--                        <option value="0">模式0</option>-->
<!--<!--                        <option value="1">模式1</option>-->
<!--<!--                    </select>-->
<!--                    --><?php //echo Html::selector($model->inspectModeSelect)?>
<!--                </td>-->
                <td class="col-md-2"></td>
                <td colspan="5">
                    <input id="post_btn" class="form-control btn btn-primary post_btn" type="button" value="新增">
                </td>
            </tr>
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
                <th>掃號名稱</th>
                <th>起始電話</th>
                <th>筆數</th>
                <th>啟用</th>
            </tr>
            <?php
            foreach($model->data as $data)
            {
                $userId = $data["UserID"];
                $callOutId = $data["CallOutID"];
                echo "<tr redirect='sweepSetting/addSweepModify?userId={$userId}&callOutId={$callOutId}'>";
                echo "<td><input type='checkbox' name='delete[]' value='{$userId},{$callOutId}'/></td>";
                echo "<td>".$data["UserID"]."</td>";
                echo "<td>".$data["PlanName"]."</td>";
                echo "<td>".$data["StartCalledNumber"]."</td>";
                echo "<td>".$data["CalledCount"]."</td>";
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

    $(document).ready(function(){

        $(".add_type").change(function(){
            var $this = $(this), type = $this.val();
            switch(type){
                case "0":
                    $(".type1").show();
                    $(".type2").hide();
                    break;
                case "1":
                    $(".type1").hide();
                    $(".type2").show();
                    break;
                case "2":
                    $(".type1").show();
                    $(".type2").hide();
                    break;
            }
        });

        $("input[name='list']").change(function(evt) {
            for(var i=0, f; f=evt.target.files[i]; i++) {
                if (!f.type.match('text/plain')) {
                    $("input[name='list']").val('');
                    alert("檔案格式不符，請上傳txt檔");
                }
            }
        })

        var cn_phone_rulem, limit_input = [];

        var render_dropdownlist = function(is_show = 1)
        {
            cn_phone_rule = cn_phone_rule || [];
            //var cn_phone_rule = <?php //echo json_encode($base["cn_phone_rule"]);?>;
            var tmp = [false, false, false, false, false, false, false, false, false, false];
            var data = [];
            var search = document.getElementById("startCalledNumber").value;

            //if(search.length<2) return;

            for(var phone in cn_phone_rule)
            {
                if (phone==search) continue;

                if (tmp.filter(function(res){ return res}).length == 10) break;

                if (phone.indexOf(search) == 0) {
                    tmp[phone.substr(search.length,1)] = true;
                }
            }

            _.forEach(tmp, function(res, key) {
                res? data.push(key): '';
            })
            limit_input = data;

            $("#dropdownMenu1").remove();
            if(data.length==0) return;

            var template = "<ul class='dropdown-menu' role='menu' aria-labelledby='dropdownMenu1' id='dropdownMenu1'>";
            data.map(function(x){
                template += "<li role='presentation'><a role='menuitem' tabindex='-1' href='#'>"+x+"</a></li>";
            })
            template += "</ul>";

            $template = $(template)
            $("#startCalledNumber").after($template);
            if(is_show)$template.show()

            $template.on("click",$template.find("a"),function(e){

                document.getElementById("startCalledNumber").value = document.getElementById("startCalledNumber").value + e.target.innerHTML;
                $("#startCalledNumber").trigger("focus");//.focus();
            });

        };



        var json_folder = folder + "<?php echo $base['setting_dir'];?>";
        $.getJSON( json_folder + "cn_phone_rule.json",{},function(data){
            cn_phone_rule = data;
            render_dropdownlist(0);
        });

        $("#startCalledNumber").on("keyup focus",function(e){

            console.log(limit_input)
            console.log(limit_input.indexOf(0))
            console.log(limit_input.indexOf(0) == -1)
            console.log(limit_input.indexOf(0) === -1)
            console.log(limit_input.indexOf(parseInt(String.fromCharCode(e.keyCode)))==-1)
            if(e.type=="keyup" &&
                limit_input!=null &&
                limit_input.length>0 &&
                limit_input.indexOf( parseInt(String.fromCharCode(e.keyCode)) )==-1 &&
                e.keyCode>=48 && e.keyCode<=57)//0~9
            {
                alert("請照提示字元輸入");
                $(this).val($(this).val().substr(0,$(this).val().length-1));
            }

            if(cn_phone_rule[e.target.value])
            {
                var len = cn_phone_rule[e.target.value];
                $( "#startCalledNumber" ).rules( "add", {
                    minlength: len,
                    maxlength: len,
                    messages: {
                        minlength: "請輸入滿"+len+"碼",
                        maxlength: "請勿超過"+len+"碼"
                    }
                });
            }
            render_dropdownlist();
        }).on("blur",function(e){
            var timer = setTimeout(function(){
                if(!$("#startCalledNumber").is(":focus"))
                    $("#dropdownMenu1").remove();
            },200)

        })
    })
</script>

<?php
$this->partialView($bottom_view_path);
?>