<template>
    <div :class="{'w-72':!this.small,'w-[190px]':this.small}">
        <div class="relative h-10">
            <input v-model="inputValue" @input="validateInput"
                class="peer w-full h-full bg-transparent text-blue-gray-700 font-sans font-normal outline-none focus:outline-none disabled:bg-blue-gray-50 disabled:border-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 border focus:border-2 border-t-transparent focus:border-t-transparent text-sm px-3 py-2.5 rounded-[7px] border-blue-gray-200 focus:border-gray-900"
                placeholder="" />
            <label
                class="flex w-full h-full select-none pointer-events-none absolute left-0 font-normal overflow-visible truncate peer-placeholder-shown:text-blue-gray-500 leading-tight peer-focus:leading-tight peer-disabled:text-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500 transition-all -top-1.5 peer-placeholder-shown:text-sm text-[11px] 
                before:content-[' '] before:block before:box-border before:w-2.5 before:h-1.5 before:mt-[6.5px] before:mr-1 peer-placeholder-shown:before:border-transparent before:rounded-tl-md before:border-t peer-focus:before:border-t-2 before:border-l peer-focus:before:border-l-2 
                before:pointer-events-none before:transition-all peer-disabled:before:border-transparent 
                after:content-[' '] after:block after:flex-grow after:box-border after:w-2.5 after:h-1.5 after:mt-[6.5px] after:ml-1 peer-placeholder-shown:after:border-transparent after:rounded-tr-md after:border-t peer-focus:after:border-t-2 after:border-r peer-focus:after:border-r-2 
                after:pointer-events-none after:transition-all peer-disabled:after:border-transparent 
                peer-placeholder-shown:leading-[3.75] text-gray-500 peer-focus:text-gray-900 before:border-blue-gray-200 peer-focus:before:border-gray-900 after:border-blue-gray-200 peer-focus:after:border-gray-900">
                {{ placeholder }}
            </label>
            <p class="absolute right-[2px] top-1/2 transform -translate-y-1/2 text-blue-gray-700 text-xs px-0.5">
                {{ remainingChars }}
            </p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'InputName',
    props: {
        placeholder: {
            type: String,
            default: 'Username'
        },
        modelValue: {
            type: String,
            default: ''
        },
        small: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            inputValue: this.modelValue
        };
    },
    computed: {
        remainingChars() {
            return 25 - this.inputValue.length;
        }
    },
    methods: {
        validateInput() {
            // Permitir letras, espaços e até 3 dígitos
            let digitCount = 0;
            const cleanedInput = this.inputValue
                .split('')
                .filter(char => {
                    if (/\d/.test(char)) {
                        // Contar dígitos e permitir no máximo 3
                        if (digitCount < 3) {
                            digitCount++;
                            return true;
                        }
                        return false;
                    }
                    return true; // Permitir letras e outros caracteres
                })
                .join('')
                .slice(0, 25);  // Limitar o comprimento total a 25 caracteres

            this.inputValue = cleanedInput;

            // Emit the updated value to the parent component
            this.$emit('update:modelValue', this.inputValue);
        }
    },
    watch: {
        modelValue(newValue) {
            this.inputValue = newValue;
        }
    }
};
</script>
