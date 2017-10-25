/**
 * Created by Administrator on 2017/6/5.
 */
$(document).ready(function(){
    //check_user_info();

    $("input[name='binding_mobile']").bind('blur', function() {
        check_user_info();
    });

});

function check_user_info(){
    var binding_mobile= $("input[name='binding_mobile']").val();
    var binding_user_id_old= $("input[name='binding_user_id_old']").val();
    if(binding_mobile ==''){
        //$(".err_span").html('');
        //$(this).parent(".item_input").append("<span class='tip_span err_span' style='color:#ff2200'>请输入手机号码</span>");
        alert("请输入手机号码");
        $("input[name='binding_mobile']").val('');
        $("#binding_user_id").html("");
        return false;
    }
    var myreg = /^(1[0-9]{10})$/;
    if(!myreg.test(binding_mobile))
    {
        //$(".err_span").html('');
        //$(this).parent(".item_input").append("<span class='tip_span err_span' style='color:#ff2200'>请输入有效的手机号码</span>");
        alert("请输入有效的手机号码");
        $("input[name='binding_mobile']").val('');
        $("#binding_user_id").html("");
        return false;
    }

    var query=new Object();
    query.binding_mobile=binding_mobile;
    query.binding_user_id_old=binding_user_id_old;
    query.ajax=1;
    $.ajax({
        url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=check_user",
        data: query,
        dataType: "json",
        success: function(obj){
            isSearch = false;
            if(obj.status=='1')
            {
                //alert("有效会员");
                $("#binding_user_id").html("ID："+obj.user.id+", 呢称："+obj.user.nick_name);
            }
            else if(obj.status=='0')
            {
                alert(obj.info);
                $("input[name='binding_mobile']").val('');
                $("#binding_user_id").html("");

            }

        }
    });
}
