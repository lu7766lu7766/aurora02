$(document).ready(function () {
    /*------------ submit btn control --------------*/
    $(".delete_btn").confirm({
        text: "刪除確認",
        confirm: function (button) {
            $("#status").val("delete");
            $(button).Submit();
        },
        post: true,
        confirmButton: "確定",
        cancelButton: "取消",
        confirmButtonClass: "btn-danger",
        cancelButtonClass: "btn-default"
    });

    $(".delete_all_btn").confirm({
        text: "確定要全部刪除",
        confirm: function (button) {
            $("#status").val("deleteAll");
            $(button).Submit();
        },
        post: true,
        confirmButton: "確定",
        cancelButton: "取消",
        confirmButtonClass: "btn-danger",
        cancelButtonClass: "btn-default"
    });

    $(".post_btn").click(function () {
        $("#status").val("post");
        $(this).Submit();
    });

    $(".get_btn").click(function () {
        $("#status").val("get");
        $(this).Submit();
    })

    $(".update_btn").click(function () {
        $("#status").val("update");
        $(this).Submit();
    });

    $(".search_btn").click(function () {
        $("#status").val("search");
        $(this).Submit();
    });

    /*-----------------------------------*/

    function menuInit() {
        $(".lastmenu").each(function () {
            $this = $(this);
            if ($this.children("li").length == 0) {
                $this.parent().remove();
            }
        });

        $(".submenu").each(function () {
            $this = $(this);
            if ($this.children("li").length == 0) {
                $this.parent().remove();
            }
        });

        $("#sidebar-wrapper > ul > li > a").click(function () {
            var _this = $(this);
            if (_this.next("ul").length > 0) {
                if (_this.next().is(":visible")) {

                    _this.html(_this.html().replace("▼", "►")).next().hide();
                } else {

                    _this.html(_this.html().replace("►", "▼")).next().show();
                }

                return false;
            }
        });

        $(".submenu > li > a").click(function () {
            var _this = $(this);
            if (_this.next("ul").length > 0) {
                if (_this.next().is(":visible")) {

                    _this.html(_this.html().replace("▼", "►")).next().hide();
                } else {

                    _this.html(_this.html().replace("►", "▼")).next().show();
                }

                return false;
            }
        });

    }

    menuInit();

    $("#userSelet").change(function () {
        var $this = $(this);
        var permission = $this.find(":selected").attr("permission");
        $.post(folder + "index/chgUser", {
            choiceUser: $this.val(),
            choicePermission: permission
        }, function (data) {
            console.log(data)
            location.reload();
            //$("#sidebar-wrapper").html(data);
            //menuInit();
        });

        $(".user-choice").html($this.val());

        if (!$("#drop_btn").is(":hidden")) {
            $("#drop_btn").trigger("click");
        }
    });

    //if($(".now").length>0)
    //{
    //    setInterval(function(){$(".now").html(new Date().Format("Y-m-d H:i:s"))},1000);
    //}

    $(".bootstrap-switch").bootstrapSwitch();

    $(".checkAll").click(function () {
        $this = $(this);
        $("input[name='" + $this.attr("for") + "']").prop("checked", $this.prop("checked"));
    })

    $.each($("[redirect]>*:not(:has(input:not([readonly]), button))"), function () {
        $(this).click(function () {
            var url = $(this).parent().attr("redirect");
            redirect(url);
        })
    });

    /**
     * datetimepicker
     * */
    if ($('.time-picker').length > 0) {
        $.each($('.time-picker'), function () {
            var $this = $(this)
            if ($this.prop("readonly")) {
                $this.prop("readonly", false)
                $this.keypress(function (event) {
                    event.preventDefault();
                });
            }
            $this.datetimepicker({
                datepicker: false,
                format: $this.attr("format") || 'H:i:00',
                step: parseInt($this.attr("step")) || 30,
                onChangeDateTime: function (currentTime, $input) {

                },
                scrollMonth: false,
                scrollInput: false
            });
        })

    }

    if ($('.date-picker').length > 0) {
        $.each($('.date-picker'), function () {
            var $this = $(this)
            if ($this.prop("readonly")) {
                $this.prop("readonly", false)
                $this.keypress(function (event) {
                    event.preventDefault();
                });
            }
            $this.datetimepicker({
                timepicker: false,
                format: 'Y/m/d',
                scrollMonth: false,
                scrollInput: false
            });
        })
    }

    if ($('.datetime-picker').length > 0) {
        $('.datetime-picker').datetimepicker({
            format: 'Y/m/d H:i:00',
            step: 30,
            scrollMonth: false,
            scrollInput: false
        });
    }

    /**
     * bootstrap datetimepicker
     * */
    if ($('.bootstrap-time-picker').length > 0 || $('.bootstrap-date-picker').length > 0) {
        $.fn.datetimepicker.dates['en'] = {
            days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"],
            daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日"],
            daysMin: ["日", "一", "二", "三", "四", "五", "六", "日"],
            months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
            monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
            today: "今天",
            suffix: [],
            meridiem: ["上午", "下午"]
        }
        if ($('.bootstrap-time-picker').length > 0) {
            $.each($('.bootstrap-time-picker'), function () {
                var $this = $(this)
                if ($this.prop("readonly")) {
                    $this.prop("readonly", false)
                    $this.keypress(function (event) {
                        event.preventDefault();
                    });
                }
                $this.datetimepicker({
                    startView: 1,
                    format: $this.attr("format") || 'hh:ii:00',
                    autoclose: true,
                    minuteStep: parseInt($this.attr("step")) || 5,
                    scrollMonth: false,
                    scrollInput: false
                });
            })
        }

        if ($('.bootstrap-date-picker').length > 0) {

            $.each($('.bootstrap-date-picker'), function () {
                var $this = $(this)
                if ($this.prop("readonly")) {
                    $this.prop("readonly", false)
                    $this.keypress(function (event) {
                        event.preventDefault();
                    });
                }
                $this.datetimepicker({
                    startView: 2,
                    minView: 2,
                    format: $this.attr("format") || 'yyyy/mm/dd',
                    autoclose: true,
                    todayBtn: true,
                    scrollMonth: false,
                    scrollInput: false
                });
                $('.datetimepicker-days .switch').removeClass('switch')
            })
        }
    }

    /**
     * 只允許輸入數字
     * */
    $(".num-only").keyup(function () {
        if ($(this).is(".rigorous"))
            this.value = this.value.replace(/[^\d]+/g, '');
        else
            this.value = this.value.replace(/[^\-\d]+/g, '');
    })

    /**
     * 只允許輸入英文及數字
     * */
    $(".engnum-only").keyup(function () {
        this.value = this.value.replace(/[^\w\.\/]/ig, '');
    })

    $(".today-date, .today-time").each(function () {
        if (this.value == "") {
            this.value = $(this).hasClass("today-date") ? new Date().Format("Y/m/d") : new Date().Format("H:i:s");
        }
    });

    $(".tomorrow-date,.tomorrow-time").each(function () {
        if (this.value == "") {
            this.value = $(this).hasClass("tomorrow-date") ? new Date().AddDay(1).Format("Y/m/d") : new Date().AddDay(1).Format("H:i:s");
        }
    });

    $(".yesterday-date,.yesterday-time").each(function () {
        if (this.value == "") {
            this.value = $(this).hasClass("yesterday-date") ? new Date().AddDay(-1).Format("Y/m/d") : new Date().AddDay(-1).Format("H:i:s");
        }
    })

    $(".readonly").attr("readonly", true).css("background-color", "#999999");

    /*--------page control--------*/

    var page = parseInt($("#page_select").val());
    var prev_page = page - 1;
    var next_page = page + 1;
    var last_page = parseInt($("#last_page").val() - 1);

    $(".first-page").css("cursor", "pointer").click(function () {
        $("#page").val(0);
        $(this).Submit();
    });

    $(".prev-page").css("cursor", "pointer").click(function () {
        $("#page").val(prev_page < 0 ? 0 : prev_page);
        $(this).Submit();
    });

    $(".next-page").css("cursor", "pointer").click(function () {

        $("#page").val(next_page > last_page ? last_page : next_page);
        $(this).Submit();
    });

    $(".last-page").css("cursor", "pointer").click(function () {
        $("#page").val(last_page);
        $(this).Submit();
    });

    $(".page-select").change(function () {
        $("#page").val($(this).val());
        $(this).Submit();
    })

    $(".per-page").blur(function () {
    });
    /*----------------------------*/
});

