<script setup>
import axios from "axios";
import { ref, onMounted, onBeforeMount, reactive, watch, computed } from "vue";
import { useForm, router, usePage, Link } from '@inertiajs/vue3';
import { convertToHumanReadable, cl, formatToUserTimezone, usePermission, convertToIndexedValueObject } from '@/Composables';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';
import BaseSelectInputField from '@/Components/BaseSelectInputField.vue'
import CustomSelectInputField2 from '@/Components/CustomSelectInputField2.vue'
import { useQuery, keepPreviousData } from '@tanstack/vue-query'
import { FlexRender, getCoreRowModel, getPaginationRowModel, useVueTable, getSortedRowModel, getFilteredRowModel } from '@tanstack/vue-table'

const { has } = usePermission();
const page = usePage();
const user = computed(() => page.props.auth.user)

const props = defineProps({
    errors:Object,
})

const pageSizeArray  = ref(convertToIndexedValueObject(
	[ 5, 10, 50, 100 ]
));

const dataColumns = [
  {
    accessorKey: 'id',
    header: 'ID',
    enableSorting: false,
  },
  {
    accessorKey: 'first_name',
    header: 'FIRST NAME',
  },
  {
    accessorKey: 'assigne_id',
    header: 'ASSIGNEE',
  },
  {
    accessorKey: 'contacted_at',
    header: 'LAST CALLED',
  },
  {
    accessorKey: 'give_up_at',
    header: 'GIVE UP?',
  },
  {
    accessorKey: 'date',
    header: 'DATE OPPD IN',
  },
  {
    accessorKey: 'phone_number',
    header: 'PHONE NUMBER',
  },
  {
    accessorKey: 'email',
    header: 'Email',
  },
  {
    accessorKey: 'country',
    header: 'Country',
  },
  {
    accessorKey: 'vc',
    header: 'VC',
  },
  {
    accessorKey: 'data_type',
    header: 'DATA TYPE',
  },
  {
    accessorKey: 'data_source',
    header: 'DATA SOURCE',
  },
  {
    accessorKey: 'data_code',
    header: 'DATA CODE',
  },
]

let tableRowData = ref({});
let total_rows = ref();

const sorting = ref([{
  id: 'id',
  desc: true, //sort by id in descending order by default
}])
const filter = ref('');

onMounted(() => {
    // setTimeout(() => {
    //     // getData(pagination);
    // }, 200);
});

const pagination = ref({
    pageIndex: 1,
    pageSize: 10,
});

// const updatePaginationOnChange = () => {
    // cl(table.getPageOptions);
    // cl(table.getPageCount);
    // cl(table.getRowCount);
    // cl(isPlaceholderData['data']);
    // cl(pagination.value);
    // cl(table.getCanPreviousPage());
// }

const getData = async (pagination) => {
    try {
        const response = await axios.get(route('applications.getAllLeads'), {
            method: 'GET',
            // body: JSON.stringify(pagination),
            params: {
                pagination: pagination.value,
            },
        });

        tableRowData.value = await response.data.rows.data;
        total_rows.value = response.data.total_rows;

        return tableRowData.value;
    } catch (error) {
        console.error("Error fetching data:", error);
    }
};

//Use controlled state values to fetch data
const { data, isPlaceholderData } = useQuery({
    queryKey: [pagination],
    queryFn: () => getData(pagination),
    placeholderData: keepPreviousData,
})

const table = useVueTable({
    get data() {
        return data && data.value ? data.value : []
    },
    columns: dataColumns,
    getCoreRowModel: getCoreRowModel(),
    manualPagination: true,
    get rowCount() {
        return total_rows.value;
    },
    state: {
        get pagination() {
            return pagination.value;
        },
        get sorting() {
            return sorting.value
        },
        get globalFilter() {
            return filter.value
        },
    },
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onSortingChange: updater => {
        sorting.value =
        typeof updater === 'function'
            ? updater(sorting.value)
            : updater
    },
    onPaginationChange: updater => {
        pagination.value =
        typeof updater === 'function'
            ? updater(pagination.value)
            : updater
    },
})

const updatePageSize = (val) => {
    table.setPageSize(pageSizeArray.value[val]['value']);
}

</script>

