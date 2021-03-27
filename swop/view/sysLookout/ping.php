<?php
//echo "<pre>";print_r($this);
$this->partialView($top_view_path);
?>
<script>
    $(document).ready(function(){

        $("#ping").click(function(){
            $this = $(this);
            $this.button('loading');
            $("#ping_result").html("Loading.");
            var timer = setInterval(function(){
                $("#ping_result").append(".");
            },500)
            $.post(folder + "sysLookout/ajaxPing", {
                ip: $("#ip").val()
            }, function (data) {
                clearInterval(timer);
                $this.button('reset');
                $("#ping_result").html(data);
            })
        });
    })
</script>
    <h3 id="title"><?php echo $this->menu->currentName?></h3>
<table  width="800" class="table table-v">
    <tbody>
    <tr>
        <td class="col-md-3">Host:</td>
        <td class="col-md-9">
            <div class="input-group">
                <input type="text" class="form-control" value="168.95.1.1" id="ip">
                <span class="input-group-btn">
                <button class="btn btn-info" type="button" id="ping"
                        data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing">Ping!</button>
                </span>
            </div>

        </td>
    </tr>
    <tr style="height: 50px;">
        <td>結果:</td>
        <td  id="ping_result">
        </td>
    </tr>
    </tbody>
</table>

<?php
$this->partialView($bottom_view_path);
?>