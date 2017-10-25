const _baseUrl = process.env.API_ROOT
export default {
    getIndex() {
        return _baseUrl + '?ctl=index_h5&act=index'
    },
    getUserinfo() {
        return _baseUrl + '?ctl=user_h5&act=userinfo'
    },
    getTheme() {
        return _baseUrl + '?ctl=index_h5&act=theme'
    },
    distributionInitRegister() {
        return _baseUrl + '?ctl=share_distribution&act=index'
    },
 	distributionRegister() {
        return _baseUrl + '?ctl=share_distribution&act=register'
    },
    get_verifycode() {
        return _baseUrl + '?ctl=login&act=send_mobile_verify'
    }
}