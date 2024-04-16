<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import EditNotificationForm from './Partials/EditNotificationForm.vue'

const props = defineProps({
	data: {
        type: Object,
        default: () => ({}),
    },
});

const pageTitle = "Edit Notification";

</script>

<template>
	<AuthenticatedLayout 
		title="Edit Notification"
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
					:parentHref="route('notifications.index')" 
					:parentTitle="'Notfications'" 
					:title="pageTitle"
				/>
			</div>
		</template>

		<div class="rounded-xl">
			<EditNotificationForm 
				:data="props.data"
				class="w-full" 
			/>
		</div>
	</AuthenticatedLayout>
</template>
