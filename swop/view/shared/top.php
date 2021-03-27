<!DOCTYPE html>
<html>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<title>
    電訪系統
</title>

<head>
    <?php echo Bundle::$allLink ?>
    <?php
    //Bundle::addLink("default");
    //Bundle::addLink("bootstrap");
    $choice_id = $model->session["choice"];
    $isRoot = $model->session["isRoot"];
    ?>

    <script>
        var base_url = "<?php echo $base["url"] ?>";
        var folder = "<?php echo $base["folder"]?>";
        var apiUrl = "<?php echo getApiUrl('')?>";
        var downloaderUrl = "<?php echo getDownloaderUrl('')?>";
        var controller = "<?php echo $this->controller?>";
        var ctrl_uri = folder + controller + "/";
        var action = "<?php echo $this->action?>";
        var choice = "<?php echo $choice_id?>";
        var isRoot = <?php echo $isRoot ? 'true' : 'false'?>;
        var isLoginRoot = <?php echo $model->session['login']['UserID'] == 'root' ? 'true' : 'false'?>;

        // if (isLoginRoot) {
        //   apiUrl = '//125.227.84.247:8099/'
        //   downloaderUrl = '//125.227.84.247:8099/'
        // }

        $(document).ready(function () {

            <?php
            if ($model->warning != "") {
                echo "alert('{$model->warning}');";
            }
            ?>

            $("body").css("padding-top", $("#header").height());

            $("#page-content-wrapper").css("min-height", $(window).height() - $("#header").height());

            $("#main-content").css({
                "min-height": $("#page-content-wrapper").height() - $("#footer").height(),
                "padding-bottom": 15
            });

            $("#menu-toggle").click(function (e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled");
            });

            if ($(".slideactive").length)
                $('#sidebar-wrapper').animate({
                    scrollTop: $(".slideactive").offset().top - $(".slideactive").height() * 2
                }, 1000);

            $(".reboot_btn").confirm({
                text: "確定要重啟?",
                confirm: function (button) {
                    location.href = base_url + 'index/reboot'
                },
                post: true,
                confirmButton: "確定",
                cancelButton: "取消",
                confirmButtonClass: "btn-danger",
                cancelButtonClass: "btn-default"
            });

            $(".shotdown_btn").confirm({
                text: "確定要關機?",
                confirm: function (button) {
                    location.href = base_url + 'index/shotdown'
                },
                post: true,
                confirmButton: "確定",
                cancelButton: "取消",
                confirmButtonClass: "btn-danger",
                cancelButtonClass: "btn-default"
            });
        })
    </script>
    <style>
        [v-cloak] {
            display: none;
        }
    </style>
</head>

<body>

<nav id="header" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" id="drop_btn" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar eye-protector-processed"
                      style="transition: background 0.3s ease; background-color: rgb(193, 230, 198);"></span>
                <span class="icon-bar eye-protector-processed"
                      style="transition: background 0.3s ease; background-color: rgb(193, 230, 198);"></span>
                <span class="icon-bar eye-protector-processed"
                      style="transition: background 0.3s ease; background-color: rgb(193, 230, 198);"></span>
            </button>
            <a class="navbar-brand" id="menu-toggle" style="cursor:pointer">電訪系統</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse" aria-expanded="false" style="height: 1px;">
            <div class="navbar-right">
                <div class="navbar-form navbar-text navbar-left text-primary">
                    <?php
                    echo Html::selector($model->empSelect);
                    ?>
                </div>
                <ul class="nav navbar-nav navbar-left">
                    <li><a href="<?php echo $base["url"] . "index/index" ?>">首頁</a></li>
                    <!--<li><a href="<?php echo $base["url"] . "index/service" ?>">服務</a></li>-->
                    <?php if ($isRoot){ ?>
                    <li><a class="shotdown_btn" href="javascript:;">關機</a><?php } ?>
                        <!-- -->
                        <?php if ($isRoot){ ?>
                    <li><a class="reboot_btn" href="javascript:;">重啟</a><?php } ?>
                    <li><a href="<?php echo $base["url"] . "index/password" ?>">密碼</a></li>
                    <li><a href="<?php echo $base["url"] . "index/logout" ?>">登出</a></li>
                </ul>
            </div>

        </div>
    </div>
</nav>
<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php echo $this->menu->CreateMenu($model->permission, $choice_id); ?>
    </div>
    <div id="page-content-wrapper">
        <div id="main-content" class="container-fluid">

