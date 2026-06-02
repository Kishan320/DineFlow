<template>
  <div class="auth-root">
    <div class="auth-card">
      <div class="flex flex-col items-center gap-2 mb-8">
        <AppLogo :size="48" />
        <h1 class="text-xl font-bold" style="color:var(--foreground)">Create account</h1>
        <p class="text-sm" style="color:var(--muted-foreground)">Register to get started with DineFlow</p>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="auth-label">Full Name <span style="color:var(--danger)">*</span></label>
          <input
            v-model="form.name"
            type="text"
            placeholder="John Doe"
            class="auth-input"
            :style="errors.name ? 'border-color:var(--danger)' : ''"
            autocomplete="name"
          />
          <p v-if="errors.name" class="auth-error">{{ errors.name }}</p>
        </div>

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
          <label class="auth-label">Password <span style="color:var(--danger)">*</span></label>
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
              placeholder="Repeat password"
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

        <button type="submit" class="auth-btn" :disabled="authStore.loading">
          <span v-if="authStore.loading" class="auth-spinner" />
          {{ authStore.loading ? 'Creating account…' : 'Create Account' }}
        </button>
      </form>

      <p class="text-center text-sm mt-6" style="color:var(--muted-foreground)">
        Already have an account?
        <RouterLink to="/login" class="font-semibold" style="color:var(--primary)">Sign in</RouterLink>
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
const showConfirm = ref(false);
const serverError = ref('');

const schema = yup.object({
  name:                  yup.string().required('Full name is required').max(255),
  email:                 yup.string().email('Enter a valid email').required('Email is required'),
  password:              yup.string().min(8, 'Password must be at least 8 characters').required('Password is required'),
  password_confirmation: yup.string()
    .oneOf([yup.ref('password')], 'Passwords do not match')
    .required('Please confirm your password'),
});

const form   = reactive({ name: '', email: '', password: '', password_confirmation: '' });
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
    await authStore.register({ ...form });
    snackbar.success('Account created successfully!');
    router.push('/');
  } catch (e) {
    serverError.value = e?.message || 'Registration failed. Please try again.';
  }
}
</script>
