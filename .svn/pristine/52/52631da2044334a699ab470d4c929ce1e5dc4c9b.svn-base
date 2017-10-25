/**
 * 原生trim() 基于IE9以下兼容
 */
if (!String.prototype.trim) {
  	String.prototype.trim = function () {
    	return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
  	};
}

/** 
 * 删除数组指定id的值删除当前对象
 */
Array.prototype.del = function(filter){
	var idx = filter;
	if(typeof filter == 'function'){
		for(var i=0;i<this.length;i++){
			if(filter(this[i],i)) idx = i;
		}
	}
	this.splice(idx,1)
};

/**
 * 删除数组指定的某个元素
 */
Array.prototype.remove = function(val) {
	var index = this.indexOf(val);
	if (index > -1) {
		this.splice(index, 1);
	}
};

/**
 * 检测操作
 * @type {Object}
 */
var $checkAction = {

	/**
	 * 检测字符串最小长度
	 * @param  {String}  value  指定字符串
	 * @param  {Number}  length 指定长度
	 * @param  {Boolean} isByte 是否字节检测
	 * @return {Boolean}         true: 该字符串不小于指定长度; false: 该字符串小于指定长度
	 */
	minLength: function(value, length , isByte){
		var strLength = value.trim().length;
	    if(isByte)
	        strLength = $objAction.getStringLength(value);
	        
	    return strLength >= length;
	},

	/**
	 * 检测字符串最大长度
	 * @param  {String}  value  指定字符串
	 * @param  {Number}  length 指定长度
	 * @param  {Boolean} isByte 是否字节检测
	 * @return {Boolean}         true: 该字符串不大于指定长度; false: 该字符串大于指定长度
	 */
	maxLength: function(value, length , isByte){
		var strLength = value.trim().length;
	    if(isByte)
	        strLength = $objAction.getStringLength(value);
	        
	    return strLength <= length;
	},

	/**
	 * 检测手机号是否合法
	 * @param  {Number} value 指定手机号
	 * @return {Boolean}       true: 合法; false: 不合法
	 */
	checkMobilePhone: function(value){
		if(value.trim()!='')
	        return /^1[34578]\d{9}$/.test(value.trim());
	    else
	        return false;
	},

	/**
	 * 	检测邮箱地址是否合法
	 * @param  {String} val 指定邮箱地址
	 * @return {Boolean}     true: 合法; false: 不合法
	 */
	checkEmail: function(val){
		var reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/; 
		return reg.test(val);
	},

	/**
	 * 检测数值是否为整数
	 * @param  {Number} val 指定数值
	 * @return {Boolean}     true: 是整数; false: 不是整数
	 */
	checkint: function(val){
		return val%1 === 0
	},

	/**
	 * 检测指定对象是否为空
	 * @param  {任意类型} val 指定对象
	 * @return {Boolean}     true: 为空; false: 不为空
	 */
	checkEmpty: function(val){
	    switch (typeof val){
	        case 'undefined' : return true;
	        case 'string'    : if($.trim(val).length == 0) return true; break;
	        case 'boolean'   : if(!val) return true; break;
	        case 'number'    : if(0 === val) return true; break;
	        case 'object'    :
	            if(null === val) return true;
	            if(undefined !== val.length && val.length==0) return true;
	            for(var k in val){return false;} return true;
	            break;
	    }
	    return false;
	},

	/**
	 * 检测身份证是否合法
	 * @param  {任意类型} val 指定对象
	 * @return {Boolean}     true: 为空; false: 不为空
	 */
	checkID: function(val){
	   	if(val.trim()!='')
	        return /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/.test(val.trim());
	    else
	        return false;
	}

},

/**
 * 对象值操作
 * @type {Object}
 */
$objAction = {
	/**
	 * 从数组中删除指定值元素
	 * @param  {Array} arr 指定数组
	 * @param  {[type]} val 要删除的元素
	 * @return {Array}     返回删除后的数组
	 */
	removeByValue: function(arr, val){
	 	for(var i=0; i<arr.length; i++) {
	    	if(arr[i] == val) {
	      		arr.splice(i, 1);
	      		break;
	    	}
  		}
	},

	/**
	 * 获取字符串长度
	 * @param  {String} str 指定字符串
	 * @return {Number}     返回该字符串长度
	 */
	getStringLength: function(str){
	 	str = str.trim();
	    if(str=="")
	        return 0; 
	        
	    var length=0; 
	    for(var i=0;i <str.length;i++) 
	    { 
	        if(str.charCodeAt(i)>255)
	            length+=2; 
	        else
	            length++; 
	    }
	    return length;
	},

	/**
	 * 获取地址栏请求参数
	 * @param  {String} name 指定地址栏参数属性
	 * @return {[type]}      返回参数属性值
	 */
	getQueryString: function(name) {
     	var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     	var r = window.location.search.substr(1).match(reg);
     	if(r!=null)return  unescape(r[2]); return null;
	},

	/**
	 * 限制只能输入金额
	 * @param  {[type]} th 指定对象
	 * @return {[type]}    [description]
	 */
	amount: function(th){
	    var regStrs = [
	        ['^0(\\d+)$', '$1'], //禁止录入整数部分两位以上，但首位为0
	        ['[^\\d\\.]+$', ''], //禁止录入任何非数字和点
	        ['\\.(\\d?)\\.+', '.$1'], //禁止录入两个以上的点
	        ['^(\\d+\\.\\d{2}).+', '$1'] //禁止录入小数点后两位以上
	    ];
	    for(i=0; i<regStrs.length; i++){
	        var reg = new RegExp(regStrs[i][0]);
	        th.value = th.value.replace(reg, regStrs[i][1]);
	    }
	}
},

