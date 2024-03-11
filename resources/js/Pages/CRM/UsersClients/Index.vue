<script setup>
import { ref, onMounted } from "vue";
import { useToast } from "vue-toastification";
import CustomToastification from '@/Components/CustomToastification.vue';
import UserDetailsModal from './Partials/UserDetailsModal.vue'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Vue3Datatable from '@/Components/Vue3Datatable.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
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
	{ field: 'actions', title: 'ACTIONS', headerClass: "dark:text-gray-300 text-sm w-max", filter: false },
    { field: "site", title: "USERNAME (SITE)", headerClass: "text-gray-300 text-sm w-max" },
    { field: "account_id", title: "ACCOUNT ID", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "full_legal_name", title: "FULL LEGAL NAME", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "lead_status", title: "LEAD STATUS", headerClass: "text-gray-300 text-sm w-max", type: 'number'  },
    { field: "client_stage", title: "CLIENT STAGE", headerClass: "text-gray-300 text-sm w-max", type: 'number'  },
    { field: "rank", title: "RANK", headerClass: "text-gray-300 text-sm w-max", type: 'number'  },
    { field: "account_manager", title: "ACC. MANAGER", headerClass: "text-gray-300 text-sm w-max", type: 'number'  },
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
			<Vue3Datatable 
				:cols="colArray" 
				:targetApi="'/data/users-clients'"
				:createLink="route('users-clients.create')"
				:detailsLink="'users-clients'"
				:categoryFilters="'/data/users-clients/categories'"
				:modalComponent="UserDetailsModal"
				:exportRoute="'users-clients.export'"
			></Vue3Datatable>
		</div>
	</AuthenticatedLayout>
</template>
