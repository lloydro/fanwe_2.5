<template>
    <div class="bannerBox">
        <div class="swiper-wrapper">
            <div class="swiper-slide" v-for="item in imgBanner">
                <img :src="item.image" alt="item.title" @click="linkBanner(item.url)">
            </div>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</template>
<script>
    import { Toast } from 'mint-ui'
    import Swiper from '../../../static/js/swiper/swiper-3.4.2.min.js'
    require('../../../static/js/swiper/swiper-3.4.2.min.css')

    export default {
        props: ['imgBanner'],
        methods: {
            linkBanner(link_url, open_url_type=0){
                let obj = { url:link_url, open_url_type:open_url_type };
                try{
                    App.open_type(JSON.stringify(obj));
                }
                catch(e){
                    Toast("SDK调用失败");
                }
            }
        }
    }
</script>
<style lang="less">
    @import (once) "../../assets/css/variable.less";
    .bannerBox{
        width: 100%;
        height: 33.33vw;
        overflow: hidden;
        position: relative;
        & .swiper-slide{
            width: 100%;
            height: 100%;
            & .swiper-img-link{
                display: block;
                width: 100%;
                height: 100%;
            }
            & img{
                display: block;
                width: 100%;
                height: 100%;
            }
        }
        & .swiper-pagination{
      	    bottom: 5px!important;
            & .swiper-pagination-bullet{
          		background: #000;
          		opacity: 0.5;
          	}
          	& .swiper-pagination-bullet-active{
          		background: @color-theme;
          		opacity: 0.8
          	}
        }
    }
</style>
