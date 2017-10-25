<template>
	<div class="content">
		<mt-loadmore :top-method="loadTop" ref="loadmore">
			<template v-if="layout == 'default'">
				<banner-default :imgBanner="imgBanner"></banner-default>
				<list-default :list="list"></list-default>
			</template>
			<template v-if="layout == 'green'">
				<banner-green :imgBanner="imgBanner"></banner-green>
				<list-green :list="list"></list-green>
			</template>
		</mt-loadmore>
	</div>
</template>
<script>
    import Vue from 'vue'
	import api from '@/config/api'
  	import axios from 'axios'
	import { Toast, Indicator, Loadmore} from 'mint-ui'
	import BannerDefault from '@/components/Banner/Default'
	import BannerGreen from '@/components/Banner/Green'
	import ListDefault from '@/components/LiveList/Default'
	import ListGreen from '@/components/LiveList/Green'
	Vue.component(Loadmore.name, Loadmore);
	export default{
		components: {
			BannerDefault,
			BannerGreen,
			ListDefault,
			ListGreen
		},
		data(){
			return{
				imgBanner: '',
				list:'',
				onTopLoaded:true
			}
		},
		// 解除keep-alive的缓存
	  	beforeRouteEnter: (to, from, next) => {
		    next(vm => {
		      	vm.$store.commit('hideHeader', 0)
	    	})
	    	next()
		},
		created(){
			this.getData();
		},
		mounted(){
		},
		methods:{
		  	getData(isRefresh=false){
		  		// 抓取数据
		  		! isRefresh && Indicator.open();
	        	let self=this;
	        	axios.get(api.getIndex()).then(res=>{
        			self.list = res.data.list;
	        		if(isRefresh){
	        			self.$nextTick(function(){
			          		self.onTopLoaded = true;
			          		self.onBottomLoaded=true;
			          	});
	        		}
	        		else{
	        			Indicator.close();
	        			self.imgBanner = res.data.banner;
      					self.$nextTick(function(){
      						setTimeout(function(){
				          		self.initSwiper();
      						}, 1);
			          	});
	        		}
		          	
		        }).catch(err=>console.log(err));
	      	},
	      	loadTop() {
	      		// 下拉刷新
	      		let self = this;
	      		self.getData(true);
	      		if(self.onTopLoaded){
	      			setTimeout(function(){
	                  	self.$refs.loadmore.onTopLoaded();
	              	}, 500);
	              	self.onTopLoaded = false;
	      		}
			},
	      	initSwiper(){
	      		// 初始化轮播
		      	let self = this;
		      	switch(self.layout){
		      		case 'default':
		      			var mySwiperOne = new Swiper('.bannerBox', {
				         	pagination : '.swiper-pagination',
				         	autoplay: 3000,//可选选项，自动滑动
				         	speed:300,
				         	loop:true
				        });
				        break;
				    case 'green':
				    	var mySwiperTwo = new Swiper('.bannerBox', {
		          			pagination : '.swiper-pagination',
		          			paginationType : 'progress',
					        autoplay: 3000,//可选选项，自动滑动
					        speed:300,
					        loop:true
				        });
				        break;
		      	}
	      	},
	      	live(){

	      	}
		},
		computed: {
            layout () {
              return this.$store.state.layout
            },
            isHideHeader () {
              return this.$store.state.isHideHeader
            }
        }
	}
</script>
<style lang="less">
	.mint-loadmore{
		min-height: 100%;
	}
</style>