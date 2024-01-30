<script setup>
import { cl, back } from '@/Composables'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { PlusIcon } from '@/Components/Icons/solid'
import { DeleteIcon } from '@/Components/Icons/outline'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Checkbox from '@/Components/Checkbox.vue'
import CustomTextInputField from '@/Components/CustomTextInputField.vue'
import CustomSelectInputField from '@/Components/CustomSelectInputField.vue'
import CustomDateTimeInputField from '@/Components/CustomDateTimeInputField.vue'
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue'
import dayjs from 'dayjs';

// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    selectedRowData: {
        type: Object,
        default: () => ({}),
    },
})

</script>

<template>
    <div class="grid grid-cols-1 lg:grid-cols-3 p-9 gap-6">
        <div class="col-span-1 flex flex-col divide-y divide-gray-500 gap-6">
            <div class="input-group">
                <Label
                    :value="props.selectedRowData.first_name + ' ' + (props.selectedRowData.last_name ?? '')"
                    class="mt-4 !text-2xl !font-bold pl-2"
                >
                </Label>
                <CustomLabelGroup
                    :inputId="'dob'"
                    :labelValue="'DOB:'"
                    :dataValue="props.selectedRowData.date_of_birth ?? ''"
                />
                <CustomLabelGroup
                    :inputId="'country'"
                    :labelValue="'Country:'"
                    :dataValue="props.selectedRowData.country ?? ''"
                />
                <CustomLabelGroup
                    :inputId="'address'"
                    :labelValue="'Address:'"
                    :dataValue="props.selectedRowData.address ?? ''"
                />
                <CustomLabelGroup
                    :inputId="'phone_number'"
                    :labelValue="'Phone Number:'"
                    :dataValue="props.selectedRowData.phone_number ?? ''"
                />
                <CustomLabelGroup
                    :inputId="'phone_number_alt_1'"
                    :labelValue="'Alt Phone Number 1:'"
                    :dataValue="props.selectedRowData.phone_number_alt_1 ?? ''"
                />
                <CustomLabelGroup
                    :inputId="'phone_number_alt_2'"
                    :labelValue="'Alt Phone Number 2:'"
                    :dataValue="props.selectedRowData.phone_number_alt_2 ?? ''"
                />
                <CustomLabelGroup
                    :inputId="'phone_number_alt_3'"
                    :labelValue="'Alt Phone Number 3:'"
                    :dataValue="props.selectedRowData.phone_number_alt_3 ?? ''"
                />
                <CustomLabelGroup
                    :inputId="'email'"
                    :labelValue="'Email:'"
                    :dataValue="props.selectedRowData.email ?? ''"
                />
                <CustomLabelGroup
                    :inputId="'email_alt_1'"
                    :labelValue="'Alt Email 1:'"
                    :dataValue="props.selectedRowData.email_alt_1 ?? ''"
                />
                <CustomLabelGroup
                    :inputId="'email_alt_2'"
                    :labelValue="'Alt Email 2:'"
                    :dataValue="props.selectedRowData.email_alt_2 ?? ''"
                />
                <CustomLabelGroup
                    :inputId="'email_alt_3'"
                    :labelValue="'Alt Email 3:'"
                    :dataValue="props.selectedRowData.email_alt_3 ?? ''"
                />
            </div>
            <div class="input-group">
                <CustomLabelGroup
                    :inputId="'leadFrontEditedAt'"
                    :labelValue="'Edited at'"
                    :dataValue="props.selectedRowData.id"
                />
                <CustomLabelGroup
                    :inputId="'leadFrontCreatedAt'"
                    :labelValue="'Created at'"
                    :dataValue="'hi'"
                />
                <CustomLabelGroup
                    :inputId="'leadFrontCreatedAt'"
                    :labelValue="'Created at'"
                    :dataValue="'hi'"
                />
                <CustomLabelGroup
                    :inputId="'leadFrontCreatedAt'"
                    :labelValue="'Created at'"
                    :dataValue="'hi'"
                />
            </div>
        </div>
        <div class="col-span-2 flex flex-col divide-y divide-gray-500 gap-6">
            <div class="input-group grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="col-span-1">
                    <CustomLabelGroup
                        :inputId="'account_manager'"
                        :labelValue="'Account Manager'"
                        :dataValue="props.selectedRowData.account_manager"
                    />
                    <CustomLabelGroup
                        :inputId="'agents_book'"
                        :labelValue="'Agents Book'"
                        :dataValue="props.selectedRowData.agents_book"
                    />
                    <CustomLabelGroup
                        :inputId="'campaign_product'"
                        :labelValue="'Campaign Product'"
                        :dataValue="props.selectedRowData.campaign_product"
                    />
                    <CustomLabelGroup
                        :inputId="'data_code'"
                        :labelValue="'Data Code'"
                        :dataValue="props.selectedRowData.data_code"
                    />
                    <CustomLabelGroup
                        :inputId="'data_source'"
                        :labelValue="'Data Source'"
                        :dataValue="props.selectedRowData.data_source"
                    />
                    <CustomLabelGroup
                        :inputId="'data_type'"
                        :labelValue="'Data Type'"
                        :dataValue="props.selectedRowData.data_type"
                    />
                    <CustomLabelGroup
                        :inputId="'occupation'"
                        :labelValue="'Occupation'"
                        :dataValue="props.selectedRowData.occupation"
                    />
                    <CustomLabelGroup
                        :inputId="'sdm'"
                        :labelValue="'Sdm'"
                        :dataValue="props.selectedRowData.sdm"
                    />
                    <CustomLabelGroup
                        :inputId="'vc'"
                        :labelValue="'VC'"
                        :dataValue="props.selectedRowData.vc"
                    />
                </div>
                <div class="col-span-1">
                    <CustomLabelGroup
                        :inputId="'leadFrontEditedAt'"
                        :labelValue="'Edited at'"
                        :dataValue="props.selectedRowData.id"
                    />
                    <CustomLabelGroup
                        :inputId="'leadFrontCreatedAt'"
                        :labelValue="'Created at'"
                        :dataValue="props.selectedRowData.id"
                    />
                    <CustomLabelGroup
                        :inputId="'leadFrontCreatedAt'"
                        :labelValue="'Created at'"
                        :dataValue="props.selectedRowData.id"
                    />
                    <CustomLabelGroup
                        :inputId="'leadFrontCreatedAt'"
                        :labelValue="'Created at'"
                        :dataValue="props.selectedRowData.id"
                    />
                    <CustomLabelGroup
                        :inputId="'leadFrontEditedAt'"
                        :labelValue="'Edited at'"
                        :dataValue="props.selectedRowData.id"
                    />
                    <CustomLabelGroup
                        :inputId="'leadFrontCreatedAt'"
                        :labelValue="'Created at'"
                        :dataValue="props.selectedRowData.id"
                    />
                    <CustomLabelGroup
                        :inputId="'leadFrontCreatedAt'"
                        :labelValue="'Created at'"
                        :dataValue="props.selectedRowData.id"
                    />
                    <CustomLabelGroup
                        :inputId="'leadFrontCreatedAt'"
                        :labelValue="'Created at'"
                        :dataValue="props.selectedRowData.id"
                    />
                </div>
                <div class="col-span-1">
                    <CustomLabelGroup
                        :inputId="'leadFrontEditedAt'"
                        :labelValue="'Edited at'"
                        :dataValue="props.selectedRowData.id"
                    />
                    <CustomLabelGroup
                        :inputId="'leadFrontCreatedAt'"
                        :labelValue="'Created at'"
                        :dataValue="props.selectedRowData.id"
                    />
                    <CustomLabelGroup
                        :inputId="'leadFrontCreatedAt'"
                        :labelValue="'Created at'"
                        :dataValue="props.selectedRowData.id"
                    />
                    <CustomLabelGroup
                        :inputId="'leadFrontCreatedAt'"
                        :labelValue="'Created at'"
                        :dataValue="props.selectedRowData.id"
                    />
                    <CustomLabelGroup
                        :inputId="'leadFrontEditedAt'"
                        :labelValue="'Edited at'"
                        :dataValue="props.selectedRowData.id"
                    />
                    <CustomLabelGroup
                        :inputId="'leadFrontCreatedAt'"
                        :labelValue="'Created at'"
                        :dataValue="props.selectedRowData.id"
                    />
                    <CustomLabelGroup
                        :inputId="'leadFrontCreatedAt'"
                        :labelValue="'Created at'"
                        :dataValue="props.selectedRowData.id"
                    />
                    <CustomLabelGroup
                        :inputId="'leadFrontCreatedAt'"
                        :labelValue="'Created at'"
                        :dataValue="props.selectedRowData.id"
                    />
                </div>
            </div>
            <div class="input-group">
                <CustomLabelGroup
                    :inputId="'leadFrontEditedAt'"
                    :labelValue="'Edited at'"
                    :dataValue="props.selectedRowData.id"
                />
                <CustomLabelGroup
                    :inputId="'leadFrontCreatedAt'"
                    :labelValue="'Created at'"
                    :dataValue="'hi'"
                />
                <CustomLabelGroup
                    :inputId="'leadFrontCreatedAt'"
                    :labelValue="'Created at'"
                    :dataValue="'hi'"
                />
                <CustomLabelGroup
                    :inputId="'leadFrontCreatedAt'"
                    :labelValue="'Created at'"
                    :dataValue="'hi'"
                />
            </div>
        </div>
    </div>
    <div class="flex justify-end p-9">
        <div class="rounded-md shadow-lg flex flex-row gap-2">
            <Button 
                :variant="'secondary'"
                :size="'sm'"
                class="justify-center px-6 py-2 rounded-md border border-purple-500" 
                @click="cl(props.selectedRowData)"
            >
                Reset
            </Button>
            <Button 
                :size="'sm'"
                class="justify-center px-6 py-2 rounded-md border border-gray-500" 
                @click=""
            >
                Apply
            </Button>
        </div>
    </div>
</template>
