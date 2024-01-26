<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import CreateLeadFrontForm from './Partials/CreateLeadFrontForm.vue'

const pageTitle = "Create Lead Front";
</script>

<template>
	<AuthenticatedLayout 
		title="Create Lead Front"
	>
		<template #header>
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
		</template>

		<div class="rounded-xl">
			<CreateLeadFrontForm class="w-full" />
		</div>
	</AuthenticatedLayout>
</template>
