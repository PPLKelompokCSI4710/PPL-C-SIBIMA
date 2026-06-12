<script setup>
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { Link, useForm, usePage } from '@inertiajs/vue3';

    defineProps({
        mustVerifyEmail: {
            type: Boolean,
        },
        status: {
            type: String,
        },
    });

    const user = usePage().props.auth.user;
    const isMahasiswa = user.roles?.includes('mahasiswa') || false;
    const isStaff = user.roles?.includes('dosen') || user.roles?.includes('admin') || false;

    const form = useForm({
        name: user.name,
        email: user.email,
        // Mahasiswa fields
        nim: isMahasiswa ? (user.mahasiswa?.nim || '') : '',
        nama_lengkap: isMahasiswa ? (user.mahasiswa?.nama_lengkap || '') : '',
        program_studi: isMahasiswa 
            ? (user.mahasiswa?.program_studi || '') 
            : (isStaff ? (user.program_studi || '') : ''),
        fakultas: isMahasiswa 
            ? (user.mahasiswa?.fakultas || '') 
            : (isStaff ? (user.fakultas || '') : ''),
        angkatan: isMahasiswa ? (user.mahasiswa?.angkatan || '') : '',
        semester: isMahasiswa ? (user.mahasiswa?.semester || '') : '',
        no_telepon: isMahasiswa ? (user.mahasiswa?.no_telepon || '') : '',
        tanggal_lahir: isMahasiswa ? (user.mahasiswa?.tanggal_lahir ? user.mahasiswa.tanggal_lahir.substring(0, 10) : '') : '',
        alamat: isMahasiswa ? (user.mahasiswa?.alamat || '') : '',
        // Staff/Dosen fields
        kode_dosen: isStaff ? (user.kode_dosen || '') : '',
        kuota_pembimbingan: isStaff ? (user.kuota_pembimbingan || 0) : 0,
    });
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <form class="mt-6 space-y-6" @submit.prevent="form.patch(route('profile.update'))">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <InputLabel for="name" value="Name" />

                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="name"
                    />

                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="email" value="Email" />

                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full"
                        required
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
            </div>

            <!-- ─── Mahasiswa Form Fields ─── -->
            <div v-if="isMahasiswa" class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-slate-100 pt-6">
                <div>
                    <InputLabel for="nim" value="NIM" />
                    <TextInput
                        id="nim"
                        v-model="form.nim"
                        type="text"
                        class="mt-1 block w-full bg-slate-50"
                        required
                        disabled
                        title="NIM tidak dapat diubah"
                    />
                    <InputError class="mt-2" :message="form.errors.nim" />
                </div>

                <div>
                    <InputLabel for="nama_lengkap" value="Nama Lengkap" />
                    <TextInput
                        id="nama_lengkap"
                        v-model="form.nama_lengkap"
                        type="text"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.nama_lengkap" />
                </div>

                <div>
                    <InputLabel for="program_studi" value="Program Studi" />
                    <TextInput
                        id="program_studi"
                        v-model="form.program_studi"
                        type="text"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.program_studi" />
                </div>

                <div>
                    <InputLabel for="fakultas" value="Fakultas" />
                    <TextInput
                        id="fakultas"
                        v-model="form.fakultas"
                        type="text"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.fakultas" />
                </div>

                <div>
                    <InputLabel for="angkatan" value="Angkatan" />
                    <TextInput
                        id="angkatan"
                        v-model="form.angkatan"
                        type="number"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.angkatan" />
                </div>

                <div>
                    <InputLabel for="semester" value="Semester" />
                    <TextInput
                        id="semester"
                        v-model="form.semester"
                        type="number"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.semester" />
                </div>

                <div>
                    <InputLabel for="no_telepon" value="No Telepon" />
                    <TextInput
                        id="no_telepon"
                        v-model="form.no_telepon"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" :message="form.errors.no_telepon" />
                </div>

                <div>
                    <InputLabel for="tanggal_lahir" value="Tanggal Lahir" />
                    <TextInput
                        id="tanggal_lahir"
                        v-model="form.tanggal_lahir"
                        type="date"
                        class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" :message="form.errors.tanggal_lahir" />
                </div>

                <div class="col-span-1 md:col-span-2">
                    <InputLabel for="alamat" value="Alamat" />
                    <textarea
                        id="alamat"
                        v-model="form.alamat"
                        rows="3"
                        class="mt-1 block w-full border-gray-300 focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm"
                    ></textarea>
                    <InputError class="mt-2" :message="form.errors.alamat" />
                </div>
            </div>

            <!-- ─── Staff/Dosen Form Fields ─── -->
            <div v-if="isStaff" class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-slate-100 pt-6">
                <div>
                    <InputLabel for="kode_dosen" value="Kode Dosen" />
                    <TextInput
                        id="kode_dosen"
                        v-model="form.kode_dosen"
                        type="text"
                        class="mt-1 block w-full bg-slate-50"
                        disabled
                        title="Kode dosen tidak dapat diubah"
                    />
                    <InputError class="mt-2" :message="form.errors.kode_dosen" />
                </div>

                <div>
                    <InputLabel for="program_studi" value="Program Studi" />
                    <TextInput
                        id="program_studi"
                        v-model="form.program_studi"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" :message="form.errors.program_studi" />
                </div>

                <div>
                    <InputLabel for="fakultas" value="Fakultas" />
                    <TextInput
                        id="fakultas"
                        v-model="form.fakultas"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" :message="form.errors.fakultas" />
                </div>

                <div>
                    <InputLabel for="kuota_pembimbingan" value="Kuota Pembimbingan" />
                    <TextInput
                        id="kuota_pembimbingan"
                        v-model="form.kuota_pembimbingan"
                        type="number"
                        class="mt-1 block w-full bg-slate-50"
                        disabled
                        title="Kuota pembimbingan hanya dapat diubah oleh Admin"
                    />
                    <InputError class="mt-2" :message="form.errors.kuota_pembimbingan" />
                </div>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing"> Save </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
