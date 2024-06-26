import { useDark, useToggle } from '@vueuse/core'
import { reactive } from 'vue'
import { router } from "@inertiajs/vue3";
import { usePage } from '@inertiajs/vue3'
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
dayjs.extend(utc);
dayjs.extend(timezone);

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
    const has = (name) => usePage().props.auth.user.permissions[name];
    // const is = (name) => usePage().props.auth.user.roles.includes(name);
    const can = (name) => usePage().props.auth.user.permissions.includes(name);

    return { has, can };
}

// Replace hyphens (-) from object passed to details modal from datatable
export function replaceHyphensWithEmpty(obj) {
    for (let key in obj) {
        if (obj[key] === '-') {
            obj[key] = '';
        }
    }
}

// Processes the response received from axios get request, into an object with the key as the desired value along with its item of 'value'
export function populateArrayFromResponse(responseData, targetArray, idKey, valueKey) {
    responseData.forEach(item => {
        targetArray[item[idKey]] = {
            value: item[valueKey],
        };
    });
}

// Convert the predefined array to an object with set of indexes
export function convertToIndexedValueObject(array) {
    return array.reduce((acc, value, index) => {
        acc[index + 1] = { value };
        return acc;
    }, {});
}

/**
 * Set the current date/datetime with the UTC offset of +08 for standardization.
 * @param {boolean} withTime - Indicates whether to include time in the output.
 * @returns {string} - The formatted date/datetime string.
 */
export function setDateTimeWithOffset(withTime = false) {
    const currentDate = dayjs().utcOffset('+08:00');

    if (withTime) {
        return currentDate.format('YYYY-MM-DD HH:mm:ss.SSSSSSZZ');
    }

    return currentDate.format('YYYY-MM-DD');
}

/**
 * Set the form date/datetime input with the UTC offset of +08 for standardization.
 * @param {string | Date} date - The input date string or Date object.
 * @param {boolean} withTime - Indicates whether to include time in the output.
 * @returns {string} - The formatted date/datetime string.
 */
export function setFormattedDateTimeWithOffset(date, withTime = false) {
    const dateTime = dayjs(date).utcOffset('+08:00');

    if (withTime) {
        return dateTime.format('YYYY-MM-DD HH:mm:ss.SSSSSSZZ');
    }

    return dateTime.format('YYYY-MM-DD');
}

/**
 * Convert date/datetime retrieved from the database into a new date/datetime based on the user's timezone.
 * @param {string | Date} date - The input date string or Date object.
 * @param {string} timezone - The user's timezone.
 * @param {boolean} withTime - Indicates whether to include time in the output.
 * @returns {string} - The formatted date/datetime string.
 */
export function formatToUserTimezone(date, timezone, withTime = false) {
    const convertedDate = dayjs(date).tz(timezone);

    if (withTime) {
        return convertedDate.format('DD/MM/YYYY HH:mm:ss');
    }

    return convertedDate.format('DD/MM/YYYY');
}
