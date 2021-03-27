<?php
//echo "<pre>";print_r($this);
$this->partialView($top_view_path);
?>
    <h3 id="title"><?php echo $this->menu->currentName?></h3>
<table  width="800" class="table table-v">
    <tbody>
    <tr>
        <td>IP 位址 [WAN]:</td>
        <td>
            <input type="text" name="ipaddr" size="20" value="210.242.143.236">
        </td>
    </tr>
    </tbody>
</table>

<?php
$this->partialView($bottom_view_path);
?>