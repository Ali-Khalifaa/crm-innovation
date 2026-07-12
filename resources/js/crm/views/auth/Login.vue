<template>
  <div class="hk-wrapper hk-pg-auth" data-bs-theme="light">
    <div class="hk-pg-wrapper">
      <div class="hk-pg-body py-0">
        <div class="container-xxl">
          <div class="row vh-100 align-items-center justify-content-center">
            <div class="col-lg-5 col-md-7 col-sm-10">
              <div class="auth-content py-8">

                <!-- Logo -->
                <div class="text-center mb-5">
                  <img src="/images/logo.png" alt="CRM Innovation" style="max-height:52px;" />
                  <h4 class="mt-3 mb-1 fw-bold">{{ locale === 'ar' ? 'مرحباً بعودتك' : 'Welcome back' }}</h4>
                  <p class="text-muted">{{ locale === 'ar' ? 'سجّل دخولك إلى لوحة تحكم CRM' : 'Sign in to your CRM dashboard' }}</p>
                </div>

                <!-- Lang toggle on login page -->
                <div class="text-center mb-4">
                  <button class="btn btn-sm btn-outline-light" @click="switchLang" type="button">
                    <i class="bi bi-translate me-1"></i>
                    {{ locale === 'ar' ? 'English' : 'عربي' }}
                  </button>
                </div>

                <!-- Alert -->
                <div v-if="error" class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
                  <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                  <span>{{ error }}</span>
                </div>

                <!-- Form -->
                <form @submit.prevent="handleLogin">
                  <div class="mb-3">
                    <label class="form-label fw-semibold">
                      {{ locale === 'ar' ? 'البريد الإلكتروني' : 'Email' }}
                    </label>
                    <div class="input-group">
                      <span class="input-group-text bg-transparent"><i class="bi bi-envelope text-muted"></i></span>
                      <input
                        v-model="form.email"
                        type="email"
                        class="form-control"
                        :placeholder="locale === 'ar' ? 'بريدك@الشركة.com' : 'you@company.com'"
                        required
                        autocomplete="email"
                      />
                    </div>
                  </div>

                  <div class="mb-4">
                    <label class="form-label fw-semibold">
                      {{ locale === 'ar' ? 'كلمة المرور' : 'Password' }}
                    </label>
                    <div class="input-group">
                      <span class="input-group-text bg-transparent"><i class="bi bi-lock text-muted"></i></span>
                      <input
                        v-model="form.password"
                        type="password"
                        class="form-control"
                        :placeholder="locale === 'ar' ? 'كلمة المرور' : 'Your password'"
                        required
                        autocomplete="current-password"
                      />
                    </div>
                  </div>

                  <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                    <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    {{ loading
                      ? (locale === 'ar' ? 'جاري الدخول...' : 'Signing in…')
                      : (locale === 'ar' ? 'تسجيل الدخول' : 'Sign In') }}
                  </button>
                </form>

                <hr class="my-4" />
                <p class="text-center text-muted mb-0" style="font-size:14px;">
                  {{ locale === 'ar' ? 'ليس لديك حساب؟' : "Don't have an account?" }}
                  <a href="/register" class="text-primary fw-semibold">
                    {{ locale === 'ar' ? 'ابدأ تجربتك المجانية' : 'Start free trial' }}
                  </a>
                </p>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import { loadLanguageAsync } from 'laravel-vue-i18n'

const store  = useStore()
const router = useRouter()

const form    = ref({ email: '', password: '' })
const loading = ref(false)
const error   = ref('')
const locale  = computed(() => store.state.ui.locale)

async function switchLang() {
  const next = locale.value === 'ar' ? 'en' : 'ar'
  await loadLanguageAsync(next)
  store.commit('ui/SET_LOCALE', next)
  document.documentElement.dir  = next === 'ar' ? 'rtl' : 'ltr'
  document.documentElement.lang = next
}

async function handleLogin() {
  loading.value = true
  error.value   = ''
  try {
    await store.dispatch('auth/login', form.value)
    router.push('/crm/dashboard')
  } catch (err) {
    error.value = err.response?.data?.message ||
      (locale.value === 'ar' ? 'البريد الإلكتروني أو كلمة المرور غير صحيحة' : 'Invalid email or password')
  } finally {
    loading.value = false
  }
}
</script>
