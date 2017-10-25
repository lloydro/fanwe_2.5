var merge = require('webpack-merge')
var prodEnv = require('./prod.env')

module.exports = merge(prodEnv, {
  NODE_ENV: '"development"',
  API_ROOT: '"http://livet1.fanwe.net/mapi/index.php"'  // 生产环境 API地址
})