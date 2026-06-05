<template>
    <GuestLayout>
        <Head title="Lupa Kata Sandi - SIBIMA" />

        <div class="mb-8 text-center">
            <div
                class="w-16 h-16 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-4"
            >
                <KeyIcon class="w-8 h-8" />
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Lupa Kata Sandi?</h1>
            <p class="text-sm text-slate-500 mt-2 font-medium leading-relaxed">
                Jangan khawatir. Masukkan alamat email Anda dan kami akan mengirimkan tautan
                pemulihan kata sandi.
            </p>
        </div>

        <div
            v-if="status"
            class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-700 text-sm font-bold border border-emerald-100"
        >
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

            <div class="pt-2">
                <PrimaryButton
                    class="w-full !py-4 !rounded-xl !bg-blue-600 hover:!bg-blue-700 !text-white !font-black !text-base shadow-xl shadow-blue-600/20 transition-all hover:scale-[1.02] active:scale-[0.98]"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="!form.processing">Kirim Tautan Pemulihan</span>
                    <span v-else>Mengirim...</span>
                </PrimaryButton>
            </div>

            <div class="text-center">
                <Link
                    :href="route('login')"
                    class="text-sm font-bold text-slate-400 hover:text-blue-600 transition-colors flex items-center justify-center gap-2"
                >
                    <ChevronLeftIcon class="w-4 h-4" /> Kembali ke Halaman Masuk
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { Head, useForm, Link } from '@inertiajs/vue3';
    import { MailIcon, KeyIcon, ChevronLeftIcon } from 'lucide-vue-next';

    defineProps({
        status: {
            type: String,
        },
    });

    const form = useForm({
        email: '',
    });

    const submit = () => {
        form.post(route('password.email'));
    };
</script>