/**
 * ajax公用封装
 * @type {Object}
 */
$handleAjax = {
	ajax: function(options){
        // 利用了jquery延迟对象回调的方式对ajax封装，使用done()，fail()，always()等方法进行链式回调操作
        // 如果需要的参数更多，比如有跨域dataType需要设置为'jsonp'等等，也可以不做这一层封装，还是根据工程实际情况判断吧，重要的还是链式回调

        return $.ajax({
            url: options.url+'&itype=society_index',
            data: options.data,
            type: options.type,
            dataType: options.dataType,
            beforeSend:function(){
            	options.beforeSendCallBack && (typeof options.beforeSendCallBack == 'function') && options.beforeSendCallBack.call(this);
            },
            complete: function(){
            	options.completeCallBack && (typeof options.completeCallBack == 'function') && options.completeCallBack.call(this);
            }
        });
    },
  	handle: function(options){
  		var op = { url:'', data:{}, type:'POST', dataType:'json', isTip:true, showIndicator:true, beforeSendCallBack:null, completeCallBack:null };
		options = $.extend({},op, options);

		if(options.dataType == 'html'){
			options.data.post_type = null;
		}
		else{
			options.data.post_type = 'json';
		}
		
        return this.ajax(options).then(function(result){
            // 成功回调

            if(options.isTip){
            	if(result.status == 1){
            		return result.error || '操作成功'; // 直接返回要处理的数据，作为默认参数传入之后done()方法的回调
                }
                else{
                    return $.Deferred().reject(result.error || '操作失败'); // 返回一个失败状态的deferred对象，把错误代码作为默认参数传入之后fail()方法的回调
                }

            }
            else{
                return result; // 直接返回要处理的数据，作为默认参数传入之后done()方法的回调
            }
        }, function(err){
            // 失败回调
            return err || '请求出错！';
            // console.log(err.status); // 打印状态码
        });
    }
},

/**
 * 发送验证码
 * @type {Object}
 */
$sendVerifyCode = {
	is_disabled: false,		//当前对象是否有效
	code_lefttime: 0,		//倒计时（秒）
	code_timeer: null,		//定时器
	options:null,
	send: function(options){
    	// 发送验证码
        var self = this;
		var op = { url:APP_ROOT+"/weixin/index.php?ctl=date&act=send_mobile_verify", el:null, mobile:null };
		self.options = $.extend({},op, options);

        if(self.is_disabled){
            return false; 
        }
        else{
            $handleAjax.handle({
	  			url: self.options.url,
	  			data: { mobile:self.options.mobile },
	  			isTip: false
	  		}).done(function(result){
  			 	if(result.status == 1){
  			 		$.toast('验证码已发送');
                    // 验证码倒计时
                    self.code_lefttime = 60;
                    self.leftTimeFun();
                }
                else{
                	$.toast(result.error || '请求出错');
                }
		    }).fail(function(err){
		        $.toast(err);
		    });
        }
    },
    leftTimeFun: function(){
    	// 验证码倒计时
        var self = this, options = self.options, $el = document.getElementById(options.el);
        clearTimeout(options.code_timeer);
        $el.style.color = "#999";
        $el.style.backgroundColor = "#ccc";
        $el.innerHTML = "重新发送 "+self.code_lefttime;
        self.code_lefttime--;
        if(self.code_lefttime>0){
        	$el.setAttribute("disabled","disabled");  
            self.is_disabled=true;
            self.code_timeer = setTimeout(function(){self.leftTimeFun();},1000);
        }
        else{
            self.code_lefttime = 60;
            self.is_disabled=false;
            $el.removeAttribute("disabled");
            $el.style.color = "#fff";
        	$el.style.backgroundColor = "#f1383f";
            $el.innerHTML = "发送验证码";
        }
    }
}