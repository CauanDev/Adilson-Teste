<template>
    <div class="w-full h-full absolute bg-blue"></div>
    <div class="absolute z-50 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 ">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow-lg border-2 border-slate-300">
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-black-200 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                    data-modal-hide="default-modal" @click="this.$emit('close')">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 md:p-5 flex flex-col text-center items-center">
                    <svg class="w-12 mb-2" viewBox="0 0 117 117" version="1.1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink">
                        <title />
                        <desc />
                        <defs />
                        <g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1">
                            <g fill-rule="nonzero" id="correct">
                                <path
                                    d="M34.5,55.1 C32.9,53.5 30.3,53.5 28.7,55.1 C27.1,56.7 27.1,59.3 28.7,60.9 L47.6,79.8 C48.4,80.6 49.4,81 50.5,81 C50.6,81 50.6,81 50.7,81 C51.8,80.9 52.9,80.4 53.7,79.5 L101,22.8 C102.4,21.1 102.2,18.5 100.5,17 C98.8,15.6 96.2,15.8 94.7,17.5 L50.2,70.8 L34.5,55.1 Z"
                                    fill="#17AB13" id="Shape" />
                                <path
                                    d="M89.1,9.3 C66.1,-5.1 36.6,-1.7 17.4,17.5 C-5.2,40.1 -5.2,77 17.4,99.6 C28.7,110.9 43.6,116.6 58.4,116.6 C73.2,116.6 88.1,110.9 99.4,99.6 C118.7,80.3 122,50.7 107.5,27.7 C106.3,25.8 103.8,25.2 101.9,26.4 C100,27.6 99.4,30.1 100.6,32 C113.1,51.8 110.2,77.2 93.6,93.8 C74.2,113.2 42.5,113.2 23.1,93.8 C3.7,74.4 3.7,42.7 23.1,23.3 C39.7,6.8 65,3.9 84.8,16.2 C86.7,17.4 89.2,16.8 90.4,14.9 C91.6,13 91,10.5 89.1,9.3 Z"
                                    fill="#4A4A4A" id="Shape" />
                            </g>
                        </g>
                    </svg>
                    <h3 class="text-2xl font-bold text-black ">{{ title }} com Sucesso</h3>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
export default {
    name: 'SucessWarning',
    emits: ['close'],
    props: {
        title: String
    }
}
</script>