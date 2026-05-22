<template>
    <GuestLayout>
        <Head title="Atur Ulang Kata Sandi - SIBIMA" />

        <div class="mb-8">
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Atur Ulang</h1>
            <p class="text-slate-500 mt-2 font-medium">Buat kata sandi baru untuk akun Anda.</p>
        </div>

        <form class="space-y-5" @submit.prevent="submit">
            <div>
                <label for="email" class="block text-sm font-bold text-slate-700 mb-2"
                    >Alamat Email</label
                >
                <div class="relative group">
                    <div
                        class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors"
                    >
                        <MailIcon class="w-5 h-5" />
                    </div>
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="block w-full pl-11 !rounded-xl !border-slate-200 !bg-slate-50/50 focus:!bg-white focus:!ring-4 focus:!ring-blue-600/10 transition-all"
                        required
                        autofocus
                        autocomplete="username"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="grid grid-cols-1 gap-5">
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-2"
                        >Kata Sandi Baru</label
                    >
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors"
                        >
                            <LockIcon class="w-5 h-5" />
                        </div>
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="block w-full pl-11 !rounded-xl !border-slate-200 !bg-slate-50/50 focus:!bg-white focus:!ring-4 focus:!ring-blue-600/10 transition-all"
                            required
                            placeholder="••••••••"
                            autocomplete="new-password"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <label
                        for="password_confirmation"
                        class="block text-sm font-bold text-slate-700 mb-2"
                        >Konfirmasi Kata Sandi</label
                    >
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors"
                        >
                            <ShieldCheckIcon class="w-5 h-5" />
                        </div>
                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="block w-full pl-11 !rounded-xl !border-slate-200 !bg-slate-50/50 focus:!bg-white focus:!ring-4 focus:!ring-blue-600/10 transition-all"
                            required
                            placeholder="••••••••"
                            autocomplete="new-password"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div class="pt-4">
                <PrimaryButton
                    class="w-full !py-4 !rounded-xl !bg-blue-600 hover:!bg-blue-700 !text-white !font-black !text-base shadow-xl shadow-blue-600/20 transition-all hover:scale-[1.02] active:scale-[0.98]"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Simpan Kata Sandi Baru
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
    import { MailIcon, LockIcon, ShieldCheckIcon } from 'lucide-vue-next';

    const props = defineProps({
        email: {
            type: String,
            required: true,
        },
        token: {
            type: String,
            required: true,
        },
    });

    const form = useForm({
        token: props.token,
        email: props.email,
        password: '',
        password_confirmation: '',
    });

    const submit = () => {
        form.post(route('password.store'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    };
</script>
