var react = {
    ready:function(todo)
    {
        var timer = setInterval(function(){
            if($("[data-reactid]").length)
            {
                todo();
                clearInterval(timer);
            }
        },500);
    }
};

react.ready(function(){
    $("tr[data-update]").each(function(){

        $(this).click(function(){

            $(this).find("[data-bind]").each(function(){
                var is_index = $(this).is("[data-index]")
                $target_input = $("[name='"+$(this).attr("data-bind")+"']");
                var value = $(this).html();
                $target_input.val(value);
                //console.log($(this).html())
                if(is_index)
                {
                    $target_input.attr({"readonly": true}).css("background", "gray").change(function()
                    {
                        this.value = value;
                    })
                }
            })
            //
        })
        $(this).find("td:not(:has(input))").click(function(){
            alertify.alert('進入編輯狀態!');
        })
    })

    $(".delete_btn").confirm({
        text: "刪除確認",
        confirm: function(button) {
            $("#status").val("delete");
            $(button).Submit();
        },
        post:true,
        confirmButton: "確定",
        cancelButton: "取消",
        confirmButtonClass: "btn-danger",
        cancelButtonClass: "btn-default"
    });

    $(".delete_all_btn").confirm({
        text: "確定要全部刪除",
        confirm: function(button) {
            $("#status").val("deleteAll");
            $("form").validate().cancelSubmit = true;
            $(button).Submit();
        },
        post:true,
        confirmButton: "確定",
        cancelButton: "取消",
        confirmButtonClass: "btn-danger",
        cancelButtonClass: "btn-default"
    });
});

