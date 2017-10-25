<template>
	<div class="content">
		<template v-if="layout == 'default'">
			<uc-top-default :user="user" :shortName="short_name"></uc-top-default>
			<list-block>
				<list-block-item-default path="" icon="&#xe629;" title="等级" :after="user.user_level" funType="5"></list-block-item-default>
				<list-block-item-default path="" icon="&#xe609;" title="账户" :after="user.diamonds" funType="6"></list-block-item-default>
				<list-block-item-default path="" icon="&#xe621;" :title="ticket_name+'贡献榜'" after="" funType="7"></list-block-item-default>
				<list-block-item-default path="" icon="&#xe60c;" title="认证" :after="user.v_type" funType="8"></list-block-item-default>
			</list-block>
			<list-block>
				<list-block-item-default path="" icon="&#xe61f;" title="设置" after="" funType="10"></list-block-item-default>
			</list-block>
		</template>
		<template v-if="layout == 'green'">
			<uc-top-green :user="user" :shortName="short_name"></uc-top-green>
			<list-block>
				<list-block-item-green path="" icon="&#xe629;" title="等级" :after="user.user_level" funType="5"></list-block-item-green>
				<list-block-item-green path="" icon="&#xe609;" title="账户" :after="user.diamonds" funType="6"></list-block-item-green>
				<list-block-item-green path="" icon="&#xe621;" :title="ticket_name+'贡献榜'" after="" funType="7"></list-block-item-green>
				<list-block-item-green path="" icon="&#xe60c;" title="认证" :after="user.v_type" funType="8"></list-block-item-green>
			</list-block>
			<list-block>
				<list-block-item-green path="" icon="&#xe61f;" title="设置" after="" funType="10"></list-block-item-green>
			</list-block>
		</template>
	</div>
</template>
<script>
	import { Toast, Indicator  } from 'mint-ui'
	import UcTopDefault from '@/components/UcTop/Default'
	import UcTopGreen from '@/components/UcTop/Green'
	import ListBlock from '@/components/ListBlock'
	import ListBlockItemDefault from '@/components/ListBlockItem/Default'
	import ListBlockItemGreen from '@/components/ListBlockItem/Green'
	import api from '@/config/api';
	import axios from 'axios'

	export default{
		components: {
			UcTopDefault,
			UcTopGreen,
			ListBlock,
			ListBlockItemDefault,
			ListBlockItemGreen
		},
		data(){
			return{
				user: {},
				ticket_name: '',
				short_name: ''
			}
		},
		// 解除keep-alive的缓存
	  	beforeRouteEnter: (to, from, next) => {
		    next(vm => {
		      	vm.$store.commit('hideHeader', 1)
	    	})
	    	next()
		},
		created(){
   			this.getData();
   		},
		methods: {
			getData(){
				let self = this;
				Indicator.open();
				axios.get(api.getUserinfo())
			      	.then(res => {
			      		Indicator.close();
			      		let data = res.data;
			      		self.user =  data.user;
			      		self.ticket_name =  data.ticket_name || '印票';
			      		self.short_name =  data.account_name || '印客';
			      	})
			      	.catch(err => console.log(err))
			}
		},
		computed: {
            layout () {
              return this.$store.state.layout
            },
            isHideHeader () {
              return this.$store.state.isHideHeader
            }
        },
        activated(){
        	this.getData();
        }
	}
</script>