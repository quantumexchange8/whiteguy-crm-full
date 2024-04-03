<script setup>
import axios from "axios";
import { useForm, router, usePage, Link } from '@inertiajs/vue3';
import { ref, onMounted, reactive, watch, computed } from "vue";
import { EyeIcon } from '@heroicons/vue/outline'
import "@bhplugin/vue3-datatable/dist/style.css";
import Vue3Datatable from "@bhplugin/vue3-datatable";
import { convertToHumanReadable, cl, formatToUserTimezone } from '@/Composables';
import Label from '@/Components/Label.vue'
import Input from '@/Components/Input.vue';
import Modal from '@/Components/Modal.vue';
import Button from '@/Components/Button.vue';
import Dropdown from '@/Components/Dropdown.vue';
import { TrashIcon, PageEditIcon } from '@/Components/Icons/outline';
import CustomFileInputField from '@/Components/CustomFileInputField.vue';
import { ThreeDotsVertical, CheckCircleFillIcon, TimesCircleIcon } from '@/Components/Icons/solid';

const page = usePage();
const categories = ref([]);
const filterDateColumns = reactive([]);
const booleanColumns = reactive([]);
const filterIsOpen = ref(false);
const importModalIsOpen = ref(false);
const datatable = ref(null);
const total_rows = ref(0);
const searchInput = ref(null);
const loading = ref(true);
const rows = ref(null);
const checkedFilters = reactive({});
const unprocessedData = ref();
const selectedRowData = ref(null);
const isRowModalOpen = ref(false);
const selectedRows = ref([]);
const showDeleteModal = ref(false);
const rowDataId = ref(null);
const isExportable = ref(true);
const selectedRowsLength = ref(0);
const filteredRowsLength = ref(0);
const isImportable = ref(true);
const isDuplicate = ref(false);
const clientArray  = ref(
	[ "ALLO", "NO ALLO", "REMM", "TT", "CLEARED", "PENDING", "KICKED", "CARRIED OVER", "FREE SWITCH", "CXL", "CXL-CLIENT DROPPED" ]
);

const user = computed(() => page.props.auth.user)

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
	categoryFilters: {
		type: String,
	},
    modalComponent: {
        type: Object,
        default: null,
    },
    getDuplicatesURL: {
        type: String,
        default: '',
    },
    hasImport: {
        type: Boolean,
        default: false,
    },
    exportRoute: {
        type: String,
    },
    errors:Object,
})

const params = reactive({
    page: 1,
    pagesize: 10,
    search: '',
    column_filters: [],
    sort_column: 'id',
    sort_direction: 'desc',
});

