<script setup>
import { ref, onMounted } from "vue";
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Vue3Datatable from '@/Components/Vue3Datatable.vue'
import LeadFrontDetailsModal from './Partials/LeadFrontDetailsModal.vue';
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

const pageTitle = "Lead Fronts";
const toast = useToast();
const colArray = ref([
	{ field: 'actions', title: 'ACTIONS', headerClass: "dark:text-gray-300 text-sm", filter: false },
    { field: "id", title: "ID", headerClass: "text-gray-300 text-sm" },
    { field: "name", title: "NAME", headerClass: "text-gray-300 text-sm"  },
    { field: "product", title: "PRODUCT", headerClass: "text-gray-300 text-sm"  },
    { field: "quantity", title: "QTY", headerClass: "text-gray-300 text-sm", type: 'number'  },
    { field: "price", title: "PRICE", headerClass: "text-gray-300 text-sm", type: 'number'  },
    { field: "total", title: "TOTAL", headerClass: "text-gray-300 text-sm", type: 'number'  },
    { field: "commission", title: "COMM", headerClass: "text-gray-300 text-sm", type: 'number'  },
    { field: "assignee", title: "ASSIGNEE", headerClass: "text-gray-300 text-sm"  },
    { field: "vc", title: "VC", headerClass: "text-gray-300 text-sm"  },
    { field: "sdm", title: "SDM", headerClass: "text-gray-300 text-sm"  },
    { field: "liquid", title: "LIQUID", headerClass: "text-gray-300 text-sm"  },
    { field: "linked_lead", title: "LINKED LEAD", headerClass: "text-gray-300 text-sm"  },
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
		title="Lead Fronts"
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
		
		<!-- <div class="flex justify-end p-4">
			<div class="rounded-md shadow-lg border border-gray-500 flex justify-end">
				<Button 
					:type="'button'"
					:variant="'success'" 
					:size="'sm'" 
					class="justify-center px-6 py-2 gap-2 w-full h-full"
					:href="route('lead-fronts.create')"
				>
					Create Lead Front
				</Button>
			</div>
		</div> -->
		
		<div class="w-full pb-6">
			<Vue3Datatable 
				:cols="colArray" 
				:targetApi="'/data/lead-fronts'"
				:createLink="route('lead-fronts.create')"
				:detailsLink="'lead-fronts'"
				:categoryFilters="'/data/lead-fronts/categories'"
				:modalComponent="LeadFrontDetailsModal"
				:getDuplicatesURL="'/data/leads/duplicates'"
			></Vue3Datatable>
		</div>
	</AuthenticatedLayout>
</template>
