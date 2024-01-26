<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import { ThreeDotsVertical } from '../../Components/Icons/solid'
import CoreCard from '@/Components/CoreCard.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { ref } from 'vue'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

const categories = ref({
  Leads: [
    {
      id: 1,
      title: 'Leads',
      date: '5h ago',
      commentCount: 5,
      shareCount: 2,
    },
  ],
  Lead_Fronts: [
    {
      id: 1,
      title: 'Lead Fronts',
      date: 'Jan 7',
      commentCount: 29,
      shareCount: 16,
    },
  ],
  Users_Clients: [
    {
      id: 1,
      title: 'Users / Clients',
      date: '2d ago',
      commentCount: 9,
      shareCount: 5,
    },
  ],
  Orders: [
    {
      id: 1,
      title: 'Orders',
      date: '5h ago',
      commentCount: 5,
      shareCount: 2,
    },
  ],
  Sales_Orders: [
    {
      id: 1,
      title: 'Sales Orders',
      date: 'Jan 7',
      commentCount: 29,
      shareCount: 16,
    },
  ],
  Payment_Submissions: [
    {
      id: 1,
      title: 'Payment Submissions',
      date: '2d ago',
      commentCount: 9,
      shareCount: 5,
    },
  ],
})
</script>

<template>
	<AuthenticatedLayout title="CRM">
		<template #header>
			<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
				<h2 class="text-xl font-semibold leading-tight">
					Dashboard
				</h2>
			</div>
			<Breadcrumbs #title>CRM</Breadcrumbs>
		</template>
		
		<div class="w-full px-2 py-8 sm:px-0">
			<TabGroup>
				<TabList class="flex space-x-1 rounded-xl bg-blue-500/20 p-1">
					<Tab
						v-for="category in Object.keys(categories)"
						as="template"
						:key="category"
						v-slot="{ selected }"
					>
						<button
							:class="[
								'w-full rounded-lg py-2.5 text-sm font-medium leading-5',
								'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
								selected
									? 'bg-gray-600 text-white shadow'
									: 'text-blue-100 hover:bg-white/[0.12] hover:text-white',
							]"
						>
							{{ category }}
						</button>
					</Tab>
				</TabList>

				<TabPanels class="mt-2">
					<TabPanel
						v-for="(posts, idx) in Object.values(categories)"
						:key="idx"
						:class="[
							'rounded-xl bg-gray-600 p-2',
							'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
						]"
					>
						<ul>
							<li
								v-for="post in posts"
								:key="post.id"
								class="relative rounded-md"
							>
								<!-- <h3 class="text-sm font-medium leading-5">
									{{ post.title }}
								</h3>

								<ul
									class="mt-1 flex space-x-1 text-xs font-normal leading-4 text-gray-300"
								>
									<li>{{ post.date }}</li>
									<li>&middot;</li>
									<li>{{ post.commentCount }} comments</li>
									<li>&middot;</li>
									<li>{{ post.shareCount }} shares</li>
								</ul> -->
								<CoreCard #title>{{ post.title }}</CoreCard>
							</li>
						</ul>
					</TabPanel>
				</TabPanels>
			</TabGroup>
		</div>

		<hr class="border-b rounded-md border-gray-600 mb-8">

		<div class="core-card-section columns-2 mb-8 gap-y-8">
			<CoreCard #title>Leads</CoreCard>
			<CoreCard #title>Lead Fronts</CoreCard>
			<CoreCard #title>Users / Clients</CoreCard>
			<CoreCard #title>Orders</CoreCard>
			<CoreCard #title>Sales Orders</CoreCard>
			<CoreCard #title>Payment Submissions</CoreCard>
		</div>
		
	</AuthenticatedLayout>
</template>
