<script setup>
import { ref, onMounted } from "vue";
import "vue-toastification/dist/index.css";
import { useToast } from "vue-toastification";
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import { cl } from '@/Composables';
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import TanStackTable from '@/Components/TanStackTable.vue'
import CustomToastification from '@/Components/CustomToastification.vue';
import { useQuery, keepPreviousData } from '@tanstack/vue-query'

const props = defineProps({
  data: {
      type: Object,
      default: () => ({}),
  },
  errors:Object,
	errorMsg: {
		type: Object,
        default: () => ({}),
	}
})

const pageTitle = "Applications";
const toast = useToast();
const colArray = ref([
	{ field: 'actions', title: 'ACTIONS', headerClass: "dark:text-gray-300 text-sm w-max", filter: false, sort: false },
    { field: "id", title: "ID", headerClass: "text-gray-300 text-sm w-max" },
    { field: "name", title: "NAME", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "product", title: "PRODUCT", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "quantity", title: "QTY", headerClass: "text-gray-300 text-sm w-max", type: 'number'  },
    { field: "price", title: "PRICE", headerClass: "text-gray-300 text-sm w-max", type: 'number'  },
    { field: "lead_front_total", title: "TOTAL", headerClass: "text-gray-300 text-sm w-max", type: 'number', sort: false, filter: false  },
    { field: "lead_front_commission", title: "COMM", headerClass: "text-gray-300 text-sm w-max", type: 'number'  },
    { field: "lead_front_assignee", title: "ASSIGNEE", headerClass: "text-gray-300 text-sm w-max", sort: false },
    { field: "vc", title: "VC", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "sdm", title: "SDM", headerClass: "text-gray-300 text-s mw-max"  },
    { field: "liquid", title: "LIQUID", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "lead_id", title: "LINKED LEAD ID", headerClass: "text-gray-300 text-sm w-max"  },
]);
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
// const pagination = ref({
//     pageIndex: 1,
//     pageSize: 10,
// });
let total_rows = ref();

// const getData = async (pagination) => {
//     try {
//         const response = await axios.get(route('applications.getAllLeads'));

//         let tableRowData = await response.data.rows;
//         total_rows.value = response.data.total_rows;

//         // cl(response.data)
//         return tableRowData;
//     } catch (error) {
//         console.error("Error fetching data:", error);
//     }
// };

// //Use controlled state values to fetch data
// const { data, isPlaceholderData } = useQuery({
//     queryKey: [pagination],
//     queryFn: () => getData(pagination),
//     placeholderData: keepPreviousData,
// })

// Custom Toastification
const toastContent = {
    // Toast Component
    component: CustomToastification,

    // Component props
    props: {
        errors: props.errors ? props.errors : {},
        errorMsg: props.errorMsg ? props.errorMsg : {},
        type: props.errorMsg ? props.errorMsg.type : 'default',
		rowErrorMsg: props.errorMsg.rowErrorMsg ? props.errorMsg.rowErrorMsg : false,
    },
};

const showToast = () => {
	toast(toastContent);
}

onMounted(() => {
	if (Object.keys(props.errorMsg).length !== 0) {
		showToast();
	}
});

</script>

<template>
	<AuthenticatedLayout 
		title="Applications"
	>
		<template #header>
			<div class="rounded-xl shadow-md bg-gray-200 dark:bg-dark-eval-1 p-6">
				<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
					<h2 class="text-xl font-semibold leading-tight">
						{{ pageTitle }}
					</h2>
				</div>
				<Breadcrumbs 
					:parentTitle="'CRM'" 
					:title="pageTitle"
				/>
			</div>
		</template>

		<div class="w-full pb-6">
			<TanStackTable
				:columnData="dataColumns"
				:rowData="props.data"
				:totalRows="total_rows"
			>
			</TanStackTable>
			<!-- <TanStackTable
			>
			</TanStackTable> -->
		</div>
	</AuthenticatedLayout>
</template>
