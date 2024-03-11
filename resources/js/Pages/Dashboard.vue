<script setup>
import { ref, onMounted } from "vue";
import "vue-toastification/dist/index.css";
import { usePermission } from '@/Composables'
import { useToast } from "vue-toastification";
import CoreCard from '@/Components/CoreCard.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import DashboardDatatable from '@/Components/DashboardDatatable.vue'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import LeadFrontDetailsModal from '@/Pages/CRM/LeadFronts/Partials/LeadFrontDetailsModal.vue';
import CustomToastification from '@/Components/CustomToastification.vue';

const props = defineProps({
    errors:Object,
	errorMsg: {
		type: Object,
        default: () => ({}),
	}
})

const pageTitle = "Dashboard";
const { is } = usePermission();
const toast = useToast();

const colArray = ref([
    { field: "id", title: "ID", headerClass: "text-gray-300 text-sm w-max" },
    { field: "name", title: "NAME", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "product", title: "PRODUCT", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "quantity", title: "QTY", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "price", title: "PRICE", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "total", title: "TOTAL", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "commission", title: "COMM", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "assignee", title: "ASSIGNEE", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "vc", title: "VC", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "sdm", title: "SDM", headerClass: "text-gray-300 text-s mw-max"  },
    { field: "liquid", title: "LIQUID", headerClass: "text-gray-300 text-sm w-max"  },
    { field: "linked_lead", title: "LINKED LEAD", headerClass: "text-gray-300 text-sm w-max"  },
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
	<AuthenticatedLayout title="Dashboard">
		<template #header>
				<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
						<h2 class="text-xl font-semibold leading-tight">
								{{ pageTitle }}
						</h2>
				</div>
				<!-- <h2 v-if="is('staff') || is('crm_user')">{{ $page.props.auth.user.roles }}</h2> -->
				<Breadcrumbs 
					:title="pageTitle"
				/>
		</template>

		<div class="flex flex-col gap-6">
			<div class="w-full">
				<!-- <CoreCard 
					#title 
					:viewMoreHref="route('lead-fronts.index')"
				>
						Lead Fronts
				</CoreCard> -->
				
				<DashboardDatatable 
					:cols="colArray" 
					:targetApi="'/data/lead-fronts'"
					:createLink="route('lead-fronts.create')"
					:detailsLink="'lead-fronts'"
					:tableTitle="'Latest Lead Fronts'"
				/>
			</div>

			<div class="grid grid-cols-1 lg:grid-cols-12 mb-8 gap-6">
				<!-- <CoreCard 
					#title 
					class="col-span-full lg:col-span-4" 
					:viewMoreHref="route('crm.payment-submissions')"
				>
					Payment Submissions
				</CoreCard> -->
				<div class="col-span-full lg:col-span-4">
					<DashboardDatatable 
						:cols="colArray" 
						:targetApi="'/data/lead-fronts'"
						:createLink="route('lead-fronts.create')"
						:detailsLink="'lead-fronts'"
						:tableTitle="'Latest Payment Submissions'"
					/>
				</div>
				<div class="col-span-full lg:col-span-4">
					<DashboardDatatable 
						:cols="colArray" 
						:targetApi="'/data/lead-fronts'"
						:createLink="route('lead-fronts.create')"
						:detailsLink="'lead-fronts'"
						:tableTitle="'Latest Leads'"
					/>
				</div>
				<div class="col-span-full lg:col-span-4">
					<DashboardDatatable 
						:cols="colArray" 
						:targetApi="'/data/lead-fronts'"
						:createLink="route('lead-fronts.create')"
						:detailsLink="'lead-fronts'"
						:tableTitle="'User Clients'"
					/>
				</div>
			</div>
		</div>
	</AuthenticatedLayout>
</template>
