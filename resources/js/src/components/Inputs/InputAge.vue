<template>
    <div :class="{'w-72': !small, 'w-[190px]': small}">
        <div class="relative h-10">
            <input 
                v-model="formattedValue" 
                @input="handleInput"
                :class="{
                    'border-red-500 focus:border-red-500': !isValid,
                    'border-blue-gray-200 focus:border-gray-900': isValid
                }"
                class="peer w-full h-full bg-transparent text-blue-gray-700 font-sans font-normal outline-none focus:outline-none disabled:bg-blue-gray-50 disabled:border-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 border focus:border-2 border-t-transparent focus:border-t-transparent text-sm px-3 py-2.5 rounded-[7px]"
                placeholder="" 
            />
            <label
                :class="{
                    'text-red-500 before:border-red-500 after:border-red-500 peer-focus:text-red-500 peer-focus:before:border-red-500 peer-focus:after:border-red-500': !isValid,
                    'text-gray-500 peer-focus:text-gray-900 before:border-blue-gray-200 peer-focus:before:border-gray-900 after:border-blue-gray-200 peer-focus:after:border-gray-900': isValid
                }"
                class="flex w-full h-full select-none pointer-events-none absolute left-0 font-normal overflow-visible truncate peer-placeholder-shown:text-blue-gray-500 leading-tight peer-focus:leading-tight peer-disabled:text-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500 transition-all -top-1.5 peer-placeholder-shown:text-sm text-[11px] 
                before:content-[' '] before:block before:box-border before:w-2.5 before:h-1.5 before:mt-[6.5px] before:mr-1 peer-placeholder-shown:before:border-transparent before:rounded-tl-md before:border-t peer-focus:before:border-t-2 before:border-l peer-focus:before:border-l-2 
                before:pointer-events-none before:transition-all peer-disabled:before:border-transparent 
                after:content-[' '] after:block after:flex-grow after:box-border after:w-2.5 after:h-1.5 after:mt-[6.5px] after:ml-1 peer-placeholder-shown:after:border-transparent after:rounded-tr-md after:border-t peer-focus:after:border-t-2 after:border-r peer-focus:after:border-r-2 
                after:pointer-events-none after:transition-all peer-disabled:after:border-transparent 
                peer-placeholder-shown:leading-[3.75]">
                {{ placeholder }}
            </label>
        </div>
    </div>
</template>

<script>
export default {
    name: 'InputDateOfBirth',
    props: {
        placeholder: {
            type: String,
            default: ''
        },
        modelValue: {
            type: [String, Number],
            default: ''
        },
        small: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            rawValue: this.modelValue || '',
            formattedValue: this.formatDate(this.modelValue || ''),
            isValid: true
        };
    },
    methods: {
        handleInput(event) {
            const value = event.target.value.replace(/\D/g, '').slice(0, 8);
            let day = value.slice(0, 2);
            let month = value.slice(2, 4);
            let year = value.slice(4, 8);

            this.rawValue = value;
            this.formattedValue = this.formatDate(day, month, year);

            if (year.length === 4 && month.length === 2 && day.length === 2) {
                const inputDate = new Date(`${year}-${month}-${day}`);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                this.isValid = inputDate < today && parseInt(year) >= 1900;
            } else {
                this.isValid = false;
            }

            if (this.isValid && this.rawValue) {
                this.$emit('update:modelValue', this.rawValue);
            } else if (!this.rawValue) {
                this.$emit('update:modelValue', '');
            }
        },
        formatDate(day = '', month = '', year = '') {
            if (day.length === 2 && month.length === 2 && year.length === 4) {
                return `${day}/${month}/${year}`;
            }
            return `${day}${month ? '/' + month : ''}${year ? '/' + year : ''}`;
        }
    },
    watch: {
        modelValue(newValue) {
            if (!newValue) {
                this.rawValue = '';
                this.formattedValue = '';
                this.isValid = true; // Reset validation state
            } else {
                this.rawValue = newValue || '';
                const day = this.rawValue.slice(0, 2);
                const month = this.rawValue.slice(2, 4);
                const year = this.rawValue.slice(4, 8);
                this.formattedValue = this.formatDate(day, month, year);
                if (year.length === 4 && month.length === 2 && day.length === 2) {
                    const inputDate = new Date(`${year}-${month}-${day}`);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    this.isValid = inputDate < today && parseInt(year) >= 1900;
                } else {
                    this.isValid = false;
                }
            }
        }
    }
};
</script>
