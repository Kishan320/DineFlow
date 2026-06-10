import { onMounted, onUnmounted } from 'vue';

/**
 * Attach an IntersectionObserver to all `.lp-reveal` elements
 * inside the given container ref and add `.lp-revealed` when in view.
 */
export function useReveal(containerRef, options = {}) {
  let observer = null;

  onMounted(() => {
    observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((e) => {
          if (e.isIntersecting) e.target.classList.add('lp-revealed');
        });
      },
      { threshold: 0.08, ...options }
    );
    containerRef.value?.querySelectorAll('.lp-reveal').forEach((el) => observer.observe(el));
  });

  onUnmounted(() => observer?.disconnect());
}
