<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import CreateUsersClientsForm from './Partials/CreateUsersClientsForm.vue'

const pageTitle = "Create Users / Clients";
</script>

<template>
	<AuthenticatedLayout 
		title="Create Users / Clients"
	>
		<template #header>
			<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
				<h2 class="text-xl font-semibold leading-tight">
					{{ pageTitle }}
				</h2>
			</div>
			<Breadcrumbs 
				:topParentTitle="'CRM'" 
				:parentHref="route('users-clients.index')" 
				:parentTitle="'Users / Clients'" 
				:title="pageTitle"
			/>
		</template>

		<div class="rounded-xl">
			<CreateUsersClientsForm class="w-full" />
		</div>
	</AuthenticatedLayout>
</template>
