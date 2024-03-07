<script setup>
import { ref, onMounted, reactive } from 'vue'
import { cl, back, convertToHumanReadable } from '@/Composables'
import { 
    Disclosure, DisclosureButton, DisclosurePanel, TransitionRoot 
} from '@headlessui/vue'
import { ThreeDotsVertical } from '@/Components/Icons/solid';
import { ChevronUpIcon } from '@heroicons/vue/solid'
import Dropdown from '@/Components/Dropdown.vue'
import Button from '@/Components/Button.vue'
import Label from '@/Components/Label.vue'
import axios from "axios";
import dayjs from 'dayjs';

// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    selectedRowData: {
        type: Object,
        default: () => ({}),
    },
})

const usersClientsChangelogsData = reactive({});
let originalusersClientsChangelogsData = null;

onMounted(async () => {
  try {
    const usersClientsChangelogsResponse = await axios.get(route('users-clients.getUserChangelogs', props.selectedRowData.id));
    usersClientsChangelogsData.value = usersClientsChangelogsResponse.data;

    let usersClientsLogsArray = [];
    for (let key in usersClientsChangelogsData.value) {
        usersClientsLogsArray.push(usersClientsChangelogsData.value[key]);
    }

    usersClientsLogsArray.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

    Object.keys(usersClientsChangelogsData.value).forEach((col, index) => {
        usersClientsChangelogsData.value[col].created_at = dayjs(usersClientsChangelogsData.value[col].created_at).format('DD MMMM YYYY, hh:mm A');;
    });
    
    usersClientsChangelogsData.value = usersClientsLogsArray;
    originalusersClientsChangelogsData = usersClientsChangelogsData.value;

  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

const filterByToday = () => {
  const today = dayjs();
  usersClientsChangelogsData.value = originalusersClientsChangelogsData.filter((log) =>
    dayjs(log.created_at).isSame(today, 'day')
  );
};

const filterByPast7Days = () => {
  const sevenDaysAgo = dayjs().subtract(7, 'day');
  usersClientsChangelogsData.value = originalusersClientsChangelogsData.filter((log) =>
    dayjs(log.created_at).isAfter(sevenDaysAgo)
  );
};

const filterByThisMonth = () => {
  const startOfMonth = dayjs().startOf('month');
  usersClientsChangelogsData.value = originalusersClientsChangelogsData.filter((log) =>
    dayjs(log.created_at).isAfter(startOfMonth)
  );
};

const filterByThisYear = () => {
  const startOfYear = dayjs().startOf('year');
  usersClientsChangelogsData.value = originalusersClientsChangelogsData.filter((log) =>
    dayjs(log.created_at).isAfter(startOfYear)
  );
};

const showAll = () => {
  usersClientsChangelogsData.value = originalusersClientsChangelogsData;
};

</script>

<template>
    <div class="input-group p-8 rounded-xl">
        <div class="flex justify-between">
            <p class="dark:text-gray-300 font-semibold text-xl pb-2">User Client Changelog</p>
            <Dropdown 
                :align="'right'" 
                :width="50" 
                :contentClasses="'dark:bg-dark-eval-3'"
            >
                <template #trigger>
                    <ThreeDotsVertical class="flex-shrink-0 w-6 h-6 cursor-pointer mx-2" aria-hidden="true" />
                </template>

                <template #content>
                    <div class="p- w-full">
                        <p class="text-md p-2 text-gray-300 text-center">Date Filter</p>
                        <hr class="border-b rounded-md border-gray-600 mb-2 w-10/12 mx-auto">
                        <div class="px-6 pb-6 pt-2 flex flex-col gap-2">
                            <Button 
                                :type="'button'"
                                :variant="'info'" 
                                :size="'sm'" 
                                class="justify-center px-6 py-2 w-full h-full whitespace-nowrap"
                                @click="showAll"
                            >
                                {{ 'All' }}
                            </Button>
                            <Button 
                                :type="'button'"
                                :variant="'info'" 
                                :size="'sm'" 
                                class="justify-center px-6 py-2 w-full h-full whitespace-nowrap"
                                @click="filterByToday"
                            >
                                {{ 'Today' }}
                            </Button>
                            <Button 
                                :type="'button'"
                                :variant="'info'" 
                                :size="'sm'" 
                                class="justify-center px-6 py-2 w-full h-full whitespace-nowrap"
                                @click="filterByPast7Days"
                            >
                                {{ 'Past 7 days' }}
                            </Button>
                            <Button 
                                :type="'button'"
                                :variant="'info'" 
                                :size="'sm'" 
                                class="justify-center px-6 py-2 w-full h-full whitespace-nowrap"
                                @click="filterByThisMonth"
                            >
                                {{ 'This month' }}
                            </Button>
                            <Button 
                                :type="'button'"
                                :variant="'info'" 
                                :size="'sm'" 
                                class="justify-center px-6 py-2 w-full h-full whitespace-nowrap"
                                @click="filterByThisYear"
                            >
                                {{ 'This year' }}
                            </Button>
                        </div>
                    </div>
                </template>
            </Dropdown>
        </div>
        <div class="container hidden-scrollable max-h-[500px]">
            <!-- For User Client Changelogs -->
            <div class="w-full flex justify-center" v-if="!usersClientsChangelogsData.value || usersClientsChangelogsData.value.length === 0">
                <h3 class="font-semibold text-gray-200"> No records found for this lead front currently. </h3>
            </div>
            <div v-for="(log, index) in usersClientsChangelogsData.value" :key="index">
                <div class="flex flex-col md:grid grid-cols-12">
                    <div class="flex md:contents">
                        <div class="col-start-1 col-end-2 mr-10 md:mx-auto relative">
                            <div class="h-full w-3 flex items-center justify-center">
                                <div class="h-full w-[2px] bg-purple-200/25 pointer-events-none"></div>
                            </div>
                            <div class="w-3 h-3 absolute top-0 rounded-full bg-purple-300 shadow text-center ring ring-purple-200/25"></div>
                        </div>
                        <div class="bg-gray-700 dark:bg-gray-700/70 col-start-2 col-end-13 p-4 rounded-xl mb-4 mr-auto shadow-md w-full">
                            <h3 class="font-semibold text-gray-200">
                                [System]: {{ log.description }}
                            </h3>
                            <span class="text-xs text-gray-400">
                                {{ log.created_at }}
                            </span>
                            <div v-if="log.changes && Object.keys(log.changes).length > 0">
                                <Disclosure v-slot="{ open }">
                                    <DisclosureButton 
                                        class="bg-gray-700 dark:bg-gray-800/50 rounded-xl text-gray-300 p-2 mt-2 flex items-center w-full mb-1 text-sm font-bold"
                                    >
                                        User Client Changelogs 
                                        <span class="text-xs font-thin pl-4">
                                            ( {{ log.id }} )
                                        </span>
                                        <ChevronUpIcon
                                            :class="[
                                                open ? 'transition-all duration-200 rotate-270 transform' : '',
                                                !open ? 'transition-all duration-200 rotate-180 transform' : '',
                                            ]"
                                            class="h-5 w-5 text-gray-400 ml-auto"
                                        />
                                    </DisclosureButton>
                                    <TransitionRoot
                                        enter="transition-all ease-in duration-500"
                                        enter-from="transform opacity-0 scaleY-95"
                                        enter-to="transform opacity-100 scaleY-100"
                                        leave="transition-all ease-out duration-200"
                                        leave-from="transform opacity-100 scaleY-100"
                                        leave-to="transform opacity-0 scaleY-95"
                                    >
                                        <DisclosurePanel 
                                            class="bg-gray-700 dark:bg-gray-900/60 rounded-xl text-gray-500 p-4 text-xs flex flex-col gap-3 max-h-52 overflow-auto"
                                        >
                                            <div v-for="(value, ix) in log.changes" :key="ix">
                                                <Label
                                                    :inputId="'userClientChangelogColumn'+ix"
                                                    :labelValue="'Lead Notes'"
                                                    class="font-semibold !text-gray-200/75 text-xs col-span-1 pb-1"
                                                >
                                                    ◉ [ User Client ID: {{ log.users_clients_id }} ]: [{{ (ix === 'New') ? 'New User Client' : convertToHumanReadable(ix) }}] 
                                                </Label>
                                                <Label
                                                    :inputId="'userClientChangelog'+ix"
                                                    :labelValue="'Lead Notes'"
                                                    class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                    v-if="ix === 'New' || ix === 'Delete' || ix ==='Password'"
                                                >
                                                    ➤ {{ value.description }}.
                                                </Label>
                                                <Label
                                                    :inputId="'userClientChangelog'+ix"
                                                    :labelValue="'Lead Notes'"
                                                    class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                    v-else
                                                >
                                                    ➤ {{ convertToHumanReadable(ix) }} has been {{ (value.old !== null) ? 'changed from "' + value.old : 'set' }}" to "{{ value.new }}".
                                                </Label>
                                            </div>
                                        </DisclosurePanel>
                                    </TransitionRoot>
                                </Disclosure>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>