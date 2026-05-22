<template>
    <GuestLayout>
        <Head title="Masuk ke Portal - SIBIMA" />

        <div class="mb-8">
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Selamat Datang</h1>
            <p class="text-slate-500 mt-2 font-medium">
                Masuk untuk melanjutkan ke portal akademik SIBIMA.
            </p>
        </div>

        <div
            v-if="status"
            class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-700 text-sm font-bold border border-emerald-100 flex items-center gap-3"
        >
            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
            {{ status }}
        </div>

        <form class="space-y-6" @submit.prevent="submit">
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
                        placeholder="nama@student.telkomuniversity.ac.id"
                        autocomplete="username"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-bold text-slate-700"
                        >Kata Sandi</label
                    >
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs font-bold text-blue-600 hover:text-blue-700 transition-colors"
                    >
                        Lupa kata sandi?
                    </Link>
                </div>
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
                        autocomplete="current-password"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center cursor-pointer group">
                    <Checkbox
                        v-model:checked="form.remember"
                        name="remember"
                        class="!rounded !border-slate-300 text-blue-600 focus:ring-blue-600/20"
                    />
                    <span
                        class="ms-2 text-sm font-bold text-slate-500 group-hover:text-slate-700 transition-colors"
                        >Ingat saya</span
                    >
                </label>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full !py-4 !rounded-xl !bg-blue-600 hover:!bg-blue-700 !text-white !font-black !text-base shadow-xl shadow-blue-600/20 transition-all hover:scale-[1.02] active:scale-[0.98] flex justify-center items-center gap-2"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="!form.processing">Masuk Sekarang</span>
                    <span v-else class="flex items-center gap-2">
                        <div
                            class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"
                        />
                        Memproses...
                    </span>
                </PrimaryButton>
            </div>

            <div class="text-center pt-4">
                <p class="text-sm text-slate-500 font-medium">
                    Belum punya akun?
                    <Link
                        :href="route('register')"
                        class="text-blue-600 font-bold hover:underline underline-offset-4"
                    >
                        Daftar Sekarang
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
    import Checkbox from '@/Components/Checkbox.vue';
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';
    import { MailIcon, LockIcon } from 'lucide-vue-next';

    defineProps({
        canResetPassword: {
            type: Boolean,
        },
        status: {
            type: String,
            default: '',
        },
    });

    const form = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = () => {
        form.post(route('login'), {
            onFinish: () => form.reset('password'),
        });
    };
</script>
