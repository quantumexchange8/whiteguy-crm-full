<script setup>
import { ref, onMounted } from "vue";
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Vue3Datatable from '@/Components/Vue3Datatable.vue'
import CustomToastification from '@/Components/CustomToastification.vue';
import { useToast } from "vue-toastification";
import "vue-toastification/dist/index.css";

const props = defineProps({
    errors:Object,
	errorMsg: {
		type: Object,
        default: () => ({}),
	}
})

const pageTitle = "Users / Clients";
const toast = useToast();
const colArray = ref([
	{ field: 'actions', title: 'ACTIONS', headerClass: "dark:text-gray-300 text-sm", filter: false },
    { field: "username_site", title: "USERNAME (SITE)", headerClass: "text-gray-300 text-sm" },
    { field: "account_id", title: "ACCOUNT ID", headerClass: "text-gray-300 text-sm"  },
    { field: "full_legal_name", title: "FULL LEGAL NAME", headerClass: "text-gray-300 text-sm"  },
    { field: "lead_status", title: "LEAD STATUS", headerClass: "text-gray-300 text-sm", type: 'number'  },
    { field: "client_stage", title: "CLIENT STAGE", headerClass: "text-gray-300 text-sm", type: 'number'  },
    { field: "rank", title: "RANK", headerClass: "text-gray-300 text-sm", type: 'number'  },
    { field: "account_manager", title: "ACC. MANAGER", headerClass: "text-gray-300 text-sm", type: 'number'  },
    { field: "kyc_status", title: "KYC STATUS", headerClass: "text-gray-300 text-sm"  },
    { field: "active", title: "ACTIVE", headerClass: "text-gray-300 text-sm"  },
    { field: "sdm", title: "STAFF", headerClass: "text-gray-300 text-sm"  },
    { field: "crm", title: "CRM", headerClass: "text-gray-300 text-sm"  },
    { field: "leads", title: "LEADS", headerClass: "text-gray-300 text-sm"  },
    { field: "timezone", title: "TIMEZONE", headerClass: "text-gray-300 text-sm"  },
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
		title="Users / Clients"
	>
		<template #header>
			<div class="rounded-xl shadow-md bg-gray-200 dark:bg-dark-eval-1 pr-12 pl-14 py-6">
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
			<!-- <Vue3Datatable 
				:cols="colArray" 
				:targetApi="'/data/users-clients'"
				:createLink="route('users-clients.create')"
				:detailsLink="'users-clients'"
				:categoryFilters="'/data/lead-fronts/categories'"
				:modalComponent="LeadFrontDetailsModal"
				:getDuplicatesURL="'/data/leads/duplicates'"
				:exportRoute="'lead-fronts.export'"
			></Vue3Datatable> -->
		</div>
	</AuthenticatedLayout>
</template>
