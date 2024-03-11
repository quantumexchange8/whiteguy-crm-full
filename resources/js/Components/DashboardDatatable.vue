<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { ref, onMounted, reactive, watch } from "vue";
import { convertToHumanReadable, cl } from '@/Composables';
import { ThreeDotsVertical, CheckCircleFillIcon, TimesCircleIcon } from '@/Components/Icons/solid';
import { EyeIcon } from '@heroicons/vue/outline'
import { TrashIcon, PageEditIcon } from '@/Components/Icons/outline';
import CustomFileInputField from '@/Components/CustomFileInputField.vue';
import Vue3Datatable from "@bhplugin/vue3-datatable";
import "@bhplugin/vue3-datatable/dist/style.css";
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import Modal from '@/Components/Modal.vue';
import Label from '@/Components/Label.vue'
import Dropdown from './Dropdown.vue';
import axios from "axios";

const booleanColumns = reactive([]);
const filterIsOpen = ref(false);
const datatable = ref(null);
const total_rows = ref(0);
const searchInput = ref(null);
const loading = ref(true);
const rows = ref(null);
const unprocessedData = ref();
const selectedRowData = ref(null);
const isRowModalOpen = ref(false);
const selectedRows = ref([]);
const showDeleteModal = ref(false);
const rowDataId = ref(null);
const rowsLength = ref();

const props = defineProps({
    cols: {
      type: Array,
      default: () => [],
    },
	targetApi: {
		type: String,
	},
	createLink: {
		type: String,
	},
	detailsLink: {
		type: String,
	},
	tableTitle: {
		type: String,
        default: '',
	},
    modalComponent: {
        type: Object,
        default: null,
    },
    errors:Object,
})

onMounted(() => {
    loading.value = true;
    
    setTimeout(() => {
        getData();
        loading.value = false;
    }, 500);
});

const params = reactive({
    // page: 1,
    pagesize: 5,
    search: '',
    sort_column: 'id',
    sort_direction: 'desc',
});

const getData = async () => {
    try {
        loading.value = true;
        
        const data = await axios.get(props.targetApi, {
            method: 'GET',
            body: JSON.stringify(params),
            params: {
                page: 1,
                limit: 10,
            }
        });

        unprocessedData.value = await data.data;
        rows.value = processRows(unprocessedData.value);
        total_rows.value = rows.value.length;
        console.log(total_rows.value)

    } catch (error) {
        console.error("Error fetching data:", error);
    } finally {
        loading.value = false;
    }
};

const processRows = (rows) => {
  return rows.map(row => {
    for (let key in row) {
      if (row[key] === null || row[key] === '' || row[key] === '-') {
        row[key] = '-';
      }
    }
    return row;
  });
};

const changePage = (data) => {
    loading.value = true;
    
    setTimeout(() => {
        loading.value = false;
    }, 200);
};

const closeRowModal = () => {
    isRowModalOpen.value = false;
    selectedRowData.value = null;
};

// Shows popup modal of the individual lead's details
const rowShowDetails = (data) => {
    selectedRowData.value = data;
    isRowModalOpen.value = true;
};

// Redirects user to the selected row's edit page when user click on the table row
const rowShowEdit = (data) => {
    // window.location.href = props.detailsLink + data.id + '/edit';
    window.location.href = route(props.detailsLink + '.edit', data.id);
};

const openDeleteModal = (id) => {
    rowDataId.value = id;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    rowDataId.value = null;
    showDeleteModal.value = false;
};

const deleteLead = () => {
    router.delete(route(props.detailsLink + '.destroy', rowDataId.value), {
            preserveState : false,
            onSuccess: () => closeDeleteModal(),
        });
};
</script>

<template>
    <div class="p-6 bg-white rounded-xl shadow-md dark:bg-dark-eval-1 max-h-[340px] overflow-hidden">
        <Label
            :value="tableTitle"
            :for="tableTitle"
            class="mb-3 font-semibold !text-base"
        >
        </Label>
        <div class="grid grid-cols-1 lg:grid-cols-12 mb-1">
            <Input 
                v-model="params.search"
                ref="searchInput"
                type="text"
                placeholder="Search..."
                class="w-full col-span-full lg:col-span-3 h-4/5 text-sm"
            />
        </div>

        <vue3-datatable 
            ref="datatable"
            class="bh-datatable bh-table-responsive"
            skin="bh-table-compact"
            :rows="rows" 
            :columns="props.cols" 
            :sortable="true" 
            :loading="loading"
            :rowClass="'odd:bg-gray-200 even:bg-gray-300 dark:odd:bg-gray-600 dark:even:bg-gray-700 dark:text-gray-200'" 
            :search="params.search" 
            :sortColumn="params.sort_column"
            :sortDirection="params.sort_direction"
            :pageSize="params.pagesize"
            :totalRows="total_rows"
            @pageChange="changePage"
        >
            <template #id="rows">
                <strong><span class="text-purple-300">#{{ rows.value.id }}</span></strong>
            </template>
            <template #site="rows">
                <strong><span class="text-purple-300">{{ rows.value.username }} ({{ rows.value.site }})</span></strong>
            </template>
            <template #sdm="rows">
                <CheckCircleFillIcon 
                    class="flex-shrink-0 w-5 h-5"
                    v-if="Boolean(rows.value.sdm)"
                />
                <TimesCircleIcon 
                    class="flex-shrink-0 w-5 h-5"
                    v-else-if="!Boolean(rows.value.sdm)"
                />
            </template>
            <template #liquid="rows">
                <CheckCircleFillIcon 
                    class="flex-shrink-0 w-5 h-5"
                    v-if="Boolean(rows.value.liquid)"
                />
                <TimesCircleIcon 
                    class="flex-shrink-0 w-5 h-5"
                    v-else-if="!Boolean(rows.value.liquid)"
                />
            </template>
            <template #is_active="rows">
                <CheckCircleFillIcon 
                    class="flex-shrink-0 w-5 h-5"
                    v-if="Boolean(rows.value.is_active)"
                />
                <TimesCircleIcon 
                    class="flex-shrink-0 w-5 h-5"
                    v-else-if="!Boolean(rows.value.is_active)"
                />
            </template>
            <template #is_staff="rows">
                <CheckCircleFillIcon 
                    class="flex-shrink-0 w-5 h-5"
                    v-if="Boolean(rows.value.is_staff)"
                />
                <TimesCircleIcon 
                    class="flex-shrink-0 w-5 h-5"
                    v-else-if="!Boolean(rows.value.is_staff)"
                />
            </template>
            <template #has_crm_access="rows">
                <CheckCircleFillIcon 
                    class="flex-shrink-0 w-5 h-5"
                    v-if="Boolean(rows.value.has_crm_access)"
                />
                <TimesCircleIcon 
                    class="flex-shrink-0 w-5 h-5"
                    v-else-if="!Boolean(rows.value.has_crm_access)"
                />
            </template>
            <template #has_leads_access="rows">
                <CheckCircleFillIcon 
                    class="flex-shrink-0 w-5 h-5"
                    v-if="Boolean(rows.value.has_leads_access)" 
                />
                <TimesCircleIcon 
                    class="flex-shrink-0 w-5 h-5"
                    v-else-if="!Boolean(rows.value.has_leads_access)"
                />
            </template>
        </vue3-datatable>
    </div>
</template>
