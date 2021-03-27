<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
<?php
echo Bundle::$allLink;
//echo "<pre>";print_r($this);
$top_view_path = "";
$this->partialView($top_view_path);
?>
<body class="container-fluid">
<div class="row">
    <div class="login-box col-xs-12 col-md-offset-4 col-md-4">
        <div class="login-logo">
            <h2 class="form-signin-heading">Please sign in</h2>
            <!--<img src="/admin/upload/company/images/logo.png" alt="KenKo" s="" clear="" beauty'=""> -->
        </div>
        <p class="login-box-msg" style="letter-spacing:3px;"><b></b></p>
        <!--<form action="<?php echo $this->submit_link; ?>" method="post">-->
        <?php echo Html::form() ?>
        <div class="form-group has-feedback">
            <input class="form-control eye-protector-processed" placeholder="輸入帳號" name="username" type="text"
                   style="border-color: rgba(0, 0, 0, 0.34902);">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input class="form-control eye-protector-processed" placeholder="輸入密碼" name="password" type="password"
                   style="border-color: rgba(0, 0, 0, 0.34902);">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="form-group row">
                <span class="col-xs-5 control-label">
                    <label class="sr-only">驗證圖片</label>
                    <img width="100%" id='verification_img' alt="點擊換圖" title="點擊換圖"
                         src="<?php echo $base["folder"]; ?>createImg.php"
                         onclick="javascript:this.src='<?php echo $base["folder"]; ?>createImg.php?n='+Math.random();"
                         width="130" height="50"/>
                </span>
            <div class="col-xs-7">
                <input style="height:34px;border-color:#DCDDD" class="form-control" placeholder="輸入驗證碼" name="captcha"
                       type="text">
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <input type="submit" class="btn btn-primary btn-block" value="登入">
            </div>
        </div>
        <?php echo Html::formEnd() ?>

        <div class="text-left">
            <div class="errorMessage">
                <?php echo $model->errorMsg ?>
            </div>
        </div>
    </div>

    <?php
    $bottom_view_path = "";
    $this->partialView($bottom_view_path);
    ?>
</body>
</html>
