<template>
	<li>
		<a :href="path" class="item-content item-link" v-if="path">
			<div class="item-media"><i class="icon iconfont" v-html="icon"></i></div>
			<div class="item-inner">
					<div class="item-title">{{title}}</div>
					<div class="item-after">{{newAfter}}</div>
			</div>
		</a>
		<span class="item-content item-link" v-else @click="viewAppPage(funType)">
			<div class="item-media"><i class="icon iconfont" v-html="icon"></i></div>
			<div class="item-inner">
					<div class="item-title">{{title}}</div>
					<div class="item-after">{{newAfter}}</div>
			</div>
		</span>
	</li>
</template>
<script>
	import { Toast } from 'mint-ui'
	export default{
		props: ['path', 'icon', 'title', 'after', 'funType'],
		computed: {
			newAfter(){
				switch(this.title){
					case '等级':
						return this.after + '级';
						break;
					case '账户':
						return this.after + '钻石';
						break;
					case '认证':
						if(this.after == 0){
							return '未认证';
						}
						else if(this.after == 1){
							return '普通认证';
						}
						else{
							return '企业认证';
						}
						break;
					default:
						return this.after;
				}

			}
		},
		methods:{
			viewAppPage(fun_type){
				let json = {
					"fun_type" : fun_type
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
	.list-block .item-media .iconfont{
		color: #68d3b7;
	}
	.list-block .item-after{
		color: lighten(@color-theme, 10%);
	}
</style>