<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import CoreFullCard from '@/Components/CoreFullCard.vue'

const pageTitle = "Sale Orders";

</script>

<template>
	<AuthenticatedLayout 
		title="Sale Orders"
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
				<CoreFullCard #title></CoreFullCard>
			</div>
		</div>
	</AuthenticatedLayout>
</template>
