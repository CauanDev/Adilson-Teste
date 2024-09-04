<template>
    <div id="authentication-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 flex items-center justify-center z-50 overflow-hidden">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Adicionar Pedido
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
                    <div class="flex gap-2">
                        <div v-if="clientes.length == 0" class="flex text-center justify-center items-center">
                            <router-link to="/clientes">
                                <p class="text-gray-500 text-xs">Nenhum Cliente Disponível</p>
                                <button type="button"
                                    class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 focus:outline-none">Criar
                                    Cliente</button>
                            </router-link>
                        </div>
                        <div class="text-center" v-else>
                            <h1 class="text-xs">Escolha o Cliente</h1>
                            <select v-model="selectedCliente"
                                class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5"
                                @change="updateDiscount">
                                <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                    {{ cliente.nome }}
                                </option>
                            </select>
                        </div>
                        <div v-if="funcionarios.length == 0">
                            <router-link to="/funcionarios">
                                <p class="text-gray-500 text-xs">Nenhum Funcionario Disponível</p>
                                <button type="button"
                                    class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 focus:outline-none">Criar
                                    Funcionário</button>
                            </router-link>
                        </div>
                        <div class="text-center" v-else>
                            <h1 class="text-xs">Escolher o Funcionario</h1>
                            <select v-model="selectedFuncionario"
                                class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                                <option v-for="funcionario in funcionarios" :key="funcionario.id"
                                    :value="funcionario.id">
                                    {{ funcionario.nome }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <ProdutosNormalTable :headers="['Produto', 'Valor', '']" @update:counters="handleCountersUpdate" />

                    <div class="flex gap-2 flex-col">
                        <div v-if="isVipCliente" class="text-center w-full">
                            <h1>Desconto Aplicado (10%)</h1>
                            <InputSalario v-model="discountString" placeholder="Desconto" small
                                :desativado="true" />
                        </div>
                        <div class="text-center w-full">
                            <h1>Valor Total</h1>
                            <InputSalario v-model="totalString" placeholder="Valor Total"  small
                                :desativado="true" />
                        </div>
                    </div>
                    <button @click="submitForm" :disabled="!isFormValid"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Criar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ProdutosNormalTable from "../../Tables/ProdutosTable/ProdutosNormalTable.vue";
import InputName from "../../Inputs/InputName.vue"
import InputSalario from "../../Inputs/InputSalario.vue";
import http from "../../../services/http.js";

export default {
    name: "PedidosModal",
    components: { ProdutosNormalTable, InputSalario,InputName },
    data() {
        return {
            clientes: [],
            nameProduto:'',
            funcionarios: [],
            counters: {
                total: 0,
                items: []
            },
            selectedCliente: null,
            selectedFuncionario: null,
            isVipCliente: false // Controle para verificar se o cliente é VIP
        };
    },
    computed: {
        totalString() {
            return String(this.counters.total - this.discountAmount);
        },
        discountString() {
            return String(this.discountAmount.toFixed(2));
        },
        discountAmount() {
            return this.isVipCliente ? this.counters.total * 0.10 : 0;
        },
        isFormValid() {
            return this.selectedCliente && this.selectedFuncionario && this.counters.total > 0;
        }
    },
    methods: {
        async submitForm() {
            if (!this.isFormValid) return; // Não faz nada se o formulário não for válido

            this.$emit('addPedido', {
                clienteId: this.selectedCliente,
                funcionarioId: this.selectedFuncionario,
                total: this.counters.total - this.discountAmount,
                produtos: this.counters.items
            });
            this.$emit('close');
        },
        handleCountersUpdate(counters) {
            let newTotal = 0;
            const newItems = [];

            for (const item of counters) {
                const { name, total, quantidade } = item;
                newItems.push({ name, quantidade, total });
                newTotal += total;
            }
            this.counters = {
                total: newTotal,
                items: newItems
            };

            // Atualiza o desconto após recalcular o total
            this.updateDiscount();
        },
        async getDetails() {
            try {
                const clientes = await http.post('/filter-cliente', { "status": "Ativo" });
                const funcionarios = await http.post('/filter-funcionario', { "status": "Ativo" });
                this.clientes = clientes.data.clientes;
                this.funcionarios = funcionarios.data.funcionarios;
                this.selectedCliente = this.clientes[0].id
                this.selectedFuncionario = this.funcionarios[0].id
                this.updateDiscount(); // Atualiza o desconto após buscar os detalhes
            } catch (error) {
                console.log(error);
            }
        },
        updateDiscount() {
            const selectedCliente = this.clientes.find(cliente => cliente.id === this.selectedCliente);
            this.isVipCliente = selectedCliente && selectedCliente.tipo === 'Vip'; // Define se o cliente é VIP
        }
    },
    created() {
        this.getDetails();
    }
};
</script>
