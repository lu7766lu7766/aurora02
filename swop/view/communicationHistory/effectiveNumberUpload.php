<?php
$this->partialView($top_view_path);
?>
    <h3 id="title"><?php echo $this->menu->currentName?></h3>
<?php
echo Html::form();
echo Html::pageInput($model->page,$model->last_page,$model->per_page);
?>
<div>
    <input type="file" name="list" />
    <button type="submit" id="file" class="btn btn-default" >上傳</button>
</div>

<?php
$this->partialView($bottom_view_path);
?>