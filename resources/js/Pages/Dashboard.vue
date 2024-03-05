<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import CoreCard from '@/Components/CoreCard.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { usePermission } from '@/Composables'

const pageTitle = "Dashboard";
const { is } = usePermission();

</script>

<template>
	<AuthenticatedLayout title="Dashboard">
		<template #header>
				<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
						<h2 class="text-xl font-semibold leading-tight">
								{{ pageTitle }}
						</h2>
				</div>
				<h2 v-if="is('staff') || is('crm_user')">{{ $page.props.auth.user.roles }}</h2>
				<Breadcrumbs 
					:title="pageTitle"
				/>
		</template>

		<div class="w-full pb-6 px-2">
			<div class="rounded-xl bg-gray-600 p-2">
				<CoreCard 
					#title 
					:viewMoreHref="route('lead-fronts.index')"
				>
						Lead Fronts
				</CoreCard>
			</div>
		</div>

		<hr class="border-b rounded-md border-gray-600 mb-6">

		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 justify-items-center mb-8 gap-8 px-2 ">
			<CoreCard 
				#title 
				class="rounded-xl bg-gray-600 p-2" 
				:viewMoreHref="route('crm.payment-submissions')"
			>
				Payment Submissions
			</CoreCard>
			<CoreCard 
				#title 
				class="rounded-xl bg-gray-600 p-2" 
				:addActionTitle="'Add'"
				:addActionHref="route('leads.create')"
				:viewMoreHref="route('leads.index')"
			>
				Leads
			</CoreCard>
			<CoreCard 
				#title 
				class="rounded-xl bg-gray-600 p-2" 
				:viewMoreHref="route('users-clients.index')"
			>
				Users / Clients
			</CoreCard>
		</div>
	</AuthenticatedLayout>
</template>
