<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import EditPaymentMethodForm from './Partials/EditPaymentMethodForm.vue'

const props = defineProps({
	data: {
        type: Object,
        default: () => ({}),
    },
	sites: {
        type: Object,
        default: () => ({}),
    },
});

const pageTitle = "Edit Payment Method";

</script>

<template>
	<AuthenticatedLayout 
		title="Edit Payment Method"
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
					:parentHref="route('payment-methods.index')" 
					:parentTitle="'Payment Method'" 
					:title="pageTitle"
				/>
			</div>
		</template>

		<div class="rounded-xl">
			<EditPaymentMethodForm 
				:data="props.data" 
				:sites="props.sites" 
				class="w-full" 
			/>
		</div>
	</AuthenticatedLayout>
</template>
