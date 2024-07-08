<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import TextareaInput from "@/Components/TextareaInput.vue";
import { usePage } from '@inertiajs/vue3';

const setting = computed(() => usePage().props.setting.data);

const props = defineProps({
    ticket: {
        type: Object,
        default: () => { },
    },
});

const generateUrl = (filePath) => window.location.origin + filePath;

// Begin set status color
const statusColor = computed(() => {
    return {
        'Dalam Antrian': 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'Menunggu Diproses': 'bg-blue-100 text-blue-800 border-blue-400',
        'Sedang Diproses': 'bg-purple-100 text-purple-800 border-purple-400',
        'Terselesaikan': 'bg-green-100 text-green-800 border-green-400',
    }[props.ticket.data.status];
});
// End set status color
</script>

<template>

    <Head title="Tiket" />

    <AuthenticatedLayout>
        <div class="p-4 bg-white block sm:flex items-center justify-between shadow-sm border-b border-gray-200 lg:mt-1">
            <div class="w-full flex flex-col justify-left sm:flex-row sm:justify-between gap-y-3">
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 order-2 sm:order-1">Detail Tiket</h1>
                <nav class="flex order-1 sm:order-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <Link :href="route('dashboard')" class="text-gray-700 hover:text-gray-900 inline-flex items-center">
                            <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                </path>
                            </svg>
                            Home
                            </Link>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <Link :href="route('tickets.index')" class="text-gray-700 hover:text-gray-900 ml-1 md:ml-2 text-sm font-medium">
                                Tiket
                                </Link>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-400 ml-1 md:ml-2 text-sm font-medium" aria-current="page">Detail</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="p-4">
            <div class="p-6 bg-white block border border-gray-200 rounded-lg shadow-sm max-w-6xl mx-auto" id="invoice">
                <div class="grid grid-cols-2 items-center">
                    <div>
                        <img class="rounded-md w-32 h-20 object-cover"
                            :src="[setting.company_logo ?? generateUrl('/assets/images/default-img.png')]" alt="Company Logo">
                    </div>

                    <div class="text-right">
                        <p>
                            {{ setting.company_name }}
                        </p>
                        <p class="text-gray-500 text-sm">
                            Alamat: {{ setting.company_address ?? 'Belum di setel' }}
                        </p>
                        <p class="text-gray-500 text-sm mt-1">
                            No. Telp: {{ setting.company_telp ?? 'Belum di setel' }}
                        </p>
                    </div>
                </div>

                <!-- Client info -->
                <div class="grid grid-cols-2 items-center mt-8">
                    <div>
                        <p class="font-bold text-gray-800">
                            Dari :
                        </p>
                        <p class="text-gray-500">
                            Divisi: {{ ticket.data.user.employee.department?.section ?? 'Belum disetel' }}
                        </p>
                        <p class="text-gray-500">
                            Jabatan: {{ ticket.data.user.employee.position?.title ?? 'Belum disetel' }}
                        </p>
                        <p class="text-gray-500">
                            Nama: {{ ticket.data.user.name }}
                        </p>
                        <p class="text-gray-500">
                            Email: {{ ticket.data.user.email }}
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="">
                            No. Tiket:
                            <span class="text-gray-500">#{{ ticket.data.ticket_number }}</span>
                        </p>
                        <p>
                            Tgl Pengaduan: <span class="text-gray-500">{{ ticket.data.created_at.locale_day_date }}</span>
                        </p>
                        <p>
                            Waktu Pengaduan: <span class="text-gray-500">{{ ticket.data.created_at.locale_time }}</span>
                        </p>
                        <p>
                            Tgl Selesai:
                            <span class="text-gray-500">
                                {{ ticket.data.status === 'Terselesaikan' ? ticket.updated_at.locale_day_date_time : 'Belum Terselesaikan' }}
                            </span>
                        </p>
                        <p>
                            Status: <span :class="statusColor" class="text-sm font-medium me-2 px-2.5 py-0.5 rounded border">
                                {{ ticket.data.status }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="-mx-4 mt-8 flow-root sm:mx-0">
                    <div class="min-w-full grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-6">
                            <h2 class="2xl font-medium border-b pb-5">Pengaduan</h2>
                            <div>
                                <InputLabel for="title" value="Keluhan" />
                                <TextInput type="text" id="title" v-model="ticket.data.title" placeholder="Keluhan..." disabled />
                            </div>
                            <div>
                                <InputLabel for="description" value="Deskripsi" />
                                <TextareaInput id="description" rows="9" v-model="ticket.data.description" placeholder="Deskripsi..." disabled />
                            </div>
                            <div>
                                <InputLabel for="picture-complaint" value="Gambar Pengaduan" />
                                <img class="mb-4 rounded-lg w-28 h-28 sm:mb-0 object-cover"
                                    :src="[ticket.data.image ?? generateUrl('/assets/images/default-img.png')]" alt="Foto Keluhan">
                            </div>
                        </div>
                        <div class="space-y-6">
                            <h2 class="2xl font-medium border-b pb-5">Tanggapan</h2>
                            <div>
                                <InputLabel for="text" value="Teks" />
                                <TextareaInput id="text" v-if="ticket.data.respond !== null" v-model="ticket.data.respond.text" rows="13"
                                    placeholder="Teks..." disabled />
                                <TextareaInput id="text" v-else rows="13" placeholder="Teks..." disabled />
                            </div>
                            <div class="pt-3">
                                <InputLabel for="picture-respond" value="Gambar Tanggapan" />
                                <img class="mb-4 rounded-lg w-28 h-28 sm:mb-0 object-cover"
                                    :src="[ticket.data.respond?.image ?? generateUrl('/assets/images/default-img.png')]" alt="Gambar Tanggapan">
                            </div>
                        </div>
                    </div>
                </div>

                <!--  Footer  -->
                <div class="border-t-2 pt-4 text-xs text-gray-500 text-center mt-16">
                    Harap selalu perhatikan detail Tiket. Terimakasih
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
