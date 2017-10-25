<template>
	<div>
	  	<div class="listBox">
			<div class="temp-list">
				<template v-if="list.length">
			  		<div class="temp-item" v-for="item in list">
			  			<div class="temp-inner flex-box">
				  			<div class="user-img">
				  				<img :src="item.head_image">
				  			</div>
				  			<div class="user-msg flex-1">
				  				<p class="name">{{item.nick_name}}</p>
				  				<div class="address"><i class="iconfont">&#xe7a5;</i><em class="city">{{item.city}}</em></div>
				  			</div>
				  			<p class="wathch"><span class="num">{{item.watch_number}}</span>人观看</p>
			  			</div>
			  			<div class="temp-img" @click="joinLive(item)">
			  				<div class="up-box"><i class="iconfont">&#xe601;</i></div>
			  				<div class="img-box">
			  					<img :src="item.live_image">
			  				</div>
			  				<span class="state">
				  				<template v-if="item.live_in == 0">已停止</template>
				  				<template v-else-if="item.live_in == 1">直播</template>
				  				<template v-else-if="item.live_in == 2">创建直播</template>
				  				<template v-else-if="item.live_in == 3">回放</template>
				  			</span>
			  			</div>
			  		</div>
		  		</template>
		  		<template v-else>
		  			<div class="null-data">暂无任何直播</div>
		  		</template>
	  		</div>
	  	</div>
  	</div>
</template>
<script>
	import { Toast } from 'mint-ui'
  	export default{
  	 	props: ['list'],
    	data(){
      		return {
      
      		}
    	},
    	mounted(){
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
@import (once) "../../assets/css/variable.less";
.listBox{
	background: #e5e5e5;
	padding-top: 0.25rem;
}
.temp-list{
	& .temp-item{
		background: #fff;
		margin-bottom: 0.25rem;
		-webkit-transform: translate3d(0,0,0);
		transform:translate3d(0,0,0);

		&:last-child{
			margin-bottom: 0;
		};
		& .temp-inner{
			padding: 0.25rem 0.5rem;
			position: relative;

			& .wathch{
				position: absolute;
				right: 0.5rem;
				bottom: 0.2rem;
				line-height: 1.2rem;
				color: #999;
				font-size: 0.65rem;

				& .num{
					font-size: 0.85rem;
					margin-right: 0.25rem;
					color: @color-theme;
				}
			}
			& .user-img{
				width : 2.5rem;
				height: 2.5rem;
				border-radius: 50%;
				overflow: hidden;
				border: 0.1rem solid #ff7652;
				& img{
					display: block;
					width: 100%;
					height:100%;
				}
			}
			& .user-msg{
				margin-left: 0.5rem;
				& .name{
					font-size: 0.75rem;
					line-height: 1.4rem;
					color: #000;
					white-space: nowrap;
					text-overflow: ellipsis;
					overflow: hidden;
				}
				& .address{
					line-height: 0.8rem;
					color: #b4b4b4;
					white-space: nowrap;
					text-overflow: ellipsis;
					overflow: hidden;
					& .iconfont{
						font-size: 0.85rem;
						color: #b4b4b4;
					}
					& .city{
						font-size: 0.55rem;
						font-style: normal;
					}
				}
			}
		}
		& .temp-img{
			position: relative;
			overflow: hidden;

			& .state{
				position: absolute;
				z-index: 9;
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
			& .up-box{
				width: 100%;
				height: 100%;
				position: absolute;
				left: 0;
				right: 0;
				background: rgba(0,0,0,0.3);
				z-index: 8;
				text-align: center;
				& .iconfont{
					position: absolute;
					width: 2rem;
					height: 2rem;
					line-height: 2rem;
					left: 50%;
					top: 50%;
					margin-top: -1rem;
					margin-left: -1rem;
					font-size: 2rem;
					color: #fff;
				}
				& .scale{
					animation: scale 0.8s;
					-webkit-animation: scale 0.8s;
				}
				@keyframes scale{
					from{
						transform: scale(1,1);
						opacity: 1;
					}
					to{
						transform: scale(3,3);
						opacity: 0.2;
					}
				}
				@-webkit-keyframes scale{
					from{
						transform: scale(1,1);
						opacity: 1;
					}
					to{
						transform: scale(3,3);
						opacity: 0.2;
					}
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
	color: #000;
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