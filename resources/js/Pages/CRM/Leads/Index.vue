<script setup>
import { ref, onMounted } from "vue";
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Vue3Datatable from '@/Components/Vue3Datatable.vue'
import Button from '@/Components/Button.vue'
import LeadDetailsModal from './Partials/LeadDetailsModal.vue';
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

const pageTitle = "Leads";
const toast = useToast();
const colArray = ref([
	{ field: 'actions', title: 'ACTIONS', headerClass: "dark:text-gray-300 text-sm", filter: false },
    { field: "id", title: "ID", headerClass: "dark:text-gray-300 text-sm" },
    { field: "assignee", title: "ASSIGNEE", headerClass: "dark:text-gray-300 text-sm"  },
    { field: "last_called", title: "LAST CALLED", headerClass: "dark:text-gray-300 text-sm", type: 'date'  },
    { field: "give_up_at", title: "GIVE UP?", headerClass: "dark:text-gray-300 text-sm", type: 'date'  },
    { field: "date_oppd_in", title: "DATE OPP'D IN", headerClass: "dark:text-gray-300 text-sm", type: 'date'  },
    { field: "first_name", title: "FIRST NAME", headerClass: "dark:text-gray-300 text-sm" },
    { field: "last_name", title: "LAST NAME", headerClass: "dark:text-gray-300 text-sm"  },
    { field: "phone_number", title: "PHONE NUMBER", headerClass: "dark:text-gray-300 text-sm", type: 'number'  },
    { field: "email", title: "EMAIL", headerClass: "dark:text-gray-300 text-sm"  },
    { field: "country", title: "COUNTRY", headerClass: "dark:text-gray-300 text-sm"  },
    { field: "vc", title: "VC", headerClass: "dark:text-gray-300 text-sm"  },
    { field: "data_type", title: "DATA TYPE", headerClass: "dark:text-gray-300 text-sm"  },
    { field: "data_source", title: "DATA SOURCE", headerClass: "dark:text-gray-300 text-sm"  },
    { field: "data_code", title: "DATA CODE", headerClass: "dark:text-gray-300 text-sm"  },
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
	// console.log(props.errorMsg);
	if (Object.keys(props.errorMsg).length !== 0) {
		showToast();
	}
});

</script>

<template>
	<AuthenticatedLayout 
		title="Leads"
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
				:targetApi="'/data/leads'"
				:detailsLink="'leads'"
				:categoryFilters="'/data/leads/categories'"
				:modalComponent="LeadDetailsModal"
				:getDuplicatesURL="'/data/leads/duplicates'"
			></Vue3Datatable>
		</div>
	</AuthenticatedLayout>
</template>
