<script setup>
import { ref, onMounted, reactive } from 'vue'
import { cl, back, convertToHumanReadable, replaceHyphensWithEmpty } from '@/Composables'
import { AtSymbolIcon } from '@heroicons/vue/outline'
import { 
    TabGroup, TabList, Tab, TabPanels, TabPanel
} from '@headlessui/vue'
import { 
    PhoneIcon, MapIcon, FlagIcon, CalendarIcon 
} from '@heroicons/vue/solid'
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue'
import Button from '@/Components/Button.vue'
import axios from "axios";
import dayjs from 'dayjs';

// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    selectedRowData: {
        type: Object,
        default: () => ({}),
    },
})

const categories = ref([
    'User Info', 'Account Info', 'KYC Status', 'Permissions', 'System'
])

const leadFrontCategories = ref([
    'Basic Info', 'Financial Details', 'Additional Info', 'Timestamps'
])

const leadFrontData = reactive({});

const emit = defineEmits(['closeModal', 'rowEdit']);

onMounted(() => {
    replaceHyphensWithEmpty(props.selectedRowData);
});

</script>

<template>
    <div class="p-6">
    </div>
</template>
