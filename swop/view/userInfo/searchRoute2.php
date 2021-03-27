<?php
Bundle::addLink("validate");
Bundle::addLink("reactjs");
Bundle::addLink("alertify");
$this->partialView($top_view_path);

?>
    <script type="text/jsx">
        //input 欄位value無法異動
        var empSelect2 = <?php echo json_encode($model->empSelect2)?>;

        var edit_field = [
            [
                {text:"用戶"},
                empSelect2,
                {text:"顯示號碼"},
                {type:"text",id:"RouteCLI"},
                {text:"Trunk IP"},
                {type:"text",id:"TrunkIP"},
            ],
            [,
                {text:"前置碼"},
                {type:"text",id:"PrefixCode"},
                {text:"新增前置碼"},
                {type:"text",id:"AddPrefix"},
                {text:"Trunk port"},
                {type:"text",id:"TrunkPort",value:5060},
            ],
            [
                {text:"路由名稱"},
                {type:"text",id:"RouteName"},
                {text:"刪除幾碼"},
                {type:"text",id:"SubNum"},
                {text:"有效號判斷模式"},
                <?php echo json_encode($model->inspectModeSelect)?>,

            ],
            [
                {text:" "},
                //{type:"submit",id:"btn",class:"btn btn-primary form-control",value:"新增",colspan:"3"},
                {type:"submit",id:"btn",class:"btn btn-primary form-control",value:"新增",colspan:"5"},
            ]
        ];
        var data_list = [
            ["編號","用戶","前置碼","新增前置碼","顯示號碼","Trunk IP","Trunk Port","路由名稱","刪除幾碼","有效號判斷模式","刪除"],
            <?php
            foreach($model->userRoute as $key=>$data)
                echo json_encode(array_merge(array("index"=>$key+1),$data)).",";
            ?>
        ];
        var key_field = ["UserID","PrefixCode"];

    </script>

    <script type="text/jsx" src="<?php echo $base["folder"]?>public/jsx/editView.jsx"></script>
    <script type="text/jsx" >
        React.render(
            <View edit_field={edit_field} delete_all="false"/>,
                document.getElementById('container')
        );
    </script>
    <!--    <script type="text/jsx" src="--><?php //echo $base["folder"]?><!--public/jsx/userRatesModify.jsx"></script>-->
    <script type="text/javascript" src="<?php echo $base["folder"]?>public/jsx/editView.js"></script>
    <script>
        $(document).ready(function(){
            $("form").validate({
                rules:{
                    trunkPort: {
                        required: true,
                        range: [1, 65535]
                    }
                },
                messages: {
                    trunkPort: {
                        required: "這欄位必填",
                        range: "請輸入介於1~65535中間的值"
                    }
                }
            });
        });
    </script>

    <h3 id="title"><?php echo $this->menu->currentName;?></h3>

    <div class="table-responsive" id="container">

    </div>
<?php
$this->partialView($bottom_view_path);
?>