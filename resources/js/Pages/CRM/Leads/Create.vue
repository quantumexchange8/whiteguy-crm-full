<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import CreateLeadForm from './Partials/CreateLeadForm.vue'

const pageTitle = "Create Lead";
</script>

<template>
	<AuthenticatedLayout 
		title="Create Lead"
	>
		<template #header>
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
		</template>

		<div class="rounded-xl">
			<CreateLeadForm class="w-full" />
		</div>
	</AuthenticatedLayout>
</template>
