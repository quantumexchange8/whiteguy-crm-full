<script setup>
import { ref, onMounted } from "vue";
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Vue3Datatable from '@/Components/Vue3Datatable.vue'
import AccMngProfDetailsModal from './Partials/AccMngProfDetailsModal.vue';
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

const pageTitle = "Account Manager Profiles";
const toast = useToast();
const colArray = ref([
	{ field: 'actions', title: 'ACTIONS', headerClass: "dark:text-gray-300 text-sm w-max", filter: false, sort: false },
    { field: "id", title: "ID", headerClass: "text-gray-300 text-sm w-max" },
    { field: "file", title: "FILE", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "user_id", title: "USER", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "created_at", title: "CREATED AT", headerClass: "text-gray-300 text-sm w-max", type: 'date' },
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
		title="Account Manager Profiles"
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
				:targetApi="'/data/account-manager-profiles'"
				:createLink="route('account-manager-profiles.create')"
				:detailsLink="'account-manager-profiles'"
				:exportRoute="'account-manager-profiles.export'"
				:modalComponent="AccMngProfDetailsModal"
				:exportAllRoute="route('account-manager-profiles.getAllAccountManagerProfileForExport')"
			></Vue3Datatable>
		</div>
	</AuthenticatedLayout>
</template>
