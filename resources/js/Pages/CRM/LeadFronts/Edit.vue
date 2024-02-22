<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import EditLeadFrontForm from './Partials/EditLeadFrontForm.vue'

const props = defineProps({
	data: {
        type: Object,
        default: () => ({}),
    },
});

const pageTitle = "Edit Lead Front";
</script>

<template>
	<AuthenticatedLayout 
		title="Edit Lead Front"
	>
		<template #header>
			<div class="rounded-xl shadow-md bg-gray-200 dark:bg-dark-eval-1 pr-12 pl-14 py-6">
				<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
					<h2 class="text-xl font-semibold leading-tight">
						{{ pageTitle }}
					</h2>
				</div>
				<Breadcrumbs 
					:topParentTitle="'CRM'" 
					:parentHref="route('lead-fronts.index')" 
					:parentTitle="'Lead Front'" 
					:title="pageTitle"
				/>
			</div>
		</template>

		<div class="rounded-xl">
			<EditLeadFrontForm 
				:data="props.data" 
				class="w-full" />
		</div>
	</AuthenticatedLayout>
</template>
