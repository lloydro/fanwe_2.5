<template>
  <div class="header">
  	<div class="header-nav">
  		<div class="nav-a router-link-active">热门</div>
  	</div>
<!--   	<span class="nav-serach nav-icon iconfont">&#xe603;</span>
  	<span class="nav-msg nav-icon iconfont">&#xe602;</span> -->
  </div>
</template>
<script>
  export default{
    data(){
      return {}
    },

    mounted(){
    },
    methods: {

    }
  }
</script>
<style lang="less" scope>
@import (once) "../../assets/css/variable.less";
.header{
	position: absolute;
	width: 100%;
	height: 2rem;
	background: #fff;
	line-height: 2rem;
	top: 0;
	left: 0;
	z-index: 21;

	& .header-nav{
		width: 100%;
		height: 2rem;
		background:#fff; 
		line-height: 2rem;
		text-align: center;

		& .nav-a{
			display: inline-block;
			color: @fc-main;
			padding: 0 0.1rem;
			font-size: 0.75rem;

			
			&.router-link-active{
				position: relative;
				&:after{
					display: block;
					content: "";
					width: 100%;
					height: 0.1rem;
					background: @color-theme;
					position: absolute;
					bottom: 0;
					left: 0;
					border-radius: 0.05rem;
				}
			}
		}
	}
	& .nav-icon{
		position: absolute;
		top: 0;
		padding: 0 0.5rem;
		height: 2rem;
		line-height: 2rem;
		color: @color-theme;
		font-size: 0.8rem;
	}
	& .nav-serach{
		left: 0rem;
	}
	& .nav-msg{
		right: 0;
	}
}
</style>