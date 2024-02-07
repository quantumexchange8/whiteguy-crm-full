<script setup>
import { ref } from "vue";
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Vue3Datatable from '@/Components/Vue3Datatable.vue'
import Button from '@/Components/Button.vue'
import LeadDetailsModal from './Partials/LeadDetailsModal.vue';

const props = defineProps({
    errors:Object,
})

const pageTitle = "Leads";
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
const targetApi = '/data/leads';
const categoryFilters = '/data/leads/categories';
const detailsLink = 'leads/';

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
				<pre>{{ props.errors }}</pre>
			</div>
		</template>

		<div class="w-full pb-6">
			<Vue3Datatable 
				:cols="colArray" 
				:targetApi="targetApi"
				:detailsLink="detailsLink"
				:categoryFilters="categoryFilters"
				:modalComponent="LeadDetailsModal"
			></Vue3Datatable>
		</div>
	</AuthenticatedLayout>
</template>
