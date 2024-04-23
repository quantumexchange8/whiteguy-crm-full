<script setup>
import { ref, onMounted } from "vue";
import { Link } from '@inertiajs/vue3';
import "vue-toastification/dist/index.css";
import { usePermission } from '@/Composables'
import { useToast } from "vue-toastification";
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import DashboardDatatable from '@/Components/DashboardDatatable.vue'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import { ViewListIcon, ClipboardListIcon, DocumentTextIcon, DocumentReportIcon } from '@heroicons/vue/outline';
import CustomToastification from '@/Components/CustomToastification.vue';

const props = defineProps({
    errors:Object,
	errorMsg: {
		type: Object,
        default: () => ({}),
	}
})

const pageTitle = "Dashboard";
const { has } = usePermission();
const toast = useToast();
const totalLeadCount = ref(0);
const totalLeadFrontCount = ref(0);
const totalOrderCount = ref(0);
const totalSaleOrderCount = ref(0);

const leadFrontsColArray = ref([
    { field: "id", title: "ID", headerClass: "text-gray-300 text-sm w-max" },
    { field: "name", title: "NAME", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "product", title: "PRODUCT", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "quantity", title: "QTY", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "price", title: "PRICE", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "total", title: "TOTAL", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "commission", title: "COMM", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "assignee", title: "ASSIGNEE", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "vc", title: "VC", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "sdm", title: "SDM", headerClass: "text-gray-300 text-s mw-max"  },
    { field: "liquid", title: "LIQUID", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "lead_id", title: "LINKED LEAD", headerClass: "text-gray-300 text-sm w-max"  },
]);
const paymentSubmissionsColArray = ref([
    { field: "id", title: "ID", headerClass: "text-gray-300 text-sm w-max" },
    { field: "user_id", title: "USER", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "status", title: "STATUS", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "date", title: "DATE", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "amount", title: "CRYPTO AMOUNT", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "converted_amount", title: "CONVERTED USD AMOUNT", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "user_memo", title: "MEMO", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "payment_method_id", title: "PAYMENT METHOD", headerClass: "text-gray-300 text-sm w-max"  },
]);
const leadsColArray = ref([
    { field: "id", title: "ID", headerClass: "dark:text-gray-300 text-sm w-max" },
    { field: "first_name", title: "FIRST NAME", headerClass: "dark:text-gray-300 text-sm w-max" },
    { field: "last_name", title: "LAST NAME", headerClass: "dark:text-gray-300 text-sm w-max"  },
    { field: "assignee_id", title: "ASSIGNEE", headerClass: "dark:text-gray-300 text-sm w-max"  },
    { field: "contacted_at", title: "LAST CALLED", headerClass: "dark:text-gray-300 text-sm w-max", type: 'date'  },
    { field: "give_up_at", title: "GIVE UP?", headerClass: "dark:text-gray-300 text-sm w-max", type: 'date'  },
    { field: "date", title: "DATE OPP'D IN", headerClass: "dark:text-gray-300 text-sm w-max", type: 'date'  },
    { field: "phone_number", title: "PHONE NUMBER", headerClass: "dark:text-gray-300 text-sm w-max", type: 'number'  },
    { field: "email", title: "EMAIL", headerClass: "dark:text-gray-300 text-sm w-max"  },
    { field: "country", title: "COUNTRY", headerClass: "dark:text-gray-300 text-sm w-max"  },
    { field: "vc", title: "VC", headerClass: "dark:text-gray-300 text-sm w-max"  },
    { field: "data_type", title: "DATA TYPE", headerClass: "dark:text-gray-300 text-sm w-max"  },
    { field: "data_source", title: "DATA SOURCE", headerClass: "dark:text-gray-300 text-sm w-max"  },
    { field: "data_code", title: "DATA CODE", headerClass: "dark:text-gray-300 text-sm w-max"  },
]);
const usersColArray = ref([
    { field: "username", title: "USERNAME (SITE)", headerClass: "text-gray-300 text-sm w-max" },
    { field: "account_id", title: "ACCOUNT ID", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "full_name", title: "FULL LEGAL NAME", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "lead_status", title: "LEAD STATUS", headerClass: "text-gray-300 text-sm w-max", type: 'number'  },
    { field: "client_stage", title: "CLIENT STAGE", headerClass: "text-gray-300 text-sm w-max", type: 'number'  },
    { field: "rank", title: "RANK", headerClass: "text-gray-300 text-sm w-max", type: 'number'  },
    { field: "account_manager_id", title: "ACC. MANAGER", headerClass: "text-gray-300 text-sm w-max" },
    { field: "kyc_status", title: "KYC STATUS", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "is_active", title: "ACTIVE", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "is_staff", title: "STAFF", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "has_crm_access", title: "CRM", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "has_leads_access", title: "LEADS", headerClass: "text-gray-300 text-sm w-max"  },
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

onMounted(async () => {
	try {
		const totalLeadCountResponse = await axios.get(route('leads.getTotalLeadCount'));
		totalLeadCount.value = totalLeadCountResponse.data;

		const totalLeadFrontCountResponse = await axios.get(route('lead-fronts.getTotalLeadFrontCount'));
		totalLeadFrontCount.value = totalLeadFrontCountResponse.data;

		const totalOrderCountResponse = await axios.get(route('orders.getTotalOrderCount'));
		totalOrderCount.value = totalOrderCountResponse.data;

		const totalSaleOrderCountResponse = await axios.get(route('sale-orders.getTotalSaleOrderCount'));
		totalSaleOrderCount.value = totalSaleOrderCountResponse.data;

	} catch (error) {
		console.error('Error fetching data:', error);
	}

	if (Object.keys(props.errorMsg).length !== 0) {
		showToast();
	}
});
</script>

<template>
	<AuthenticatedLayout title="Dashboard">
		<template #header>
			<div class="rounded-xl shadow-md bg-gray-200 dark:bg-dark-eval-1 p-6">
				<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
					<h2 class="text-xl font-semibold leading-tight">
						{{ pageTitle }}
					</h2>
				</div>
				<!-- <h2 v-if="has('is_superuser')">{{ 'You are seeing this which only superuser can see' }}</h2>
				<h2 v-if="has('has_leads_access') || has('has_crm_access')">{{ 'You are seeing this which only those with leads or crm access can see' }}</h2>
				<h2 v-if="has('is_staff')">{{ 'You are seeing this which all admins can see' }}</h2>
				<h2 v-if="has('is_active')">{{ 'You are seeing this which all active users can see' }}</h2> -->
				<Breadcrumbs 
					:title="pageTitle"
				/>
			</div>
		</template>

		<div class="flex flex-col gap-6">
			<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
				<div class="col-span-full lg:col-span-3">
					<div class="flex flex-row justify-center gap-6 px-10 py-6 bg-white rounded-xl shadow-md dark:bg-dark-eval-1">
						<Link 
							:href="route('orders.index')"
							class="!text-base font-medium text-gray-700 dark:text-gray-300"
						>
							<DocumentReportIcon
								class="flex-shrink-0 w-20 h-20 text-cyan-500 transform transition duration-300 hover:scale-125"
							/>
						</Link>
						<div class="flex flex-col">
							<p class="text-5xl text-gray-200 font-bold pb-3">{{ totalOrderCount }}</p>
							<p class="text-gray-300/60">Total Orders</p>
						</div>
					</div>
				</div>
				<div class="col-span-full lg:col-span-3">
					<div class="flex flex-row justify-center gap-6 px-10 py-6 bg-white rounded-xl shadow-md dark:bg-dark-eval-1">
						<Link 
							:href="'#'"
							class="!text-base font-medium text-gray-700 dark:text-gray-300"
						>
							<DocumentTextIcon
								class="flex-shrink-0 w-20 h-20 text-cyan-500 transform transition duration-300 hover:scale-125"
							/>
						</Link>
						<div class="flex flex-col">
							<p class="text-5xl text-gray-200 font-bold pb-3">{{ totalSaleOrderCount }}</p>
							<p class="text-gray-300/60">Total Sales Orders</p>
						</div>
					</div>
				</div>
				<div class="col-span-full lg:col-span-3">
					<div class="flex flex-row justify-center gap-6 px-10 py-6 bg-white rounded-xl shadow-md dark:bg-dark-eval-1">
						<Link 
							:href="route('lead-fronts.index')"
							class="!text-base font-medium text-gray-700 dark:text-gray-300"
						>
							<ClipboardListIcon
								class="flex-shrink-0 w-20 h-20 text-cyan-500 transform transition duration-300 hover:scale-125"
							/>
						</Link>
						<div class="flex flex-col">
							<p class="text-5xl text-gray-200 font-bold pb-3">{{ totalLeadFrontCount }}</p>
							<p class="text-gray-300/60">Total Lead Fronts</p>
						</div>
					</div>
				</div>
				<div class="col-span-full lg:col-span-3">
					<div class="flex flex-row justify-center gap-6 px-10 py-6 bg-white rounded-xl shadow-md dark:bg-dark-eval-1">
						<Link 
							:href="route('leads.index')"
							class="!text-base font-medium text-gray-700 dark:text-gray-300"
						>
							<ViewListIcon
								class="flex-shrink-0 w-20 h-20 text-cyan-500 transform transition duration-300 hover:scale-125"
							/>
						</Link>
						<div class="flex flex-col">
							<p class="text-5xl text-gray-200 font-bold pb-3">{{ totalLeadCount }}</p>
							<p class="text-gray-300/60">Total Leads</p>
						</div>
					</div>
				</div>
			</div>
			<div class="w-full">
				<DashboardDatatable 
					:cols="leadFrontsColArray" 
					:targetApi="'/data/lead-fronts/latest'"
					:createLink="route('lead-fronts.create')"
					:detailsLink="'lead-fronts'"
					:tableTitle="'Latest Lead Fronts'"
				/>
			</div>
			<div class="grid grid-cols-1 lg:grid-cols-12 mb-8 gap-6">
				<div class="col-span-full lg:col-span-4">
					<DashboardDatatable 
						:cols="paymentSubmissionsColArray" 
						:targetApi="'/data/payment-submissions/latest'"
						:createLink="route('payment-submissions.create')"
						:detailsLink="'payment-submissions'"
						:tableTitle="'Latest Payment Submissions'"
					/>
				</div>
				<div class="col-span-full lg:col-span-4">
					<DashboardDatatable 
						:cols="leadsColArray" 
						:targetApi="'/data/leads/latest'"
						:createLink="route('leads.create')"
						:detailsLink="'leads'"
						:tableTitle="'Latest Leads'"
					/>
				</div>
				<div class="col-span-full lg:col-span-4">
					<DashboardDatatable 
						:cols="usersColArray" 
						:targetApi="'/data/users-clients/latest'"
						:createLink="route('users-clients.create')"
						:detailsLink="'users-clients'"
						:tableTitle="'User Clients'"
					/>
				</div>
			</div>
		</div>
	</AuthenticatedLayout>
</template>
