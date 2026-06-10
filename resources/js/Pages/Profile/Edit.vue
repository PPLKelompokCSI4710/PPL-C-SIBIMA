<script setup>
    import { computed } from 'vue';
    import { usePage, Head } from '@inertiajs/vue3';
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import StudentLayout from '@/Layouts/StudentLayout.vue';
    import StaffLayout from '@/Layouts/StaffLayout.vue';
    import DeleteUserForm from './Partials/DeleteUserForm.vue';
    import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
    import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';

    defineProps({
        mustVerifyEmail: {
            type: Boolean,
        },
        status: {
            type: String,
        },
    });

    const page = usePage();
    const roles = computed(() => page.props.auth.user?.roles || []);
    
    // Choose the layout based on user role
    const currentLayout = computed(() => {
        if (roles.value.includes('mahasiswa')) {
            return StudentLayout;
        } else if (roles.value.includes('admin') || roles.value.includes('dosen')) {
            return StaffLayout;
        }
        return AuthenticatedLayout;
    });
</script>

<template>
    <Head title="Profile" />

    <component :is="currentLayout">
        <template #header v-if="currentLayout === AuthenticatedLayout">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Profile</h2>
        </template>

        <div class="py-12 max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="bg-white p-6 md:p-8 border border-slate-200/60 rounded-3xl shadow-sm">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div class="bg-white p-6 md:p-8 border border-slate-200/60 rounded-3xl shadow-sm">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <div class="bg-white p-6 md:p-8 border border-slate-200/60 rounded-3xl shadow-sm">
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </component>
</template>
