<script setup>
import { ref, onMounted } from "vue";
import "vue-toastification/dist/index.css";
import { useToast } from "vue-toastification";
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import NotificationDetailsModal from './Partials/NotificationDetailsModal.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Vue3Datatable from '@/Components/Vue3Datatable.vue'
import CustomToastification from '@/Components/CustomToastification.vue';

const props = defineProps({
    errors:Object,
	errorMsg: {
		type: Object,
        default: () => ({}),
	}
})

const pageTitle = "Notifications";
const toast = useToast();
const colArray = ref([
	{ field: 'actions', title: 'ACTIONS', headerClass: "dark:text-gray-300 text-sm w-max", filter: false },
	{ field: 'title', title: 'TITLE', headerClass: "dark:text-gray-300 text-sm w-max" },
    { field: "notification_type", title: "NOTIFICATION TYPE", headerClass: "text-gray-300 text-sm w-max" },
    { field: "attachment", title: "ATTACHMENT", headerClass: "text-gray-300 text-sm w-max" },
    { field: "is_read", title: "NOTIFICATION VIEWED?", headerClass: "text-gray-300 text-sm w-max" },
    { field: "user_id", title: "USER", headerClass: "text-gray-300 text-sm w-max" },
    { field: "notification_created_at", title: "CREATED AT", headerClass: "text-gray-300 text-sm w-max", type: 'date' },
]);

// Custom Toastification
const toastContent = {
    // Toast Component
    component: CustomToastification,

    // Component props
    props: {
        errors: props.errors ? props.errors : {},
        errorMsg: props.errorMsg ? props.errorMsg : {},
        type: props.errorMsg ? props.errorMsg.type : 'default',
		rowErrorMsg: props.errorMsg.rowErrorMsg ? props.errorMsg.rowErrorMsg : false,
    },
};

const showToast = () => {
	toast(toastContent);
}

onMounted(() => {
	if (Object.keys(props.errorMsg).length !== 0) {
		showToast();
	}
});

</script>

<template>
	<AuthenticatedLayout 
		title="Notifications"
	>
		<template #header>
			<div class="rounded-xl shadow-md bg-gray-200 dark:bg-dark-eval-1 p-6">
				<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
					<h2 class="text-xl font-semibold leading-tight">
						{{ pageTitle }}
					</h2>
				</div>
				<Breadcrumbs 
					:parentTitle="'CRM'" 
					:title="pageTitle"
				/>
			</div>
		</template>

		<div class="w-full pb-6">
			<Vue3Datatable 
				:cols="colArray" 
				:targetApi="'/data/notifications'"
				:createLink="route('notifications.create')"
				:detailsLink="'notifications'"
				:categoryFilters="'/data/notifications/categories'"
				:modalComponent="NotificationDetailsModal"
				:customDelete="true"
				:exportRoute="'notifications.export'"
			></Vue3Datatable>
		</div>
	</AuthenticatedLayout>
</template>
