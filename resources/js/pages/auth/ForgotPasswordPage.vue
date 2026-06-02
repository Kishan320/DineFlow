<template>
  <div class="auth-root">
    <div class="auth-card">
      <div class="flex flex-col items-center gap-2 mb-8">
        <AppLogo :size="48" />
        <h1 class="text-xl font-bold" style="color:var(--foreground)">Forgot password?</h1>
        <p class="text-sm text-center" style="color:var(--muted-foreground)">
          Enter your email and we'll send you a reset link
        </p>
      </div>

      <!-- Success state -->
      <div v-if="sent" class="text-center space-y-4">
        <div class="w-14 h-14 rounded-full mx-auto flex items-center justify-center" style="background:color-mix(in srgb, var(--primary) 12%, transparent)">
          <MailCheckIcon :size="26" style="color:var(--primary)" />
        </div>
        <p class="text-sm font-medium" style="color:var(--foreground)">Check your inbox</p>
        <p class="text-sm" style="color:var(--muted-foreground)">
          We sent a password reset link to <strong>{{ form.email }}</strong>
        </p>
        <RouterLink to="/login" class="auth-btn block text-center" style="text-decoration:none">
          Back to Sign In
        </RouterLink>
      </div>

      <form v-else @submit.prevent="submit" class="space-y-4">
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

        <p v-if="serverError" class="auth-error text-center">{{ serverError }}</p>

        <button type="submit" class="auth-btn" :disabled="loading">
          <span v-if="loading" class="auth-spinner" />
          {{ loading ? 'Sending…' : 'Send Reset Link' }}
        </button>

        <RouterLink to="/login" class="block text-center text-sm font-medium" style="color:var(--muted-foreground)">
          ← Back to Sign In
        </RouterLink>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import * as yup from 'yup';
import { MailCheck as MailCheckIcon } from '@lucide/vue';
import AppLogo from '@/components/ui/AppLogo.vue';
import { authApi } from '@/services/authApi';

const loading     = ref(false);
const sent        = ref(false);
const serverError = ref('');

const schema = yup.object({
  email: yup.string().email('Enter a valid email').required('Email is required'),
});

const form   = reactive({ email: '' });
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
  loading.value = true;
  try {
    await authApi.forgotPassword({ email: form.email });
    sent.value = true;
  } catch (e) {
    serverError.value = e?.message || 'Failed to send reset link. Please try again.';
  } finally {
    loading.value = false;
  }
}
</script>
