<template>
    <AccorDion title="Filtros Disponíveis">
        <div class="flex flex-col gap-2 w-full justify-center items-center">
            <div class="flex gap-2 flex-col justify-center">
                <div class="flex justify-center gap-2 mobile">
                    <InputName v-model="nameMarca" @input="applyFilter" small placeholder="Nome da Marca" />
                    <InputName v-model="nameFornecedor" @input="applyFilter" small placeholder="Nome do Fornecedor" />
                </div>

                <div>
                    <h1 class="text-xs font-bold mb-2">Data de Inserção</h1>
                    <div class="flex gap-2">
                        <InputAge v-model="dataMinima" small @input="applyFilter" placeholder="Data Mínima" />
                        <InputAge v-model="dataMaxima" small @input="applyFilter" placeholder="Data Máxima" />
                    </div>
                </div>

                <div class="flex gap-2 text-center mobile">
                    <InputNumber v-model="quantidadeMinima" small @input="applyFilter"
                        placeholder="Quantidade Mínima" />
                    <InputNumber v-model="quantidadeMaxima" small @input="applyFilter"
                        placeholder="Quantidade Máxima" />
                </div>

                <div class="flex justify-center mobile">
                    <InputName v-model="segmento" @input="applyFilter" small placeholder="Nome do Segmento" />
                </div>
            </div>

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
import InputAge from '../Inputs/InputAge.vue';
import InputName from '../Inputs/InputName.vue';
import AccorDion from '../Accordion/AccorDion.vue';
import InputNumber from '../Inputs/InputNumber.vue';

export default {
    name: "ClientesFilter",
    components: { InputAge, InputName, AccorDion, InputNumber },
    data() {
        return {
            nameFornecedor: '',
            nameMarca: '',
            status: '',  // Inicializa sem valor selecionado
            dataMinima: '',
            dataMaxima: '',
            quantidadeMinima: '',
            quantidadeMaxima: '',
            segmento: '',
        };
    },
    methods: {
        applyFilter() {
            const filter = {};

            if (this.nameMarca.trim()) filter.nameMarca = this.nameMarca.trim();
            if (this.nameFornecedor.trim()) filter.nameFornecedor = this.nameFornecedor.trim();
            if (this.status !== '') filter.status = this.status;  // Não envia vazio no filtro
            if (this.dataMinima) filter.dataMinima = this.dataMinima;
            if (this.dataMaxima) filter.dataMaxima = this.dataMaxima;
            if (this.quantidadeMinima) filter.quantidadeMinima = this.quantidadeMinima;
            if (this.quantidadeMaxima) filter.quantidadeMaxima = this.quantidadeMaxima;
            if (this.segmento.trim()) filter.segmento = this.segmento.trim();

            this.$emit('applyFilter', filter);
        }
    }
};
</script>
