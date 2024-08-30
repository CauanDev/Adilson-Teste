<template>
    <div id="authentication-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 flex items-center justify-center z-50 overflow-hidden">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Detalhes da Compra
                    </h3>
                    <button @click="$emit('close')"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center"
                        data-modal-hide="authentication-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 flex flex-col items-center gap-2">
                    <NormalTable :headers="['Data da Compra', 'Valor de Compra', 'Valor de Venda', 'Fornecedor']"
                        :body="body" />
                    <router-link to="/produtos">
                        <button
                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            Comprar Mais Produto
                        </button>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import NormalTable from "../../Tables/NormalTable/NormalTable.vue"
export default {
    name: "ComprasModal",
    components: { NormalTable },
    props: {
        compra: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
            body: []
        };
    },
    methods: {
        getDetails() {
            this.body.push({
                created_at: this.compra.created_at,
                valor:"R$"+this.compra.valor.replace('.',','),
                preco: "R$"+this.compra.produto.preco.replace('.',','),
                fornecedor: this.compra.produto ? this.compra.produto.marca.fornecedor.name : "Produto Apagado"
            });
        }
    },
    mounted() {
        this.getDetails()
    }
};
</script>
