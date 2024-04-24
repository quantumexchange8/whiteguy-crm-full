<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import CreatePaymentSubmissionForm from './Partials/CreatePaymentSubmissionForm.vue'

const pageTitle = "Create Payment Submission";
</script>

<template>
	<AuthenticatedLayout 
		title="Create Payment Submission"
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
					:parentHref="route('payment-submissions.index')" 
					:parentTitle="'Payment Submission'" 
					:title="pageTitle"
				/>
			</div>
		</template>

		<div class="rounded-xl">
			<CreatePaymentSubmissionForm class="w-full" />
		</div>
	</AuthenticatedLayout>
</template>
