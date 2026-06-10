import { ref, watch, onUnmounted } from 'vue';

/**
 * Animate a number from 0 to `target` over `duration` ms
 * when `active` becomes true.
 */
export function useCountUp(target, duration = 2000, active) {
  const count = ref(0);
  let timer = null;

  watch(active, (val) => {
    if (!val) return;
    let start = 0;
    const step = target / (duration / 16);
    timer = setInterval(() => {
      start += step;
      if (start >= target) {
        count.value = target;
        clearInterval(timer);
      } else {
        count.value = Math.floor(start);
      }
    }, 16);
  });

  onUnmounted(() => clearInterval(timer));

  return count;
}
