<?php
Bundle::addLink("vue2");
Bundle::addLink("alertify");
$this->partialView($top_view_path);
?>
<h3 id="title"><?php echo $this->menu->currentName?></h3>
<?php
echo Html::form();
?>
<div id="effective_number">
    <div class="input-group">
        <select v-model="countryCode">
            <option v-for="(country,index) in countryList" :value="index" v-text="country"></option>
        </select>
    </div>
    <div class="form-inline">
        <div class="input-group">
            <select v-for="sn in searchNumbers" :value="sn">
                <option v-for="number in numberSelect" :value="number" v-text="number"></option>
            </select>
        </div>
        <input type="button" value="搜尋"  class="btn btn-primary" />
    </div>
    <div class="form-inline">
        <div class="input-group">
            <input type="text"class="form-control" placeholder="非指定數字請用?表示" v-model="searchNumber">
            <span class="input-group-addon">範例：098765???3</span>
        </div>
        <input type="button" value="新增"  class="btn btn-primary" @click="getNumbers"/>
    </div>
    <div class="input-group">
        <span style="color:green" v-text="'您目前輸入了'+searchNumber.length+'碼'"></span>
    </div>
    <div class="input-group">
        <span style="color:red" v-text="show_msg"></span>
    </div>
</div>
<?php
echo Html::formEnd();
?>
<script>
    var vm = new Vue({
        el: "#effective_number",
        data: {
            countryList: {
                0:'台灣',
                1:'大陸'
            },
            numberSelect: ["","?","0","1","2","3","4","5","6","7","8","9"],
            searchNumber: "",
            countryCode: 0,
            show_msg: ""
        },
        computed:{
            searchNumbers:function(){
                return this.searchNumber.length? this.searchNumber.split():
                    this.countryCode == 0? ["","","","","","","","","",""]: ["8","6","","","","","","","","","","",""]
            }
        },
        methods: {
            getNumbers: function(){
                this.show_msg = "資料送出中，請稍待";
                $.post(folder+"groupCallSetting/getEffectiveNumber",{
                    countryCode: this.countryCode,
                    searchNumber: this.searchNumber
                },function(data){
                    //console.log(data)
                    if(data.status == 0 && data.len>0)
                    {
                        //alertify.alert('已成功將'+data.len+'筆資料加入排程!');
                        this.show_msg = "已成功將"+data.len+"筆資料加入排程!";
                    }
                    else if(data.status == 0 && data.len == 0)
                    {
                        this.show_msg = "查無資料";
                    }
                    else if(data.status == -1 && data.len == -1)
                    {
                        this.show_msg = "資料筆數過超過20萬筆，請縮小查詢範圍";
                    }
                    else
                    {
                        this.show_msg = "資料新增失敗，請聯繫管理員";
                    }

                }.bind(this),"json")
            }
        }
    });
</script>
<?php
$this->partialView($bottom_view_path);
?>