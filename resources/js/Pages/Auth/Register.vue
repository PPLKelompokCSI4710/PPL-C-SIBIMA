<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';
    import { UserIcon, MailIcon, LockIcon, ShieldCheckIcon } from 'lucide-vue-next';

    const form = useForm({
        name: '',
        email: '',
        role: 'mahasiswa',
        password: '',
        password_confirmation: '',
    });

    const submit = () => {
        form.post(route('register'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    };
</script>

<template>
    <GuestLayout>
        <Head title="Daftar Akun Baru - SIBIMA" />

        <div class="mb-8">
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Buat Akun</h1>
            <p class="text-slate-500 mt-2 font-medium">
                Lengkapi data diri Anda untuk memulai bimbingan.
            </p>
        </div>

        <form class="space-y-5" @submit.prevent="submit">
            <div>
                <label for="name" class="block text-sm font-bold text-slate-700 mb-2"
                    >Nama Lengkap</label
                >
                <div class="relative group">
                    <div
                        class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors"
                    >
                        <UserIcon class="w-5 h-5" />
                    </div>
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="block w-full pl-11 !rounded-xl !border-slate-200 !bg-slate-50/50 focus:!bg-white focus:!ring-4 focus:!ring-blue-600/10 transition-all"
                        required
                        autofocus
                        placeholder="Nama Lengkap Anda"
                        autocomplete="name"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

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
                        placeholder="nama@student.telkomuniversity.ac.id"
                        autocomplete="username"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Mendaftar Sebagai</label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="relative cursor-pointer group">
                        <input
                            v-model="form.role"
                            type="radio"
                            value="mahasiswa"
                            class="sr-only peer"
                            name="role"
                        />
                        <div
                            class="p-3 text-center border-2 border-slate-200 rounded-xl peer-checked:border-blue-600 peer-checked:bg-blue-50/50 hover:bg-slate-50 transition-all"
                        >
                            <p
                                class="text-xs font-black text-slate-400 peer-checked:text-blue-600 uppercase tracking-wider"
                            >
                                Mahasiswa
                            </p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer group">
                        <input
                            v-model="form.role"
                            type="radio"
                            value="dosen"
                            class="sr-only peer"
                            name="role"
                        />
                        <div
                            class="p-3 text-center border-2 border-slate-200 rounded-xl peer-checked:border-blue-600 peer-checked:bg-blue-50/50 hover:bg-slate-50 transition-all"
                        >
                            <p
                                class="text-xs font-black text-slate-400 peer-checked:text-blue-600 uppercase tracking-wider"
                            >
                                Dosen
                            </p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer group">
                        <input
                            v-model="form.role"
                            type="radio"
                            value="admin"
                            class="sr-only peer"
                            name="role"
                        />
                        <div
                            class="p-3 text-center border-2 border-slate-200 rounded-xl peer-checked:border-blue-600 peer-checked:bg-blue-50/50 hover:bg-slate-50 transition-all"
                        >
                            <p
                                class="text-xs font-black text-slate-400 peer-checked:text-blue-600 uppercase tracking-wider"
                            >
                                Admin
                            </p>
                        </div>
                    </label>
                </div>
                <InputError class="mt-2" :message="form.errors.role" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-2"
                        >Kata Sandi</label
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
                </div>

                <div>
                    <label
                        for="password_confirmation"
                        class="block text-sm font-bold text-slate-700 mb-2"
                        >Konfirmasi</label
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
                </div>
            </div>
            <InputError class="mt-1" :message="form.errors.password" />

            <div class="pt-4">
                <PrimaryButton
                    class="w-full !py-4 !rounded-xl !bg-blue-600 hover:!bg-blue-700 !text-white !font-black !text-base shadow-xl shadow-blue-600/20 transition-all hover:scale-[1.02] active:scale-[0.98] flex justify-center items-center gap-2"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="!form.processing">Daftar Akun Baru</span>
                    <span v-else class="flex items-center gap-2">
                        <div
                            class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"
                        />
                        Mendaftarkan...
                    </span>
                </PrimaryButton>
            </div>

            <div class="text-center pt-4">
                <p class="text-sm text-slate-500 font-medium">
                    Sudah punya akun?
                    <Link
                        :href="route('login')"
                        class="text-blue-600 font-bold hover:underline underline-offset-4"
                    >
                        Masuk ke Portal
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
