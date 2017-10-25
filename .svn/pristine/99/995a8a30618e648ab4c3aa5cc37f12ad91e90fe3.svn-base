"use strict";

// 登录成功后个人资料
(function () {
	var a_width = $(".block-search-user").outerWidth();
	var userAgent = navigator.userAgent.toLowerCase();
	// Figure out what browser is being used 
	jQuery.browser = {
		version: (userAgent.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/) || [])[1],
		safari: /webkit/.test(userAgent),
		opera: /opera/.test(userAgent),
		msie: /msie/.test(userAgent) && !/opera/.test(userAgent),
		mozilla: /mozilla/.test(userAgent) && !/(compatible|webkit)/.test(userAgent)
	};
	if ($.browser.msie) {
		a_width = a_width - 4;
	}
	var _right = a_width - 23;
	var _width = a_width + 5;

	$(".block-search-user").hover(function () {
		$(".search-user").addClass("hover");
		$(".search-user-name i.up").css("display", "inline-block");
		$(".search-user-name i.down").css("display", "none");
	}, function () {
		$(".search-user").removeClass("hover");
		$(".search-user-name i.up").css("display", "none");
		$(".search-user-name i.down").css("display", "inline-block");
	});
	$(".search-user-top").css("right", _right + "px");
	$(".null").css("width", _width + "px");
})();

avalon.ready(function () {
	var search_key = $("input[name='search_key']").val();
	// 页面头部搜索
	var vm_login_tip = avalon.define({
		$id: "login_tip",
		search_key: search_key,
		search: function search() {
			// 头部搜索
			key = vm_login_tip.search_key;
			location.href = APP_ROOT + "/index.php?ctl=live&act=search&key=" + key;
		}
	});
	avalon.scan(document.getElementById('login_tip'));

	$("input[name='search_key']").bind('keypress', function (event) {
		console.log(111);
		if (event.keyCode == "13") vm_login_tip.search();
	});
});

// 保存图片
function save_img(w, h, el, ajax_url) {
	var scale_w = parseFloat(w / h);
	var el = el;
	scale_w = scale_w.toFixed(1);
	scale = scale_w + '/1';
	bind_CropAvatar(scale, function () {
		var image_url = $("input[name='" + el + "']").val();
		if (!image_url) {
			return;
		}
		var data = {};
		data[el] = image_url;
		handleAjax.handle(ajax_url, data).done(function (msg) {
			$.showSuccess(msg);
		}).fail(function (err) {
			$.showErr(err);
		});
	});
}
$(function () {
	$(".J-anchor").click(function () {
		$(this).parent().toggleClass('act');
	});
});