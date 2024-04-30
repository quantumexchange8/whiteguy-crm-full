<script setup>
import { ref, onMounted } from "vue";
import { useToast } from "vue-toastification";
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Vue3Datatable from '@/Components/Vue3Datatable.vue'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import SaleOrderDetailsModal from './Partials/SaleOrderDetailsModal.vue'
import CustomToastification from '@/Components/CustomToastification.vue';

const props = defineProps({
    errors:Object,
	errorMsg: {
		type: Object,
        default: () => ({}),
	}
})

const pageTitle = "Sale Orders";
const toast = useToast();
const colArray = ref([
	{ field: 'actions', title: 'ACTIONS', headerClass: "dark:text-gray-300 text-sm w-max", filter: false, sort: false },
    { field: "id", title: "ID (SITE)", headerClass: "text-gray-300 text-sm w-max" },
    { field: "vc", title: "VC", headerClass: "text-gray-300 text-sm w-max" },
    { field: "registered_name", title: "REGISTERED NAME", headerClass: "text-gray-300 text-sm w-max" },
    { field: "ao_numbers", title: "AO #1 / AO #2", headerClass: "text-gray-300 text-sm w-max", filter: false, sort: false },
    { field: "balance_due", title: "BALANCE DUE", headerClass: "text-gray-300 text-sm w-max", type: 'number' },
    { field: "docs_received", title: "DOCS RECEIVED", headerClass: "text-gray-300 text-sm w-max" },
    { field: "tc_sent", title: "TC SENT", headerClass: "text-gray-300 text-sm w-max", type: 'date' },
    { field: "tt_received", title: "TT RECEIVED", headerClass: "text-gray-300 text-sm w-max", type: 'date' },
    { field: "written_date", title: "WRITTEN DATE", headerClass: "text-gray-300 text-sm w-max", type: 'date' },
]);

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
		title="Sale Orders"
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
			<Vue3Datatable 
				:cols="colArray" 
				:targetApi="'/data/sale-orders'"
				:createLink="route('sale-orders.create')"
				:detailsLink="'sale-orders'"
				:categoryFilters="'/data/sale-orders/categories'"
				:modalComponent="SaleOrderDetailsModal"
				:exportRoute="'sale-orders.export'"
				:exportAllRoute="route('sale-orders.getAllSaleOrdersForExport')"
			></Vue3Datatable>
		</div>
	</AuthenticatedLayout>
</template>
