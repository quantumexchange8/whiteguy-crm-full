<script setup>
import { ref } from "vue";
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Vue3Datatable from '@/Components/Vue3Datatable.vue'
import Button from '@/Components/Button.vue'
import LeadDetailsModal from './Partials/LeadDetailsModal.vue';

const pageTitle = "Leads";
const colArray = ref([
    { field: "id", title: "ID", headerClass: "text-gray-300 text-sm" },
    { field: "assignee", title: "ASSIGNEE", headerClass: "text-gray-300 text-sm"  },
    { field: "last_called", title: "LAST CALLED", headerClass: "text-gray-300 text-sm"  },
    { field: "give_up_at", title: "GIVE UP?", headerClass: "text-gray-300 text-sm"  },
    { field: "date_oppd_in", title: "DATE OPP'D IN", headerClass: "text-gray-300 text-sm"  },
    { field: "first_name", title: "FIRST NAME", headerClass: "text-gray-300 text-sm"  },
    { field: "last_name", title: "LAST NAME", headerClass: "text-gray-300 text-sm"  },
    { field: "phone_number", title: "PHONE NUMBER", headerClass: "text-gray-300 text-sm"  },
    { field: "email", title: "EMAIL", headerClass: "text-gray-300 text-sm"  },
    { field: "country", title: "COUNTRY", headerClass: "text-gray-300 text-sm"  },
    { field: "vc", title: "VC", headerClass: "text-gray-300 text-sm"  },
    { field: "data_type", title: "DATA TYPE", headerClass: "text-gray-300 text-sm"  },
    { field: "data_source", title: "DATA SOURCE", headerClass: "text-gray-300 text-sm"  },
    { field: "data_code", title: "DATA CODE", headerClass: "text-gray-300 text-sm"  },
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
			<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
				<h2 class="text-xl font-semibold leading-tight">
					{{ pageTitle }}
				</h2>
			</div>
			<Breadcrumbs 
				:parentTitle="'CRM'" 
				:title="pageTitle"
			/>
		</template>
		
		<div class="flex justify-end p-4">
			<div class="rounded-md shadow-lg border border-gray-500 flex justify-end">
				<Button 
					:type="'button'"
					:variant="'success'" 
					:size="'sm'" 
					class="justify-center px-6 py-2 gap-2 w-full h-full"
					:href="route('leads.create')"
				>
					Create Lead
				</Button>
			</div>
		</div>

		<div class="w-full pb-6 px-2">
			<div class="rounded-xl bg-gray-600 p-2">
				<Vue3Datatable 
					:cols="colArray" 
					:targetApi="targetApi"
					:detailsLink="detailsLink"
					:categoryFilters="categoryFilters"
					:modalComponent="LeadDetailsModal"
				></Vue3Datatable>
			</div>
		</div>
	</AuthenticatedLayout>
</template>
