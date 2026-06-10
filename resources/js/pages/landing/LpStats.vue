<template>
  <section id="stats" ref="sectionRef" class="py-24 px-4 sm:px-6 lp-reveal">
    <div class="max-w-7xl mx-auto">
      <div class="lp-gold-divider mb-16" />

      <div class="grid grid-cols-2 lg:grid-cols-4 gap-12">
        <div v-for="stat in stats" :key="stat.label" class="text-center space-y-3">
          <div class="w-14 h-14 mx-auto rounded-2xl lp-gradient-bg-gold flex items-center justify-center lp-glow-gold">
            <component :is="stat.icon" :size="26" color="white" />
          </div>
          <p class="text-4xl md:text-5xl lp-font-disp font-semibold" style="color:var(--lp-fg)">
            {{ stat.prefix }}{{ displayValue(stat) }}{{ stat.suffix }}
          </p>
          <p class="text-sm font-semibold uppercase tracking-widest" style="color:var(--lp-muted-fg)">{{ stat.label }}</p>
        </div>
      </div>

      <div class="lp-gold-divider mt-16" />
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Users as UsersIcon, Sparkles as SparklesIcon, Trophy as TrophyIcon, Clock as ClockIcon } from 'lucide-vue-next';

const sectionRef = ref(null);
const active = ref(false);

const stats = [
  { icon: UsersIcon,    value: 120000, suffix: '+', label: 'Guests Served',      prefix: '' },
  { icon: SparklesIcon, value: 200,    suffix: '+', label: 'Signature Dishes',   prefix: '' },
  { icon: TrophyIcon,   value: 9,      suffix: '',  label: 'National Awards',    prefix: '' },
  { icon: ClockIcon,    value: 18,     suffix: '',  label: 'Years of Excellence', prefix: '' },
];

// Counts per stat
const counts = ref(stats.map(() => 0));
let timers = [];

function displayValue(stat) {
  const i = stats.indexOf(stat);
  const c = counts.value[i];
  return c >= 1000 ? `${(c / 1000).toFixed(c % 1000 === 0 ? 0 : 1)}K` : c.toString();
}

function startCounting() {
  stats.forEach((stat, i) => {
    let start = 0;
    const step = stat.value / (2000 / 16);
    const t = setInterval(() => {
      start += step;
      if (start >= stat.value) {
        counts.value[i] = stat.value;
        clearInterval(t);
      } else {
        counts.value[i] = Math.floor(start);
      }
    }, 16);
    timers.push(t);
  });
}

let observer = null;
onMounted(() => {
  observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting && !active.value) {
        active.value = true;
        e.target.classList.add('lp-revealed');
        startCounting();
      }
    });
  }, { threshold: 0.3 });
  if (sectionRef.value) observer.observe(sectionRef.value);
});
onUnmounted(() => { observer?.disconnect(); timers.forEach(clearInterval); });
</script>
