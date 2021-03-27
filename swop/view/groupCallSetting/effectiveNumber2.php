<?php
Bundle::addLink("vue2");
Bundle::addLink("lodash");
Bundle::addLink("alertify");
$this->partialView($top_view_path);
?>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>
<?php
echo Html::form();
?>
<div id="effective_number">
  <div class="input-group" id="searchNumber">

    可用鍵盤輸入(tab & up & down & 數字(?) & enter)
    <div>
      <select v-model="countryCode" @change="chgCountry">
        <option v-for="(country, code) in countrySelect" :value="code" v-text="country"></option>
      </select>
    </div>
    <select v-for="(numberSelect, index) in numberSelectList" @change="setPhone(index, $event.target.value)"
            class="numberSelect">
      <option v-for="s in numberSelect" :value="s" v-text="s"></option>
    </select>
    <button type="button" v-show="showSearch" @click="search" class="btn btn-primary">搜索</button>

  </div>

  <div class="input-group">
    <span style="color:red" v-text="message"></span>
  </div>

  <br>

  <div class="table-responsive col-md-12" style="display:none" v-show="resultList.length">
    <table class="table table-h able-striped table-hover table-pointer">
      <tr>
        <td>檔名</td>
        <td>筆數</td>
        <td>上傳</td>
      </tr>
      <tr v-for="list in resultList">
        <td>{{list[0]}}</td>
        <td>{{list.length}}</td>
        <td>
          <button type="button" @click="upload(list)" class="btn btn-info">上傳</button>
        </td>
      </tr>
    </table>
  </div>
