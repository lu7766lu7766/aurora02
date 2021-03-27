<?php
Bundle::addLink("validate");
Bundle::addLink("datetime");
$this->partialView($top_view_path);
?>
<script>
    $(document).ready(function () {
        $("form").validate({
            rules: {
                dateFrom: {
                    required: true
                },
                dateTo: {
                    required: true
                }
            },
            messages: {
                dateFrom: "",
                dateTo: ""
            }
        });
        $("#download").click(function () {
            var params = "?choice=" + choice + '&';
            $("input:text, input:radio:checked").each(function () {
                var key = $(this).attr('id') || $(this).attr('name')
                if (this.value != '') {
                    params += key + '=' + this.value + '&';
                }
            })
            window.open(downloaderUrl + 'downloadFile/recordFilesToZip' + params)
        })
    });
</script>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

<?php
echo Html::form();
?>
<table class="table table-v">
    <tbody>
    <tr>
        <td class="col-md-3 col-xs-5">分機號碼</td>
        <td class="col-md-9 col-xs-7">
            <input type="text" class="form-control" id="extensionNo"/>
        </td>
    </tr>
    <tr>
        <td>目的端號碼</td>
        <td>
            <input type="text" class="form-control" id="orgCalledID"/>
        </td>
    </tr>
    <tr>
        <td>通話時間</td>
        <td>
            <div class="form-inline">
                <input type="text" class="form-control" id="callDuration"/>
                <div class="input-group">
                    <label><input type="radio" value="within" name="durationCondition" checked>以內</label>
                    <label><input type="radio" value="over" name="durationCondition">超過</label>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td>日期</td>
        <td>
            <div class="input-group">
                <input type="text" class="form-control date-picker today-date" id="callStartBillingDate" placeholder="
這欄位必填"/>
                <span class="input-group-addon">~</span>
                <input type="text" class="form-control date-picker today-date" id="callStopBillingDate" placeholder="
這欄位必填"/>
            </div>

        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <input id="download" class="btn btn-default form-control" type="button" value="下載">
        </td>
    </tr>
    </tbody>
</table>
<?php
echo Html::formEnd();
?>


<?php
$this->partialView($bottom_view_path);
?>
