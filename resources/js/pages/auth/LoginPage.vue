<template>
  <div class="auth-root">
    <div class="auth-card">
      <div class="flex flex-col items-center gap-2 mb-8">
        <AppLogo :size="48" />
        <h1 class="text-xl font-bold" style="color:var(--foreground)">Welcome back</h1>
        <p class="text-sm" style="color:var(--muted-foreground)">Sign in to your DineFlow account</p>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="auth-label">Email <span style="color:var(--danger)">*</span></label>
          <input
            v-model="form.email"
            type="email"
            placeholder="admin@dineflow.com"
            class="auth-input"
            :style="errors.email ? 'border-color:var(--danger)' : ''"
            autocomplete="email"
          />
          <p v-if="errors.email" class="auth-error">{{ errors.email }}</p>
        </div>

        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="auth-label" style="margin-bottom:0">Password <span style="color:var(--danger)">*</span></label>
            <RouterLink to="/forgot-password" class="text-xs font-medium" style="color:var(--primary)">Forgot password?</RouterLink>
          </div>
          <div class="auth-input-wrap" :style="errors.password ? 'border-color:var(--danger)' : ''">
            <input
              v-model="form.password"
              :type="showPwd ? 'text' : 'password'"
              placeholder="••••••••"
              class="auth-input-inner"
              autocomplete="current-password"
            />
            <button type="button" @click="showPwd = !showPwd" class="auth-eye">
              <component :is="showPwd ? EyeOffIcon : EyeIcon" :size="16" />
            </button>
          </div>
          <p v-if="errors.password" class="auth-error">{{ errors.password }}</p>
        </div>

        <p v-if="serverError" class="auth-error text-center">{{ serverError }}</p>

        <button type="submit" class="auth-btn" :disabled="authStore.loading">
          <span v-if="authStore.loading" class="auth-spinner" />
          {{ authStore.loading ? 'Signing in…' : 'Sign In' }}
        </button>
      </form>

      <p class="text-center text-sm mt-6" style="color:var(--muted-foreground)">
        Don't have an account?
        <RouterLink to="/register" class="font-semibold" style="color:var(--primary)">Sign up</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import * as yup from 'yup';
import { Eye as EyeIcon, EyeOff as EyeOffIcon } from '@lucide/vue';
import AppLogo from '@/components/ui/AppLogo.vue';
import { useAuthStore } from '@/stores/authStore';
import { useSnackbar } from '@/composables/useSnackbar';

const router      = useRouter();
const authStore   = useAuthStore();
const snackbar    = useSnackbar();
const showPwd     = ref(false);
const serverError = ref('');

const schema = yup.object({
  email:    yup.string().email('Enter a valid email').required('Email is required'),
  password: yup.string().required('Password is required'),
});

const form   = reactive({ email: '', password: '' });
const errors = reactive({});

async function submit() {
  Object.keys(errors).forEach(k => delete errors[k]);
  serverError.value = '';
  try {
    await schema.validate(form, { abortEarly: false });
  } catch (e) {
    e.inner.forEach(err => { errors[err.path] = err.message; });
    return;
  }
  try {
    await authStore.login({ email: form.email, password: form.password });
    snackbar.success('Welcome back!');
    router.push('/');
  } catch (e) {
    serverError.value = e?.message || 'Login failed. Please try again.';
  }
}
</script>
