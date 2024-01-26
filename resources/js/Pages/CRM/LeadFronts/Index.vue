<script setup>
import { ref } from "vue";
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Vue3Datatable from '@/Components/Vue3Datatable.vue'
import Button from '@/Components/Button.vue'

const pageTitle = "Lead Fronts";
const colArray = ref([
    { field: "id", title: "ID", headerClass: "text-gray-300 text-sm" },
    { field: "name", title: "NAME", headerClass: "text-gray-300 text-sm"  },
    { field: "product", title: "PRODUCT", headerClass: "text-gray-300 text-sm"  },
    { field: "quantity", title: "QTY", headerClass: "text-gray-300 text-sm"  },
    { field: "price", title: "PRICE", headerClass: "text-gray-300 text-sm"  },
    { field: "total", title: "TOTAL", headerClass: "text-gray-300 text-sm"  },
    { field: "commission", title: "COMM", headerClass: "text-gray-300 text-sm"  },
    { field: "assignee", title: "ASSIGNEE", headerClass: "text-gray-300 text-sm"  },
    { field: "vc", title: "VC", headerClass: "text-gray-300 text-sm"  },
    { field: "sdm", title: "SDM", headerClass: "text-gray-300 text-sm"  },
    { field: "liquid", title: "LIQUID", headerClass: "text-gray-300 text-sm"  },
    { field: "linked_lead", title: "LINKED LEAD", headerClass: "text-gray-300 text-sm"  },
]);
const targetApi = '/data/lead-fronts';
const detailsLink = 'lead-fronts/';

</script>

<template>
	<AuthenticatedLayout 
		title="Lead Fronts"
	>
		<template #header>
			<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
				<h2 class="text-xl font-semibold leading-tight">
					{{ pageTitle }}
				</h2>
			</div>
			<Breadcrumbs 
				:parent-title="'CRM'" 
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
					:href="route('lead-fronts.create')"
				>
					Create Lead Front
				</Button>
			</div>
		</div>
		
		<div class="w-full pb-6 px-2">
			<div class="rounded-xl bg-gray-600 p-2">
				<Vue3Datatable 
					:cols="colArray" 
					:targetApi="targetApi"
					:detailsLink="detailsLink"
				></Vue3Datatable>
			</div>
		</div>
	</AuthenticatedLayout>
</template>
