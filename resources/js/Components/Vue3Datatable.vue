<script setup>
import { convertToHumanReadable, cl } from '@/Composables'
import { get } from "@vueuse/core";
import { ref, onMounted, reactive } from "vue";
import axios from "axios";
import Vue3Datatable from "@bhplugin/vue3-datatable";
import "@bhplugin/vue3-datatable/dist/style.css";
import Button from '@/Components/Button.vue'
import Input from '@/Components/Input.vue'
import Modal from '@/Components/Modal.vue'

const categories = ref([]);
const filterIsOpen = ref(false);
const isOpen = ref(false);
const datatable = ref(null);
const total_rows = ref(0);
const searchInput = ref(null);
const loading = ref(true);
const rows = ref(null);
const checkedFilters = ref([])
const selectedRowData = ref(null);
const isRowModalOpen = ref(false);

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
        categories.value = await data.data;
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
const getFilteredData = async (category, category_name) => {
    try {
        loading.value = true;
        
        const data = await axios.get(props.targetApi, {
            method: 'GET',
            params: {
                category: category,
                category_name: category_name,
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
    checkedFilters.value = [];
    // cl(checkedFilters.value);
};

const closeRowModal = () => {
    isRowModalOpen.value = false;
    selectedRowData.value = null;
};

// Redirects user to the selected row's edit page when user click on the table row
const rowClick = (data) => {
    // selectedRowData.value = data;
    // isRowModalOpen.value = true;
    window.location.href = props.detailsLink + data.id + '/edit';
    // console.log(data.id);
    // alert('Details \n' + data.id + ', ' + data.title + ', ' + data.content + ', ' + data.site);
};

</script>

<template>
    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-5">
            <div class="col-span-1 md:w-full">
                <Input 
                    v-model="params.search"
                    ref="searchInput"
                    type="text"
                    placeholder="Search..."
                    class="w-full"
                />
            </div>

            <div class="grid grid-cols-subgrid gap-4 col-span-3">
                <div class="lg:col-start-3 grid grid-cols-1 lg:grid-cols-3 gap-5">
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
                            :variant="'secondary'"
							:size="'sm'"
                            class="justify-center gap-2 w-full h-full" 
                            @click="openFilterModal"
                        >
                            Filters
                            <!-- <svg
                                viewBox="0 0 24 24"
                                width="20"
                                height="20"
                                stroke="currentColor"
                                stroke-width="2"
                                fill="none"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="transition inline-block"
                                :class="{ 'rotate-180': filterIsOpen }"
                            >
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg> -->
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
                                        <div class="flex flex-row flex-wrap gap-2 p-2" v-if="key === 'assignee'">
                                            <!-- for multi select loop through the array to display all assignee -->
                                            <!-- can try to use built-in checkbox component -->
                                            <div v-for="(item, index) in value" :key="index">
                                                <input 
                                                    :id="index" 
                                                    type="checkbox" 
                                                    class="hidden peer" 
                                                    name="filters[]" 
                                                    :value="item" 
                                                    v-model="checkedFilters"
                                                >
                                                <label 
                                                    :for="index" 
                                                    class="inline-flex items-center justify-between w-auto py-2 px-4 font-medium tracking-tight 
                                                        border rounded-lg cursor-pointer bg-brand-light text-brand-black border-gray-500 
                                                        peer-checked:border-violet-400 peer-checked:bg-violet-700 peer-checked:text-white">
                                                    <div class="flex items-center justify-center w-full">
                                                        <div class="text-sm dark:text-gray-300">{{ item }}</div>
                                                    </div>
                                                </label>
                                            </div>

                                            <!-- <Button 
                                                :variant="'secondary'"
                                                :size="'sm'"
                                                class="justify-center gap-2 rounded-md border border-gray-500"
                                                @click=""
                                                v-for="(item, index) in value" :key="index"
                                            >
                                                eyeyye
                                            </Button> -->
                                        </div>
                                        <div class="flex flex-row flex-wrap gap-2 p-2" v-else>
                                            <Button 
                                                :variant="'secondary'"
                                                :size="'sm'"
                                                class="justify-center gap-2 rounded-md border border-gray-500"
                                                @click="getFilteredData(key, item)"
                                                v-for="(item, index) in value" :key="index"
                                            >
                                                {{ item }}
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end px-9">
                                    <div class="rounded-md shadow-lg flex flex-row gap-2">
                                        <Button 
                                            :variant="'secondary'"
                                            :size="'sm'"
                                            class="justify-center px-6 py-2 rounded-md border border-purple-500" 
                                            @click="reset"
                                        >
                                            Reset
                                        </Button>
                                        <Button 
                                            :size="'sm'"
                                            class="justify-center px-6 py-2 rounded-md border border-gray-500" 
                                            @click="getFilteredData('assignee', checkedFilters)"
                                        >
                                            Apply
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </Modal>
                        <!-- <div class="p-2 w-[300px] h-[500px] overflow-auto flex flex-col bg-gray-700 rounded-md shadow-lg border border-gray-500 absolute right-0 top-11 mt-0.5 space-y-1 z-10 origin-top-right divide-y divide-gray-500" v-if="filterIsOpen" >
                        </div> -->
                    </div>
        
                    <div class="relative col-span-1 rounded-md shadow-lg border border-gray-500">
                        <Button 
                            :variant="'secondary'"
							:size="'sm'"
                            class="justify-center gap-2 w-full h-full" 
                            @click="isOpen = !isOpen"
                        >
                            Columns
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
                                :class="{ 'rotate-180': isOpen }"
                            >
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </Button>
                        <ul 
                            v-if="isOpen" 
                            class="absolute right-0 top-11 z-10 mt-0.5 p-2.5 min-w-[150px] bg-gray-700 rounded shadow-md space-y-1 border border-gray-500"
                        >
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
            :rowClass="'odd:bg-gray-600 even:bg-gray-700 dark:text-gray-200 cursor-pointer'" 
            :search="params.search" 
            :columnFilter="true" 
            :sortColumn="params.sort_column"
            :sortDirection="params.sort_direction"
            :hasCheckbox="true"
            :pageSize="params.pagesize"
            :totalRows="total_rows"
            @pageChange="changePage"
            @rowClick="rowClick"
        ></vue3-datatable>
        <!-- <Modal 
            :show="isRowModalOpen" 
            maxWidth="7xl"
            :closeable="true" 
            @close="closeRowModal">

            <component 
                :is="modalComponent" 
                :selectedRowData="selectedRowData"
            />
        </Modal> -->
    </div>
</template>
