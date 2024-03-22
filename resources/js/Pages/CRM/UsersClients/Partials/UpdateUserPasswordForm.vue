<script setup>
import { ref } from 'vue'
import { EyeIcon } from '@/Components/Icons/outline';
import { useForm } from '@inertiajs/vue3'
import Label from '@/Components/Label.vue'
import Input from '@/Components/Input.vue'
import InputError from '@/Components/InputError.vue'
import Button from '@/Components/Button.vue'
import CustomTextInputField from '@/Components/CustomTextInputField.vue'
import PasswordInputField from '@/Components/PasswordInputField.vue'

const props = defineProps({
    id: {
        type: [String, Number],
    }
})

const passwordInput = ref(null);

const form = useForm({
    id: props.id,
    password: '',
    password_confirmation: '',
})

const formSubmit = () => {
    console.log(form.password);
    form.put(route('users-clients.updateUserPassword'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Update Password
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Ensure your account is using a long, random password to stay
                secure.
            </p>
        </header>

        <form @submit.prevent="formSubmit" class="mt-6 space-y-6" autocomplete="off">
            <PasswordInputField
                :labelValue="'New Password'"
                :inputId="'password'"
                :withTooltip="true"
                :errorMessage="form?.errors?.password ?? '' "
                v-model="form.password"
            />
            <PasswordInputField
                :labelValue="'Confirm Password'"
                :inputId="'password_confirmation'"
                :errorMessage="form?.errors?.password_confirmation ?? '' "
                v-model="form.password_confirmation"
            />
            <div class="flex items-center gap-4 pl-3">
                <Button :disabled="form.processing">Save</Button>
            </div>
        </form>
    </section>
</template>
