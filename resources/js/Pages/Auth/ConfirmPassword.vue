<template>
    <GuestLayout>
        <Head title="Konfirmasi Kata Sandi - SIBIMA" />

        <div class="mb-8 text-center">
            <div
                class="w-16 h-16 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mx-auto mb-4"
            >
                <LockIcon class="w-8 h-8" />
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Area Keamanan</h1>
            <p class="text-sm text-slate-500 mt-2 font-medium leading-relaxed">
                Ini adalah area aman. Harap konfirmasi kata sandi Anda sebelum melanjutkan.
            </p>
        </div>

        <form class="space-y-6" @submit.prevent="submit">
            <div>
                <label for="password" class="block text-sm font-bold text-slate-700 mb-2"
                    >Kata Sandi</label
                >
                <div class="relative group">
                    <div
                        class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors"
                    >
                        <KeyIcon class="w-5 h-5" />
                    </div>
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="block w-full pl-11 !rounded-xl !border-slate-200 !bg-slate-50/50 focus:!bg-white focus:!ring-4 focus:!ring-blue-600/10 transition-all"
                        required
                        placeholder="••••••••"
                        autocomplete="current-password"
                        autofocus
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full !py-4 !rounded-xl !bg-blue-600 hover:!bg-blue-700 !text-white !font-black !text-base shadow-xl shadow-blue-600/20 transition-all hover:scale-[1.02] active:scale-[0.98]"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Konfirmasi Akses
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { Head, useForm } from '@inertiajs/vue3';
    import { LockIcon, KeyIcon } from 'lucide-vue-next';

    const form = useForm({
        password: '',
    });

    const submit = () => {
        form.post(route('password.confirm'), {
            onFinish: () => form.reset(),
        });
    };
</script>
