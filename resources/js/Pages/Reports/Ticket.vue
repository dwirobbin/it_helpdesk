<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import DatePickerInput from "@/Components/DatePickerInput.vue";
import VueMultiselect from 'vue-multiselect';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm } from '@inertiajs/vue3';
import html2pdf from 'html2pdf.js';
import { usePermission } from "@/Composables/permissions";
import * as XLSXStyle from 'xlsx-js-style';
import { saveAs } from 'file-saver';

const { hasAnyRole } = usePermission();

const props = defineProps({
    types: {
        type: Array,
        default: () => [],
    },
    tickets: {
        type: Object,
        default: () => { },
    },
});

const statusColor = (status) => {
    return {
        'Dalam Antrian': 'text-yellow-800',
        'Menunggu Diproses': 'text-blue-800',
        'Sedang Diproses': 'text-purple-800',
        'Terselesaikan': 'text-green-800',
    }[status];
};

const form = useForm({
    start_date: null,
    end_date: null,
    type: null,
});

const resetData = () => {
    form.start_date = null;
    form.end_date = null;
    form.type = null;
};

const onGenerate = () => {
    form.post(route('reports.tickets.create'));
};

const onExportToPdf = () => {
    const element = document.getElementById('element-to-convert');
    const opt = {
        margin: 2,
        filename: 'ticket.pdf',
        html2canvas: { scale: 1 },
        pagebreak: { mode: ['avoid-all'] },
        jsPDF: { format: 'a4', orientation: 'landscape' }
    };

    html2pdf().from(element).set(opt).save();
};

// Function to get table data as Array of Arrays (AoA)
const getTableData = (tableId) => {
    const tableElement = document.getElementById(tableId);
    const headerCells = tableElement.querySelectorAll('thead th');
    const header = Array.from(headerCells).map(cell => cell.textContent.trim());

    const rows = tableElement.querySelectorAll('tbody tr');
    const aoa = [];
    rows.forEach(row => {
        const rowData = [];
        row.querySelectorAll('td').forEach(cell => {
            rowData.push(cell.textContent.trim());
        });
        aoa.push(rowData);
    });

    return { header, aoa };
};

// Function to create worksheet with header and data, applying styles
const createWorksheet = (header, aoa) => {
    const ws = XLSXStyle.utils.aoa_to_sheet([header, ...aoa]);

    // Set header row to bold
    const headerRange = XLSXStyle.utils.decode_range(ws['!ref']);
    for (let i = headerRange.s.c; i <= headerRange.e.c; i++) {
        const cellAddress = XLSXStyle.utils.encode_cell({ r: headerRange.s.r, c: i });
        const cell = ws[cellAddress];
        if (cell && cell.t === 's') { // if the cell is a text cell
            if (!cell.s) cell.s = {}; // ensure cell has a style object
            cell.s.font = { bold: true }; // set font bold
        }
    }

    return ws;
};

// Function to adjust column widths based on content length
const adjustColumnWidths = (ws, aoa) => {
    const colWidths = [];
    const extraWidth = 2; // Additional width for each column

    aoa.forEach(row => {
        row.forEach((cell, colIndex) => {
            const cellLength = cell ? String(cell).length : 0;
            const adjustedWidth = cellLength + extraWidth;
            if (!colWidths[colIndex] || adjustedWidth > colWidths[colIndex]) {
                colWidths[colIndex] = adjustedWidth;
            }
        });
    });

    ws['!cols'] = colWidths.map(width => ({ wch: width }));
};

const onExportToExcel = () => {
    const { header, aoa } = getTableData('table');

    const wb = XLSXStyle.utils.book_new();
    const ws = createWorksheet(header, aoa);
    adjustColumnWidths(ws, aoa);

    XLSXStyle.utils.book_append_sheet(wb, ws, 'Sheet1');

    const wbout = XLSXStyle.write(wb, { bookType: 'xlsx', type: 'array' });
    const blob = new Blob([wbout], { type: 'application/octet-stream' });
    saveAs(blob, 'data.xlsx');
};
</script>

