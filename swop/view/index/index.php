<?php

//echo "<pre>";print_r($this);
$this->partialView($top_view_path);
?>
    <h3><strong>歡迎 <?php echo $model->choice?> 登入電訪系統</strong></h3>
<?php
$this->partialView($bottom_view_path);
?>