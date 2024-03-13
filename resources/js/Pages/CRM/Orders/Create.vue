<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import CreateOrderForm from './Partials/CreateOrderForm.vue'

const pageTitle = "Create Order";
</script>

<template>
	<AuthenticatedLayout 
		title="Create Order"
	>
		<template #header>
			<div class="rounded-xl shadow-md bg-gray-200 dark:bg-dark-eval-1 p-6">
				<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
					<h2 class="text-xl font-semibold leading-tight">
						{{ pageTitle }}
					</h2>
				</div>
				<Breadcrumbs 
					:topParentTitle="'CRM'" 
					:parentHref="route('orders.index')" 
					:parentTitle="'Orders'" 
					:title="pageTitle"
				/>
			</div>
		</template>

		<div class="rounded-xl">
			<CreateOrderForm class="w-full" />
		</div>
	</AuthenticatedLayout>
</template>
