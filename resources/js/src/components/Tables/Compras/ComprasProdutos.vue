<template>
    <div v-if="body.length > 0" class="flex justify-center max-h-[200px] overflow-y-auto w-full sm:w-[100%]">
        <div class="w-full">
            <table class="w-full text-sm text-left rtl:text-right text-black">
                <thead class="text-xs uppercase bg-gray-300 text-center text-black sticky top-0">
                    <tr>
                        <th class="px-2 py-2 border-b border-gray-400">Nome do Produto</th>
                        <th class="px-2 py-2 border-b border-gray-400">Quantidade</th>
                        <th class="px-2 py-2 border-b border-gray-400">Marca</th>
                        <th class="px-2 py-2 border-b border-gray-400">Selecione</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr v-for="(item, index) in body" :key="index" class="border-b border-gray-400 text-center">
                        <td class="px-2 py-2 whitespace-nowrap text-[11px]">{{ item.name }}</td>
                        <td class="px-2 py-2 whitespace-nowrap text-[11px]">{{ item.quantidade }}</td>
                        <td class="px-2 py-2 whitespace-nowrap text-[11px]">{{ item.marca }}</td>
                        <td class="px-2 py-2 whitespace-nowrap text-[11px]">
                            <input type="radio" :value="item" v-model="selectedProductId" name="productSelection" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: 'NormalTable',
    props: {
        body: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            selectedProductId: null
        };
    },
    watch: {
        selectedProductId(newVal) {
            this.$emit('productSelected', newVal);
        },
        body(newVal) {
            if (newVal.length === 1) {
                this.selectedProductId = newVal[0];
            }
        }
    }
};
</script>
