<template>
  <router-view />
  <ToastContainer />
  <UpgradeModal :show="planLimitModal" @close="store.commit('ui/SET_PLAN_LIMIT_MODAL', false)" />
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import ToastContainer from './components/ToastContainer.vue'
import UpgradeModal from './components/UpgradeModal.vue'

const store = useStore()
const planLimitModal = computed(() => store.state.ui.planLimitModal)

onMounted(async () => {
  store.commit('ui/INIT_DARK')
  // Only call fetchMe if logged in but user data not yet populated
  if (store.getters['auth/isLoggedIn'] && !store.state.auth.user) {
    try { await store.dispatch('auth/fetchMe') } catch {}
  }
})
</script>

<style>
/* Minimal — template CSS handles everything */
a { text-decoration: none; }
</style>
