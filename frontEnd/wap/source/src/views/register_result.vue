<template>
	<div class="active-container">
		<div class="main">
			<img src="../assets/img/img-activeTwo-1.jpg" />
			<active-text :avatar="head_image" :nickName="nick_name" :text="text"></active-text>
			<active-btn :btnImg="btnImg" :submit="download"></active-btn>
		</div>
	</div>
</template>
<style>
	img{ max-width:100% }
</style>
<script type="text/javascript">
	import api from '../config/api';
	import activeText from '../components/activeText.vue'
	import activeBtn from '../components/activeBtn.vue'
	export default{
        components: {
        	activeText,
        	activeBtn
	  	},
		data(){
			return {
				scrollWatch:true,
				head_image: '',
				nick_name: '',
				text: '感谢你的加入，么么哒！',
				app_down_url: '',
				btnImg: require('@/assets/img/btn_download.png')
			}
		},
		created(){
			this.get();
		},
		mounted: function() {
			$(window).scrollTop(0);
		    $(window).on('scroll', () => {
		      	if (this.scrollWatch) {
	           		//your code here
        		}
		    });
        },
     	methods: {
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
	                	this.app_down_url = sessionStorage.getItem("app_down_url") || '';
	                }
	                else{
	                	MessageBox(result.error);
	                }

		        })
		        .catch(err => console.log(err))
     		},
     		download(){
     			location.href = this.app_down_url;
     		}
        },
        destroyed () {
		    this.scrollWatch = false;
	  	}
	}
</script>