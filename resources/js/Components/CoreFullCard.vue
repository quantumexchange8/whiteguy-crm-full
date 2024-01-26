<script setup>
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import { ThreeDotsVertical } from '@/Components/Icons/solid'
import { CheckCircleIcon } from '@/Components/Icons/outline'
import { TimesCircleIcon } from '@/Components/Icons/solid'
import { ref, computed } from 'vue'
import CustomSearchBar from '@/Components/CustomSearchBar.vue'

const props = defineProps({
	addActionHref: {
		type: String,
		default: '#'
	},
	addActionTitle: String
})

const columnName = ref(
	[
		"ID", "NAME", "PRODUCT", "QTY", "PRICE", "TOTAL", "COMM", "ASSIGNEE", "VC", "SDM", "LIQUID", "LINKED LEAD"
	]
);
const rowData = ref(
	[
		{
			id: 101,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'No',
			liquid: 'Yes',
			linkedLead: 'Sufyan',
		},
		{
			id: 102,
			name: 'Sufyan',
			product: 'BTG:AFRICA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'No',
			linkedLead: 'Sufyan',
		},
		{
			id: 103,
			name: 'Kelly',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'Yes',
			linkedLead: 'Sufyan',
		},
		{
			id: 104,
			name: 'Sufyan',
			product: 'BTG:DENMARK',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'No',
			liquid: 'Yes',
			linkedLead: 'Sufyan',
		},
		{
			id: 105,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'Yes',
			linkedLead: 'Sufyan',
		},
		{
			id: 101,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'Yes',
			linkedLead: 'Sufyan',
		},
		{
			id: 101,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'Yes',
			linkedLead: 'Sufyan',
		},
		{
			id: 101,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'No',
			linkedLead: 'Sufyan',
		},
		{
			id: 101,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'Yes',
			linkedLead: 'Sufyan',
		},
		{
			id: 101,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'Yes',
			linkedLead: 'Sufyan',
		},
		{
			id: 101,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'Yes',
			linkedLead: 'Sufyan',
		},
		{
			id: 101,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'Yes',
			linkedLead: 'Sufyan',
		},
		{
			id: 101,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'No',
			liquid: 'Yes',
			linkedLead: 'Sufyan',
		},
		{
			id: 101,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'Yes',
			linkedLead: 'Sufyan',
		},
		{
			id: 101,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'Yes',
			linkedLead: 'Sufyan',
		},
		{
			id: 101,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'Yes',
			linkedLead: 'Sufyan',
		},
		{
			id: 101,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'Yes',
			linkedLead: 'Sufyan',
		},
		{
			id: 101,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'No',
			linkedLead: 'Sufyan',
		},
		{
			id: 101,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'Yes',
			linkedLead: 'Sufyan',
		},
		{
			id: 101,
			name: 'Sufyan',
			product: 'BTG:ASIA',
			qty: 2500.00,
			price: 3.00,
			total: 9000.00,
			comm: 2400.00,
			assignee: '100(ADMIN)',
			vc: 'ABC',
			sdm: 'Yes',
			liquid: 'No',
			linkedLead: 'Sufyan',
		}
	]
);

const searchQuery = ref('');
const sortColumn = ref(null);
const sortDirection = ref('asc');

