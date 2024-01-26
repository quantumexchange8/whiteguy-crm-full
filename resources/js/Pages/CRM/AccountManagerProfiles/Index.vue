<script setup>
import { ref } from "vue";
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Vue3Datatable from '@/Components/Vue3Datatable.vue'

const pageTitle = "Account Manager Profiles";
const colArray = ref([
    { field: "id", title: "ID", headerClass: "text-gray-300 text-sm" },
    { field: "file", title: "FILE", headerClass: "text-gray-300 text-sm"  },
    { field: "user", title: "USER", headerClass: "text-gray-300 text-sm"  },
    { field: "created_at", title: "CREATED AT", headerClass: "text-gray-300 text-sm"  },
]);
const targetApi = '/data/account-manager-profiles';
const detailsLink = 'leads/add';

</script>

<template>
	<AuthenticatedLayout 
		title="Account Manager Profiles"
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
