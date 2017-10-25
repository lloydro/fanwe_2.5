'use strict';

var code_lefttime;
var code_timeer = null;
var vm = avalon.define({
    $id: "login",
    is_errorinput: false,
    is_disabled: false,
    login_type: 0,
    mobile: '',
    verify_coder: '',
    password: '',
    changeLoginType: function changeLoginType(login_type) {
        this.login_type = login_type;
        // avalon.scan(document.getElementById('login'));
    },

    login: function login() {
        // 登录
        var self = this;
        if (this.check()) {
            console.log(11);
            var form_data = self.login_type == 0 ? { mobile: self.mobile, verify_coder: self.verify_coder, type: 0 } : { mobile: self.mobile, password: self.password, type: 1 };
            $.ajax({
                url: APP_ROOT + "/mapi/index.php?ctl=login&act=do_login&itype=society_index",
                data: form_data,
                type: "POST",
                dataType: "json",
                success: function success(result) {
                    if (result.status == 1) {
                        layer.closeAll();
                        layer.msg(result.error || '操作成功', {
                            time: 1000
                        });
                        setTimeout(function () {
                            location.href = TMPL_REAL + "/index.php?ctl=society&act=user_manage";
                        }, 1000);
                    } else {
                        layer.msg(result.error || '操作失败');
                    }
                }
            });
        }
    },
    check: function check(el) {
        var self = this;
        // 验证表单
        if (this.login_type == 0) {
            if ($checkAction.checkEmpty(self.mobile)) {
                $(".errortip").html("手机号码不能为空");
                self.is_errorinput = true;
                return false;
            } else if (!$checkAction.checkMobilePhone(self.mobile)) {
                $(".errortip").html("手机号码格式错误");
                self.is_errorinput = true;
                return false;
            } else if (!$checkAction.maxLength(self.mobile, 11, true)) {
                $(".errortip").html("长度不能超过11位");
                self.is_errorinput = true;
                return false;
            } else {
                $(".errortip").html("");
                self.is_errorinput = false;
                return true;
            }
        } else {
            if ($checkAction.checkEmpty(self.mobile)) {
                $(".errortip").html("手机号码不能为空");
                self.is_errorinput = true;
                return false;
            } else if (!$checkAction.checkMobilePhone(self.mobile)) {
                $(".errortip").html("手机号码格式错误");
                self.is_errorinput = true;
                return false;
            } else if (!$checkAction.maxLength(self.mobile, 11, true)) {
                $(".errortip").html("长度不能超过11位");
                self.is_errorinput = true;
                return false;
            } else if ($checkAction.checkEmpty(self.password)) {
                $(".errortip").html("请输入密码");
                self.is_errorinput = true;
                return false;
            } else {
                $(".errortip").html("");
                self.is_errorinput = false;
                return true;
            }
        }
    },
    send_code: function send_code() {
        var countdown = 0;
        // 发送验证码
        if (vm.is_disabled) {
            layer.msg('发送速度太快了');
            return false;
        } else {
            var thiscountdown = $("#j-send-code");
            var query = new Object();
            query.mobile = vm.mobile;
            $.ajax({
                url: APP_ROOT + "/mapi/index.php?ctl=login&act=send_mobile_verify&itype=society_index",
                data: query,
                type: "POST",
                dataType: "json",
                success: function success(result) {
                    console.log(result);
                    if (result.status == 1) {
                        countdown = 60;
                        // 验证码倒计时

                        code_lefttime = 60;
                        vm.code_lefttime_fuc("#j-send-code", code_lefttime);
                        // $.showSuccess(result.info);
                        return false;
                    } else {
                        layer.msg(result.error);
                        return false;
                    }
                }
            });
        }
    },
    code_lefttime_fuc: function code_lefttime_fuc(verify_name, code_lefttime) {
        // 验证码倒计时
        clearTimeout(code_timeer);
        $(verify_name).html("重新发送 " + code_lefttime);
        code_lefttime--;
        if (code_lefttime > 0) {
            $(verify_name).attr("disabled", "disabled");
            vm.is_disabled = true;
            code_timeer = setTimeout(function () {
                vm.code_lefttime_fuc(verify_name, code_lefttime);
            }, 1000);
        } else {
            code_lefttime = 60;
            vm.is_disabled = false;
            $(verify_name).removeAttr("disabled");
            $(verify_name).html("发送验证码");
        }
    }
});
avalon.scan(document.getElementById('login'));