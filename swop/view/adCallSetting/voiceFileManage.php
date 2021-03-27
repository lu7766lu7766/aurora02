<?php
Bundle::addLink("vue2");
Bundle::addLink("vue-component");
Bundle::addLink("lodash");
$this->partialView($top_view_path);
?>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>
<?php
echo Html::form();
?>
<div id="container" v-cloak class="table-responsive col-md-12">

    <input type="hidden" name="modifyName" :value="modifyName"/>

    <h3>請選擇其中一種方式進行上傳</h3>

    <div class="form-group">
        <label>上傳方式1:</label>
        <input type="file" name="voiceFile"/>
        <span class="help-block">只支援Wav格式, 8Khz, 16-bit,Mono(檔名請勿含有中文)</span>
    </div>

    <div class="form-group">
        <label>上傳方式2:</label>
        <input type="file" name="voiceFile2"/>
        <span class="help-block">只支援Wav格式(檔名請勿含有中文)(限59.120.75.13機器使用)</span>
    </div>

    <button type="submit" id="file" class="btn btn-default">上傳</button>
    <delete-btn @click="deleteAllFile()" value="Delete All" alert="確定要全部刪除"></delete-btn>

    <table class="table table-h table-striped table-hover table-pointer" v-if="datas.length">
        <tbody>
        <tr>
            <th>
                <input type='checkbox' v-model="isAllChecked"/>
            </th>
            <th>音檔名</th>
            <th>刪除</th>
        </tr>
        <tr v-for="data in datas" :redirect="controller + '/downloadVoiceFile?fileName=' + data.fileName">
            <td>
                <input type='checkbox' v-model="data.isChecked"/>
            </td>
            <td>{{ data.fileName }}</td>
            <td>
                <delete-btn @click="deleteFile(data.fileName)"/>
            </td>
        </tr>
        </tbody>
    </table>

</div>
<script>

    var vm = new Vue({
        el: "#container",
        data: {
            datas: [],
            controller: controller,
            modifyName: ''
        },
        methods: {
            deleteFile: function (fileName) {
                $.post(folder + "adCallSetting/ajaxDeleteVoiceFile", {
                    fileName: fileName
                }, function (data) {
                    this.dataProccess(data)
                }.bind(this), 'json');
            },
            deleteAllFile: function () {
                var fileNames = _.map(this.datas.filter(function (data) {
                    return data.isChecked
                }), _.property('fileName'))
                $.post(folder + "adCallSetting/ajaxDeleteAllVoiceFile", {
                    fileNames: fileNames
                }, function (data) {
                    this.dataProccess(data)
                }.bind(this), 'json');
            },
            dataProccess: function (voiceFileList) {
                this.datas = []
                voiceFileList.forEach(function (fileName) {
                    this.datas.push({
                        isChecked: false,
                        fileName: fileName
                    })
                }.bind(this))
            },
            getBase64: function (file) {
                var reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function () {
//					this.base64 = reader.result
//					console.log(reader.result);
                }.bind(this);
                reader.onerror = function (error) {
//					console.log('Error: ', error);
                };
                return reader
            }.bind(this)
        },
        computed: {
            isAllChecked: {
                get: function () {
                    var res = true
                    this.datas.forEach(function (data) {
                        if (!data.isChecked) {
                            res = false
                            return false
                        }
                    }.bind(this))
                    return res
                },
                set: function (val) {
                    this.datas.forEach(function (data) {
                        data.isChecked = val
                    }.bind(this))
                }
            }
        },
        created: function () {
            var voiceFileList = <?php echo json_encode($this->voiceFileList) ?>;
            this.dataProccess(voiceFileList)
        },
        mounted: function () {
            $("input[name='voiceFile']").change(function (evt) {
                for (var i = 0, f; f = evt.target.files[i]; i++) {
                    if (_.find(this.datas, {fileName: f.name})) {
                        alert("檔案已存在");
                        return
                    }
                    if (!f.type.match('audio/wav')) {
                        $("input[name='voiceFile']").val('');
                        alert("檔案格式不符，請上傳wav檔");
                        return
                    }
                    this.modifyName = 'voiceFile'
//					this.base64 = ''
                }
            }.bind(this))
            $("input[name='voiceFile2']").change(function (evt) {
                for (var i = 0, f; f = evt.target.files[i]; i++) {
                    if (_.find(this.datas, {fileName: f.name})) {
                        alert("檔案已存在");
                        return
                    }
                    if (!f.type.match('audio/wav')) {
                        $("input[name='voiceFile2']").val('');
                        alert("檔案格式不符，請上傳wav檔");
                        return
                    }

                    this.modifyName = 'voiceFile2'
//					this.getBase64(f)
                }
            }.bind(this))
        }
    })
</script>
<?php
$this->partialView($bottom_view_path);
?>
