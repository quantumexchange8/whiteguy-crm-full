<script setup>
import { ref, onMounted, reactive } from "vue";
import Vue3Datatable from "@bhplugin/vue3-datatable";
import "@bhplugin/vue3-datatable/dist/style.css";
import axios from "axios";
import Input from '@/Components/Input.vue'
import Button from '@/Components/Button.vue'

const filterIsOpen = ref(false);
const isOpen = ref(false);
const datatable = ref(null);
const total_rows = ref(0);
const searchInput = ref(null);
const loading = ref(true);
const rows = ref(null);

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
})

onMounted(() => {
    getData();
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
        console.log
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

// Actions / Bulk Actions
const getFilteredRows = () => {
    const filteredRows = datatable.value.getFilteredRows();
    console.log(filteredRows);
    alert('Check console log to see the filtered rows');
};

const getSelectedRows = () => {
    const selected = datatable.value.getSelectedRows();
    console.log(selected);
    alert('Rows selected: ' + selected?.length || 0);
};

const clearSelectedRows = () => {
    const selected = datatable.value.getSelectedRows();
    datatable.value.clearSelectedRows();
    alert('Rows unselected: ' + selected?.length || 0);
};

const selectRow = (index) => {
    datatable.value.selectRow(index);
    alert('Rows selected with position: ' + (index + 1));
};

const unselectRow = (index) => {
    datatable.value.unselectRow(index);
    alert('Rows unselected with position: ' + (index + 1));
};

const isRowSelected = (index) => {
    const selected = datatable.value.isRowSelected(index);
    alert('Rows with position ' + (index + 1) + ' selected: ' + selected);
};

const getColumnFilters = () => {
    console.log(datatable.value.getColumnFilters());
    alert('Check console log to see the column filters');
};

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

const reset = () => {
    clearColumnFiltersInts();
    params.search = '';
};

const rowClick = (data) => {
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
                            @click="filterIsOpen = !filterIsOpen"
                        >
                            Filters
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
                                :class="{ 'rotate-180': filterIsOpen }"
                            >
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </Button>
                        <div class="p-2 flex flex-col bg-gray-700 rounded-md shadow-lg border border-gray-500 absolute right-0 top-11 mt-0.5 space-y-1 z-10 origin-top-right divide-y divide-gray-100" v-if="filterIsOpen" >
                            <div class="p-2">
                                <button type="button" class="btn btn-outline text-gray-300" @click="getFilteredRows()">Get Filtered Rows</button>
                            </div>
                            <div class="p-2 flex flex-col gap-2">
                                <button type="button" class="btn btn-outline text-gray-300 text-left" @click="getSelectedRows()">Get Selected Rows</button>
                                <button type="button" class="btn btn-outline text-gray-300 text-left" @click="clearSelectedRows()">Clear Selected Rows</button>
                            </div>
                            <div class="p-2 flex flex-col gap-2">
                                <button type="button" class="btn btn-outline text-gray-300 text-left" @click="selectRow(1)">Select 2<sup class="mr-1">nd</sup> Rows</button>
                                <button type="button" class="btn btn-outline text-gray-300 text-left" @click="unselectRow(1)">Unselect 2<sup class="mr-1">nd</sup> Row</button>
                                <button type="button" class="btn btn-outline text-gray-300 text-left" @click="isRowSelected(1)">Check 2<sup class="mr-1">nd</sup> Row Selected</button>
                            </div>
                            <div class="p-2">
                                <button type="button" class="btn btn-outline text-gray-300" @click="getColumnFilters()">Get Column Filters</button>
                            </div>
                        </div>
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
    </div>
</template>
