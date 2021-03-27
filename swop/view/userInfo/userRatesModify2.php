<?php
Bundle::addLink("validate");
Bundle::addLink("reactjs");
Bundle::addLink("alertify");
$this->partialView($top_view_path);

?>

    <script type="text/jsx">
        //input 欄位value無法異動

        var edit_field = [
            [
                {text:"時間1"},
                {type:"text",id:"Time1",class:"form-control",value:"0"},
                {text:"費用1"},
                {type:"text",id:"RateValue1",class:"form-control",value:"0.0001"},
                {text:"成本1"},
                {type:"text",id:"RateCost1",class:"form-control",value:"0"},
            ],
            [,
                {text:"時間2"},
                {type:"text",id:"Time2",class:"form-control",value:"0"},
                {text:"費用2"},
                {type:"text",id:"RateValue2",class:"form-control",value:"0.0001"},
                {text:"成本2"},
                {type:"text",id:"RateCost2",class:"form-control",value:"0"},
            ],
            [
                {text:"前置碼"},
                {type:"text",id:"PrefixCode",class:"form-control"},
//                {text:"刪除幾碼"},
//                <?php //echo json_encode($model->subNumSelect)?>//,
//                {text:"新增前置碼"},
//                {type:"text",id:"AddPrefix",class:"form-control"},
                {text:" "},
                {type:"submit",id:"btn",class:"btn btn-primary form-control",value:"新增",colspan:"3"},

            ],
//            [
//
//            ]
        ];
        var data_list = [
            ["前置碼","時間1","費用1","成本1","時間2","費用2","成本2","刪除"],
            <?php
            foreach($model->rateDetail as $data)
            echo json_encode($data).",";
            ?>
        ];//
        var key_field = ["PrefixCode"];

    </script>
    <script type="text/jsx" src="<?php echo $base["folder"]?>public/jsx/editView.jsx"></script>
    <script type="text/jsx" >
        React.render(
            <View edit_field={edit_field} />,
            document.getElementById('container')
        );
    </script>
<!--    <script type="text/jsx" src="--><?php //echo $base["folder"]?><!--public/jsx/userRatesModify.jsx"></script>-->
    <script type="text/javascript" src="<?php echo $base["folder"]?>public/jsx/editView.js"></script>
    <script>
        react.ready(function(){
            $("form").validate({
                rules:{
                    PrefixCode: {
                        required: true
                    },
                    RateValue1: {
                        maxlength: 8
                    },
                    RateValue2: {
                        maxlength: 8
                    }
                },
                messages: {
                    PrefixCode: "這欄位必填",
                    RateValue1: "最多到小數點六位",
                    RateValue2: "最多到小數點六位"
                }
            });
        });
    </script>
    <h3 id="title"><?php echo $this->menu->currentName;?></h3>
    <!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

    <div class="table-responsive" id="container">

    </div>
<?php
$this->partialView($bottom_view_path);
?>
