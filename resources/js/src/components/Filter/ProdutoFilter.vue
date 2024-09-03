<template>
    <AccorDion title="Filtros Disponíveis">
        <div class="flex flex-col gap-2 w-full justify-center items-center mt-2">
            <div class="flex justify-center gap-2">
                <InputName v-model="name" small @input="applyFilter" placeholder="Nome do Produto" />
                <InputName v-model="marca" small @input="applyFilter" placeholder="Nome da Marca" />

            </div>
            <div class="flex flex-col justify-center gap-2">
                <div>
                    <h1 class="text-xs font-bold mb-2">Estoque</h1>
                    <div class="flex gap-2">
                        <InputNumber v-model="quantidadeMin" small @input="applyFilter"
                            placeholder="Quantidade Mínima" />
                        <InputNumber v-model="quantidadeMax" small @input="applyFilter"
                            placeholder="Quantidade Máxima" />
                    </div>
                </div>
                <div>
                    <h1 class="text-xs font-bold mb-2">Quantidade de Pedidos</h1>
                    <div class="flex gap-2">
                        <InputNumber v-model="pedidosMin" small @input="applyFilter" placeholder="Quantidade Mínima" />
                        <InputNumber v-model="pedidosMax" small @input="applyFilter" placeholder="Quantidade Máxima" />
                    </div>
                </div>
                <div>
                    <h1 class="text-xs font-bold mb-2">Valor do Produto</h1>
                    <div class="flex gap-2">
                        <InputSalario v-model="valorMin" small @input="applyFilter" placeholder="Valor Mínimo" />
                        <InputSalario v-model="valorMax" small @input="applyFilter" placeholder="Valor Máximo" />
                    </div>
                </div>
            </div>
            <div class="flex gap-2 text-center">
                <div>
                    <h1 class="text-xs font-bold mb-2">Data de Inserção</h1>
                    <div class="flex gap-2">
                        <InputAge v-model="dataMinima" small @input="applyFilter" placeholder="Data Mínima" />
                        <InputAge v-model="dataMaxima" small @input="applyFilter" placeholder="Data Máxima" />
                    </div>
                </div>
            </div>
            <InputName v-model="segmento" small @input="applyFilter" placeholder="Segmento Produto" />

            <div class="flex justify-center items-center gap-2">
                <div class="text-center">
                    <h1 class="text-xs mb-2">Selecione o Status</h1>
                    <div class="flex gap-3 items-center mobile">
                        <label class="flex">
                            <input type="radio" :value="'Suspenso'" v-model="status" class="form-radio text-blue-600"
                                @change="applyFilter">
                            <span class="ml-2 text-gray-900">Suspenso</span>
                        </label>
                        <label class="flex">
                            <input type="radio" :value="'Ativo'" v-model="status" class="form-radio text-blue-600"
                                @change="applyFilter">
                            <span class="ml-2 text-gray-900">Ativo</span>
                        </label>
                        <label class="flex">
                            <input type="radio" :value="'all'" v-model="status" class="form-radio text-blue-600"
                                @change="applyFilter">
                            <span class="ml-2 text-gray-900">Todas as Opções</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </AccorDion>
</template>

<script>
import InputName from "../Inputs/InputName.vue";
import AccorDion from "../Accordion/AccorDion.vue";
import InputSalario from "../Inputs/InputSalario.vue";
import InputNumber from "../Inputs/InputNumber.vue"
import InputAge from "../Inputs/InputAge.vue";
export default {
    name: "ProdutoFilter",
    components: { InputNumber, AccorDion, InputName, InputAge, InputSalario },
    data() {
        return {
            name: '',
            quantidadeMin: '',
            quantidadeMax: '',
            status: 'all',
            dataMinima: '',
            dataMaxima: '',
            valorMin: '',
            valorMax: '',
            pedidosMin: '',
            pedidosMax: '',
            marca: '',
            segmento:''
        };
    },
    methods: {
        applyFilter() {
            const filter = {};

            if (this.name.trim()) filter.name = this.name.trim();
            if (this.marca.trim()) filter.marca = this.marca.trim();
            if (this.quantidadeMin) filter.quantidadeMin = this.quantidadeMin;
            if (this.quantidadeMax) filter.quantidadeMax = this.quantidadeMax;
            if (this.status !== 'all') filter.status = this.status;
            if (this.dataMinima) filter.dataMinima = this.dataMinima;
            if (this.dataMaxima) filter.dataMaxima = this.dataMaxima;
            if (this.valorMin) filter.valorMin = this.valorMin;
            if (this.valorMax) filter.valorMax = this.valorMax;
            if (this.pedidosMin) filter.pedidosMin = this.pedidosMin;
            if (this.pedidosMax) filter.pedidosMax = this.pedidosMax;
            if (this.segmento) filter.segmento = this.segmento;
            this.$emit('applyFilter', filter);
        }
    }
};
</script>
