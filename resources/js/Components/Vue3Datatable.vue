<script setup>
import { convertToHumanReadable, cl } from '@/Composables'
import { get } from "@vueuse/core";
import { ref, onMounted, reactive, watch } from "vue";
import { useForm } from '@inertiajs/vue3'
import axios from "axios";
import Vue3Datatable from "@bhplugin/vue3-datatable";
import "@bhplugin/vue3-datatable/dist/style.css";
import Button from '@/Components/Button.vue'
import Input from '@/Components/Input.vue'
import Modal from '@/Components/Modal.vue'
import CustomFileInputField from '@/Components/CustomFileInputField.vue'
import { TrashIcon, EyeIcon, PageEditIcon } from '@/Components/Icons/outline'
import Dropdown from './Dropdown.vue'
import { router } from '@inertiajs/vue3'

const categories = ref([]);
const filterIsOpen = ref(false);
const importModalIsOpen = ref(false);
const datatable = ref(null);
const total_rows = ref(0);
const searchInput = ref(null);
const loading = ref(true);
const rows = ref(null);
const checkedFilters = reactive({
    contact_outcome: [],
    stage: [],
    assignee: [],
    last_called: '',
    give_up_at: '',
});
const selectedRowData = ref(null);
const isRowModalOpen = ref(false);
const selectedRows = ref([]);
const isExportable = ref(true);
const selectedRowsLength = ref(0);
const filteredRowsLength = ref(0);
const isImportable = ref(true);

const props = defineProps({
    cols: {
      type: Array,
      default: () => [],
    },
	targetApi: {
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
    errors:Object,
})

onMounted(() => {
    getData();
    getCategoryFilters();
});

const params = reactive({
    page: 1,
    pagesize: 10,
    search: '',
    column_filters: [],
    sort_column: 'id',
    sort_direction: 'asc',
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
        // cl(data);
        rows.value = await data.data;
        total_rows.value = data.data.length;

    } catch (error) {
        console.error("Error fetching data:", error);
    } finally {
        loading.value = false;
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
        // console.log(data);
        categories.value = data.data;
        // categoriesCO.value = data.data.contact_outcome;
        // categoriesS.value = data.data.stage;
        // categoriesLA.value = data.data.last_called;
        // categoriesGUA.value = data.data.give_up_at;
        // categoriesA.value = data.data.assignee;
        // console.log(categories);
        // total_rows.value = data.data.length;

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
        console.log(data);
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
    checkedFilters.contact_outcome = [];
    checkedFilters.stage = [];
    checkedFilters.assignee = [];
    checkedFilters.last_called = '';
    checkedFilters.give_up_at = '';
    closeFilterModal();
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
    window.location.href = props.detailsLink + data.id + '/edit';
};

const form = useForm({
	leadExcelFile: '',
});

// Get input file and send to controller for importing to db
const importExcel = (e) => {
    e.preventDefault();
    cl(form.leadExcelFile);
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
        window.location.href = route('leads.export', { leads: selectedRows.value });
    }

    clearSelectedRows()

    selected.forEach((col) => {
        selectedRows.value.push(col.id);
    });
    router.get(route('leads.export', { leads: selectedRows.value }), {
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

const addFilter = (category, category_item) => {
    // checkedCategories.value.forEach((col) => {
    //     // cl(checkedCategories.value.indexOf(col));
    //     if (checkedCategories.value.indexOf(col) > -1 && col.categoryItem === category_item) {
    //         checkedCategories.value.splice(checkedCategories.value.indexOf(col), 1);
    //     }
    // });

    // checkedCategories.value.push({category: category, categoryItem: category_item});
    cl(checkedFilters);

}
</script>

<template>
    <div class="p-6 bg-white rounded-xl shadow-md dark:bg-dark-eval-1">
		<div class="flex justify-end pb-6">
			<div class="rounded-md shadow-lg border border-gray-500 flex justify-end">
				<Button 
					:type="'button'"
					:variant="'success'" 
					:size="'sm'" 
					class="justify-center px-6 py-2 gap-2 w-full h-full"
					:href="route('leads.create')"
				>
					Create Lead
				</Button>
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
                                    <p class="text-sm p-2 dark:text-gray-300 text-center font-bold pb-4">Import Excel</p>
                                    <div class="px-6 pb-6">
                                        <!-- <form class="flex flex-col gap-4" @submit="importExcel">
                                            <CustomFileInputField
                                                :inputId="'leadExcelFile'"
                                                v-model="form.leadExcelFile"
                                            />
                                            <Button 
                                                :variant="'success'"
                                                :size="'sm'"
                                                :disabled="isImportable"
                                                class="justify-center gap-2 w-full h-full"
                                            >
                                                Import File
                                            </Button>
                                        </form> -->
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
                            @close="closeImportModal">

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
                                                <input 
                                                    :id="key + index" 
                                                    type="radio" 
                                                    class="hidden peer" 
                                                    :name="key" 
                                                    :value="item" 
                                                    v-model="checkedFilters[key]"
                                                    v-if="key === 'last_called' || key === 'give_up_at'"
                                                >
                                                <input 
                                                    :id="key + index" 
                                                    type="checkbox" 
                                                    class="hidden peer" 
                                                    :name="key" 
                                                    :value="item" 
                                                    v-model="checkedFilters[key]"
                                                    v-else
                                                >
                                                <label 
                                                    :for="key + index" 
                                                    class="inline-flex items-center justify-between w-auto py-2 px-4 font-medium tracking-tight 
                                                        border rounded-lg cursor-pointer bg-brand-light text-brand-black border-gray-500
                                                        peer-checked:border-violet-400 peer-checked:bg-violet-700 peer-checked:text-white">
                                                    <div class="flex items-center justify-center w-full">
                                                        <div class="text-sm dark:text-gray-300">{{ item }}</div>
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
            <template #actions="rows">
                <div class="flex flex-row flex-nowrap gap-4">
                    <Button 
                        :type="'button'"
                        :size="'sm'"
                        class="justify-center gap-2 h-full" 
                        @click="rowShowDetails(rows.value)"
                    >
                        <EyeIcon class="flex-shrink-0 w-6 h-6 cursor-pointer" aria-hidden="true" />
                    </Button>
                    <Button 
                        :type="'button'"
                        :variant="'info'"
                        :size="'sm'"
                        class="justify-center gap-2 h-full" 
                        @click="rowShowEdit(rows.value)"
                    >
                        <PageEditIcon class="flex-shrink-0 w-6 h-6 cursor-pointer" aria-hidden="true" />
                    </Button>
                    <Button 
                        :type="'button'"
                        :variant="'danger'"
                        :size="'sm'"
                        class="justify-center gap-2 h-full" 
                        @click=""
                    >
                        <TrashIcon class="flex-shrink-0 w-6 h-6 cursor-pointer" aria-hidden="true" />
                    </Button>
                    <!-- <button type="button" class="btn btn-success !py-1" @click="viewUser(rows.value)">View</button>
                    <button type="button" class="btn btn-danger !py-1" @click="deleteUser(rows.value)">Delete</button> -->
                </div>
            </template>
        </vue3-datatable>
        <Modal 
            :show="isRowModalOpen" 
            maxWidth="7xl"
            :closeable="true" 
            @close="closeRowModal">

            <component 
                :is="modalComponent" 
                :selectedRowData="selectedRowData"
            />
        </Modal>
    </div>
</template>
