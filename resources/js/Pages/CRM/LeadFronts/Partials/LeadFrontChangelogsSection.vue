<script setup>
import { ref, onMounted, reactive } from 'vue'
import { cl, back, convertToHumanReadable } from '@/Composables'
import { 
    Disclosure, DisclosureButton, DisclosurePanel, TransitionRoot 
} from '@headlessui/vue'
import { ChevronUpIcon } from '@heroicons/vue/solid'
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

const leadFrontChangelogsData = reactive({});

onMounted(async () => {
  try {
    // cl(props.selectedRowData);
    const leadFrontChangelogsResponse = await axios.get(route('lead-fronts.getLeadFrontChangelogs', props.selectedRowData.linked_lead));
    leadFrontChangelogsData.value = leadFrontChangelogsResponse.data;

    // Format the created_at dates
    leadFrontChangelogsData.value.forEach(log => {
      log.created_at = dayjs(log.created_at).format('DD MMMM YYYY, hh:mm A');
    });

  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

</script>

<template>
    <div class="input-group p-8 rounded-xl">
        <p class="dark:text-gray-300 font-semibold text-2xl pb-2">Lead Front Changelog</p>
        <div class="container">
            <!-- For Lead Front Changelogs -->
            <div class="w-full flex justify-center" v-if="!leadFrontChangelogsData.value || leadFrontChangelogsData.value.length === 0">
                <h3 class="font-semibold text-gray-200"> No records found for this lead front currently. </h3>
            </div>
            <div v-for="(log, index) in leadFrontChangelogsData.value" :key="index">
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
                            <div v-if="log.lead_front_changes && Object.keys(log.lead_front_changes).length > 0">
                                <Disclosure v-slot="{ open }">
                                    <DisclosureButton 
                                        class="bg-gray-700 dark:bg-gray-800/50 rounded-xl text-gray-300 p-2 mt-2 flex items-center w-full mb-1 text-sm font-bold"
                                    >
                                        Lead Front Changelogs 
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
                                            <div v-for="(value, ix) in log.lead_front_changes" :key="ix">
                                                <Label
                                                    :inputId="'leadChangelogColumn'+ix"
                                                    :labelValue="'Lead Notes'"
                                                    class="font-semibold !text-gray-200/75 text-xs col-span-1 pb-1"
                                                >
                                                    ◉ [ Lead Front ID: {{ value.id }} ]: [{{ (ix === 'New') ? 'New Lead Front' : convertToHumanReadable(ix) }}] 
                                                </Label>
                                                <Label
                                                    :inputId="'leadChangelog'+ix"
                                                    :labelValue="'Lead Notes'"
                                                    class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                    v-if="ix === 'New' || ix === 'Delete'"
                                                >
                                                    ➤ {{ value.description }}.
                                                </Label>
                                                <Label
                                                    :inputId="'leadChangelog'+ix"
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