<template>

    <Head title="Laporan Tiket" />

    <AuthenticatedLayout>
        <div class="p-4 bg-white block sm:flex items-center justify-between shadow-sm border-b border-gray-200 lg:mt-1">
            <div class="w-full flex flex-col justify-left sm:flex-row sm:justify-between gap-y-3">
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 order-2 sm:order-1">Laporan Tiket</h1>
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
                                <span class="text-gray-400 ml-1 md:ml-2 text-sm font-medium" aria-current="page">Laporan Tiket</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="p-4">
            <div class="p-6 bg-white block border border-gray-200 rounded-lg shadow-sm">
                <form v-if="hasAnyRole(['Super Admin', 'It Support'])" @submit.prevent="onGenerate"
                    class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="">
                        <InputLabel for="date-of-birth" value="Tanggal Mulai" required />
                        <DatePickerInput id="date-of-birth" v-model="form.start_date" placeholder="e.g. 01/05/2024" />
                        <InputError class="mt-1.5" :message="form.errors.start_date" />
                    </div>
                    <div class="">
                        <InputLabel for="date-of-birth" value="Tanggal Akhir" required />
                        <DatePickerInput id="date-of-birth" v-model="form.end_date" placeholder="e.g. 06/05/2024" />
                        <InputError class="mt-1.5" :message="form.errors.end_date" />
                    </div>
                    <div class="">
                        <InputLabel for="type" value="Tipe" />
                        <VueMultiselect id="type" v-model="form.type" :options="types" placeholder="Pilih" />
                        <InputError class="mt-1.5" :message="form.errors.type" />
                    </div>
                    <div class="col-span-1 lg:col-span-3 flex space-x-2">
                        <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Generate
                        </PrimaryButton>
                        <button type="button" @click="resetData"
                            class="text-gray-900 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-primary-300 border border-gray-200 font-medium inline-flex items-center rounded-lg text-base px-3 py-2.5 text-center dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                            Reset
                        </button>
                    </div>
                </form>
            </div>

            <div v-if="hasAnyRole(['Super Admin', 'It Support']) && tickets !== undefined"
                class="mt-4 p-4 bg-white block border border-gray-200 rounded-lg shadow-sm">
                <!-- Card Header -->
                <div class="mb-1 w-full text-center space-y-2 sm:space-x-4 sm:space-y-0">
                    <PrimaryButton type="button" @click="onExportToPdf" class="inline-flex items-center justify-center">
                        <svg class="-ml-1 mr-1 h-6 w-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 13V4M7 14H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2m-1-5-4 5-4-5m9 8h.01" />
                        </svg>
                        Download As PDF
                    </PrimaryButton>
                    <PrimaryButton type="button" @click="onExportToExcel" class="inline-flex items-center justify-center">
                        <svg class="-ml-1 mr-1 h-6 w-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 13V4M7 14H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2m-1-5-4 5-4-5m9 8h.01" />
                        </svg>
                        Download As Excel
                    </PrimaryButton>
                </div>

                <!-- Card Body -->
                <div class="flex flex-col mt-5">
                    <div id="element-to-convert" class="overflow-x-auto rounded-lg">
                        <div class="align-middle inline-block">
                            <div class="shadow overflow-hidden">
                                <table id="table" class="table-fixed divide-y xl:w-full divide-gray-200 mt-0 pt-0">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th scope="col"
                                                class="p-4 w-4 text-left whitespace-nowrap text-xs font-medium text-gray-500 uppercase">
                                                #
                                            </th>
                                            <th scope="col"
                                                class="p-4 text-left whitespace-nowrap text-xs font-medium text-gray-500 uppercase cursor-pointer">
                                                No. Tiket
                                            </th>
                                            <th scope="col"
                                                class="p-4 text-left whitespace-nowrap text-xs font-medium text-gray-500 uppercase cursor-pointer">
                                                Nama
                                            </th>
                                            <th scope="col"
                                                class="p-4 w-1/6 text-left whitespace-nowrap text-xs font-medium text-gray-500 uppercase cursor-pointer">
                                                Keluhan
                                            </th>
                                            <th scope="col"
                                                class="p-4 w-1/5 text-left whitespace-nowrap text-xs font-medium text-gray-500 uppercase">
                                                Tgl Pengaduan
                                            </th>
                                            <th scope="col"
                                                class="p-4 w-1/6 text-left whitespace-nowrap text-xs font-medium text-gray-500 uppercase">
                                                Tgl Selesai
                                            </th>
                                            <th scope="col"
                                                class="p-4 w-1/6 text-left whitespace-nowrap text-xs font-medium text-gray-500 uppercase cursor-pointer">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template v-if="tickets.data.length">
                                            <tr v-for="(ticket, index) in tickets?.data" :key="index" class="hover:bg-gray-100">
                                                <td class="p-4 w-4 text-base font-medium text-gray-900">
                                                    {{ index + 1 }}
                                                </td>
                                                <td class="p-4 text-base font-medium text-gray-900">
                                                    {{ ticket.ticket_number }}
                                                </td>
                                                <td class="p-4 text-base text-wrap font-medium text-gray-900">
                                                    {{ ticket.user.name }}
                                                </td>
                                                <td class="p-4 w-1/6 text-base font-medium text-wrap text-gray-900">
                                                    {{ ticket.title }}
                                                </td>
                                                <td class="p-4 w-1/5 text-base font-medium text-wrap text-gray-900">
                                                    {{ ticket.created_at.locale_day_date_time }}
                                                </td>
                                                <td class="p-4 w-1/6 text-base font-medium text-wrap text-gray-900">
                                                    {{ ticket.status === 'Terselesaikan' ? ticket.updated_at.locale_day_date_time : 'Belum Terselesaikan' }}
                                                </td>
                                                <td class="p-4 w-1/6 whitespace-nowrap">
                                                    <span :class="statusColor(ticket.status)" class="rounded text-base font-medium">
                                                        {{ ticket.status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </template>

                                        <template v-else>
                                            <tr>
                                                <td colspan="7" class="p-4 text-center text-gray-900">
                                                    Belum tersedia data sesuai dengan input yang anda berikan.
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
