<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { cl, back, setDateTimeWithOffset, setFormattedDateTimeWithOffset, populateArrayFromResponse } from '@/Composables'
import Label from '@/Components/Label.vue'
import Modal from '@/Components/Modal.vue'
import Button from '@/Components/Button.vue'
import { TimesCircleIcon } from '@/Components/Icons/solid';
import CustomLabelGroup2 from '@/Components/CustomLabelGroup2.vue'
import CustomTextInputField2 from '@/Components/CustomTextInputField2.vue'
import CustomFileInputField2 from '@/Components/CustomFileInputField2.vue'
import CustomSelectInputField2 from '@/Components/CustomSelectInputField2.vue'

// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    errors:Object,
    data: {
        type: Object,
        default: () => ({}),
    },
    sites: {
        type: Object,
        default: () => ({}),
    },
})

const selectedSite = ref(null);
const siteArray  = reactive({});

onMounted(async () => {
    form.sites.forEach((item) => {
        item['edited'] = false;
    })

    try {
        const siteResponse = await axios.get(route('payment-methods.getAllSites'));
        populateArrayFromResponse(siteResponse.data, siteArray, 'id', 'domain');

    } catch (error) {
        console.error('Error fetching data:', error);
    }
});

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
    _method: 'put',
    id: props.data.id,
	title: props.data.title,
	description: props.data.description,
	bank_name: props.data.bank_name,
	account_name: props.data.account_name,
	account_number: props.data.account_number,
	logo: props.data.logo,
	new_logo: '',
    edited_at: props.data.edited_at,
    created_at: `${props.data.created_at}00`,
	sites: props.sites ? props.sites : [],
});

// Post form fields to controller after executing the checking and parsing the input fields
const formSubmit = () => {
	form.edited_at = setDateTimeWithOffset(true);

    if (form.sites.length > 0) {
        form.sites.forEach(item => {
            item.site_id = parseInt(item.site_id);
            item.edited_at = (item.edited) 
                                ? setDateTimeWithOffset(true) 
                                : setFormattedDateTimeWithOffset(item.edited_at, true);

            item.created_at = (item.created_at === '') 
                                ? setDateTimeWithOffset(true) 
                                : setFormattedDateTimeWithOffset(item.created_at, true);
        });
    }

	form.post(route('payment-methods.update', form.id), {
		preserveScroll: true,
		onSuccess: () => form.reset(),
	})
};

const deleteSitePaymentMethod = (id) => {
    form.delete(route('payment-methods.deleteSitePaymentMethod', id));
}

const addSitePaymentMethod = () => {
    form.sites.push({
        'id': '',
        'site_id': '',
        'edited_at': '',
        'created_at': '',
        'edited': true,
    });
}

const removeSitePaymentMethod = (i, id) => {
    if (id !== undefined && id !== '') {
        deleteSitePaymentMethod(id);
    }
    form.sites.splice(i, 1);
    closeSitePaymentMethodModal();
}

const openSitePaymentMethodModal = (i) => {
    selectedSite.value = i;
}

const closeSitePaymentMethodModal = () => {
    selectedSite.value = null;
}

const updateSites = (site, item) => {
    item.edited = true;
}

</script>

