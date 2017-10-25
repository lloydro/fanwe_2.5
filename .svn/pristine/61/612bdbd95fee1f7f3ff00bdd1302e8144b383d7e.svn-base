import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const store = new Vuex.Store({
	state:{
		layout: 'default', // 主题布局
		isHideHeader: false,  // 是否隐藏首页头部
		isHideFooter: false
	},
	getters: {

	},
	mutations: {
		getLayout (state, a) {
	      state.layout = a;
	    },
	    hideHeader(state, a){
	    	state.isHideHeader = a;
	    },
	    hideFooter(state, a){
	    	state.isHideFooter = a;
	    }
	},
	acitons: {

	}
});

export default store