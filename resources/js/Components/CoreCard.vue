<script setup>
import Dropdown from './Dropdown.vue'
import DropdownLink from './DropdownLink.vue'
import { ThreeDotsVertical } from './Icons/solid'
import { CheckCircleIcon } from './Icons/outline'
import { TimesCircleIcon } from './Icons/solid'
import { isDark } from '@/Composables'
import Button from '@/Components/Button.vue'

const props = defineProps({
    viewMoreHref: String,
	addActionHref: {
		type: String,
		default: '#'
	},
	addActionTitle: String
})

</script>

<template>
	<div class="core-card-wrapper">
		<div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
			<div class="core-card">
				<div class="core-card-header">
					<h4 class="card-title mb-3"><slot name="title" /></h4>

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
					<div class="text-center p-4">
						<p class="mb-2 text-gray-300">The latest 5 records</p>
					</div>

					<!-- <ul role="list" class="divide-y divide-gray-100">
						<li class="flex justify-between gap-x-6 py-5">
							<div class="flex min-w-0 gap-x-4 w-2/12">
								<div class="min-w-0 flex-auto">
									<p class="text-sm font-semibold leading-6 text-gray-300">ID</p>
								</div>
							</div>
							<div class="flex min-w-0 gap-x-4 w-3/12">
								<div class="min-w-0 flex-auto">
									<p class="text-sm font-semibold leading-6 text-gray-300">Assignee</p>
								</div>
							</div>
							<div class="flex min-w-0 gap-x-4 w-2/12">
								<div class="min-w-0 flex-auto">
									<p class="text-sm font-semibold leading-6 text-gray-300">Name</p>
								</div>
							</div>
							<div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end w-3/12">
								<p class="text-sm leading-6 text-gray-300">Phone Number</p>
							</div>
						</li>
						<li class="flex justify-between gap-x-6 py-5">
							<div class="flex min-w-0 gap-x-4 w-2/12">
								<div class="min-w-0 flex-auto">
									<p class="text-xs font-semibold leading-6 text-gray-400">147244</p>
								</div>
							</div>
							<div class="flex min-w-0 gap-x-4 w-3/12">
								<div class="min-w-0 flex-auto">
									<p class="text-xs font-semibold leading-6 text-gray-400">123(ADMIN)</p>
								</div>
							</div>
							<div class="flex min-w-0 gap-x-4 w-2/12">
								<div class="min-w-0 flex-auto">
									<p class="text-xs font-semibold leading-6 text-gray-400">Martin</p>
								</div>
							</div>
							<div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end w-3/12">
								<p class="text-xs leading-6 text-gray-400">1747784561</p>
							</div>
						</li>
						<li class="flex justify-between gap-x-6 py-5">
							<div class="flex min-w-0 gap-x-4 w-2/12">
								<div class="min-w-0 flex-auto">
									<p class="text-xs font-semibold leading-6 text-gray-400">147243</p>
								</div>
							</div>
							<div class="flex min-w-0 gap-x-4 w-3/12">
								<div class="min-w-0 flex-auto">
									<p class="text-xs font-semibold leading-6 text-gray-400">123(ADMIN)</p>
								</div>
							</div>
							<div class="flex min-w-0 gap-x-4 w-2/12">
								<div class="min-w-0 flex-auto">
									<p class="text-xs font-semibold leading-6 text-gray-400">John</p>
								</div>
							</div>
							<div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end w-3/12">
								<p class="text-xs leading-6 text-gray-400">1747784561</p>
							</div>
						</li>
						<li class="flex justify-between gap-x-6 py-5">
							<div class="flex min-w-0 gap-x-4 w-2/12">
								<div class="min-w-0 flex-auto">
									<p class="text-xs font-semibold leading-6 text-gray-400">147242</p>
								</div>
							</div>
							<div class="flex min-w-0 gap-x-4 w-3/12">
								<div class="min-w-0 flex-auto">
									<p class="text-xs font-semibold leading-6 text-gray-400">123(ADMIN)</p>
								</div>
							</div>
							<div class="flex min-w-0 gap-x-4 w-2/12">
								<div class="min-w-0 flex-auto">
									<p class="text-xs font-semibold leading-6 text-gray-400">hakan</p>
								</div>
							</div>
							<div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end w-3/12">
								<p class="text-xs leading-6 text-gray-400">1747784561</p>
							</div>
						</li>
						<li class="flex justify-between gap-x-6 py-5">
							<div class="flex min-w-0 gap-x-4 w-2/12">
								<div class="min-w-0 flex-auto">
									<p class="text-xs font-semibold leading-6 text-gray-400">147241</p>
								</div>
							</div>
							<div class="flex min-w-0 gap-x-4 w-3/12">
								<div class="min-w-0 flex-auto">
									<p class="text-xs font-semibold leading-6 text-gray-400">123(ADMIN)</p>
								</div>
							</div>
							<div class="flex min-w-0 gap-x-4 w-2/12">
								<div class="min-w-0 flex-auto">
									<p class="text-xs font-semibold leading-6 text-gray-400">Micheal</p>
								</div>
							</div>
							<div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end w-3/12">
								<p class="text-xs leading-6 text-gray-400">1747784561</p>
							</div>
						</li>
					</ul> -->

					<section class="container mx-auto font-mono">
						<div class="w-full mb-8 overflow-hidden rounded-lg shadow-2xl">
							<div class="w-full overflow-x-auto">
								<table class="w-full">
									<thead>
										<tr class="text-md font-semibold tracking-wide text-left text-gray-300 bg-gray-700 uppercase">
											<th class="px-4 py-3">ID</th>
											<th class="px-4 py-3">Name</th>
											<th class="px-4 py-3">Product</th>
											<th class="px-4 py-3">Qty</th>
											<th class="px-4 py-3">Price</th>
											<th class="px-4 py-3">Total</th>
											<th class="px-4 py-3">Comm</th>
											<th class="px-4 py-3">Assignee</th>
											<th class="px-4 py-3">VC</th>
											<th class="px-4 py-3">SDM</th>
											<th class="px-4 py-3">Liquid</th>
											<th class="px-4 py-3">Linked Lead</th>
										</tr>
									</thead>
									<tbody>
										<tr class="text-gray-300">
											<td class="px-4 py-3 text-sm font-semibold">101</td>
											<td class="px-4 py-3 text-ms font-semibold border-l border-r border-gray-500">Sufyan</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">BTG:ASIA</td>
											<td class="px-4 py-3 text-ms font-semibold border-l border-r border-gray-500">2500.00</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">3.00</td>
											<td class="px-4 py-3 text-ms font-semibold border-l border-r border-gray-500">9000.00</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">2400.00</td>
											<td class="px-4 py-3 text-ms font-semibold border-l border-r border-gray-500">100(ADMIN)</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">ABC</td>
											<td class="px-4 py-3 text-ms font-semibold border-l border-r border-gray-500">
												<TimesCircleIcon class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
											</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">
												<CheckCircleIcon class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
											</td>
											<td class="px-4 py-3 text-sm">Sufyan</td>
										</tr>
										<tr class="text-gray-300">
											<td class="px-4 py-3 text-sm font-semibold">102</td>
											<td class="px-4 py-3 text-md font-semibold border-l border-r border-gray-500">Steven</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">BTG:ASIA</td>
											<td class="px-4 py-3 text-md font-semibold border-l border-r border-gray-500">3000.00</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">3.20</td>
											<td class="px-4 py-3 text-md font-semibold border-l border-r border-gray-500">9600.00</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">2500.00</td>
											<td class="px-4 py-3 text-md font-semibold border-l border-r border-gray-500">100(ADMIN)</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">ABC</td>
											<td class="px-4 py-3 text-md font-semibold border-l border-r border-gray-500">
												<CheckCircleIcon class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
											</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">
												<CheckCircleIcon class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
											</td>
											<td class="px-4 py-3 text-sm">Steven</td>
										</tr>
										<tr class="text-gray-300">
											<td class="px-4 py-3 text-sm font-semibold">103</td>
											<td class="px-4 py-3 text-md font-semibold border-l border-r border-gray-500">Nora</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">BTG:ASIA</td>
											<td class="px-4 py-3 text-md font-semibold border-l border-r border-gray-500">4000.00</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">3.50</td>
											<td class="px-4 py-3 text-md font-semibold border-l border-r border-gray-500">14000.00</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">3000.00</td>
											<td class="px-4 py-3 text-md font-semibold border-l border-r border-gray-500">100(ADMIN)</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">ABC</td>
											<td class="px-4 py-3 text-md font-semibold border-l border-r border-gray-500">
												<CheckCircleIcon class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
											</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">
												<CheckCircleIcon class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
											</td>
											<td class="px-4 py-3 text-sm">Nora</td>
										</tr>
										<tr class="text-gray-300">
											<td class="px-4 py-3 text-sm font-semibold">104</td>
											<td class="px-4 py-3 border-l border-r border-gray-500 text-md font-semibold">Ali</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">BTG:ASIA</td>
											<td class="px-4 py-3 border-l border-r border-gray-500 text-md font-semibold">1500.00</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">2.50</td>
											<td class="px-4 py-3 border-l border-r border-gray-500 text-md font-semibold">3750.00</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">1200.00</td>
											<td class="px-4 py-3 border-l border-r border-gray-500 text-md font-semibold">100(ADMIN)</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">ABC</td>
											<td class="px-4 py-3 border-l border-r border-gray-500 text-md font-semibold">
												<CheckCircleIcon class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
											</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">
												<CheckCircleIcon class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
											</td>
											<td class="px-4 py-3 text-sm">Ali</td>
										</tr>
										<tr class="text-gray-300">
											<td class="px-4 py-3 text-sm font-semibold">105</td>
											<td class="px-4 py-3 border-l border-r border-gray-500 text-md font-semibold">Khalid</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">BTG:ASIA</td>
											<td class="px-4 py-3 border-l border-r border-gray-500 text-md font-semibold">2400.00</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">2.90</td>
											<td class="px-4 py-3 border-l border-r border-gray-500 text-md font-semibold">6960.00</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">2000.00</td>
											<td class="px-4 py-3 border-l border-r border-gray-500 text-md font-semibold">100(ADMIN)</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">ABC</td>
											<td class="px-4 py-3 border-l border-r border-gray-500 text-md font-semibold">
												<CheckCircleIcon class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
											</td>
											<td class="px-4 py-3 text-sm border-l border-r border-gray-500">
												<TimesCircleIcon class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
											</td>
											<td class="px-4 py-3 text-sm">Khalid</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</section>

					<div class="flex justify-center mt-4">
						<Button 
							:variant="'primary'" 
							:size="'base'"
							:href="viewMoreHref"
							class="justify-center gap-2"
						>
							<span>View More</span>
						</Button>
						<!-- <div class="transform transition-transform focus:scale-95 active:scale-95 hover:scale-110 w-28">
							<a :href="viewMoreHref" class="bg-purple-800 rounded-lg text-white shadow-md shadow-blue-600/50 py-2 px-4">View more</a>
						</div> -->
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
