<template>
	<div class="user-top">
		<div class="user-top-bar">
<!-- 			<span class="search"></span> -->
			<span class="gift-total" v-cloak>送出{{user.use_diamonds}}</span>
<!-- 			<span class="letter"></span> -->
		</div>
		<img :src="user.head_image" class="avator" />
		<div v-cloak>{{shortName}}:{{user.id}}</div>
		<div>
			<span v-text="user.nick_name"></span>
			<span :class="[user.sex == 2 ? 'sex-women' : 'sex-man']"></span>
			<span class="grade" v-text="user.user_level"></span>
			<!-- <span class="edit"></span> -->
		</div>
		<div class="sign" v-text="user.signature"></div>
		<div class="total" v-cloak>
			<span @click="viewAppPage(2)">直播 {{user.video_count}}</span>
			<span @click="viewAppPage(3)">关注 {{user.focus_count}}</span>
			<span @click="viewAppPage(4)">粉丝 {{user.fans_count}}</span>
		</div>
	</div>
</template>
<script>
	import { Toast } from 'mint-ui'
	export default{
		props: ['user', 'shortName'],
		methods: {
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
			  		Toast("SDK调用失败");
			  	}
			}
		}
	}
</script>
<style lang="less" scoped>
	@import (once) "../../assets/css/variable.less";
	.user-top{
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		margin-top: 1rem;
		.user-top-bar{
			padding: 0 1rem;
			width: 100%;
			display: flex;
			justify-content: center;
			span{
				width: 20%;
				display: block;
				&:before{
					font-family: 'iconfont';
					font-size: 0.8rem;
					color: @color-theme;
					padding-right: 0.25rem;
				}
			}
			.search{
				text-align: left;
				&:before{
					content: '\e603';
				}
			}
			.gift-total{
				width: 60%;
				text-align: center;
				flex-shrink: 0;
				color: @fc-darker;
				&:before{
					content: '\e68d';
				}
			}
			.letter{
				text-align: right;
				&:before{
					content: '\e602';
				}
			}
		}
		
		.avator{	
			width: 5rem;
			height: 5rem;
			border-radius: 100%;
			margin-top: 0.5rem;
		}
		div{
			margin-top: 0.25rem;
		}
		.sign{
			margin-top: 0.5rem;
		}
		.sex-man{
			&:before{
				content: '\e65d';
				vertical-align: baseline;
				font-size: 0.7rem;
				font-family: 'iconfont';
				padding: 0.1rem;
				background-color: #10c9f5;
				color: #fff;
			}
		}
		.sex-women{
			&:before{
				content: '\e65e';
				vertical-align: baseline;
				font-size: 0.7rem;
				font-family: 'iconfont';
				padding: 0.1rem;
				background-color: #10c9f5;
				color: #fff;
			}
		}
		.grade{
			&:before{
				content: '\e641';
				vertical-align: baseline;
				font-family: 'iconfont';
				font-size: 0.7rem;
			}
			background-color: @color-theme;
			color: #fff;
			padding: 0.1rem 0.15rem 0.1rem 0.1rem;
			border-radius: 3px;
		}
		.edit{
			&:before{
				content: '\e61b';
				vertical-align: baseline;
				font-size: 0.7rem;
				font-family: 'iconfont';
				padding: 0.1rem;
				color: @color-red;
			}
		}
		.sign{
			color: @fc-darker;
		}
		.total{
			margin-top: 1.25rem;
			padding: 0.9rem 0;
			background-color: @color-theme;
			color: #fff;
			width: 100%;
			display: flex;
			justify-content: center;
			span{
				position: relative;
				width: 100%;
				text-align: center;
				&:after{
					position: absolute;
					content: '';
					display: block;
					width: 1px;
					height: 100%;
					border-right: 1px solid #fff;
					position: absolute;
					right: 0;
					top: 0;
				}
				&:last-child{
					&:after{
						content: none;
					}
				}
			}
		}
	}
</style>