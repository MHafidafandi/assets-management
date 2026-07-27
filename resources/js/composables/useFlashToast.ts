import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { toast } from 'vue-sonner';

/**
 * Watches Inertia shared flash data and triggers Sonner toasts.
 * Call once in your layout or app-level component.
 */
export function useFlashToast() {
    const page = usePage();

    watch(
        () => page.props.flash,
        (flash) => {
            if (flash?.success) {
                toast.success(flash.success);
            }

            if (flash?.error) {
                toast.error(flash.error);
            }
        },
        { immediate: true },
    );
}
