<template>
    <div id="popup-modal" tabindex="-1"
        class="fixed inset-0 flex items-center justify-center z-50 overflow-y-auto overflow-x-hidden">
        <div class="relative p-4 w-full max-w-md max-h-full bg-white rounded-lg shadow">
            <button type="button" class=" bg-transparent hover:bg-gray-200 hover:text-black-200 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="default-modal" @click="this.$emit('close')">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                  </svg>
                <span class="sr-only">Close modal</span>
              </button>
            <div class="p-4 text-center">
                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="black" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <h3 class="mb-5 text-lg font-normal text-black">{{title}}</h3>
                <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2" @click="this.$emit('greenButton')">{{greenButton}}</button>
                <button type="button" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2" @click="this.$emit('close')">{{redButton}}</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ConfirmationModal",
    props:{
        title:{
            required:true
        },
        greenButton:{
            required:true
        },
        redButton:{
            required:true
        }
    }
}
</script>
