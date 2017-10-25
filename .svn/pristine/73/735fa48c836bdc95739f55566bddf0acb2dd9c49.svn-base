<template>
	<div class="active-container">
		<div class="main">
			<img src="../assets/img/img-activeOne-1.jpg" />
			<active-text :avatar="head_image" :nickName="nick_name"></active-text>
			<div class="from-group-block">
				<div class="from-group">
					<input type="number" name="" v-model="mobile" placeholder="请输入手机号码" class="form-input" />
				</div>
				<div class="from-group last">
					<input type="number" name="" v-model="verify_code" placeholder="请输入验证码" class="form-input" />
					<img src="../assets/img/btn_verifyCode.png" class="btn-verifyCode" @click="send_code" />
				</div>
			</div>
			<active-btn :btnImg="btnImg" :submit="invit"></active-btn>
		</div>
	</div>
</template>
<style lang="less">
	img{ max-width:100% }
	.active-container{
		position: absolute;
		width: 100%;
		min-height: 100%;	
		background-size:100% auto;
		background-color: #fed23d;
		background-repeat: no-repeat;
		text-align: center;
		.main{
			max-width: 750px;
			min-width: 320px;
			margin:0 auto;
		}
		.from-group-block{
			margin-top:20px;
		}
		.from-group{
			display: flex;
			width: 90%;
			margin: 0 auto 10px;
		}
		.from-group.last{
			margin-bottom: 0;
		}
		.form-input{
			display: block;
			border:0;
			width: 50%;
			height: 40px;
			padding:10px;
			flex:1;
		}
		.btn-verifyCode{
			display: block;
			height: 40px;
			margin-left: 10px;
		}
	}
</style>
<script>
	import api from '../config/api';
	import activeText from '../components/activeText.vue'
	import activeBtn from '../components/activeBtn.vue'
	import { Toast, Indicator, MessageBox } from 'mint-ui'
	export default{
	  	components: {
        	activeText,
        	activeBtn
	  	},
		data(){
			return{
				btnImg: require('@/assets/img/btn_success.png'),
				head_image: '',
				nick_name: '',
				mobile: '',
				from_nick_name: '',
				verify_code: '',
				is_disabled: false,
				scrollWatch: true
			}
		},
		beforeRouteEnter(to, from, next){
		 	next(vm => {
		    	vm.$store.commit('hideHeader', 1)
		    	vm.$store.commit('hideFooter', 1)
		    });
		},
		created(){
			this.get();
		},
		mounted(){
		 	$(window).scrollTop(0);
		    $(window).on('scroll', () => {
		      	if (this.scrollWatch) {
	           		//your code here
        		}
		    });
		},
		methods:{
			get(){
			// 抓取数据
				let self = this;
				self.axios.get(api.distributionInitRegister(),{
			    	params: {
						"share_id": self.$route.query.share_id
			    	}
			  	})
	        	.then(res => {
		    		let result = res.data;
		    		document.title = result.app_name || '方维直播';
		    		if(result.status == 1){
	                	this.head_image = result.head_image;
	                	this.nick_name = result.nick_name;
	                }
	                else{
	                	MessageBox(result.error);
	                }

		        })
		        .catch(err => console.log(err))
			},
			invit(){
			// 确定提交
				let self = this;
				if(self.check()){
					Indicator.open();
					self.axios.post(api.distributionRegister()+'&mobile='+self.mobile+'&verify_coder='+self.verify_code+'&share_id='+self.$route.query.share_id)
				  	.then(res => {
			    		let result = res.data;
			    		Indicator.close();
			    		if(result.status == 1){
			    			sessionStorage.setItem("app_down_url",result.app_down_url);
			    			Toast({message:result.error || '操作成功', iconClass: 'icon icon-success', duration: 1500});
			    			setTimeout(function(){self.$router.push({ path: '/register_result', query: { share_id: self.$route.query.share_id } })},2000);
		                }
		                else{
		                	Toast(result.error);
		                }
				  	})
				  	.catch(err => console.log(err))

				}
			},
			send_code: function(e){
	        // 发送验证码
				let self = this;
		        if(this.is_disabled){
		        	Toast('发送速度太快了');
		            return false; 
		        }
		        else{
		        	if(!self.mobile){
		        		Toast('请输入手机号'); return false;
		        	}
		        }
		        self.axios.get(api.get_verifycode(),{
	            	params:{
	            		mobile: self.mobile
	            	}
	            })
	        	.then(res => {
		    		console.log(res.data);
		    		let result = res.data;
		    		if(result.status == 1){
		    			Toast(result.error || '操作成功');
                    }
                    else{
                    	if(result.error){
                    		Toast(result.error || '操作失败');
                    	}
                    }

		        })
		        .catch(err => console.log(err))
		    },
		    code_lefttime_fuc: function(verify_name,code_lefttime){
	        // 验证码倒计时
		        clearTimeout(code_timeer);
		        $(verify_name).html("重新发送 "+code_lefttime);
		        code_lefttime--;
		        if(code_lefttime >0){
		            $(verify_name).attr("disabled","disabled");
		            self.is_disabled=true;
		            code_timeer = setTimeout(function(){self.code_lefttime_fuc(verify_name,code_lefttime);},1000);
		        }
		        else{
		            code_lefttime = 60;
		            self.is_disabled=false;
		            $(verify_name).removeAttr("disabled");
		            $(verify_name).html("发送验证码");
		        }
		    },
		    check(){
		    // 表单验证
		    	if(!this.mobile){
		    		Toast('请输入手机号');
					return false;
				}
				else if(!this.verify_code){
					Toast('请输入验证码');
					return false;
				}
				else{
					return true;
				}
		    }
		},
		destroyed () {
		    this.scrollWatch = false;
	  	},
	  	watch: {
	  		'$route' (to, from) {
	      		this.get();
		    }
	  	},
	  	computed: {
            isHideHeader () {
              return this.$store.state.isHideHeader
            }
        },
	}
</script>