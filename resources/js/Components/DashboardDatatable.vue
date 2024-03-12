<script setup>
import { ThreeDotsVertical, CheckCircleFillIcon, TimesCircleIcon } from '@/Components/Icons/solid';
import CustomFileInputField from '@/Components/CustomFileInputField.vue';
import { TrashIcon, PageEditIcon } from '@/Components/Icons/outline';
import { convertToHumanReadable, cl } from '@/Composables';
import { ref, onMounted, reactive, watch } from "vue";
import Vue3Datatable from "@bhplugin/vue3-datatable";
import { useForm, router } from '@inertiajs/vue3';
import "@bhplugin/vue3-datatable/dist/style.css";
import Dropdown from '@/Components/Dropdown.vue';
import { EyeIcon } from '@heroicons/vue/outline';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import Modal from '@/Components/Modal.vue';
import Label from '@/Components/Label.vue'
import { Link } from '@inertiajs/vue3';
import axios from "axios";
import dayjs from 'dayjs';

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
let originalData = null;

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
        originalData = rows.value;
        total_rows.value = rows.value.length;

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

// Dropdown Filter by Date
const filterByToday = () => {
    const today = dayjs();
    rows.value = originalData.filter((log) =>
      dayjs(log.created_at).isSame(today, 'day')
    );
  };
  
const filterByPast7Days = () => {
    const sevenDaysAgo = dayjs().subtract(7, 'day');
    rows.value = originalData.filter((log) =>
        dayjs(log.created_at).isAfter(sevenDaysAgo)
    );
};

const filterByThisMonth = () => {
    const startOfMonth = dayjs().startOf('month');
    rows.value = originalData.filter((log) =>
        dayjs(log.created_at).isAfter(startOfMonth)
    );
};

const filterByThisYear = () => {
    const startOfYear = dayjs().startOf('year');
    rows.value = originalData.filter((log) =>
        dayjs(log.created_at).isAfter(startOfYear)
    );
};

const showAll = () => {
    rows.value = originalData;
};
</script>

<template>
    <div class="px-5 py-6 bg-white rounded-xl shadow-md dark:bg-dark-eval-1 max-h-[340px] overflow-hidden">
        <div class="flex flex-row justify-between">
            <Link 
                :href="route(detailsLink + '.index')"
                class="!text-base font-medium text-gray-700 dark:text-gray-300 underline"
            >
                {{ tableTitle }}
            </Link>
            <Dropdown 
                    :align="'right'" 
                    :width="50" 
                    :contentClasses="'dark:bg-dark-eval-3'"
                >
                <template #trigger>
                    <ThreeDotsVertical class="flex-shrink-0 w-6 h-6 cursor-pointer mx-2" aria-hidden="true" />
                </template>

                <template #content>
                    <div class="p- w-full">
                        <p class="text-md p-2 text-gray-300 text-center">Action</p>
                        <hr class="border-b rounded-md border-gray-600 mb-2 w-10/12 mx-auto">
                        <div class="px-6 pb-6 pt-2 flex flex-col gap-2">
                            <Button 
                                :type="'button'"
                                :variant="'info'" 
                                :size="'sm'" 
                                class="justify-center px-2 w-full h-full whitespace-nowrap"
                                @click="showAll()"
                            >
                                {{ 'All' }}
                            </Button>
                            <Button 
                                :type="'button'"
                                :variant="'info'" 
                                :size="'sm'" 
                                class="justify-center px-2 w-full h-full whitespace-nowrap"
                                @click="filterByToday()"
                            >
                                {{ 'Today' }}
                            </Button>
                            <Button 
                                :type="'button'"
                                :variant="'info'" 
                                :size="'sm'" 
                                class="justify-center px-2 w-full h-full whitespace-nowrap"
                                @click="filterByPast7Days()"
                            >
                                {{ 'Past 7 days' }}
                            </Button>
                            <Button 
                                :type="'button'"
                                :variant="'info'" 
                                :size="'sm'" 
                                class="justify-center px-2 w-full h-full whitespace-nowrap"
                                @click="filterByThisMonth()"
                            >
                                {{ 'This month' }}
                            </Button>
                            <Button 
                                :type="'button'"
                                :variant="'info'" 
                                :size="'sm'" 
                                class="justify-center px-2 w-full h-full whitespace-nowrap"
                                @click="filterByThisYear()"
                            >
                                {{ 'This year' }}
                            </Button>
                        </div>
                    </div>
                </template>
            </Dropdown>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-12 mt-4">
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
                <Link 
                    :href="route(detailsLink + '.edit', rows.value.id)"
                    class="font-medium"
                >
                    <strong><span class="text-purple-300">#{{ rows.value.id }}</span></strong>
                </Link>
            </template>
            <template #site="rows">
                <Link 
                    :href="route(detailsLink + '.edit', rows.value.id)"
                    class="font-medium"
                >
                    <strong><span class="text-purple-300">{{ rows.value.username }} ({{ rows.value.site }})</span></strong>
                </Link>
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
            <template #last_called="rows">
                <div class="min-w-max">{{ rows.value.last_called }}</div>
            </template>
            <template #give_up_at="rows">
                <div class="min-w-max">{{ rows.value.give_up_at }}</div>
            </template>
            <template #date_oppd_in="rows">
                <div class="min-w-max">{{ rows.value.date_oppd_in }}</div>
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
