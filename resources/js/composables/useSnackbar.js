import { toast } from 'vue-sonner';

export function useSnackbar() {
  return {
    success: (msg, opts) => toast.success(msg, opts),
    error:   (msg, opts) => toast.error(msg, opts),
    info:    (msg, opts) => toast.info(msg, opts),
    warning: (msg, opts) => toast.warning(msg, opts),
    show:    (msg, opts) => toast(msg, opts),
  };
}
