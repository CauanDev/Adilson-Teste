<template>
    <AccorDion title="Filtros Disponíveis">
        <div class="flex flex-col gap-2 w-full justify-center items-center mt-2">
            <div class="flex flex-col justify-center gap-2">
                <div>
                    <h1 class="text-xs font-bold mb-2">Data de Inserção</h1>
                    <div class="flex gap-2">
                        <InputAge v-model="dataMinima" small @input="applyFilter" placeholder="Data Mínima" />
                        <InputAge v-model="dataMaxima" small @input="applyFilter" placeholder="Data Máxima" />
                    </div>
                </div>
                <div>
                    <h1 class="text-xs font-bold mb-2">Quantidade de Marcas</h1>
                    <div class="flex gap-2">
                        <InputName v-model="quantidadeMinima" @input="applyFilter" small placeholder="Quantidade Mínima" />
                        <InputName v-model="quantidadeMaxima" @input="applyFilter" small placeholder="Quantidade Máxima" />     
                    </div>
                </div>
                <div class="flex gap-2">
                    <InputName v-model="nomeFornecedor" @input="applyFilter" small placeholder="Nome do Fornecedor" />
                    <InputName v-model="nomeMarca" @input="applyFilter" small placeholder="Nome da Marca" />                    
                </div>
                <div class="flex justify-center">
                    <InputName v-model="segmento" @input="applyFilter" small placeholder="Segmento da Marca" />                   
                </div>
                <div class="flex justify-center items-center gap-2">
                    <div class="text-center">
                        <h1 class="text-xs">Selecione o Status</h1>
                        <div class="flex gap-3 items-center mobile">
                            <label class="flex">
                                <input type="radio" :value="'Suspenso'" v-model="status"
                                    class="form-radio text-blue-600" @change="applyFilter">
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
        </div>
    </AccorDion>
</template>

<script>
import AccorDion from '../Accordion/AccorDion.vue';
import InputName from '../Inputs/InputName.vue';
import InputAge from '../Inputs/InputAge.vue';

export default {
    name: "FornecedorFilter",
    components: { AccorDion, InputName, InputAge },
    data() {
        return {
            nomeFornecedor: '',
            nomeMarca: '',
            status: 'all',
            segmento: '',
            quantidadeMinima: '',
            quantidadeMaxima: '',
            dataMinima: '',
            dataMaxima: '',
        };
    },
    methods: {
        applyFilter() {
            const filter = {};
            if (this.nomeFornecedor.trim()) filter.nomeFornecedor = this.nomeFornecedor.trim();
            if (this.nomeMarca.trim()) filter.nomeMarca = this.nomeMarca.trim();
            if (this.status !== 'all') filter.status = this.status;
            if (this.segmento.trim()) filter.segmento = this.segmento.trim();
            if (this.quantidadeMinima) filter.quantidadeMinima = this.quantidadeMinima;
            if (this.quantidadeMaxima) filter.quantidadeMaxima = this.quantidadeMaxima;
            if (this.dataMinima) filter.dataMinima = this.dataMinima;
            if (this.dataMaxima) filter.dataMaxima = this.dataMaxima;
            this.$emit('applyFilter', filter);
        }
    }
};
</script>
