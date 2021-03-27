<?php
//echo "<pre>";print_r($this);
$this->partialView($top_view_path);
?>
<script>
    $(document).ready(function(){
        $("#ping_btn").click(function(){
            $loading = $("#loading");
            $loading.removeClass("hidden");
            var domain = $("#domain").val();
            $.post(folder+"env/ping",{
                domain:domain
            },function(data){
                $("#ping_result").html(data);
                $loading.addClass("hidden");
            })
        })
    })
</script>
    <h3 id="title"><?php echo $this->menu->currentName?></h3>
<table  width="800" class="table table-v">
    <tbody>
    <tr>
        <td>連線測試對象:</td>
        <td>
            <input type="text" id="domain" size="50" value="google.com">
        </td>
    </tr>
    <tr>
        <td></td>
        <td height="26">
            <div class="form-inline">
                <input id="ping_btn" class="btn btn-default form-group" type="button" value="測試">
                <div id="loading" class="form-group hidden">
                    <div class="bounce1"></div>
                    <div class="bounce2"></div>
                    <div class="bounce3"></div>
                </div>
            </div>

        </td>
    </tr>
    </tbody>
</table>
<div id="ping_result" class="text-center">

</div>

<?php
$this->partialView($bottom_view_path);
?>