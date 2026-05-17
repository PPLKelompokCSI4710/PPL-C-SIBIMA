<template>
    <GuestLayout>
        <Head title="Verifikasi Email - SIBIMA" />

        <div class="mb-8 text-center">
            <div
                class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4"
            >
                <MailIcon class="w-8 h-8" />
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Verifikasi Email</h1>
            <p class="text-sm text-slate-500 mt-2 font-medium leading-relaxed">
                Terima kasih telah mendaftar! Sebelum memulai, harap verifikasi alamat email Anda
                dengan mengklik tautan yang baru saja kami kirimkan.
            </p>
        </div>

        <div
            v-if="verificationLinkSent"
            class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-700 text-sm font-bold border border-emerald-100 flex items-center gap-3"
        >
            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
            Tautan verifikasi baru telah dikirim ke email Anda.
        </div>

        <form @submit.prevent="submit">
            <div class="space-y-4">
                <PrimaryButton
                    class="w-full !py-4 !rounded-xl !bg-blue-600 hover:!bg-blue-700 !text-white !font-black !text-base shadow-xl shadow-blue-600/20 transition-all hover:scale-[1.02] active:scale-[0.98]"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Kirim Ulang Email Verifikasi
                </PrimaryButton>

                <div class="flex justify-center">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="text-sm font-bold text-slate-400 hover:text-red-600 transition-colors"
                    >
                        Keluar (Log Out)
                    </Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
    import { computed } from 'vue';
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';
    import { MailIcon } from 'lucide-vue-next';

    const props = defineProps({
        status: {
            type: String,
        },
    });

    const form = useForm({});

    const submit = () => {
        form.post(route('verification.send'));
    };

    const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>
