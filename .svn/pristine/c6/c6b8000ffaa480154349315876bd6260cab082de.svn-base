// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import store from './store';
import App from './App'
import api from './config/api'
import router from './router'
import 'mint-ui/lib/style.css'

//添加mockjs拦截请求，模拟返回服务器数据
import mock from './config/mock'

Vue.use(VueAxios, axios)
Vue.config.productionTip = false

router.beforeEach((to, from, next) => {
	// 获取布局
	axios.get(api.getTheme())
		.then(res => {
          	var data = res.data;
		    store.commit('getLayout', data.layout)

	    })
	    .catch(err => console.log(err))
	    next()
});

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: { App }
})


