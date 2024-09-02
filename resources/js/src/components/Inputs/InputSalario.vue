<template>
    <div :class="{'w-72': !small, 'w-[190px]': small}">
        <div class="relative w-full h-10">
            <input v-model="inputValue" @input="validateInput" :disabled="this.desativado"
                :class="{ 'text-center': this.desativado }"
                class="peer w-full h-full bg-transparent text-blue-gray-700 font-sans font-normal outline outline-0 focus:outline-0 disabled:bg-blue-gray-50 disabled:border-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 border focus:border-2 border-t-transparent focus:border-t-transparent text-sm px-3 py-2.5 rounded-[7px] border-blue-gray-200 focus:border-gray-900"
                placeholder="" />
            <label
                class="flex w-full h-full select-none pointer-events-none absolute left-0 font-normal !overflow-visible truncate peer-placeholder-shown:text-blue-gray-500 leading-tight peer-focus:leading-tight peer-disabled:text-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500 transition-all -top-1.5 peer-placeholder-shown:text-sm text-[11px] peer-focus:text-[11px] before:content[' '] before:block before:box-border before:w-2.5 before:h-1.5 before:mt-[6.5px] before:mr-1 peer-placeholder-shown:before:border-transparent before:rounded-tl-md before:border-t peer-focus:before:border-t-2 before:border-l peer-focus:before:border-l-2 before:pointer-events-none before:transition-all peer-disabled:before:border-transparent after:content[' '] after:block after:flex-grow after:box-border after:w-2.5 after:h-1.5 after:mt-[6.5px] after:ml-1 peer-placeholder-shown:after:border-transparent after:rounded-tr-md after:border-t peer-focus:after:border-t-2 after:border-r peer-focus:after:border-r-2 after:pointer-events-none after:transition-all peer-disabled:after:border-transparent peer-placeholder-shown:leading-[3.75] text-gray-500 peer-focus:text-gray-900 before:border-blue-gray-200 peer-focus:before:!border-gray-900 after:border-blue-gray-200 peer-focus:after:!border-gray-900">
                {{ placeholder }}
            </label>
        </div>
        <span v-if="!isValid && inputValue.length > 0" class="text-red-500 text-xs">Valor inválido</span>
    </div>
</template>
<script>
export default {
    name: 'InputSalario',
    props: {
        placeholder: {
            type: String,
            default: 'Salário'
        },
        modelValue: {
            type: String,
            default: ''
        },
        desativado: {
            type: Boolean
        },
        small: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            inputValue: this.formatCurrency(this.modelValue),
            isValid: true
        };
    },
    methods: {
        validateInput() {
            const maxDigits = 8;
            let valueWithoutPrefix = this.inputValue.replace('R$', '').trim();

            valueWithoutPrefix = valueWithoutPrefix.replace(/[^0-9,]/g, '');

            if (valueWithoutPrefix.includes(',')) {
                const parts = valueWithoutPrefix.split(',');
                parts[0] = parts[0].slice(0, maxDigits - 3);
                parts[1] = parts[1].slice(0, 2);
                valueWithoutPrefix = parts.join(',');
            } else {
                valueWithoutPrefix = valueWithoutPrefix.slice(0, maxDigits);
            }

            this.inputValue = `R$ ${valueWithoutPrefix}`;
            this.isValid = valueWithoutPrefix.length === 0 || !isNaN(valueWithoutPrefix.replace(',', '.'));
            this.$emit('update:modelValue', this.inputValue);
        },
        formatCurrency(value) {
            if (!value.startsWith('R$')) {
                value = `R$ ${value}`;
            }
            return value;
        }
    },
    watch: {
        modelValue(newValue) {
            this.inputValue = this.formatCurrency(newValue);
        }
    }
};
</script>
