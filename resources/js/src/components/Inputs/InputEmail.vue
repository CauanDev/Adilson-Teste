<template>
    <div class="w-72">
        <div class="relative w-full min-w-[200px] h-10">
            <div class="absolute grid place-items-center text-blue-gray-500 top-2/4 right-3 -translate-y-[14px]">
                <div v-if="loading">
                    <svg class="inline w-[20px] text-gray-600 animate-spin fill-[#000099]"
                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor" />
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill" />
                    </svg>
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <input v-model="inputValue" @input="onInput" :disabled="loading"
                :class="{ 
                    'border-red-500 focus:border-red-500': !isValid && !isVerified, 
                    'border-green-500 focus:border-green-500': isVerified, 
                    'border-blue-gray-200 focus:border-gray-900': isValid && !isVerified 
                }"
                class="peer w-full h-full bg-transparent text-blue-gray-700 font-sans font-normal outline outline-0 focus:outline-0 disabled:bg-blue-gray-50 disabled:border-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 border focus:border-2 border-t-transparent focus:border-t-transparent text-sm px-3 py-2.5 rounded-[7px]"
                placeholder="" />
            <label
                :class="{ 
                    'text-red-500 before:border-red-500 after:border-red-500 peer-focus:text-red-500 peer-focus:before:border-red-500 peer-focus:after:border-red-500': !isValid && !isVerified, 
                    'text-green-500 peer-focus:text-green-500 before:border-green-500 peer-focus:before:border-green-500 after:border-green-500 peer-focus:after:border-green-500': isVerified, 
                    'text-gray-500 peer-focus:text-gray-900 before:border-blue-gray-200 peer-focus:before:border-gray-900 after:border-blue-gray-200 peer-focus:after:border-gray-900': isValid && !isVerified 
                }"
                class="flex w-full h-full select-none pointer-events-none absolute left-0 font-normal !overflow-visible truncate leading-tight peer-focus:leading-tight peer-disabled:text-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500 transition-all -top-1.5 peer-placeholder-shown:text-sm text-[11px] before:content[' '] before:block before:box-border before:w-2.5 before:h-1.5 before:mt-[6.5px] before:mr-1 peer-placeholder-shown:before:border-transparent before:rounded-tl-md before:border-t peer-focus:before:border-t-2 before:border-l peer-focus:before:border-l-2 before:pointer-events-none before:transition-all peer-disabled:before:border-transparent after:content[' '] after:block after:flex-grow after:box-border after:w-2.5 after:h-1.5 after:mt-[6.5px] after:ml-1 peer-placeholder-shown:after:border-transparent after:rounded-tr-md after:border-t peer-focus:after:border-t-2 after:border-r peer-focus:after:border-r-2 after:pointer-events-none after:transition-all peer-disabled:after:border-transparent peer-placeholder-shown:leading-[3.75]">
                {{ placeholder }}
            </label>
        </div>
    </div>
</template>

<script>
import http from "../../services/http"
export default {
    name: 'InputEmail',
    props: {
        placeholder: {
            type: String,
            default: 'Email'
        },
        modelValue: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            inputValue: this.modelValue,
            isValid: true,
            isVerified: false, // Adiciona a variável isVerified
            typingTimer: null,
            typingDelay: 1000,
            loading: false,
        };
    },
    methods: {
        onInput() {
            this.validateInput();

            clearTimeout(this.typingTimer);

            this.typingTimer = setTimeout(() => {
                this.verifyEmail();
            }, this.typingDelay);
        },
        validateInput() {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            this.isValid = emailPattern.test(this.inputValue);

            if (!this.isValid) {
                this.inputValue = this.inputValue;
            }
        },
        async verifyEmail() {
            if (this.isValid) {
                this.loading = true;
                try {
                    const data = await http.post('/verify-email', { "email": this.inputValue });
                    console.log(data.data)
                    if (data.data.state === "undeliverable") {
                        this.isValid = false;
                        this.isVerified = false;
                    } else {
                        this.isVerified = true;
                        this.$emit('update:modelValue', this.inputValue);
                    }
                } catch (error) {
                    console.error('Erro ao verificar o email:', error);
                    this.isValid = false;
                    this.isVerified = false;
                }
                this.loading = false;
            }
        }
    },
    watch: {
        modelValue(newValue) {
            this.inputValue = newValue;
        }
    }
};
</script>
