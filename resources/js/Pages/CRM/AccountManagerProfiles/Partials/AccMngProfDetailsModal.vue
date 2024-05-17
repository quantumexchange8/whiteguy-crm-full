<script setup>
import axios from "axios";
import { usePage } from '@inertiajs/vue3'
import { ref, onMounted, reactive, computed } from 'vue'
import { AtSymbolIcon } from '@heroicons/vue/outline'
import { PhoneIcon, MapIcon, FlagIcon, CalendarIcon } from '@heroicons/vue/solid'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import { cl, back, convertToHumanReadable, replaceHyphensWithEmpty, formatToUserTimezone } from '@/Composables'
import AccMngProfChangelogsSection from './AccMngProfChangelogsSection.vue'
import Button from '@/Components/Button.vue'
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue'

// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    selectedRowData: {
        type: Object,
        default: () => ({}),
    },
})

const profileCategories = ref([
    'Profile Information', 'Timestamps'
])

const leadData = reactive({});
const emit = defineEmits(['closeModal', 'rowEdit']);
const page = usePage();
const user = computed(() => page.props.auth.user)

onMounted(async () => {
    replaceHyphensWithEmpty(props.selectedRowData);
});

</script>

<template>
    <div class="p-6">
        <div class="p-3 rounded-lg grid grid-cols-1 lg:grid-cols-3 lg:gap-6">
            <div class="col-span-2 flex flex-col gap-6 mb-8 lg:mb-0">
                <div class="input-group flex flex-col">
                    <p class="dark:text-gray-300 font-semibold text-xl pb-2">
                        User Details
                        <span class="text-xs font-thin pl-4">
                            ( {{ selectedRowData.user ? selectedRowData.user.id : '' }} )
                        </span>
                    </p>
                    <div class="grid grid-cols-1 lg:grid-cols-12 lg:gap-4">
                        <p class="dark:text-gray-300 lg:col-span-6 font-semibold text-2xl pb-2">
                            {{ selectedRowData.user ? selectedRowData.user.full_name : '' }}
                        </p>
                        <div class="flex flex-col max-w-full lg:col-span-6 gap-5 bg-gray-100 dark:bg-gray-900/60 p-3 rounded-lg">
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                                <p class="dark:text-gray-400 text-sm pr-2 lg:col-span-4 whitespace-nowrap">
                                    <span class="text-xs font-thin">
                                        <PhoneIcon
                                            class="h-4 w-4 text-purple-400 inline-block"
                                        />
                                    </span>
                                    | {{ selectedRowData.user ? selectedRowData.user.phone_number : '' }}
                                </p>
                                <span class="text-gray-800 dark:text-gray-500 text-sm leading-tight lg:col-span-1">•</span>
                                <p class="dark:text-gray-400 text-sm px-2 lg:col-span-7">
                                    <span class="text-xs font-thin">
                                        <AtSymbolIcon
                                            class="h-4 w-4 text-purple-400 inline-block"
                                        />
                                    </span>
                                    | {{ selectedRowData.user ? selectedRowData.user.email : '' }}
                                </p>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                                <p class="dark:text-gray-400 text-sm pr-2 lg:col-span-5">
                                    <span class="text-xs font-thin">
                                        <FlagIcon
                                            class="h-4 w-4 text-purple-400 inline-block"
                                        />
                                    </span>
                                    | {{ selectedRowData.user ? selectedRowData.user.country : '' }}
                                </p>
                            </div>
                            <div class="flex">
                                <p class="dark:text-gray-400 text-sm">
                                    <span class="text-xs font-thin">
                                        <MapIcon
                                            class="h-4 w-4 text-purple-400 inline-block"
                                        />
                                    </span>
                                    | {{ selectedRowData.user ? selectedRowData.user.address : '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="input-group">
                    <p class="dark:text-gray-300 font-semibold text-xl pb-2">
                        Profile Details
                        <span class="text-xs font-thin pl-4">
                            ( {{ props.selectedRowData?.id ?? '' }} )
                        </span>
                    </p>
                    <div class="w-full p-2 sm:px-0">
                        <TabGroup>
                            <TabList class="flex space-x-1 rounded-xl bg-blue-600/30 p-1 flex-wrap md:flex-nowrap">
                                <Tab
                                    v-for="category in profileCategories"
                                    as="template"
                                    :key="category"
                                    v-slot="{ selected }"
                                >
                                    <button
                                        :class="[
                                            'w-full rounded-lg py-2.5 text-xs font-medium leading-5',
                                            'ring-white/60 ring-offset-2 ring-offset-purple-400 focus:outline-none focus:ring-2',
                                            selected
                                                ? 'bg-white text-blue-800 shadow'
                                                : 'text-gray-300 hover:bg-white/[0.12] hover:text-white',
                                        ]"
                                    >
                                        {{ category }}
                                    </button>
                                </Tab>
                            </TabList>

                            <TabPanels class="mt-2" v-if="Object.keys(props.selectedRowData).length > 0">
                                <TabPanel
                                    :class="[
                                        'rounded-xl bg-gray-200 dark:bg-gray-700 p-3',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <CustomLabelGroup
                                        :inputId="'file'"
                                        :labelValue="'File'"
                                        :dataValue="props.selectedRowData && props.selectedRowData.file ? props.selectedRowData.file : '-'"
                                        class="text-gray-700"
                                    />
                                </TabPanel>
                                <TabPanel
                                    :class="[
                                        'rounded-xl bg-gray-200 dark:bg-gray-700 p-3',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <div class="grid grid-cols-1 sm:grid-cols-3">
                                        <CustomLabelGroup
                                            :inputId="'edited_at'"
                                            :labelValue="'Edited at'"
                                            :dataValue="props.selectedRowData.edited_at ? formatToUserTimezone(props.selectedRowData.edited_at, user.timezone, true) : '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'created_at'"
                                            :labelValue="'Created at'"
                                            :dataValue="props.selectedRowData.created_at ? formatToUserTimezone(props.selectedRowData.created_at, user.timezone, true) : '-'"
                                        />
                                    </div>
                                </TabPanel>
                            </TabPanels>
                            <TabPanels class="mt-2" v-else>
                                <TabPanel
                                    :class="[
                                        'rounded-xl bg-gray-200 dark:bg-gray-700 p-3',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <div class="p-2 sm:p-4 sm:h-46 rounded-2xl shadow-lg gap-5 select-none ">
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 sm:p-2">
                                            <div class="bg-gray-200 w-full animate-pulse h-8 rounded-2xl col-span-2" ></div>
                                            <div class="bg-gray-200 w-full animate-pulse h-8 rounded-2xl" ></div>
                                            <div class="bg-gray-200 w-full animate-pulse h-8 rounded-2xl" ></div>
                                            <div class="bg-gray-200 w-full animate-pulse h-8 rounded-2xl col-span-2" ></div>
                                            <div class="bg-gray-200 w-full animate-pulse h-8 rounded-2xl col-span-2" ></div>
                                            <div class="bg-gray-200 w-full animate-pulse h-8 rounded-2xl" ></div>
                                        </div>
                                    </div>
                                </TabPanel>
                            </TabPanels>
                        </TabGroup>
                    </div>
                </div>
            </div>
            <div class="col-span-1">
                <AccMngProfChangelogsSection
                    :selectedRowData="props.selectedRowData"
                    class="w-full"
                />
            </div>
        </div>
        <div class="flex justify-end p-9">
            <div class="rounded-md shadow-lg flex flex-row gap-2">
                <div class="action-button-group">
                    <Button 
                        :type="'button'"
                        :variant="'info'" 
                        :size="'base'"
                        class="justify-center gap-2 px-6 py-2 form-actions"
                        @click="emit('closeModal')"
                    >
                        <span>Back</span>
                    </Button>
                    <Button 
                        :type="'button'"
                        :size="'base'" 
                        class="justify-center gap-2 px-6 py-2 form-actions"
                        @click="emit('rowEdit', props.selectedRowData)"
                    >
                        <span>Edit</span>
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
