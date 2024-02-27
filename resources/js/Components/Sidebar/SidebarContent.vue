<script setup>
import PerfectScrollbar from '@/Components/PerfectScrollbar.vue'
import SidebarLink from '@/Components/Sidebar/SidebarLink.vue'
import { DashboardIcon } from '@/Components/Icons/outline'
import SidebarCollapsible from '@/Components/Sidebar/SidebarCollapsible.vue'
import SidebarCollapsibleItem from '@/Components/Sidebar/SidebarCollapsibleItem.vue'
import { TemplateIcon } from '@heroicons/vue/outline'
</script>

<template>
    <PerfectScrollbar 
        tagname="nav" 
        aria-label="main" 
        class="relative flex flex-col flex-1 max-h-full gap-4 px-3"
    >
        <SidebarLink 
            title="Dashboard" 
            :href="route('dashboard')" 
            :active="route().current('dashboard')"
        >
            <template #icon>
                <DashboardIcon 
                    class="flex-shrink-0 w-6 h-6" 
                    aria-hidden="true" 
                />
            </template>
        </SidebarLink>

        <SidebarCollapsible 
            title="CRM" 
            :active="route().current('crm.*')"
        >
            <template #icon>
                <TemplateIcon 
                    class="flex-shrink-0 w-6 h-6" 
                    aria-hidden="true" 
                />
            </template>

            <SidebarCollapsibleItem 
                :href="route('crm.account-manager-profiles')" 
                title="Account Manager Profiles" 
                :active="route().current('crm.account-manager-profiles')" 
            />
            <SidebarCollapsibleItem 
                :href="route('crm.announcements')" 
                title="Announcements" 
                :active="route().current('crm.announcements')" 
            />
            <SidebarCollapsibleItem 
                :href="route('crm.applications')" 
                title="Applications" 
                :active="route().current('crm.applications')" 
            />
            <SidebarCollapsibleItem 
                :href="route('crm.documents')" 
                title="Documents" 
                :active="route().current('crm.documents')" 
            />
            <SidebarCollapsibleItem 
                :href="route('crm.internal-news')" 
                title="Internal News" 
                :active="route().current('crm.internal-news')" 
            />
            <SidebarCollapsibleItem 
                :href="route('lead-fronts.index')" 
                title="Lead Fronts" 
                :active="route().current('lead-fronts.index')" 
            />
            <SidebarCollapsibleItem 
                :href="route('crm.lead-uploads')" 
                title="Lead Uploads" 
                :active="route().current('crm.lead-uploads')" 
            />
            <SidebarCollapsibleItem 
                :href="route('leads.index')" 
                title="Leads" 
                :active="route().current('leads.index')" 
            />
            <SidebarCollapsibleItem 
                :href="route('crm.notifications')" 
                title="Notifications" 
                :active="route().current('crm.notifications')" 
            />
            <SidebarCollapsibleItem 
                :href="route('crm.orders')" 
                title="Orders" 
                :active="route().current('crm.orders')" 
            />
            <SidebarCollapsibleItem 
                :href="route('crm.payment-methods')" 
                title="Payment Methods" 
                :active="route().current('crm.payment-methods')" 
            />
            <SidebarCollapsibleItem 
                :href="route('crm.payment-submissions')" 
                title="Payment Submissions" 
                :active="route().current('crm.payment-submissions')" 
            />
            <SidebarCollapsibleItem 
                :href="route('crm.private-messages')" 
                title="Private Messages" 
                :active="route().current('crm.private-messages')" 
            />
            <SidebarCollapsibleItem 
                :href="route('crm.sale-orders')" 
                title="Sale Orders" 
                :active="route().current('crm.sale-orders')" 
            />
            <SidebarCollapsibleItem 
                :href="route('users-clients.index')" 
                title="Users / Clients" 
                :active="route().current('users-clients.index')" 
            />
        </SidebarCollapsible>

        <SidebarCollapsible 
            title="Resources" 
            :active="route().current('resources.*')"
        >
            <template #icon>
                <TemplateIcon 
                    class="flex-shrink-0 w-6 h-6" 
                    aria-hidden="true" 
                />
            </template>

            <SidebarCollapsibleItem 
                :href="route('resources.index')"
                title="Resources" 
                :active="route().current('resources.index')" 
            />
        </SidebarCollapsible>

        <SidebarCollapsible 
            title="News" 
            :active="route().current('news.*')"
        >
            <template #icon>
                <TemplateIcon 
                    class="flex-shrink-0 w-6 h-6" 
                    aria-hidden="true" 
                />
            </template>

            <SidebarCollapsibleItem 
                :href="route('news.index')"
                title="News" 
                :active="route().current('news.index')" 
            />
        </SidebarCollapsible>

        <SidebarCollapsible 
            title="Statistics" 
            :active="route().current('statistics.*')"
        >
            <template #icon>
                <TemplateIcon 
                    class="flex-shrink-0 w-6 h-6" 
                    aria-hidden="true" 
                />
            </template>

            <SidebarCollapsibleItem 
                :href="route('statistics.index')"
                title="Statistics" 
                :active="route().current('statistics.index')" 
            />
        </SidebarCollapsible>

        <SidebarCollapsible 
            title="Performance" 
            :active="route().current('performance.*')"
        >
            <template #icon>
                <TemplateIcon 
                    class="flex-shrink-0 w-6 h-6" 
                    aria-hidden="true" 
                />
            </template>

            <SidebarCollapsibleItem 
                :href="route('performance.index')"
                title="Performance" 
                :active="route().current('performance.index')" 
            />
        </SidebarCollapsible>

        <SidebarCollapsible 
            title="Board" 
            :active="route().current('board.*')"
        >
            <template #icon>
                <TemplateIcon 
                    class="flex-shrink-0 w-6 h-6" 
                    aria-hidden="true" 
                />
            </template>

            <SidebarCollapsibleItem 
                :href="route('board.index')"
                title="Board" 
                :active="route().current('board.index')" 
            />
        </SidebarCollapsible>

        <SidebarCollapsible 
            title="Logs" 
            :active="route().current('logs.*')"
        >
            <template #icon>
                <TemplateIcon 
                    class="flex-shrink-0 w-6 h-6" 
                    aria-hidden="true" 
                />
            </template>

            <SidebarCollapsibleItem 
                :href="route('logs.index')"
                title="Logs" 
                :active="route().current('logs.index')" 
            />
        </SidebarCollapsible>

        <SidebarLink 
            title="Instant Message" 
            :href="route('instant-message.index')" 
            :active="route().current('instant-message.index')"
        >
            <template #icon>
                <DashboardIcon 
                    class="flex-shrink-0 w-6 h-6" 
                    aria-hidden="true" 
                />
            </template>
        </SidebarLink>

        <!-- <SidebarCollapsible title="Components" :active="route().current('components.*')">
            <template #icon>
                <TemplateIcon class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </template>

            <SidebarCollapsibleItem :href="route('components.buttons')" title="Buttons" :active="route().current('components.buttons')" />
        </SidebarCollapsible> -->
        <!-- Examples -->
        <!--
        => External link example
        <SidebarLink
            title="Github"
            href="https://github.com/kamona-wd/kui-laravel-breeze"
            external
            target="_blank"
        >
        </SidebarLink>

        => Collapsible examples
        <SidebarCollapsible title="Users" :active="$page.url.startsWith('/users')">
            <SidebarCollapsibleItem :href="route('users.index')" title="List" :active="$page.url === '/users/index'" />
            <SidebarCollapsibleItem :href="route('users.create')" title="Create" :active="$page.url === '/users/create'" />
        </SidebarCollapsible>

        <SidebarCollapsible title="Users" :active="usePage().url.value.startsWith('/users')">
            <template #icon>
                <UserIcon
                    class="flex-shrink-0 w-6 h-6"
                    aria-hidden="true"
                />
            </template>

            <SidebarCollapsibleItem :href="route('users.index')" title="List" :active="route().current('users.index')" />
            <SidebarCollapsibleItem :href="route('users.create')" title="Create" :active="route().current('users.create')" />
        </SidebarCollapsible>-->
    </PerfectScrollbar>
</template>
