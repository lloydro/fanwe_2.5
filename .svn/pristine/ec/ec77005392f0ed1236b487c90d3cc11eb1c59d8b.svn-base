'use strict';

var user = {
  showLoginLayer: function showLoginLayer() {
    // 登陆
    layer.load();
    $handleAjax.handle({
      url: TMPL_REAL + "/index.php?ctl=login&act=pop&tmpl_pre_dir=inc",
      isTip: false,
      dataType: 'html',
      data: {}
    }).done(function (result) {
      layer.closeAll();
      layer.open({
        type: 1,
        title: '登陆',
        skin: 'layui-layer-rim user-login-rim', //加上边框
        area: ['initial', '400px'], //宽高
        content: result
      });
    }).fail(function (err) {
      console.log(err);
    });
  },
  loginOut: function loginOut() {
    // 退出登陆
    $handleAjax.handle({
      url: APP_ROOT + "/index.php?ctl=login&act=logout",
      isTip: false
    }).done(function (result) {
      if (result.status == 1) {
        layer.msg(result.error, {
          time: 1000
        });
        setTimeout(function () {
          location.reload();
        }, 1000);
      } else {
        layer.msg(result.error, {
          time: 1000
        });
      }
    }).fail(function (err) {
      console.log(err);
    });
  },
  weixinLogin: function weixinLogin() {
    // 微信登陆
    $handleAjax.handle({
      url: APP_ROOT + "/index.php?ctl=login&act=weixin_login&tmpl_pre_dir=inc",
      isTip: false,
      dataType: 'html',
      data: {}
    }).done(function (result) {
      layer.open({
        type: 1,
        title: false,
        closeBtn: 0,
        shadeClose: true,
        skin: 'yourclass',
        content: result
      });
    }).fail(function (err) {
      console.log(err);
    });
  },
  societyWxLogin: function societyWxLogin() {
    // 公会微信登录
    $handleAjax.handle({
      url: APP_ROOT + "/index.php?ctl=society&act=wx_entry&tmpl_pre_dir=inc",
      isTip: false,
      dataType: 'html',
      data: {}
    }).done(function (result) {
      layer.open({
        type: 1,
        title: '微信登录',
        closeBtn: 0,
        shadeClose: true,
        skin: 'yourclass',
        content: result
      });
    }).fail(function (err) {
      console.log(err);
    });
  }
};

$(function () {
  $(".J-login").on('click', function () {
    user.showLoginLayer();
  });
  $("#J_loginOut").on('click', function () {
    user.loginOut();
  });
  $("#J_wxLogin").on('click', function () {
    user.weixinLogin();
  });
});