<script setup>
import { ChevronUpIcon } from '@heroicons/vue/solid'
import { onMounted, computed, ref } from "vue";
import { CheckCircleIcon, ErrorCircleIcon, InfoCircleIcon } from '@/Components/Icons/outline';
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'

const props = defineProps({
    errors: {
        type: Object,
        default: () => ({}),
    },
    errorMsg: {
        type: Object,
        default: () => ({}),
    },
    type: {
        type: String,
        default: 'default'
    },
    rowErrorMsg: {
        type: Boolean,
        default: false
    },
})

onMounted(() => {
    // console.log(props.errorMsg);
});

const typeClass = computed(() => {
    return {
        success: CheckCircleIcon,
        error: ErrorCircleIcon,
        default: InfoCircleIcon,
    }[props.type]
})

</script>

<template>
    <div class="flex flex-col items-center">
        <component 
            :is="typeClass" 
            class="flex-shrink-0 w-36 h-36 mb-10"
        />
        <div class="px-8 flex flex-col items-center">
            <p class="dark:text-gray-200 text-gray-700 font-semibold text-lg">{{ props.errorMsg ? props.errorMsg.title : '' }}</p>
            <hr class="border-b rounded-md border-gray-600 mb-2 w-full mx-auto">
            <p class="dark:text-gray-300 text-gray-500 text-sm p-3">{{ props.errorMsg ? props.errorMsg.content : '' }}</p>
        </div>
        <Disclosure v-slot="{ open }" v-if="rowErrorMsg">
            <DisclosureButton 
                class="py-2 bg-purple-700 px-6 rounded-lg text-sm mt-10 font-semibold mb-2"
            >
                <span>Read Details</span>
                <ChevronUpIcon
                    :class="open ? 'rotate-180 transform' : ''"
                    class="h-5 w-5 text-purple-500 inline-block"
                />
            </DisclosureButton> 
            <transition
                enter-active-class="transition duration-200 ease-in-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-200 ease-in-out"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
            >
                <DisclosurePanel 
                    class="text-gray-500 w-full max-h-[300px] overflow-auto"
                >
                    <table class="table-auto w-full rounded-lg bg-gray-700">
                        <thead>
                            <tr class="text-md font-bold tracking-wide text-left text-gray-300">
                                <th class="px-4 py-3">Row</th>
                                <th class="px-4 py-3">Error Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-gray-300 bg-gray-800" v-for="error in props.errors">
                                <td class="px-4 py-3 text-sm font-semibold border-r border-gray-500">{{ error.row }}</td>
                                <td class="px-4 py-3 text-sm font-semibold">{{ error.errors[0].replace('/[ | ]/g', '') }}</td>
                                <!-- <td class="px-4 py-3 text-sm font-semibold">{{ console.log(error.errors) }}</td> -->
                            </tr>
                        </tbody>
                    </table>
                </DisclosurePanel>
            </transition>
        </Disclosure>
    </div>
</template>
