<template>
	<div class="active-text">
		<div class="text-top">
			<img src="../assets/img/bg-text-top.jpg" />
		</div>
		<div class="text-main">
			<div class="avatar">
				<img :src="avatar" />
			</div>
			<div class="title">hi，我是{{nickName}}！#</div>
			<div class="text" v-text="text" v-show="text"></div>
		</div>
		<div class="text-footer">
			<img src="../assets/img/bg-text-bottom.jpg" />
		</div>
	</div>
</template>
<style lang="less" scoped>
	.active-text{
		background-repeat: no-repeat;
		background-position: 0 0;
		background-size: 100% auto;
	}
	.text-top img{
		display: block;
	}
	.text-main{
		padding: 15px 40px 0;
		text-align: center;
		background:url(../assets/img/bg-text-middle.jpg) repeat-y 0 0;
		background-size: 100% auto;
		display: flex;
		flex-direction: column;
		align-items: center;
		.avatar{
			width: 60px;
			height: 60px;
			border-radius: 50%;
			overflow: hidden;
			img{
				width: 100%;
				height: 100%;
				border-radius: 50%;
				overflow: hidden;
			}
		}
		.title{
			margin-top: 15px;
		}
		.text{
			width: 60%;
			font-size:15px;
			margin-top:25px;
		}
	}
</style>
<script>
	export default{
	  	props: ['avatar', 'nickName', 'text'],
	  	data(){
	  		return {
	  			init_avatar: require('@/assets/img/img-avatar.jpg')
	  		}
	  	}
	}
</script>