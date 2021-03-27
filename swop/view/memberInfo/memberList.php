<?php
//echo "<pre>";print_r($this);
$this->partialView($top_view_path);
$choice_id = $model->session["choice"];
?>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->


<div class="table-responsive">
    <?php
    echo Html::form();
    ?>
    <ul class="pager responsive">
        <li class="first-page"><a href="#">第一頁</a></li>
        <li class="prev-page"><a href="#">上一頁</a></li>
        <li class="next-page"><a href="#">下一頁</a></li>
        <li class="last-page"><a href="#">最後一頁</a></li>
        <li><a href="#">第<?php echo Html::selector($model->pageSelect) ?>頁</a></li>
        <li><a href="#">(共<?php echo $model->last_page ?>頁，<?php echo $model->total ?>筆資料)</a></li>
    </ul>
    <div class="form-inline form-group">
        <input type="button" class="btn btn-danger delete_btn" value="Delete">
        <a href="javascript:redirect('memberInfo/memberAdd')" class="btn btn-primary post_btn"
           style="color:white">新增</a>
        <input type="text" name="search" class="form-control" placeholder="搜索關鍵字" value="<?php echo $model->search ?>"/>
        <input type="button" class="btn btn-info search_btn" value="搜索">
    </div>

    <table class="table table-h table-pointer table-striped table-hover">
        <tbody>
        <tr>
            <th>
                <input type='checkbox' class="checkAll" for="delete[]"/>
            </th>
            <th>姓名</th>
            <th>手機</th>
            <th>電話</th>
            <th>地址</th>
        </tr>
        <?php
        foreach ($model->data as $user) {
            $ID = $user['ID'];
            echo "<tr redirect='memberInfo/memberModify?ID={$ID}'>";
            echo "<td>";
            echo "<input type='checkbox' name='delete[]' value='{$ID}'/>";
            echo "</td>";
            echo "<td>{$user['LastName']}{$user['FirstName']}</td>";
            echo "<td>{$user['Cellphone']}</td>";
            echo "<td>{$user['Telephone']}</td>";
            echo "<td>{$user['City']}{$user['Address']}</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
    <?php
    echo Html::formEnd();
    ?>
</div>

<?php
$this->partialView($bottom_view_path);
?>
