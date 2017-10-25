<template>
	<div class="listBox">
	  	<div class="hot t-line">
			热门直播
		</div>
		<div class="temp-list flex-box">
			<template v-if="list.length">
			  	<div class="temp-item" v-for="item in list">
			  		<div class="temp-img" @click="joinLive(item)">
			  			<div class="up-box"></div>
			  			<div class="img-box">
			  				<img :src="item.live_image">
			  			</div>
			  			<span class="state">
			  				<template v-if="item.live_in == 0">已停止</template>
			  				<template v-else-if="item.live_in == 1">直播</template>
			  				<template v-else-if="item.live_in == 2">创建直播</template>
			  				<template v-else-if="item.live_in == 3">回放</template>
			  			</span>
			  			<div class="address">
				  			<i class="iconfont">&#xe7a5;</i>
				  			<em class="city">{{item.city}}</em>
			  			</div>
			  			<p class="wathch"><span class="num">{{item.watch_number}}</span>人观看</p>
			  		</div>
			  		<div class="temp-inner">
			  			<p class="name">{{item.nick_name}}</p>
			  		</div>
			  	</div>
		  	</template>
		  	<template v-else>
		  		<div class="null-data">暂无任何直播</div>
		  	</template>
	  	</div>
	</div>
</template>
<script>
	import { Toast } from 'mint-ui'
	export default{
 		props: ['list'],
    	mounted() {

    	},
    	methods: {
    		joinLive(item){	
				// 加入房间
				let json = {
					"createrId" : item.user_id,
					"groupId" : item.group_id,
					"roomId" : item.room_id,
					"loadingVideoImageUrl" : item.live_image,
					"create_type" : item.create_type,
					"videoType" : item.video_type
				}
		        json = JSON.stringify(json);
		        try{
			  		App.join_live(json);
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
.listBox{
	background: #fff;
}
.temp-list{
	-webkit-box-sizing:border-box;
    box-sizing: border-box;
    flex-wrap: wrap;
	& .temp-item{
		width:50%;
		margin-bottom: 0.5rem;

		&:nth-child(odd){
			padding-right: 0.25rem;
			padding-left: 0.5rem;
		}
		&:nth-child(even){
			padding-left: 0.25rem;
			padding-right: 0.5rem;
		}
		& .temp-inner{
			position: relative;
			& .name{
				font-size: 0.6rem;
				line-height: 1.2rem;
				color: @fc-main;
				white-space: nowrap;
				text-overflow: ellipsis;
				overflow: hidden;
			}
		}
		& .temp-img{
			position: relative;
			border-radius: 0.2rem;
			overflow: hidden;

			& .state{
				position: absolute;
				z-index: 11;
				right: 0.5rem;
				top: 0.5rem;
				padding: 0 0.5rem;
				height: 1.2rem;
				line-height: 1.2rem;
				border-radius: 0.6rem;
				background: rgba(0,0,0,0.3);
				border: 0.1rem solid rgba(255,255,255,0.8);
				font-size: 0.48rem;
				text-align: center;
				color: #fff;
			}
			& .wathch{
				position: absolute;
				z-index: 11;
				right: 0.2rem;
				bottom: 0;
				line-height: 1rem;
				color: #fff;
				font-size: 0.55rem;

				& .num{
					font-size: 0.65rem;
					margin-right: 0.05rem;
				}
			}

			& .address{
				position:absolute;
				z-index: 11;
				bottom: 0;
				left: 0.2rem;
				line-height: 1rem;
				color: #fff;
				white-space: nowrap;
				text-overflow: ellipsis;
				overflow: hidden;
				& .iconfont{
					font-size: 0.75rem;
					color: #fff;
				}
				& .city{
					font-size: 0.55rem;
					font-style: normal;
				}
			}

			& .img-box{
				position: relative;
				overflow: hidden;
				&:after{
					display:block;
					content: "";
					padding-top: 100%;
					width: 100%;
				}
					
				& img{
					display: block;
					position: absolute;
					left: 0;
					top: 0;
					width: 100%;
					height: 100%;
				}
			}

			& .up-box{
				width: 100%;
				height: 100%;
				position: absolute;
				left: 0;
				right: 0;
				background: rgba(0,0,0,0.3);
				z-index: 10;
				text-align: center;	
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
.hot{
	margin-left: 0.5rem;
	height: 2rem;
	line-height: 2rem;
	color: @fc-main;
	font-size: 0.75rem;
	margin-top: 0.5rem;
	position: relative;
	& .more{
		position: absolute;
		right: 0.5rem;
		top:0;
		line-height: 2rem;
		font-size: 0.55rem;
		color: #ff7551;
	}
}
.t-line{
  position: relative;
}
.t-line::before{
  content: '';
  width: 100%;
  height:1px;
  background:#e6e6e6;
  position: absolute;
  left: 0;
  top: 0;
  -webkit-transform: scaleY(0.5) translate3d(0,0,0);
          transform: scaleY(0.5) translate3d(0,0,0);
  -webkit-transform-origin:0 0;
  transform-origin: 0 0;
  z-index:5;
}
</style>