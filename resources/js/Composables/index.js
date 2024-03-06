import { useDark, useToggle } from '@vueuse/core'
import { reactive } from 'vue'
import { router } from "@inertiajs/vue3";
import { usePage } from '@inertiajs/vue3'

export const isDark = useDark()
export const toggleDarkMode = useToggle(isDark)

export const sidebarState = reactive({
    isOpen: window.innerWidth > 1024,
    isHovered: false,
    handleHover(value) {
        if (window.innerWidth < 1024) {
            return
        }
        sidebarState.isHovered = value
    },
    handleWindowResize() {
        if (window.innerWidth <= 1024) {
            sidebarState.isOpen = false
        } else {
            sidebarState.isOpen = true
        }
    },
})

export const scrolling = reactive({
    down: false,
    up: false,
})

let lastScrollTop = 0

export const handleScroll = () => {
    let st = window.pageYOffset || document.documentElement.scrollTop
    if (st > lastScrollTop) {
        // downscroll
        scrolling.down = true
        scrolling.up = false
    } else {
        // upscroll
        scrolling.down = false
        scrolling.up = true
        if (st == 0) {
            //  reset
            scrolling.down = false
            scrolling.up = false
        }
    }
    lastScrollTop = st <= 0 ? 0 : st // For Mobile or negative scrolling
}


// testing
export function cl(val) {
    console.log(val);
}

export function back() {
	router.post('/clear-session-messages');
    window.history.back();
}

export function convertToHumanReadable(str) {
    var i, frags = str.split(/_|-/);
    for (i=0; i<frags.length; i++) {
      frags[i] = frags[i].charAt(0).toUpperCase() + frags[i].slice(1);
    }
    return frags.join(' ');
}

// Check whether currently logged-in user has the role/permission assigned to them
export function usePermission() {
    const is = (name) => usePage().props.auth.user.roles.includes(name);
    const can = (name) => usePage().props.auth.user.permissions.includes(name);

    return { is, can };
}

// Replace hyphens (-) from object passed to details modal from datatable
export function replaceHyphensWithEmpty(obj) {
    for (let key in obj) {
        if (obj[key] === '-') {
            obj[key] = '';
        }
    }
}
