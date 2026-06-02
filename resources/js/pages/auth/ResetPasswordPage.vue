<template>
  <div class="auth-root">
    <div class="auth-card">
      <div class="flex flex-col items-center gap-2 mb-8">
        <AppLogo :size="48" />
        <h1 class="text-xl font-bold" style="color:var(--foreground)">Reset password</h1>
        <p class="text-sm" style="color:var(--muted-foreground)">Enter your new password below</p>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="auth-label">Email <span style="color:var(--danger)">*</span></label>
          <input
            v-model="form.email"
            type="email"
            placeholder="you@example.com"
            class="auth-input"
            :style="errors.email ? 'border-color:var(--danger)' : ''"
            autocomplete="email"
          />
          <p v-if="errors.email" class="auth-error">{{ errors.email }}</p>
        </div>

        <div>
          <label class="auth-label">New Password <span style="color:var(--danger)">*</span></label>
          <div class="auth-input-wrap" :style="errors.password ? 'border-color:var(--danger)' : ''">
            <input
              v-model="form.password"
              :type="showPwd ? 'text' : 'password'"
              placeholder="Min. 8 characters"
              class="auth-input-inner"
              autocomplete="new-password"
            />
            <button type="button" @click="showPwd = !showPwd" class="auth-eye">
              <component :is="showPwd ? EyeOffIcon : EyeIcon" :size="16" />
            </button>
          </div>
          <p v-if="errors.password" class="auth-error">{{ errors.password }}</p>
        </div>

        <div>
          <label class="auth-label">Confirm Password <span style="color:var(--danger)">*</span></label>
          <div class="auth-input-wrap" :style="errors.password_confirmation ? 'border-color:var(--danger)' : ''">
            <input
              v-model="form.password_confirmation"
              :type="showConfirm ? 'text' : 'password'"
              placeholder="Repeat new password"
              class="auth-input-inner"
              autocomplete="new-password"
            />
            <button type="button" @click="showConfirm = !showConfirm" class="auth-eye">
              <component :is="showConfirm ? EyeOffIcon : EyeIcon" :size="16" />
            </button>
          </div>
          <p v-if="errors.password_confirmation" class="auth-error">{{ errors.password_confirmation }}</p>
        </div>

        <p v-if="serverError" class="auth-error text-center">{{ serverError }}</p>

        <button type="submit" class="auth-btn" :disabled="loading">
          <span v-if="loading" class="auth-spinner" />
          {{ loading ? 'Resetting…' : 'Reset Password' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import * as yup from 'yup';
import { Eye as EyeIcon, EyeOff as EyeOffIcon } from '@lucide/vue';
import AppLogo from '@/components/ui/AppLogo.vue';
import { authApi } from '@/services/authApi';
import { useSnackbar } from '@/composables/useSnackbar';

const router      = useRouter();
const route       = useRoute();
const snackbar    = useSnackbar();
const loading     = ref(false);
const showPwd     = ref(false);
const showConfirm = ref(false);
const serverError = ref('');

const schema = yup.object({
  email:                 yup.string().email('Enter a valid email').required('Email is required'),
  password:              yup.string().min(8, 'Password must be at least 8 characters').required('Password is required'),
  password_confirmation: yup.string()
    .oneOf([yup.ref('password')], 'Passwords do not match')
    .required('Please confirm your password'),
});

const form   = reactive({ email: '', password: '', password_confirmation: '' });
const errors = reactive({});

onMounted(() => {
  // Pre-fill email from query param if present
  if (route.query.email) form.email = route.query.email;
});

async function submit() {
  Object.keys(errors).forEach(k => delete errors[k]);
  serverError.value = '';
  try {
    await schema.validate(form, { abortEarly: false });
  } catch (e) {
    e.inner.forEach(err => { errors[err.path] = err.message; });
    return;
  }
  loading.value = true;
  try {
    await authApi.resetPassword({
      token:                 route.params.token,
      email:                 form.email,
      password:              form.password,
      password_confirmation: form.password_confirmation,
    });
    snackbar.success('Password reset successfully!');
    router.push('/login');
  } catch (e) {
    serverError.value = e?.message || 'Failed to reset password. The link may have expired.';
  } finally {
    loading.value = false;
  }
}
</script>
