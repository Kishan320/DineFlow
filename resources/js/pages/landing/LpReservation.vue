<template>
  <section id="reservation" ref="sectionRef" class="py-24 px-4 sm:px-6" style="background:rgba(240,234,224,0.3)">
    <div class="max-w-7xl mx-auto">

      <div class="text-center mb-12 lp-reveal">
        <p class="text-xs font-bold uppercase tracking-[0.3em] mb-3" style="color:var(--lp-primary)">Reserve Your Seat</p>
        <h2 class="lp-text-section lp-font-disp font-medium tracking-tight" style="color:var(--lp-fg)">
          Book a&nbsp;<span class="lp-gradient-gold lp-font-disp italic">Table.</span>
        </h2>
      </div>

      <div class="grid lg:grid-cols-2 gap-12 items-stretch">

        <!-- Left: Image + Info -->
        <div class="lp-reveal space-y-6">
          <div class="relative rounded-2xl overflow-hidden aspect-[4/3]">
            <LpImage src="https://img.rocket.new/generatedImages/rocket_gen_img_186321a6e-1772087389567.png" alt="Elegant restaurant dining room" :fill="true" class="object-cover" />
            <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.1) 50%, transparent 100%)" />
            <div class="absolute bottom-6 left-6 right-6 lp-glass-card rounded-xl p-4">
              <p class="text-white lp-font-disp italic text-lg">"A table reserved is a memory in the making."</p>
              <p class="text-white/70 text-sm mt-1">— Chef Arjun Malhotra</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div v-for="info in infoCards" :key="info.label" class="lp-luxury-card p-4 flex items-start gap-3">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:rgba(200,150,90,0.1)">
                <component :is="info.icon" :size="16" style="color:var(--lp-primary)" />
              </div>
              <div>
                <p class="text-xs font-medium" style="color:var(--lp-muted-fg)">{{ info.label }}</p>
                <p class="text-sm font-semibold mt-0.5" style="color:var(--lp-fg)">{{ info.value }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Form / Success -->
        <div class="lp-reveal lp-stagger-1">
          <!-- Success State -->
          <div v-if="submitted" class="lp-luxury-card p-10 flex flex-col items-center justify-center text-center h-full gap-5">
            <div class="w-20 h-20 rounded-full lp-gradient-bg-gold flex items-center justify-center lp-glow-gold">
              <CheckIcon :size="36" color="white" />
            </div>
            <h3 class="text-2xl lp-font-disp font-semibold" style="color:var(--lp-fg)">Reservation Confirmed!</h3>
            <p class="leading-relaxed" style="color:var(--lp-muted-fg)">
              Thank you, {{ form.name.split(' ')[0] }}. Your table for {{ form.guests }} on {{ form.date }} at {{ form.time }} has been reserved. A confirmation will arrive at {{ form.email }} shortly.
            </p>
            <button @click="resetForm" class="lp-btn-outline px-6 py-2.5 text-sm mt-2">Make Another Reservation</button>
          </div>

          <!-- Form -->
          <form v-else @submit.prevent="handleSubmit" class="lp-luxury-card p-8 space-y-5 h-full">
            <div class="grid sm:grid-cols-2 gap-5">
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color:var(--lp-muted-fg)">Full Name</label>
                <input v-model="form.name" type="text" placeholder="Priya Sharma" :class="['lp-input', errors.name ? 'lp-error' : '']" />
                <p v-if="errors.name" class="text-xs text-red-500 mt-1">{{ errors.name }}</p>
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color:var(--lp-muted-fg)">Email</label>
                <input v-model="form.email" type="email" placeholder="priya@example.com" :class="['lp-input', errors.email ? 'lp-error' : '']" />
                <p v-if="errors.email" class="text-xs text-red-500 mt-1">{{ errors.email }}</p>
              </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color:var(--lp-muted-fg)">Phone</label>
                <input v-model="form.phone" type="tel" placeholder="9876543210" :class="['lp-input', errors.phone ? 'lp-error' : '']" />
                <p v-if="errors.phone" class="text-xs text-red-500 mt-1">{{ errors.phone }}</p>
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color:var(--lp-muted-fg)">Guests</label>
                <input v-model="form.guests" type="number" min="1" max="20" :class="['lp-input', errors.guests ? 'lp-error' : '']" />
                <p v-if="errors.guests" class="text-xs text-red-500 mt-1">{{ errors.guests }}</p>
              </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color:var(--lp-muted-fg)">Date</label>
                <input v-model="form.date" type="date" :class="['lp-input', errors.date ? 'lp-error' : '']" />
                <p v-if="errors.date" class="text-xs text-red-500 mt-1">{{ errors.date }}</p>
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color:var(--lp-muted-fg)">Time</label>
                <select v-model="form.time" :class="['lp-input', errors.time ? 'lp-error' : '']">
                  <option value="">Select time</option>
                  <option v-for="t in timeSlots" :key="t" :value="t">{{ t }}</option>
                </select>
                <p v-if="errors.time" class="text-xs text-red-500 mt-1">{{ errors.time }}</p>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color:var(--lp-muted-fg)">Occasion</label>
              <select v-model="form.occasion" class="lp-input">
                <option v-for="o in occasions" :key="o" :value="o">{{ o }}</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color:var(--lp-muted-fg)">Special Requests</label>
              <textarea v-model="form.message" rows="3" placeholder="Dietary requirements, seating preferences, allergies..." class="lp-input resize-none" />
            </div>

            <button type="submit" :disabled="submitting" class="lp-btn-primary w-full py-4 text-base justify-center gap-2" style="border-radius:var(--lp-radius)">
              <span v-if="submitting" class="w-5 h-5 border-2 border-white/40 border-t-white rounded-full animate-spin" />
              <template v-else>
                <CalendarIcon :size="18" color="white" />
                Confirm Reservation
              </template>
            </button>
          </form>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Clock as ClockIcon, MapPin as MapPinIcon, Phone as PhoneIcon, Calendar as CalendarIcon, Check as CheckIcon } from 'lucide-vue-next';
