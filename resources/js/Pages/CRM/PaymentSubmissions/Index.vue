<script setup>
import { ref, onMounted } from "vue";
import "vue-toastification/dist/index.css";
import { useToast } from "vue-toastification";
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import PaymentSubmissionDetailsModal from './Partials/PaymentSubmissionDetailsModal.vue'
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

const pageTitle = "Payment Submissions";
const toast = useToast();
const colArray = ref([
	{ field: 'actions', title: 'ACTIONS', headerClass: "dark:text-gray-300 text-sm w-max", filter: false, sort: false },
    { field: "id", title: "ID", headerClass: "text-gray-300 text-sm w-max" },
    { field: "user_id", title: "USER", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "status", title: "STATUS", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "date", title: "DATE", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "amount", title: "CRYPTO AMOUNT", headerClass: "text-gray-300 text-sm w-max", type: 'number' },
    { field: "converted_amount", title: "CONVERTED USD AMOUNT", headerClass: "text-gray-300 text-sm w-max", type: 'number' },
    { field: "user_memo", title: "MEMO", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "payment_method_id", title: "PAYMENT METHOD", headerClass: "text-gray-300 text-sm w-max"  },
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
		title="Payment Submissions"
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
				:targetApi="'/data/payment-submissions'"
				:createLink="route('payment-submissions.create')"
				:detailsLink="'payment-submissions'"
				:categoryFilters="'/data/payment-submissions/categories'"
				:modalComponent="PaymentSubmissionDetailsModal"
				:exportRoute="'payment-submissions.export'"
				:exportAllRoute="route('payment-submissions.getAllPaymentSubmissionsForExport')"
			></Vue3Datatable>
		</div>
	</AuthenticatedLayout>
</template>
