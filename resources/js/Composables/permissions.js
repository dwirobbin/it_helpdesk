import { usePage } from "@inertiajs/vue3";

const page = usePage();

export function usePermission() {
    const hasAnyRole = (names) => {
        const roles = page.props.auth.user.data.role.name;
        return names.some((name) => roles.includes(name));
    };

    return { hasAnyRole };
}
