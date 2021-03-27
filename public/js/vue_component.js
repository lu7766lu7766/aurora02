Vue.component('concurrent-calls', {
    props: ['values', 'name', 'id', 'calloutid', 'value'],
    template: '<select :id="id" :name="name" @change="change($event.target.value, calloutid)" @focus="focus" @blur="blur" :value="value" >' +
    '<option v-for="value in values" :value="value">每１秒{{value}}通</option>' +
    '</select>',
    methods: {
        change: function (value, calloutid) {
            this.$emit('change', value, calloutid)
        },
        focus: function () {
            this.$emit('focus')
        },
        blur: function () {
            this.$emit('blur')
        }
    }
})

Vue.component('delete-btn', {
    props: {
        value: {
            type: String,
            default: 'Delete'
        },
        alert: {
            type: String,
            default: '刪除確認'
        }
    },
    template: '<button ref="dBtn" type="button" class="btn btn-danger" >{{value}}</button>',
    methods: {},
    mounted: function () {
        $(this.$el).confirm({
            text: this.alert,
            confirm: function (button) {
                this.$emit('click')
            }.bind(this),
            post: true,
            confirmButton: "確定",
            cancelButton: "取消",
            confirmButtonClass: "btn-danger",
            cancelButtonClass: "btn-default"
        });
    }
})

Vue.component('date-time-picker', {
    props: ['date', 'time', 'type'],
    template: '<div class="input-group">' +
    '<input v-if="type == \'datepicker\' || type == \'datetimepicker\'" type="text" class="form-control" :value="date" ref="datePicker" >' +
    '<span v-if="type == \'datetimepicker\'"class="input-group-addon"> </span>' +
    '<input v-if="type == \'timepicker\' || type == \'datetimepicker\'" type="text" class="form-control" :value="time" ref="timePicker" >' +
    '</div>'
    ,
    methods: {},
    mounted: function () {
        $(this.$refs.datePicker).datetimepicker({
            timepicker: false,
            format: 'Y/m/d',
            onChangeDateTime: function (currentTime, $input) {
                //console.log('update:date', $input.val())
                this.$emit('update:date', $input.val())
            }.bind(this),
            scrollMonth: false,
            scrollInput: false
        })
        $(this.$refs.timePicker).datetimepicker({
            datepicker: false,
            format: 'H:i:00',
            step: 30,
            onChangeDateTime: function (currentTime, $input) {
                //console.log('update:time', $input.val())
                this.$emit('update:time', $input.val())
            }.bind(this),
            scrollMonth: false,
            scrollInput: false
        })
    }
})
