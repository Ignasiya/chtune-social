<script setup>
import DeleteUserForm from './DeleteUserForm.vue';
import UpdatePasswordForm from './UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './UpdateProfileInformationForm.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <div class="space-y-6">
        <div class="p-4 sm:p-8 bg-white dark:bg-neutral-800 shadow sm:rounded-lg">
            <UpdateProfileInformationForm
                :must-verify-email="mustVerifyEmail"
                :status="status"
                class="max-w-xl"
            />
        </div>

        <div class="p-4 sm:p-8 bg-white dark:bg-neutral-800 shadow sm:rounded-lg">
            <UpdatePasswordForm class="max-w-xl"/>
        </div>

        <div class="p-4 sm:p-8 bg-white dark:bg-neutral-800 shadow sm:rounded-lg">
            <DeleteUserForm class="max-w-xl"/>
        </div>
    </div>
</template>
