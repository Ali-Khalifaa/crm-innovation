import axios from "axios";
import Cookies from "js-cookie";
import store from "../store/admin.js";
import adminRoute from "../router/adminRoute.js";


const adminApi = axios.create({
    baseURL: `${window.location.origin}/api/`,
    transformResponse: [
        (data) => {
            if (typeof data === 'string') {
                data = data.replace(/^﻿+/, '');
                try { return JSON.parse(data); } catch(e) { return data; }
            }
            return data;
        }
    ],
});

adminApi.interceptors.request.use(
    function (config) {
        config.headers['lang'] = 'ar';
        config.headers['Accept-Language'] = 'ar';
        config.headers['Authorization'] = "Bearer " + (Cookies.get("token") || '');
        return config;
    },
    function (error) {
        return Promise.reject(error);
    }
);
adminApi.defaults.headers.common['Accept'] = 'application/json';
adminApi.defaults.headers.common['lang'] = 'ar';
adminApi.defaults.headers.common['Accept-Language'] = 'ar';

adminApi.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    if (error?.response?.status === 401) {
        adminRoute.push({name: 'login'});
        store.commit('authAdmin/logoutToken');
    } else {
        return Promise.reject(error);
    }
});
// end axios
export default adminApi;