<template>
    <div class="form-wrapper">
        <form novalidate @submit.prevent="formSubmit">
            <div class="sticky top-[72px] z-10 mb-6 border border-gray-700 rounded-md">
                <div class="dark:bg-dark-eval-1 bg-white rounded-md flex flex-row items-center pr-12 pl-14 py-2 gap-8">
                    <p class="text-xl font-semibold leading-tight">Action</p>
                    <span class="border-l border-gray-500 h-20"></span>
                    <div class="action-button-group">
                        <Button 
                            :type="'button'"
                            :variant="'info'" 
                            :size="'base'"
                            @click="back"
                            class="justify-center gap-2 form-actions"
                        >
                            <span>Back</span>
                        </Button>
                        <Button 
                            :variant="'primary'" 
                            :size="'base'" 
                            class="justify-center gap-2 form-actions"
                            :disabled="form.processing"
                        >
                            <span>{{ form.processing ? 'Saving...' : 'Save' }}</span>
                        </Button>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="form-input-section col-span-1 lg:col-span-8 dark:bg-dark-eval-1">
                    <div class="px-3">
                        <div class="flex flex-row justify-between items-center mb-2">
                            <Label
                                :value="'Payment Method Details'"
                                class="mt-4 !text-2xl !font-bold pl-2"
                            >
                            </Label>
                        </div>
                        <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                    </div>
                    <div class="input-group grid grid-cols-1 gap-6">
                        <CustomTextInputField2
                            :inputType="'text'"
                            :inputId="'title'"
                            :labelValue="'Title'"
                            class="col-span-full w-full"
                            :dataValue="props.data.title"
                            :errorMessage="(form.errors) ? form.errors.title : '' "
                            v-model="form.title"
                        />
                        <CustomTextInputField2
                            :inputType="'textarea'"
                            :inputId="'description'"
                            :labelValue="'Description'"
                            class="col-span-full w-full"
                            :dataValue="props.data.description"
                            :errorMessage="(form.errors) ? form.errors.description : '' "
                            v-model="form.description"
                        />
                        <CustomTextInputField2
                            :inputType="'text'"
                            :inputId="'bank_name'"
                            :labelValue="'Cryptocurrency'"
                            class="col-span-full w-full"
                            :dataValue="props.data.bank_name"
                            :errorMessage="(form.errors) ? form.errors.bank_name : '' "
                            v-model="form.bank_name"
                        />
                        <CustomTextInputField2
                            :inputType="'text'"
                            :inputId="'account_name'"
                            :labelValue="'Network'"
                            class="col-span-full w-full"
                            :dataValue="props.data.account_name"
                            :errorMessage="(form.errors) ? form.errors.account_name : '' "
                            v-model="form.account_name"
                        />
                        <CustomTextInputField2
                            :inputType="'text'"
                            :inputId="'account_number'"
                            :labelValue="'Wallet Address'"
                            class="col-span-full w-full"
                            :dataValue="props.data.account_number"
                            :errorMessage="(form.errors) ? form.errors.account_number : '' "
                            v-model="form.account_number"
                        />
                        <CustomLabelGroup2
                            :inputId="'logo'"
                            :labelValue="'Current Logo: '"
                            class="col-span-full w-full"
                            :dataValue="false"
                        >
                            <strong><span class="text-purple-300">{{ form.logo }}</span></strong>
                        </CustomLabelGroup2>
                        <CustomFileInputField2
                            :inputId="'new_logo'"
                            :labelValue="'New Logo'"
                            :withLabel="true"
                            class="col-span-full w-full"
                            :errorMessage="(form.errors) ? form.errors.new_logo : '' "
                            v-model="form.new_logo" 
                        />
                    </div>
                </div>
                <div class="form-input-section !flex flex-col col-span-1 lg:col-span-4 dark:bg-dark-eval-1">
                    <div class="px-3">
                        <div class="flex flex-row justify-between items-center mb-2">
                            <Label
                                :value="'Site Payment Method'"
                                class="mt-4 !text-2xl !font-bold pl-2"
                            >
                            </Label>
                        </div>
                        <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                    </div>
                    <div class="input-group mb-3 divide-y divide-gray-500/80" v-if="form.sites.length > 0">
                        <div 
                            class="flex flex-row"
                            :class="{ 'pt-2': i !== 0, 'pb-2': i !== (form.sites.length - 1) }"
                            v-for="(item, i) in form.sites" :key="i"
                        >
                            <CustomSelectInputField2
                                :inputArray="siteArray"
                                :inputId="'site_id_'+(i)"
                                :labelValue="'Site #'+(i+1)"
                                :customValue="true"
                                :dataValue="item.site_id"
                                class="col-span-full w-full"
                                :errorMessage="(form.errors) ? form.errors['site_id.' + (i) + '.price'] : '' "
                                @change="updateSites($event.target.value, item)"
                                v-model="item.site_id"
                            />
                            <span class="inline">
                                <Button 
                                    :type="'button'"
                                    :variant="'transparent'" 
                                    :size="'sm'" 
                                    class="justify-center gap-2 w-full"
                                    @click="openSitePaymentMethodModal(i)"
                                >
                                    <TimesCircleIcon class="flex-shrink-0 w-5 h-5 cursor-pointer" aria-hidden="true" />
                                </Button>
                            </span>
                            <Modal 
                                :show="i === selectedSite" 
                                maxWidth="2xl" 
                                :closeable="true" 
                                @close="closeSitePaymentMethodModal">

                                <div class="modal">
                                    <p class="text-gray-200 text-2xl bg-red-500 p-4 rounded-md font-bold">Delete Site Confirmation.</p>
                                    <p class="text-gray-200 text-lg p-4">Are you sure that you want to <span class="font-bold">delete</span> this site?</p>
                                </div>
                                <div class="flex flex-row justify-end p-9">
                                    <div class="dark:bg-gray-600 p-4 rounded-md flex gap-4">
                                        <Button 
                                            :type="'button'"
                                            :variant="'info'" 
                                            :size="'sm'"
                                            @click="closeSitePaymentMethodModal"
                                            class="justify-center px-6 py-2"
                                        >
                                            <span>Back</span>   
                                        </Button>
                                        <Button 
                                            :type="'button'"
                                            :variant="'danger'" 
                                            :size="'sm'" 
                                            class="justify-center px-6 py-2"
                                            @click="removeSitePaymentMethod(i, item.id)"
                                        >
                                            <span>Confirm</span>
                                        </Button>
                                    </div>
                                </div>
                            </Modal>
                        </div>
                    </div>
                    <Button 
                        :type="'button'"
                        :variant="'success'" 
                        :size="'sm'"
                        @click="addSitePaymentMethod"
                        class="justify-center gap-2 px-8 mr-auto"
                    >
                        <span>Add Site</span>
                    </Button>
                </div>
            </div>
        </form>
    </div>
</template>
