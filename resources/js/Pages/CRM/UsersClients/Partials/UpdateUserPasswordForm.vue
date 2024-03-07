<script setup>
import { ref } from 'vue'
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
    form.put(route('users-clients.updateUserPassword'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation')
                passwordInput.value.focus()
            }
        },
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

        <form @submit.prevent="formSubmit" class="mt-6 space-y-6">
            <CustomTextInputField
                :labelValue="'Password'"
                :inputId="'password'"
                :withTooltip="true"
                :errorMessage="form?.errors?.password ?? '' "
                v-model="form.password"
            >
                <p class="font-bold">The password must contain:</p>
                <ul class="pl-6 list-disc font-semibold">
                    <li>at least 8 characters.</li>
                    <li>at least one uppercase letter.</li>
                    <li>at least one lowercase letter.</li>
                    <li>at least one symbol.</li>
                    <li>at least one number.</li>
                </ul>
            </CustomTextInputField>
            <PasswordInputField
                :labelValue="'Confirm Password'"
                :inputId="'password_confirmation'"
                :errorMessage="form?.errors?.password_confirmation ?? '' "
                v-model="form.password_confirmation"
            />
            <!-- <div>
                <Label for="password" value="New Password" />
                <Input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />

                <InputError :message="form.errors.password" class="mt-2" />
            </div>
            <div>
                <Label for="password_confirmation" value="Confirm Password" />
                <Input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2"
                />
            </div> -->
            <div class="flex items-center gap-4 pl-3">
                <Button :disabled="form.processing">Save</Button>
                <Transition
                    enter-from-class="opacity-0"
                    leave-to-class="opacity-0"
                    class="transition ease-in-out"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
