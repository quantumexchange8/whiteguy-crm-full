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

export const CheckCircleFillIcon = defineComponent({
    setup() {
        return () => (
            <svg 
                style="color: rgb(76, 175, 80);" 
                xmlns="http://www.w3.org/2000/svg" 
                width="24" 
                height="24"
                fill="currentColor" 
                class="bi bi-check-circle-fill" 
                viewBox="0 0 19 19"
            > 
                <path 
                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 
                    0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" 
                    fill="#4caf50"
                >
                </path> 
            </svg>
        )
    },
})

export const QuestionCircleFillIcon = defineComponent({
    setup() {
        return () => (
            <svg 
                xmlns="http://www.w3.org/2000/svg" 
                width="16" 
                height="16" 
                fill="#3fa7e8" 
                class="bi bi-question-circle-fill" 
                viewBox="0 0 16 16"
            > 
                <path 
                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 
                    1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 
                    1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 
                    1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 
                    0 .241.247zm2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 
                    0 .533.425.927 1.01.927z"
                /> 
            </svg>
        )
    },
})
