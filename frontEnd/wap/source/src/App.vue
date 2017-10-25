<template>
    <div class="page" id="app">
        <header-default v-if="! isHideHeader && layout == 'default'"></header-default>   
        <header-green v-if="! isHideHeader && layout == 'green'"></header-green>   
        <footer-nav-default v-if="! isHideFooter && layout == 'default'"></footer-nav-default>
        <footer-nav-green v-if="! isHideFooter && layout == 'green'"></footer-nav-green>
        <keep-alive>
        	<router-view></router-view>
        </keep-alive>
    </div>
</template>

<script>
import FooterNavDefault from '@/components/FooterNav/Default'
import FooterNavGreen from '@/components/FooterNav/Green'
import HeaderDefault from '@/components/Header/Default'
import HeaderGreen from '@/components/Header/Green'
export default {
    name: 'app',
    components: {
    	FooterNavDefault,
    	FooterNavGreen,
        HeaderDefault,
        HeaderGreen
    },
 	computed: {
        isHideHeader () {
          return this.$store.state.isHideHeader
        },
        isHideFooter() {
            return this.$store.state.isHideFooter
        },
      	layout () {
      		return this.$store.state.layout
    	}
    }
}
</script>

<style lang="less">
    @import './assets/css/reset.css';
    @import './assets/css/variable.less';
    @import './assets/css/public.less';
    @import './assets/css/sui.css';
    body{
    	width: 100%;
    	height: 100%;
    }
    .page{
    	position: absolute;
    	top: 0;
    	bottom: 0;
    	left: 0;
    	right: 0;
    }
    .content{
        position: absolute;
        width: 100%;
        top: 0;
        bottom: 0;
        overflow: scroll;
    }
    .footer ~ .content{
        bottom: 2rem;
    }
    .header ~ .content{
        top: 2rem;
    }
    .null-data{
        width: 100%;
        text-align: center;
        padding: 1.5rem 0.75rem;
        color: @fc-lighter;
        background-color: #fafafa;
        &:before{
            display: block;
            content: '\e63f';
            font-family: 'iconfont';
            font-size: 5rem;
            color: @fc-lighter;
            line-height: 1;
        }
        &.hide{
            display: none;
        }
        span{
            margin-top: 0.5rem;
            display: block;
        }
    }
    [v-cloak] {
        display: none;
    }
</style>