function redirect(url) {
    url = url || "indel/indel";
    url = folder + url;
    location.href = url;
}

////////////////////////////////////////////////////////

//function一定要大寫，可能是跟原始funciton做出區別
Date.prototype.Format = function (fmt) {
    var o = {
        "Y": this.getFullYear(),
        "y+": this.getFullYear(),
        "m": this.getMonth() + 1,                 //
        "d": this.getDate(),                    //
        "H": this.getHours(),                   //
        "i": this.getMinutes(),                 //
        "s": this.getSeconds(),                 //
        "S": this.getMilliseconds()             //
    };

    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt))
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ?
                o[k].toString().length == 1 ? "0" + o[k] : o[k] :
                o[k].toString().substr(o[k].toString().length - RegExp.$1.length));
    return fmt;
};

Date.prototype.AddDay = function (day) {
    return new Date(this.getTime() + 1000 * 60 * 60 * 24 * day);
};

String.prototype.padLeft = function (prefix, len) {
    //alert(this);
    if (this.length >= len)
        return this;
    else
        return (prefix + this).padLeft(prefix, len);
};

Array.prototype.distinc = function () {
    return this.filter(function (el, i, arr) {
        return arr.indexOf(el) === i
    })
}

////////////////////////////////////////////////////////

jQuery.fn.extend({
    Submit: function () {
        return this.each(function () {
            $submit = $("<button></button>").hide();
            $(this).after($submit);
            $submit.trigger("click");
        });
    },
    fadeInOut: function () {
        return this.each(function (time) {
            $this = $(this)
            time = time || 3000;
            $this.removeClass("hidden");
            $this.fadeIn(1000);
            setTimeout(function () {
                $this.fadeOut(1000)
            }, time);
        })
    }
});

//////////////////////////////////////////////////////// cookie

function setCookie(name, value)//两个参数，一个是cookie的名子，一个是值
{
    var Days = 30; //此 cookie 将被保存 30 天
    var exp = new Date();    //new Date("December 31, 9998");
    exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
    document.cookie = name + "=" + encodeURI(value) + ";expires=" + exp.toGMTString();
}

function getCookie(name)//取cookies函数
{
    var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
    if (arr != null) return decodeURI(arr[2]);
    return null;

}

function delCookie(name)//删除cookie
{
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval = getCookie(name);
    if (cval != null) document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
}


////////////////////////////////////////////////////////// js download

function download(filename, text) {
    var pom = document.createElement('a');
    pom.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    pom.setAttribute('download', filename);

    if (document.createEvent) {
        var event = document.createEvent('MouseEvents');
        event.initEvent('click', true, true);
        pom.dispatchEvent(event);
    }
    else {
        pom.click();
    }
}

String.prototype.br2nl = function () {
    return this.replace(/\<br(\s*)?\/?\>/ig, '\n')
}
