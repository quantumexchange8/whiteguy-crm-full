// Extra icons

import { defineComponent } from 'vue'

export const EmptyCircleIcon = defineComponent({
    setup() {
        return () => (
            <svg
                viewBox="0 0 20 20"
                fill="currentColor"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M10 18C12.1217 18 14.1566 17.1571 15.6569 15.6569C17.1571 14.1566 18 12.1217 18 10C18 7.87827 17.1571 5.84344 15.6569 4.34315C14.1566 2.84285 12.1217 2 10 2C7.87827 2 5.84344 2.84285 4.34315 4.34315C2.84285 5.84344 2 7.87827 2 10C2 12.1217 2.84285 14.1566 4.34315 15.6569C5.84344 17.1571 7.87827 18 10 18Z"
                />
            </svg>
        )
    },
})

export const ThreeDotsVertical = defineComponent({
    setup() {
        return () => (
            <svg 
                style="color: rgb(112, 112, 112);" 
                xmlns="http://www.w3.org/2000/svg" 
                width="16" 
                height="16" 
                fill="currentColor" 
                class="bi bi-three-dots-vertical" 
                viewBox="0 0 16 16"
            > 
            
                <path 
                    d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" 
                    fill="#707070">
                </path>
            </svg>
        )
    },
})

export const TimesCircleIcon = defineComponent({
    setup() {
        return () => (
            <svg 
                style="color: rgb(212, 122, 37);" 
                xmlns="http://www.w3.org/2000/svg"
                enable-background="new 0 0 24 24" 
                width="16" 
                height="16"
                viewBox="0 0 24 24"
            >
                <path 
                    d="M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z M15.7,14.3c0.4,0.4,0.4,1,0,1.4c-0.4,0.4-1,0.4-1.4,0L12,13.4l-2.3,2.3c-0.4,0.4-1,0.4-1.4,0c-0.4-0.4-0.4-1,0-1.4l2.3-2.3L8.3,9.7c-0.4-0.4-0.4-1,0-1.4c0.4-0.4,1-0.4,1.4,0l2.3,2.3l2.3-2.3c0.4-0.4,1-0.4,1.4,0c0.4,0.4,0.4,1,0,1.4L13.4,12L15.7,14.3z" 
                    fill="#d47a25">
                </path>
            </svg>
        )
    },
})

export const PlusIcon = defineComponent({
    setup() {
        return () => (
            <svg 
                style="color: white" 
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor" 
                class="bi bi-plus-lg"
                width="16" 
                height="16"
                viewBox="0 0 16 16"
            >
                <path 
                    d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z" 
                    fill-rule="evenodd"
                    fill="white">
                </path>
            </svg>
        )
    },
})
