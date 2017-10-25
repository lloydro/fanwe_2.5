<template>
	<div class="user-top">
		<div class="user-top-info">
			<div class="avator">
				<img :src="user.head_image" class="avator" />
			</div>
			<div class="info">
				<p class="info-title">
					<span v-text="user.nick_name"></span>
					<span :class="[user.sex == 2 ? 'sex-women' : 'sex-man']"></span>
					<span class="grade" v-text="user.user_level"></span>
				</p>
				<p class="info-after">
					<span v-cloak>{{shortName}} {{user.id}}</span>
<!-- 					<span>查看编辑主页<i class="icon iconfont">&#xe632;</i></span> -->
				</p>
			</div>
		</div>
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
	@import (once) "../../assets/css/green.less";
	.user-top{
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		background-color: #fff;
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
				text-align: right;
				&:before{
					content: '\e603';
				}
			}
			.gift-total{
				width: 60%;
				text-align: center;
				flex-shrink: 0;
				&:before{
					content: '\e68d';
				}
			}
			.letter{
				text-align: left;
				&:before{
					content: '\e602';
				}
			}
		}
		.user-top-info{
			display: flex;
			width: 100%;
			padding: 0.75rem;
			.avator{	
				width: 3rem;
				height: 3rem;
				border-radius: 100%;
				margin-right: 0.75rem;
			}
			.info{
				width: 100%;
				p{
					line-height: 1.8;
				}
				.info-title{
					font-size: 0.9rem;
				}
				.info-after{
					display: flex;
					justify-content: space-between;
					color: @fc-light;
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
					font-size: 0.7rem;
				}
			}
			&:after{
				-webkit-transform: scaleY(.5);
    			transform: scaleY(.5);
				content: '';
			    position: absolute;
			    left: 0;
			    bottom: 0;
			    right: auto;
			    top: auto;
			    height: 1px;
			    width: 100%;
			    background-color: #e7e7e7;
			    display: block;
			    z-index: 15;
			    -webkit-transform-origin: 50% 100%;
			    transform-origin: 50% 100%;
			}
		}
		.sign{
			color: @fc-dark;
		}
		.total{
			padding: 0.9rem 0;
			color: #fff;
			width: 100%;
			display: flex;
			justify-content: center;
			border-top: 1px solid @bdcolor;
			color: @fc-main;
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
					border-right: 1px solid @bdcolor;
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