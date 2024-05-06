<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import EditSaleOrderForm from './Partials/EditSaleOrderForm.vue'

const props = defineProps({
	data: {
        type: Object,
        default: () => ({}),
    },
	saleOrderItemsData: {
        type: Object,
        default: () => ({}),
    },
});

const pageTitle = "Edit Sale Order";

</script>

<template>
	<AuthenticatedLayout 
		title="Edit Sale Order"
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
					:parentHref="route('sale-orders.index')" 
					:parentTitle="'Sale Orders'" 
					:title="pageTitle"
				/>
			</div>
		</template>

		<div class="rounded-xl">
			<EditSaleOrderForm 
				:data="props.data" 
				:saleOrderItemsData="props.saleOrderItemsData"
				class="w-full" 
			/>
		</div>
	</AuthenticatedLayout>
</template>
