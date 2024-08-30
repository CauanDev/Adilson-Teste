<template>
    <div class="custom-number-input h-10 w-28">
        <div class="flex flex-row h-10 w-full rounded-lg relative bg-transparent mt-1">
            <button @click="decrement"
                class="bg-gray-300 text-gray-600 hover:text-gray-700 hover:bg-gray-400 h-full w-20 rounded-l cursor-pointer outline-none">
                <span class="m-auto text-2xl font-thin">−</span>
            </button>
            <input type="number" :value="count" :max="maxValue" disabled
                class="outline-none focus:outline-none text-center w-full bg-gray-300 font-semibold text-md hover:text-black focus:text-black cursor-default flex items-center text-gray-700"
                name="custom-input-number" />
            <button @click="increment"
                class="bg-gray-300 text-gray-600 hover:text-gray-700 hover:bg-gray-400 h-full w-20 rounded-r cursor-pointer">
                <span class="m-auto text-2xl font-thin">+</span>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ItemCounter',
    props: {
        value: {
            type: Number,
            default: 0 // Caso a prop não seja passada, inicia em 0
        },
        maxValue: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            count: this.value // Inicializa com o valor da prop 'value'
        };
    },
    methods: {
        increment() {
            if (this.count < this.maxValue) {
                this.count++;
                this.emitUpdate();
            }
        },
        decrement() {
            if (this.count > 0) {
                this.count--;
                this.emitUpdate();
            }
        },
        emitUpdate() {
            this.$emit('update:value', this.count);
        }
    },
    watch: {
        value(newValue) {
            this.count = newValue; // Atualiza o contador se o valor da prop mudar
        }
    }
};
</script>