<template>
    <div class="py-12 bg-white rounded-xl shadow-md dark:bg-dark-eval-1">
        <div class="px-4 sm:px-6 lg:px-6">
            <div class="mt-8">
                <div class="-mx-4 -my-2 sm:-mx-6 lg:-mx-8">
                    <div class="min-w-full py-3 align-middle sm:px-6 lg:px-8">
                        <div class="mb-5">
                            <Input
                                type="text"
                                class="rounded"
                                placeholder="Search"
                                v-model="filter"
                            />
                        </div>
                        <div class="mb-4 overflow-auto rounded-lg">
                            <table class="min-w-full overflow-hidden">
                                <thead>
                                    <tr
                                        v-for="headerGroup in table.getHeaderGroups()"
                                        :key="headerGroup.id"
                                    >
                                        <th
                                            v-for="header in headerGroup.headers"
                                            :key="header.id"
                                            scope="col"
                                            class="px-3 py-4 text-left text-sm font-bold text-gray-300 bg-gray-600/80"
                                            :class="{
                                                'cursor-pointer select-none': header.column.getCanSort(),
                                            }"
                                            @click="header.column.getToggleSortingHandler()?.($event)"
                                        >
                                        <FlexRender
                                            :render="header.column.columnDef.header"
                                            :props="header.getContext()"
                                        />
                                            {{ { asc: ' ↑', desc: '↓' }[header.column.getIsSorted()] }}
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-500/70">
                                    <tr v-for="row in table.getRowModel().rows" :key="row.id" class="bg-gray-700/60 dark:text-gray-200">
                                        <td
                                            v-for="cell in row.getVisibleCells()"
                                            :key="cell.id"
                                            class="whitespace-nowrap px-3 py-4 text-xs text-gray-300"
                                        >
                                        <FlexRender
                                            :render="cell.column.columnDef.cell"
                                            :props="cell.getContext()"
                                        />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-12">
                            <div class="text-sm col-span-3 text-gray-300 flex flex-row">
                                <p class="self-center">
                                    Showing {{ table.getState().pagination.pageIndex === 1 ? 1 : 1 + ((table.getState().pagination.pageIndex - 1) * table.getState().pagination.pageSize) }} to
                                    {{ ((table.getState().pagination.pageIndex + 1) * table.getState().pagination.pageSize) > table.getRowCount() 
                                            ? table.getRowCount() 
                                            : ((table.getState().pagination.pageIndex) * table.getState().pagination.pageSize) }} of
                                    {{ table.getRowCount() }} entries
                                </p>
                                <BaseSelectInputField
                                    :inputArray="pageSizeArray"
                                    :inputId="'page_size'"
                                    :dataValue="2"
                                    :customValue="true"
                                    @change="updatePageSize($event.target.value)"
                                    class="self-end"
                                />
                            </div>
                            <div class="pt-2 col-span-9 ml-auto flex flex-row gap-2">
                                <button
                                    class="border-2 text-sm border-gray-300 rounded-full px-1.5 py-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-dark-eval-1 hover:bg-blue-500"
                                    @click="table.setPageIndex(1)"
                                >
                                    &lt;&lt;
                                </button>
                                <button
                                    class="border-2 text-sm border-gray-300 rounded-full px-2.5 py-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-dark-eval-1 hover:bg-blue-500"
                                    :disabled="!table.getCanPreviousPage() || table.getState().pagination.pageIndex === 1"
                                    @click="table.previousPage()"
                                >
                                    &lt;
                                </button>
                                <button
                                    class="border-2 text-sm border-gray-200 rounded-full px-2.5 py-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-dark-eval-1 hover:bg-blue-500"
                                    :disabled="!table.getCanNextPage()"
                                    @click="table.nextPage();"
                                >
                                    >
                                </button>
                                <button
                                    class="border-2 text-sm border-gray-300 rounded-full px-1.5 py-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-dark-eval-1 hover:bg-blue-500"
                                    @click="table.setPageIndex(table.getPageCount())"
                                >
                                    >>
                                </button>
                                <!-- <button
                                    class="border-2 text-sm border-gray-300 rounded-full px-1.5 py-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-dark-eval-1 hover:bg-blue-500"
                                    @click="updatePaginationOnChange()"
                                >
                                    ?
                                </button> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
