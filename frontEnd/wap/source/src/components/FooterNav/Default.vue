<template>
  	<div class="footer">
  		<div class="footer-menu flex-box">
	  		<router-link class="menu-a flex-1" :to="{path: '/index', replace: true}">
	  			<i class="iconfont">&#xe63a;</i>
	  		</router-link>
	  		<span class="menu-a menu-play" @click="live"><i class="iconfont">&#xe604;</i></span>
	  		<router-link class="menu-a flex-1" :to="{path: '/uc_index', replace: true}">
	  			<i class="iconfont">&#xe622;</i></span>
	  		</router-link>
  		</div>	
  	</div>
</template>
<script>
	import { Toast } from 'mint-ui'
  	export default{
    	data(){
  			return {}
    	},
    	mounted(){
    	},
    	methods: {
	      	live(){
	  			// 我要直播
	      		let json = {
					"fun_type" : 1
				}
		        json = JSON.stringify(json);
		        console.log(json);
		        try{
			  		App.fw_common(json);
			  	}
			  	catch(e){
			  		Toast('SDK调用失败');
			  	}
	      	}
	    }
	}
</script>
<style lang="less" scoped>
@import (once) "../../assets/css/variable.less";
.footer{
	position: absolute;
	width: 100%;
	height: 2rem;
	background: #fff;
	line-height: 2rem;
	bottom: 0;
	left: 0;
	z-index: 21;

	& .footer-menu{
		width: 100%;
		height: 2rem;
		background:@color-theme; 
		line-height: 2rem;
		text-align: center;

		& .menu-a{
			color: #fff;
			
			& .iconfont{
				font-size: 1.2rem;
				opacity: 0.6;
			}
			&.router-link-active{
				& .iconfont{
					opacity: 1;
				}
			}
		}
		& .menu-play{
			width: 2.5rem;
			height: 2.5rem;
			line-height: 2.5rem;
			border-radius: 50%;
			margin-top: -0.5rem;
			background:@color-theme; 
			opacity: 1;
			& .iconfont{
				font-size: 1.8rem;
				color: #fff;
			}
		}
	}
}
.flex-box{
  display:-webkit-box;
  display:-webkit-flex;
  display: flex;
}
.flex-1{
  -webkit-flex:1;
  -webkit-box-flex:1;
  flex:1;
}
</style>