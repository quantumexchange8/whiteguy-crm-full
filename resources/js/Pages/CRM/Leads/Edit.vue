<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import EditLeadForm from './Partials/EditLeadForm.vue'

const props = defineProps({
	data: {
        type: Object,
        default: () => ({}),
    },
	leadFrontData: {
        type: Object,
        default: () => ({}),
    },
	leadNotesData: {
        type: Object,
        default: () => ({}),
    },
});

const pageTitle = "Edit Lead";

</script>

<template>
	<AuthenticatedLayout 
		title="Edit Lead"
	>
		<template #header>
			<div class="rounded-xl shadow-md bg-gray-200 dark:bg-dark-eval-1 p-9">
				<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
					<h2 class="text-xl font-semibold leading-tight">
						{{ pageTitle }}
					</h2>
				</div>
				<Breadcrumbs 
					:topParentTitle="'CRM'" 
					:parentHref="route('leads.index')" 
					:parentTitle="'Leads'" 
					:title="pageTitle"
				/>
			</div>
		</template>

		<div class="rounded-xl">
			<EditLeadForm 
				:data="props.data" 
				:leadFrontData="leadFrontData" 
				:leadNotesData="leadNotesData"
				class="w-full" 
			/>
		</div>
	</AuthenticatedLayout>
</template>
