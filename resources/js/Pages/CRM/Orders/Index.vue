<script setup>
import { ref, onMounted } from "vue";
import "vue-toastification/dist/index.css";
import { useToast } from "vue-toastification";
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import OrderDetailsModal from './Partials/OrderDetailsModal.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Vue3Datatable from '@/Components/Vue3Datatable.vue'
import CustomToastification from '@/Components/CustomToastification.vue';

const props = defineProps({
    errors:Object,
	errorMsg: {
		type: Object,
        default: () => ({}),
	}
})

const pageTitle = "Orders";
const toast = useToast();
const colArray = ref([
	{ field: 'actions', title: 'ACTIONS', headerClass: "dark:text-gray-300 text-sm w-max", filter: false, sort: false },
	{ field: 'id', title: 'ID', headerClass: "dark:text-gray-300 text-sm w-max" },
    { field: "trade_id", title: "Trade ID", headerClass: "text-gray-300 text-sm w-max" },
    { field: "order_date", title: "Date", headerClass: "text-gray-300 text-sm w-max", type: 'date' },
    { field: "action_type", title: "Action Type", headerClass: "text-gray-300 text-sm w-max" },
    { field: "stock_type", title: "Stock Type", headerClass: "text-gray-300 text-sm w-max" },
    { field: "stock", title: "Stock", headerClass: "text-gray-300 text-sm w-max" },
    { field: "unit_price", title: "Unit Price", headerClass: "text-gray-300 text-sm w-max", type: 'number' },
    { field: "quantity", title: "Quantity", headerClass: "text-gray-300 text-sm w-max", type: 'number' },
    { field: "order_total_price", title: "Total Price", headerClass: "text-gray-300 text-sm w-max", type: 'number', sort:false },
    { field: "profit", title: "Profit / Loss", headerClass: "text-gray-300 text-sm w-max", type: 'number' },
    { field: "status", title: "Status", headerClass: "text-gray-300 text-sm w-max" },
    { field: "order_confirmed_at", title: "Confirmed", headerClass: "text-gray-300 text-sm w-max", filter: false },
    { field: "limb_stage", title: "Limb Stage", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "user_id", title: "User / Client", headerClass: "text-gray-300 text-sm w-max" },
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
		title="Orders"
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
				:targetApi="'/data/orders'"
				:createLink="route('orders.create')"
				:detailsLink="'orders'"
				:categoryFilters="'/data/orders/categories'"
				:modalComponent="OrderDetailsModal"
				:customDelete="true"
				:exportRoute="'orders.export'"
			></Vue3Datatable>
		</div>
	</AuthenticatedLayout>
</template>
