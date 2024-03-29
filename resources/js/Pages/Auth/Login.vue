<script setup>
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import { Link, useForm } from '@inertiajs/vue3'
import { setDateTimeWithOffset } from '@/Composables'
import { MailIcon, LockClosedIcon, LoginIcon } from '@heroicons/vue/outline'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
import Button from '@/Components/Button.vue'
import Checkbox from '@/Components/Checkbox.vue'
import GuestLayout from '@/Layouts/Guest.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import ValidationErrors from '@/Components/ValidationErrors.vue'
import PasswordInputField from '@/Components/PasswordInputField.vue'
import CustomTextInputField from '@/Components/CustomTextInputField.vue'

dayjs.extend(utc);
dayjs.extend(timezone);

defineProps({
    canResetPassword: Boolean,
    status: String,
})

const form = useForm({
    username: '',
    password: '',
	last_login: '',
    remember: false
})

const submit = () => {
    form.last_login = setDateTimeWithOffset(true);

    form.post(route('login'), {
        onSuccess: () => form.reset('password'),
    })
}
</script>

<template>
    <GuestLayout title="Log in">
        <ValidationErrors class="mb-4" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="grid gap-6 py-4">
                <div class="space-y-2">
                    <CustomTextInputField
                        :labelValue="'Username'"
                        :inputId="'username'"
                        :errorMessage="form?.errors?.username ?? '' "
                        v-model="form.username"
                    />
                </div>

                <div class="space-y-2">
                    <PasswordInputField
                        :labelValue="'Password'"
                        :inputId="'password'"
                        :errorMessage="form?.errors?.password ?? '' "
                        v-model="form.password"
                    />
                </div>

                <!-- <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>

                    <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-blue-500 hover:underline">
                        Forgot your password?
                    </Link>
                </div> -->

                <div class="pt-4">
                    <Button class="justify-center gap-2 w-full" :disabled="form.processing" v-slot="{iconSizeClasses}">
                        <LoginIcon aria-hidden="true" :class="iconSizeClasses" />
                        <span>Log in</span>
                    </Button>
                </div>

                <!-- <p class="text-sm text-gray-600 dark:text-gray-400">
                    Don't have an account?
                    <Link :href="route('register')" class="text-blue-500 hover:underline">
                        Register
                    </Link>
                </p> -->
            </div>
        </form>
    </GuestLayout>
</template>