const filteredData = computed(() => {
  return rowData.value.filter((data) => {
    // Adjust the conditions based on your search requirements
    return (
      data.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      data.product.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      data.vc.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      data.linkedLead.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  }).sort((a, b) => {
    if (!sortColumn) return 0;

    const aValue = String(a[sortColumn.value]).toLowerCase();
    const bValue = String(b[sortColumn.value]).toLowerCase();

    if (aValue === bValue) return 0;

    return sortDirection.value === 'asc'
      ? aValue.localeCompare(bValue)
      : bValue.localeCompare(aValue);
  });
});

const handleSort = (column) => {
  if (sortColumn.value === column) {
    // If clicking the same column, reverse the sort direction
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    // If clicking a new column, set the sort column to that column
    sortColumn.value = column;
    sortDirection.value = 'asc';
  }
};

</script>

<template>
	<div class="core-card-wrapper">
		<div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
			<div class="core-card">
				<div class="flex flex-row justify-end mb-6">
					<h4 class="card-title mb-3"><slot name="title" /></h4>
					
					<div class="mr-12">
						<input
							v-model="searchQuery"
							type="text"
							class="pl-4 border border-gray-500 rounded-md dark:bg-dark-eval-1"
							placeholder="Search"
						/>
					</div>

					<Dropdown align="right" :width="50" :contentClasses="'dark:bg-dark-eval-3'">
						<template #trigger>
							<ThreeDotsVertical class="flex-shrink-0 w-6 h-6 cursor-pointer" aria-hidden="true" />
						</template>

						<template #content>
							<div class="py-1">
								<p class="text-md p-2 text-gray-300 text-center">Action</p>
	
								<hr class="border-b rounded-md border-gray-600 mb-2 w-10/12 mx-auto">
	
								<DropdownLink :href="addActionHref" class="dropdown-link-item text-center">
									{{ addActionTitle }}
								</DropdownLink>
							</div>
						</template>
					</Dropdown>
				</div>

				<div class="flex flex-col justify-center">
					<section class="container mx-auto font-mono">
						<div class="w-full mb-8 overflow-hidden rounded-lg shadow-2xl">
							<div class="w-full overflow-x-auto">
								<table class="w-full">
									<thead>
										<tr class="text-md font-semibold tracking-wide text-left text-gray-300 bg-gray-700 uppercase">
											<th @click="handleSort(data)" class="px-4 py-3" v-for="data in columnName">{{ data }}</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<CustomSearchBar 
												:productData="rowData.product">
											</CustomSearchBar>
										</tr>
										<tr class="text-gray-300" v-for="data in filteredData">
											<td class="px-4 py-3 text-sm font-semibold"><a :href="route('leads.index')" class="text-blue-300">{{ data.id }}</a></td>
											<td class="px-4 py-3 text-ms font-semibold border-l border-r border-gray-500">{{ data.name }}</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">{{ data.product }}</td>
											<td class="px-4 py-3 text-ms font-semibold border-l border-r border-gray-500">{{ data.qty }}</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">{{ data.price }}</td>
											<td class="px-4 py-3 text-ms font-semibold border-l border-r border-gray-500">{{ data.total }}</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">{{ data.comm }}</td>
											<td class="px-4 py-3 text-ms font-semibold border-l border-r border-gray-500">{{ data.assignee }}</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">{{ data.vc }}</td>
											<td class="px-4 py-3 text-ms font-semibold border-l border-r border-gray-500">
												<CheckCircleIcon 
													class="flex-shrink-0 w-6 h-6" 
													aria-hidden="true" 
													v-if="data.sdm === 'Yes'" 
												/>
												<TimesCircleIcon 
													class="flex-shrink-0 w-6 h-6" 
													aria-hidden="true" 
													v-else 
												/>
											</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">
												<CheckCircleIcon 
													class="flex-shrink-0 w-6 h-6" 
													aria-hidden="true"
													v-if="data.liquid === 'Yes'" 
												/>
												<TimesCircleIcon 
													class="flex-shrink-0 w-6 h-6" 
													aria-hidden="true" 
													v-else 
												/>
											</td>
											<td class="px-4 py-3 text-sm">{{ data.linkedLead }}</td>
										</tr>
										<template v-if="filteredData.length === 0">
											<tr>
												<td :colspan="columnName.length" class="px-4 py-3 text-gray-500 text-center">
													No results found.
												</td>
											</tr>
										</template>
									</tbody>
								</table>
							</div>
						</div>
					</section>
				</div>
			</div>
		</div>
	</div>
</template>