import { useReveal } from '@/composables/useReveal.js';
import LpImage from './LpImage.vue';

const sectionRef = ref(null);
useReveal(sectionRef, { threshold: 0.05 });

const timeSlots = ['12:00 PM','12:30 PM','1:00 PM','1:30 PM','2:00 PM','7:00 PM','7:30 PM','8:00 PM','8:30 PM','9:00 PM','9:30 PM'];
const occasions = ['None','Birthday','Anniversary','Business Dinner','Proposal','Family Gathering','Other'];

const infoCards = [
  { icon: ClockIcon,  label: 'Lunch Hours', value: 'Mon–Sun: 12pm–3pm' },
  { icon: ClockIcon,  label: 'Dinner Hours', value: 'Mon–Sun: 7pm–11pm' },
  { icon: MapPinIcon, label: 'Location',     value: 'Connaught Place, New Delhi' },
  { icon: PhoneIcon,  label: 'Call Us',      value: '+91 11 4567 8900' },
];

const defaultForm = { name: '', email: '', phone: '', date: '', time: '', guests: '2', occasion: 'None', message: '' };
const form      = reactive({ ...defaultForm });
const errors    = reactive({});
const submitted = ref(false);
const submitting = ref(false);

function validate() {
  Object.keys(errors).forEach(k => delete errors[k]);
  if (!form.name.trim())                        errors.name   = 'Full name is required';
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) errors.email = 'Please enter a valid email';
  if (!/^[6-9]\d{9}$/.test(form.phone))         errors.phone  = 'Enter a valid 10-digit Indian mobile number';
  if (!form.date)                               errors.date   = 'Please select a date';
  if (!form.time)                               errors.time   = 'Please select a time';
  if (!form.guests || parseInt(form.guests) < 1) errors.guests = 'At least 1 guest required';
  return Object.keys(errors).length === 0;
}

async function handleSubmit() {
  if (!validate()) return;
  submitting.value = true;
  await new Promise(r => setTimeout(r, 1500));
  submitting.value = false;
  submitted.value  = true;
}

function resetForm() {
  Object.assign(form, defaultForm);
  Object.keys(errors).forEach(k => delete errors[k]);
  submitted.value = false;
}
</script>
