<?php
Bundle::addLink("alertify");
Bundle::addLink("vue2");
Bundle::addLink("lodash");
$this->partialView($top_view_path);
$choice_id = $model->session["choice"];
?>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->
<?php
echo Html::form();
?>
<input type="button" class="btn btn-danger delete_btn" value="Delete">
<div class="table-responsive" v-cloak id="container">
    <table class="table table-h table-pointer table-striped table-hover">
        <tbody>
        <tr>
            <th v-if="isRoot">刪除</th>
            <th>費率表代號</th>
            <th>費率表名稱</th>
            <th>操作</th>
        </tr>
        <tr v-for="(data, index) in rateGroup" :key="index">
            <td v-if="isRoot">
                <input type='checkbox' name='delete[]' :value='data.RateGroupID'/>
            </td>
            <td>
                {{ data.RateGroupID }}
            </td>
            <td>
                <span v-if="isRoot">
                    <input type="text"
                           class="form-control"
                           v-model="data.RateGroupName"
                           @change="doGroupNameChange(data.RateGroupID, data.RateGroupName)">
                </span>
                <span v-else>{{ data.RateGroupName }}</span>
            </td>
            <td>
                <input type="button" class="btn-info" value="編輯"
                       @click="toModify(data.RateGroupID, data.RateGroupName)">
            </td>
        </tr>
        </tbody>
    </table>
</div>

<?php
echo Html::formEnd();
?>
<?php
$this->partialView($bottom_view_path);
?>
<script>
    new Vue({
        el: '#container',
        data: {
            rateGroup: _.orderBy(_.map(<?php echo json_encode($model->rateGroup) ?>, function (x) {
                x.RateGroupID = +x.RateGroupID
                return x
            }), 'RateGroupID'),
            isRoot: isRoot
        },
        methods: {
            toModify(id, name) {
                redirect('userInfo/userRatesModify?rateGroupId=' + id + '&rateGroupName=' + name)
            },
            doGroupNameChange: function (id, newName) {
                $.post(ctrl_uri + 'ajax_setGroupName',
                    {
                        'RateGroupID': id,
                        'RateGroupName': newName
                    },
                    function (res) {
                        // var index = _.findIndex(this.rateGroup, {RateGroupID: id})
                        // this.rateGroup[index].RateGroupName = newName
                        alertify.alert('已成功修改!');
                    }.bind(this))
            }
        },
        mounted: function () {
            console.log(this.rateGroup)
        }
    })
</script>
