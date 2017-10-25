import Vue from 'vue'
import Router from 'vue-router'
import index from '@/views/index'
import uc_index from '@/views/uc_index'
import register from '@/views/register'
import register_result from '@/views/register_result'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'index',
      component: index
    },
    {
      path: '/index',
      name: 'index',
      component: index
    },
    {
      path: '/uc_index',
      name: 'uc_index',
      component: uc_index
    },
    {
      path: '/register',
      name: 'register',
      component: register
    },
    {
      path: '/register_result',
      name: 'register_result',
      component: register_result
    }
  ]
})
