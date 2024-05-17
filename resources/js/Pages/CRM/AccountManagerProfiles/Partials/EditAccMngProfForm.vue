<script setup>
import { ref, onMounted, reactive, computed } from 'vue'
import { useForm, usePage, Link } from '@inertiajs/vue3'
import { cl, back, setDateTimeWithOffset, formatToUserTimezone } from '@/Composables'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue'
import CustomFileInputField from '@/Components/CustomFileInputField.vue'
import CustomSelectInputField from '@/Components/CustomSelectInputField.vue'

// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    errors:Object,
    data: {
        type: Object,
        default: () => ({}),
    },
})

const page = usePage();
const userListArray  = reactive({});
const user = computed(() => page.props.auth.user)

onMounted(async () => {
    try {
        const userListResponse = await axios.get(route('users-clients.getUserList'));

        userListResponse.data.forEach(item => {
            userListArray[item['id']] = {
                value: item['username'] + " (" + item.site['name'] + ")",
            };
        });
    } catch (error) {
        console.error('Error fetching data:', error);
    }
});

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
    _method: 'put',
	id: props.data.id,
	file: props.data.file,
	user_id: props.data.user_id,
	edited_at: props.data.edited_at,
	created_at: `${props.data.created_at}00`,
    new_file: null,
});

// Post form fields to controller after executing the checking and parsing the input fields
const formSubmit = () => {
    form.edited_at = setDateTimeWithOffset(true);

	form.post(route('account-manager-profiles.update', form.id), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    })
};
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
            <div class="form-input-section dark:bg-dark-eval-1">
                <div class="px-3">
                    <div class="flex flex-row justify-between items-center mb-2">
                        <Label
                            :value="'Personal Information'"
                            class="mt-4 !text-2xl !font-bold pl-2"
                        >
                        </Label>
                    </div>
                    <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                </div>
				<div class="input-group-wrapper grid grid-cols-1 lg:grid-cols-2 gap-8">
					<div class="input-group grid grid-cols-1 lg:grid-cols-2 gap-6 col-span-1">
                        <CustomFileInputField
                            :inputId="'new_file'"
                            :labelValue="'File'"
                            :withLabel="true"
                            :errorMessage="(form.errors) ? form.errors.new_file : '' "
                            v-model="form.new_file" 
                        />
                        <CustomLabelGroup
                            :inputId="'file'"
                            :labelValue="'Currently: '"
                            :dataValue="false"
                        >
                            <strong><span class="text-purple-300">{{ form.file }}</span></strong>
                        </CustomLabelGroup>
                        <CustomSelectInputField
                            :inputArray="userListArray"
                            :inputId="'user_id'"
                            :labelValue="'User'"
                            :dataValue="props.data.user_id"
                            :customValue="true"
                            :errorMessage="(form.errors) ? form.errors.user_id : '' "
                            v-model="form.user_id"
                        />
					</div>
					<div class="input-group grid grid-cols-1 lg:grid-cols-2 gap-6 col-span-1">
                        <CustomLabelGroup
                            :inputId="'edited_at'"
                            :labelValue="'Edited at'"
                            :dataValue="props.data ? formatToUserTimezone(props.data.edited_at, user.timezone, true) : '-'"
                            v-model="form.edited_at"
                        />
                        <CustomLabelGroup
                            :inputId="'created_at'"
                            :labelValue="'Created at'"
                            :dataValue="props.data ? formatToUserTimezone(props.data.created_at, user.timezone, true) : '-'"
                            v-model="form.created_at"
                        />
					</div>
				</div>
            </div>
        </form>
    </div>
</template>
