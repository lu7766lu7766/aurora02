<?php
//echo "<pre>";print_r($this);
Bundle::addLink("alertify");

$this->partialView($top_view_path);
?>
<script>
    $(document).ready(function(){
        $("#user_login").html($("#userSelet").val());
        $("#update_btn").click(function(){
            if($("#password").val()==""){
                alertify.alert("密碼不得為空白");
                return false;
            }

            if($("#password").val()==$("#confirm_password").val())
            {
                var userId = $("#userSelet").val();
                var password = $("#password").val();
                $.post(folder+"index/update_pwd",{
                    userId:userId,
                    password:password
                },function(data){
                    console.log(data)
                    if($.trim(data)=="success"){
//                        fadeInOut($("#success"));
                        $("#success").fadeInOut();
                    }else{
//                        fadeInOut($("#error"));
                        $("#error").fadeInOut();
                    }
                })
            }
            else
            {
//                fadeInOut($("#info"));
                $("#info").fadeInOut();
            }
        });
        $(".glyphicon-screenshot").css("cursor","pointer").click(function(){
            $pwd = $(this).prev()
            $pwd.attr("type",$pwd.attr("type")=="password"?"text":"password")
        })
    })
</script>
    <h3 id="title"><?php echo $this->menu->currentName?></h3>
<table  width="800" class="table table-v">
    <tbody>
    <tr>
        <td class="col-md-3">登入帳號:</td>
        <td class="col-md-9">
            <span class="user-choice"><?php echo $model->choice?></span>
        </td>
    </tr>
    <tr>
        <td>新密碼:</td>
        <td>
            <div class="input-group">
                <input id="password" class="form-control" type="password">
                <span class="glyphicon glyphicon-screenshot input-group-addon"></span>
            </div>
        </td>
    </tr>
    <tr>
        <td>確認新密碼:</td>
        <td>
            <div class="input-group">
                <input id="confirm_password" class="form-control" type="password">
                <span class="glyphicon glyphicon-screenshot input-group-addon"></span>
            </div>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <input id="update_btn" class="btn btn-primary form-control" type="button" value="更新">
        </td>
    </tr>
    </tbody>
</table>
    <div id="success" class="alert alert-success hidden">
        <strong>Success!</strong> 密碼更新成功.
    </div>
    <div id="error" class="alert alert-warning hidden">
        <strong>Warning!</strong> 密碼更新失敗.
    </div>
    <div id="info" class="alert alert-info hidden">
        <strong>Info!</strong> 密碼強度不足或兩次輸入不同.
    </div>

<div id="ping_result" class="text-center">

</div>

<?php
$this->partialView($bottom_view_path);
?>