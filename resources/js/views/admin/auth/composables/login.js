import adminApi from "../../../../api/adminAxios";
import { ref } from "vue";
import { useStore } from 'vuex'
import Cookies from "js-cookie";
import {useRouter} from "vue-router";

export default function login() {
    const errors = ref({}); //errors for create or update
    const loading = ref(false);
    const store = useStore();
    const router = useRouter();

    //store fine  request
    const adminLogin = async (data) => {
        loading.value = true;
        errors.value = {};
        try {
            console.log('[login] sending request...');
            const result = await adminApi.post('dashboard/auth/login', data);
            console.log('[login] response:', result.data);
            const l = result.data.data;
            console.log('[login] access_token:', l?.access_token ? 'EXISTS' : 'MISSING');
            Cookies.set("token", l.access_token);
            console.log('[login] cookie set, redirecting...');
            window.location.href = '/admin/dashboard';
        } catch (e) {
            console.error('[login] ERROR:', e);
            loading.value = false;
            if (e?.response?.data) {
                errors.value = e.response.data;
            }
        }
    }

    return {
        errors,
        loading,
        adminLogin,
    };
}

