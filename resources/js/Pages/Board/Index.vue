<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'

const pageTitle = "Board";

</script>

<template>
	<AuthenticatedLayout title="Board">
		<template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    {{ pageTitle }}
                </h2>
            </div>
            <Breadcrumbs 
                :title="pageTitle"
            />
		</template>
	</AuthenticatedLayout>
</template>