onMounted(() => {
    // loading.value = true;
    
    // setTimeout(() => {
        // loading.value = false;
    // }, 500);
        params.search = '';
        getData();
        getCategoryFilters();
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
        total_rows.value = data.data.length;
        isDuplicate.value = false;

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

const getDuplicatedData = async () => {
    try {
        loading.value = true;
        
        const data = await axios.get(props.getDuplicatesURL);
        rows.value = await data.data;
        total_rows.value = data.data.length;
        isDuplicate.value = true;

    } catch (error) {
        console.error("Error fetching data:", error);
    } finally {
        loading.value = false;
    }
};

const toggleLeadsData = () => {
      isDuplicate.value = !isDuplicate.value;
      
      if (isDuplicate.value) {
        getDuplicatedData();
      } else {
        getData();
      }
};

const changePage = (data) => {
    loading.value = true;
    
    setTimeout(() => {
        loading.value = false;
    }, 200);
};

// Get list of sub-categories for each category
const getCategoryFilters = async () => {
    try {
        loading.value = true;
        
        const data = await axios.get(props.categoryFilters);
        categories.value = data.data;
    } catch (error) {
        console.error("Error fetching data:", error);
    } finally {
        loading.value = false;
    }
    closeFilterModal();
};

// Get filtered data based on user selected filter
const getFilteredData = async (checkedFilters) => {
    try {
        loading.value = true;
        
        const data = await axios.get(props.targetApi, {
            method: 'GET',
            params: {
                checkedFilters: checkedFilters,
            }
        });
        rows.value = await data.data;
        total_rows.value = data.data.length;

    } catch (error) {
        console.error("Error fetching data:", error);
    } finally {
        loading.value = false;
    }
    closeFilterModal();
};

const openFilterModal = () => {
    filterIsOpen.value = true;
}

const closeFilterModal = () => {
    filterIsOpen.value = false;
}

const openImportModal = () => {
    importModalIsOpen.value = true;
}

const closeImportModal = () => {
    importModalIsOpen.value = false;
}

const checkIfHasFile = () => {
    if (form.leadExcelFile !== undefined || form.leadExcelFile !== '') {
        isImportable.value = false;
    } else {
        isImportable.value = true;
    }
}

const clearColumnFiltersInts = () => {
    props.cols.forEach((col, index) => {
        // Reset the search value for each column
        col.value = '';
        // Clear the filter for the corresponding column in params.column_filters
        params.column_filters[index] = null;
    });
    
    // Trigger the datatable to reset filters
    datatable.value.reset();
};

// Resets and clears the search inputs and filters
const reset = () => {
    clearColumnFiltersInts();
    params.search = '';
    getData();
    Object.keys(checkedFilters).forEach(key => {
        checkedFilters[key] = [];
    });
    
    closeFilterModal();
};

const closeRowModal = () => {
    isRowModalOpen.value = false;
    selectedRowData.value = null;
    processRows(rows.value);
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

const form = useForm({
	leadExcelFile: '',
});

// Get input file and send to controller for importing to db
const importExcel = (e) => {
    e.preventDefault();
    // cl(form.leadExcelFile);
    if (form.leadExcelFile !== undefined || form.leadExcelFile !== '') {
        form.post(route('leads.import'), {
            preserveState : false,
        })
    }
};

// Gets selected rows or all rows based on user selection and sends to controller for exporting to excel file
const exportToExcel = (type) => {
    const selected = datatable.value.getSelectedRows();
    const filteredRows = datatable.value.getFilteredRows();
    selectedRowsLength.value = selected.length;
    filteredRowsLength.value = filteredRows.length;

    switch (type) {
        case 'All':
            selectedRows.value = rows.value.map(col => col.id);
            break;
        case 'Filtered':
            selectedRows.value = filteredRows.map(col => col.id);
            break;
        case 'Selected':
            selectedRows.value = selected.map(col => col.id);
            break;
        default:
            selectedRows.value = [];
            console.log(`Error: Invalid export type.`);
    }

    if (selectedRows.value.length > 0) {
        window.location.href = route(props.exportRoute, { selectedRowsData: selectedRows.value });
    }

    clearSelectedRows()

    selected.forEach((col) => {
        selectedRows.value.push(col.id);
    });
    router.get(route(props.exportRoute, { selectedRowsData: selectedRows.value }), {
        onSuccess: () => clearSelectedRows(),
    })
};

const clearSelectedRows = () => {
    datatable.value.clearSelectedRows();
    selectedRows.value = [];
};

// Check if user selected any rows and only enable if more than 0
const checkForBulkActions = () => {
    // Check if there is any file input - disable import button if no file
    if (form.leadExcelFile !== undefined && form.leadExcelFile !== '') {
        isImportable.value = false;
    } else {
        isImportable.value = true;
    }

    // Check for selected rows
    const selected = datatable.value.getSelectedRows();
    const filteredRows = datatable.value.getFilteredRows();

    selectedRowsLength.value = selected.length;
    filteredRowsLength.value = filteredRows.length;

    if (selected.length > 0) {
        isExportable.value = false;
    } else {
        isExportable.value = true;
    }
};

const cancelImportFile = () => {
    form.leadExcelFile = '';
    closeImportModal();

    cl(form.leadExcelFile);
}

// Watch categories for changes
watch(() => categories.value, () => {
    Object.keys(categories.value).forEach(key => {
        if (categories.value[key].includes("Today")) {
            filterDateColumns.push(key);
        }

        if (categories.value[key].every(val => val === 0 || val === 1)) {
            booleanColumns.push(key);
        }
    });
}, { immediate: true });

// Watch for changes in filter categories and initialize checkedFilters object
watch(() => categories.value, (newVal) => {
    Object.keys(newVal).forEach(key => {
        checkedFilters[key] = [];
    });
    
}, { immediate: true });

</script>

<template>
    <div class="p-6 bg-white rounded-xl shadow-md dark:bg-dark-eval-1">
        <div class="flex flex-row justify-between pb-6">
            <p class="text-xl pl-8 dark:text-gray-300 font-semibold text-center">{{ isDuplicate ? 'Duplicates' : '' }}</p>
            <div class="flex flex-row">
                <div class="rounded-md shadow-lg border border-gray-500 flex justify-end">
                    <Button 
                        :type="'button'"
                        :variant="'success'" 
                        :size="'sm'" 
                        class="justify-center px-6 py-2 gap-2 w-full h-full"
                        :href="props.createLink"
                    >
                        Create {{ convertToHumanReadable(props.detailsLink).slice(0, props.detailsLink.length - 1) }}
                    </Button>
                </div>
                <Dropdown 
                    :align="'right'" 
                    :width="50" 
                    :contentClasses="'dark:bg-dark-eval-3'"
                    v-if="hasImport"
                >
                    <template #trigger>
                        <ThreeDotsVertical class="flex-shrink-0 w-6 h-6 cursor-pointer mx-2" aria-hidden="true" />
                    </template>
    
                    <template #content>
                        <div class="p- w-full">
                            <p class="text-md p-2 text-gray-300 text-center">Action</p>
                            <hr class="border-b rounded-md border-gray-600 mb-2 w-10/12 mx-auto">
                            <div class="px-6 pb-6 pt-2">
                                <Button 
                                    :type="'button'"
                                    :variant="'info'" 
                                    :size="'sm'" 
                                    class="justify-center px-6 py-2 w-full h-full whitespace-nowrap"
                                    @click="toggleLeadsData()"
                                >
                                    {{ isDuplicate ? 'Leads' : 'Leads Duplicates' }}
                                </Button>
                            </div>
                        </div>
                    </template>
                </Dropdown>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-5">
            <div class="col-span-2 md:w-full">
                <div class="lg:col-start-1 grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <Input 
                        v-model="params.search"
                        ref="searchInput"
                        type="text"
                        placeholder="Search..."
                        class="w-full"
                    />
                </div>
            </div>

            <div class="gap-4 col-span-2">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
                    <div class="relative col-span-1 rounded-md shadow-lg border border-gray-500 hover:bg-dark-eval-2">
                        <Dropdown 
                            :contentClasses="'dark:bg-dark-eval-3 bg-gray-200'"
                            class="cursor-pointer"
                            @click="checkForBulkActions"
                        >
                            <template #trigger>
                                <div class="text-sm text-gray-300 hover:text-gray-200 p-2 flex justify-center">
                                    Bulk Actions 
                                    <span class="pl-2">
                                        <svg
                                            viewBox="0 0 24 24"
                                            width="20"
                                            height="20"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            fill="none"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="transition inline-block"
                                        >
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </div>
                            </template>

                            <template #content>
                                <div class="p-2 w-full">
                                    <p class="text-base p-2 dark:text-gray-300 text-center">Actions</p>
                                    <hr class="border-b rounded-md border-gray-600 mb-2 w-11/12 mx-auto">
                                    <div v-if="hasImport">
                                        <p class="text-sm p-2 dark:text-gray-300 text-center font-bold pb-4">Import Excel</p>
                                        <div class="px-6 pb-6">
                                            <Button 
                                                :type="'button'"
                                                :variant="'success'"
                                                :size="'sm'"
                                                class="justify-center gap-2 w-full h-full"
                                                @click="openImportModal"
                                            >
                                                Import File
                                            </Button>
                                        </div>
                                        <hr class="border-b rounded-md border-gray-600 mb-2 w-9/12 mx-auto">
                                    </div>
                                    <p class="text-sm p-2 dark:text-gray-300 text-center font-bold pb-4">Export to Excel</p>
                                    <div class="px-6 pb-6 flex flex-col gap-2">
                                        <Button 
                                            :type="'button'"
                                            :variant="'success'"
                                            :size="'sm'"
                                            class="justify-center gap-2 w-full h-full" 
                                            @click="exportToExcel('All')"
                                        >
                                            Export All
                                        </Button>
                                        <Button 
                                            :type="'button'"
                                            :variant="'success'"
                                            :size="'sm'"
                                            class="justify-center gap-2 w-full h-full" 
                                            @click="exportToExcel('All')"
                                        >
                                            Export Filtered  ({{ filteredRowsLength }})
                                        </Button>
                                        <Button 
                                            :type="'button'"
                                            :variant="'success'"
                                            :size="'sm'"
                                            :disabled="isExportable"
                                            class="justify-center gap-2 w-full h-full"
                                            @click="exportToExcel('Selected')"
                                        >
                                            Export Selected ({{ selectedRowsLength }})
                                        </Button>
                                    </div>
                                </div>
                            </template>
                        </Dropdown>
                        <Modal 
                            :show="importModalIsOpen" 
                            maxWidth="2xl" 
                            :closeable="true" 
                            @close="closeImportModal"
                            v-if="hasImport"
                        >
                            <div class="p-9">
                                <p class="text-gray-200 text-2xl bg-blue-500 p-4 rounded-md font-bold mb-6">Import Excel File</p>
                                <div class="flex flex-col justify-center divide-y divide-gray-500">
                                    <div class="p-2">
                                        <form class="flex flex-col gap-4" @submit="importExcel">
                                            <CustomFileInputField
                                                :inputId="'leadExcelFile'"
                                                v-model="form.leadExcelFile"
                                                @change="checkIfHasFile"
                                            />
                                            <div class="flex justify-end px-9">
                                                <div class="rounded-md shadow-lg flex flex-row gap-2">
                                                    <Button 
                                                        :type="'button'"
                                                        :variant="'secondary'"
                                                        :size="'sm'"
                                                        class="justify-center px-6 py-2 border border-purple-500" 
                                                        @click="cancelImportFile"
                                                    >
                                                        Cancel
                                                    </Button>
                                                    <Button 
                                                        :size="'sm'"
                                                        :disabled="isImportable"
                                                        class="justify-center px-6 py-2"
                                                    >
                                                        Import File
                                                    </Button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </Modal>
                    </div>
                    <div class="relative col-span-1 rounded-md shadow-lg border border-gray-500">
                        <Button 
                            :variant="'secondary'"
							:size="'sm'"
                            class="justify-center gap-2 w-full h-full" 
                            @click="reset()"
                        >
                            Reset
                        </Button>
                    </div>
                    <div class="relative col-span-1 rounded-md shadow-lg border border-gray-500">
                        <Button 
                            :type="'button'"
                            :variant="'secondary'"
							:size="'sm'"
                            class="justify-center gap-2 w-full h-full" 
                            @click="openFilterModal"
                        >
                            Filters
                        </Button>
                        <Modal 
                            :show="filterIsOpen" 
                            maxWidth="2xl" 
                            :closeable="true" 
                            @close="closeFilterModal">

                            <div class="p-9">
                                <p class="text-gray-200 text-2xl bg-blue-500 p-4 rounded-md font-bold mb-6">Filters</p>
                                <div class="flex flex-col justify-center divide-y divide-gray-500">
                                    <div class="p-2" v-for="(value, key) in categories">
                                        <p class="dark:text-gray-300">By {{ convertToHumanReadable(key) }}</p>
                                        <div class="flex flex-row flex-wrap gap-2 p-2">
                                            <div v-for="(item, index) in value" :key="index">
                                                <!-- {{ cl(value + ":-" + index + ":" + item) }} -->
                                                <input 
                                                    :id="key + index" 
                                                    type="radio" 
                                                    class="hidden peer" 
                                                    :name="key" 
                                                    :value="item" 
                                                    v-model="checkedFilters[key]"
                                                    v-if="filterDateColumns.includes(key)"
                                                >
                                                <input 
                                                    :id="key + index" 
                                                    type="checkbox" 
                                                    class="hidden peer" 
                                                    :name="key" 
                                                    :value="item.id" 
                                                    v-model="checkedFilters[key]"
                                                    v-else
                                                >
                                                <label 
                                                    :for="key + index" 
                                                    class="inline-flex items-center justify-between w-auto py-2 px-4 font-medium tracking-tight 
                                                        border rounded-lg cursor-pointer bg-brand-light text-brand-black border-gray-500
                                                        peer-checked:border-violet-400 peer-checked:bg-violet-700 peer-checked:text-white">
                                                    <div class="flex items-center justify-center w-full">
                                                        <div class="text-sm dark:text-gray-300">{{ booleanColumns.includes(key) ? (item ? 'Yes' : 'No') : (item?.title ?? item) }}</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end px-9">
                                    <div class="rounded-md shadow-lg flex flex-row gap-2">
                                        <Button 
                                            :type="'button'"
                                            :variant="'secondary'"
                                            :size="'sm'"
                                            class="justify-center px-6 py-2 border border-purple-500" 
                                            @click="reset"
                                        >
                                            Reset
                                        </Button>
                                        <Button 
                                            :type="'button'"
                                            :size="'sm'"
                                            class="justify-center px-6 py-2" 
                                            @click="getFilteredData(checkedFilters)"
                                        >
                                            Apply
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </Modal>
                    </div>
                    <div class="relative col-span-1 rounded-md shadow-lg border border-gray-500 hover:bg-dark-eval-2">
                        <Dropdown 
                            :contentClasses="'dark:bg-dark-eval-3'"
                            class="cursor-pointer"
                        >
                            <template #trigger>
                                <div class="text-sm text-gray-300 hover:text-gray-200 p-2 flex justify-center">
                                    Columns 
                                    <span class="pl-2">
                                        <svg
                                            viewBox="0 0 24 24"
                                            width="20"
                                            height="20"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            fill="none"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="transition inline-block"
                                        >
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </div>
                            </template>

                            <template #content>
                                <div class="py-1">
                                    <ul class="p-4">
                                        <li 
                                            v-for="col in props.cols" 
                                            :key="col.field"
                                        >
                                            <label class="flex items-center gap-2 w-full cursor-pointer text-gray-300 hover:text-black">
                                                <input 
                                                    type="checkbox" class="form-checkbox" 
                                                    :checked="!col.hide" 
                                                    @change="col.hide = !$event.target.checked" 
                                                />
                                                <span>{{ col.title }}</span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>
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
            :columnFilter="true" 
            :sortColumn="params.sort_column"
            :sortDirection="params.sort_direction"
            :hasCheckbox="true"
            :pageSize="params.pagesize"
            :totalRows="total_rows"
            :stickyFirstColumn="true"
            :stickyHeader="true"
            @pageChange="changePage"
        >
            <template #id="rows">
                <strong><span class="text-purple-300">#{{ rows.value.id }}</span></strong>
            </template>
            <template #username="rows">
                <strong><span class="text-purple-300">{{ rows.value.username }} ({{ rows.value.site.name }})</span></strong>
            </template>
            <template #lead_assignee="rows">
                <Link
                    :href="route('users-clients.edit', rows.value.assignee.id)"
                    class="font-medium"
                >
                    <strong><span class="text-purple-300 hover:text-purple-400">{{ rows.value.assignee.username }} ({{ rows.value.assignee.site.name }})</span></strong>
                </Link>
            </template>
            <template #lead_front_assignee="rows">
                <Link
                    :href="route('users-clients.edit', rows.value.lead.assignee.id)"
                    class="font-medium"
                >
                    <strong><span class="text-purple-300 hover:text-purple-400">{{ rows.value.lead.assignee.username }} ({{ rows.value.lead.assignee.site.name }})</span></strong>
                </Link>
            </template>
            <template #account_manager_id="rows">
                <Link
                    :href="route('users-clients.edit', rows.value.account_manager.id)"
                    class="font-medium"
                    v-if="rows.value.account_manager !== '-'"
                >
                    <strong><span class="text-purple-300 hover:text-purple-400">{{ rows.value.account_manager_id }}</span></strong>
                </Link>
                <span v-else class="text-gray-300">{{ rows.value.account_manager_id }}</span>
            </template>
            <template #lead_id="rows">
                <Link
                    :href="route('leads.edit', rows.value.lead.id)"
                    class="font-medium"
                >
                    <strong><span class="text-purple-300 hover:text-purple-400">{{ rows.value.lead.first_name + ' ' + rows.value.lead.last_name }}</span></strong>
                </Link>
            </template>
            <template #actions="rows">
                <div class="flex flex-row flex-nowrap gap-1">
                    <Button 
                        :type="'button'"
                        :variant="'transparent'"
                        :size="'xs'"
                        class="justify-center h-full" 
                        @click="rowShowDetails(rows.value)"
                    >
                        <EyeIcon class="flex-shrink-0 w-5 h-5 cursor-pointer text-cyan-500" aria-hidden="true" />
                    </Button>
                    <div class="border border-gray-500/80 h-5 self-center"></div>
                    <Button 
                        :type="'button'"
                        :variant="'transparent'"
                        :size="'xs'"
                        class="justify-center h-full" 
                        @click="rowShowEdit(rows.value)"
                    >
                        <PageEditIcon class="flex-shrink-0 w-5 h-5 cursor-pointer text-blue-500" aria-hidden="true" />
                    </Button>
                    <div class="border border-gray-500/80 h-5 self-center"></div>
                    <Button 
                        :type="'button'"
                        :variant="'transparent'"
                        :size="'xs'"
                        class="justify-center h-full" 
                        @click="openDeleteModal(rows.value.id)"
                    >
                        <TrashIcon class="flex-shrink-0 w-5 h-5 cursor-pointer text-red-600" aria-hidden="true" />
                    </Button>
                    <Modal 
                        :show="showDeleteModal" 
                        maxWidth="2xl" 
                        :closeable="true" 
                        @close="closeDeleteModal">

                        <div class="modal">
                            <p class="text-gray-200 text-2xl bg-red-500 p-4 rounded-md font-bold">
                                Delete {{ convertToHumanReadable(props.detailsLink) }} Confirmation.
                            </p>
                            <p class="text-gray-200 text-lg p-4">
                                Are you sure that you want to <span class="font-bold">delete</span> this 
                                {{ convertToHumanReadable(props.detailsLink).toLowerCase().slice(0, props.detailsLink.length - 1) }}?
                            </p>
                        </div>
                        <div class="flex flex-row justify-end p-9">
                            <div class="dark:bg-gray-600 p-4 rounded-md flex gap-4">
                                <Button 
                                    :type="'button'"
                                    :variant="'info'" 
                                    :size="'sm'"
                                    @click="closeDeleteModal"
                                    class="justify-center px-6 py-2"
                                >
                                    <span>Back</span>   
                                </Button>
                                <Button 
                                    :type="'button'"
                                    :variant="'danger'" 
                                    :size="'sm'" 
                                    class="justify-center px-6 py-2"
                                    @click="deleteLead"
                                >
                                    <span>Confirm</span>
                                </Button>
                            </div>
                        </div>
                    </Modal>
                </div>
            </template>
            <template #lead_front_commission="rows">
                <div class="min-w-max">{{ (parseFloat(rows.value.commission) / 100 * (parseFloat(rows.value.quantity) * parseFloat(rows.value.price))).toFixed(2) }}</div>
            </template>
            <template #lead_front_total="rows">
                <div class="min-w-max">{{ (parseFloat(rows.value.quantity) * parseFloat(rows.value.price)).toFixed(2) }}</div>
            </template>
            <template #contacted_at="rows">
                <div class="min-w-max">{{ (rows.value.contacted_at && rows.value.contacted_at !== '-') ? formatToUserTimezone(rows.value.contacted_at, user.timezone, true) : '-' }}</div>
            </template>
            <template #give_up_at="rows">
                <div class="min-w-max">{{ (rows.value.give_up_at && rows.value.give_up_at !== '-') ? formatToUserTimezone(rows.value.give_up_at, user.timezone, true) : '-' }}</div>
            </template>
            <template #date="rows">
                <div class="min-w-max">{{ (rows.value.date && rows.value.date !== '-') ? formatToUserTimezone(rows.value.date, user.timezone, true) : '-' }}</div>
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
            <template #comfirmed_at="rows">
                <CheckCircleFillIcon 
                    class="flex-shrink-0 w-5 h-5"
                    v-if="Boolean(rows.value.comfirmed_at)"
                />
                <TimesCircleIcon 
                    class="flex-shrink-0 w-5 h-5"
                    v-else-if="!Boolean(rows.value.comfirmed_at)"
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
            <template #client_stage="rows">
                {{ rows.value.client_stage || '-' }}
            </template>
        </vue3-datatable>
        <Modal 
            :show="isRowModalOpen" 
            maxWidth="custom-full"
            :closeable="true" 
            @close="closeRowModal"
        >
            <component 
                :is="modalComponent" 
                :selectedRowData="selectedRowData"
                @closeModal="closeRowModal"
                @rowEdit="rowShowEdit"
            />
        </Modal>
    </div>
</template>
