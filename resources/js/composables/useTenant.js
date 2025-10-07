import { ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3";

export function useTenant() {
    const page = usePage();
    const currentTenant = computed(() => page.props.tenant ?? null);

    function tenantRoute(routeName, params = {}) {
        if (!currentTenant.value) return null; // avoid calling route() without tenant
        try {
            return route(routeName, { tenant: currentTenant.value, ...params });
        } catch (e) {
            console.warn(`Invalid route or missing tenant for ${routeName}`, e);
            return null;
        }
    }

    return { tenantRoute, currentTenant };
}