</div>
<?php
echo Html::formEnd();
?>
<script>
  var vm = new Vue({
    el: '#effective_number',
    data: {
      countryCode: '0',
      countrySelect: {
        '0': '台灣',
        '1': '中國',
      },
      twRules: {},
      cnRules: {},
      searchNumber: '',
      limitLen: -1,
      numberSelectDefault: ['', '?', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
      numberSelectList: [],
      numberLimitLen: -1,
      numberLimitLenList: [],
      showSearch: false,
      message: '',
      searchResult: [],
    },
    methods: {
      chgCountry: function () {
        this.searchNumber = ''
        this.numberSelectList = []
        this.checkPhone(0)
      },
      setPhone: function (index, value) {
        var len = index + 1
        this.searchNumber = this.searchNumber.substr(0, index)
        this.numberSelectList = this.numberSelectList.splice(0, len)
        this.searchNumber += value
        this.checkPhone(len)
      },
      checkPhone: function (len) {
        // 預設國碼開頭
        if (len === 0) {
          if (this.countryCode === '0') {
            this.searchNumber = '0'
            this.numberSelectList.push(['0'])
          }
          else if (this.countryCode === '1') {
            this.searchNumber = '86'
            this.numberSelectList.push(['8'])
            this.numberSelectList.push(['6'])
          }
        }
        var numberCurrent = ['', '?']
        this.numberLimitLenList = []

        if (this.countryCode === '0') {
          _.forEach(this.twRules, function (val, key) {
            if (key.startsWith(this.searchNumber) && key.length >= this.searchNumber.length) {
              numberCurrent.push(key.substr(this.searchNumber.length, 1))
              this.numberLimitLenList.push(parseInt(val))
            }
          }.bind(this))
        }
        else if (this.countryCode === '1') {
          _.forEach(this.cnRules, function (val, key) {
            if (key.startsWith(this.searchNumber) && key.length >= this.searchNumber.length) {
              numberCurrent.push(key.substr(this.searchNumber.length, 1))
              this.numberLimitLenList.push(parseInt(val))
            }
          }.bind(this))
        }
        // console.log(this.numberLimitLenList)

        // 移除重複
        numberCurrent = numberCurrent.distinc()
        this.numberLimitLenList = _.uniq(this.numberLimitLenList)
        // 如果沒有match到
//                if (numberCurrent.length === 2) {
        numberCurrent = _.clone(this.numberSelectDefault)
//                }
        // 比對到多個長度，取最大值
        if (this.numberLimitLenList.length > 0) {
          this.numberLimitLen = _.max(this.numberLimitLenList)
        }
        // 如果號碼長度小於最終長度
        if (this.numberSelectList.length < this.numberLimitLen) {
          this.numberSelectList.push(numberCurrent)
          this.$nextTick(function () {
            $('.numberSelect:last').val('')
          })
          this.showSearch = false
        }
        else {
          this.showSearch = true
        }
      },
      search: function () {
        this.message = '搜索中請稍待...'
        $.post(folder + controller + '/getEffectiveNumber2', {
          countryCode: this.countryCode,
          searchNumber: this.searchNumber,
        }, function (result) {
          if (result.status == 0 && result.data.length > 0) {
            this.searchResult = result.data
            this.message = ''
          }
          else if (result.status == 0 && result.data.length == 0) {
            this.message = '查無資料'
          }
          else if (result.status == -1 && result.data == -1) {
            this.message = '資料筆數過超過20萬筆，請縮小查詢範圍'
          }
          else {
            this.message = '資料新增失敗，請聯繫管理員'
          }
        }.bind(this), 'json')
      },
      upload: function (list) {
        this.message = '上傳中請稍待...'
        $.post(folder + controller + '/addDefaultSchedule', {
          list: list.join(','),
        }, function () {
          this.message = ''
          alertify.alert('新增成功')
        }.bind(this))
      },
    },
    computed: {
      resultList: function () {
        var max_limit = 5000
        var len = Math.ceil(this.searchResult.length / max_limit)
        var searchResult = _.map(this.searchResult, function (x) {
          return x.number
        })
        var list = []
        _.times(len, function () {
          list.push(
            searchResult.splice(0, max_limit),
          )
        }.bind(this))
        return list
      },
    },
    mounted: function () {
      this.$nextTick(function () {
        this.chgCountry(0)
      })
    },
  })
  var json_folder = folder + '<?php echo $base['setting_uri'];?>'
  $.getJSON(json_folder + 'tw_phone_rule.json', function (data) {
    vm.twRules = data
    vm.chgCountry(0)
  })
  $.getJSON(json_folder + 'cn_phone_rule.json', {}, function (data) {
    vm.cnRules = data
  })
  $(window).keyup(function (e) {
    var key = ''
    //@keyup.down="countryCode='1'" @keyup.up="countryCode='0'"
    //@keyup.enter="search"
    switch (e.keyCode) {
      case 49: //1
        key = '1'
        break
      case 50: //2
        key = '2'
        break
      case 51: //3
        key = '3'
        break
      case 52: //4
        key = '4'
        break
      case 53: //5
        key = '5'
        break
      case 54: //6
        key = '6'
        break
      case 55: //7
        key = '7'
        break
      case 56: //8
        key = '8'
        break
      case 57: //9
        key = '9'
        break
      case 48: //0
        key = '0'
        break
      case 191: //?
        key = '?'
        break
      case 8: //back
        vm.setPhone(vm.searchNumber.length - 2, vm.searchNumber.substr(-2, 1))
        break
      case 13: //enter
        vm.search()
        break
      case 38: //up
        vm.countryCode = '0'
        vm.chgCountry()
        break
      case 40: //down
        vm.countryCode = '1'
        vm.chgCountry()
        break
    }
    if (key != '') {
      lastSelectReset(key)
    }
  })

  var lastSelectReset = function (key) {

    if ($('.numberSelect:last').find('[value=\'' + key + '\']').length) {
      var len = vm.numberSelectList.length
      $('.numberSelect:last').val(key)
      vm.searchNumber = (vm.searchNumber.substr(0, len - 1) + key)
      vm.checkPhone(len)
    }
  }
</script>
<?php
$this->partialView($bottom_view_path);
?>
