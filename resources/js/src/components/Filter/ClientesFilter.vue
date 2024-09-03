<template>
    <AccorDion title="Filtros Disponíveis">
        <div class="flex flex-col gap-2 w-full justify-center items-center">
            <div class="flex flex-col gap-2">
                <div class="flex flex-col justify-center items-center gap-2 ">
                    <div>
                        <h1 class="text-xs font-bold mb-2">Data de Inserção</h1>
                        <div class="flex gap-2">
                            <InputAge v-model="dataMinima" small @input="applyFilter" placeholder="Data Mínima" />
                            <InputAge v-model="dataMaxima" small @input="applyFilter" placeholder="Data Máxima" />
                        </div>
                    </div>
                    <div>
                        <h1 class="text-xs font-bold mb-2">Quantidade de Pedidos</h1>
                        <div class="flex gap-2">
                            <InputNumber v-model="quantidadeMinima" small @input="applyFilter"
                                placeholder="Quantidade Mínima" />
                            <InputNumber v-model="quantidadeMaxima" small @input="applyFilter"
                                placeholder="Quantidade Máxima" />
                        </div>
                    </div>
                    <div class="flex gap-2 ">
                        <InputIdade v-model="idadeMin" small @input="applyFilter" placeholder="Idade Mínima" />
                        <InputIdade v-model="idadeMax" small @input="applyFilter" placeholder="Idade Máxima" />
                    </div>
                    <InputName v-model="name" small @input="applyFilter" placeholder="Digite o Nome" />
                </div>
                <div class="flex justify-center items-center gap-2">
                    <div class="text-center">
                        <h1 class="text-xs">Selecione o Sexo</h1>
                        <div class="flex gap-3 items-center mobile">
                            <label class="flex">
                                <input type="radio" :value="'Masculino'" v-model="sexo" class="form-radio text-blue-600"
                                    @change="applyFilter">
                                <span class="ml-2 text-gray-900">Masculino</span>
                            </label>
                            <label class="flex">
                                <input type="radio" :value="'Feminino'" v-model="sexo" class="form-radio text-blue-600"
                                    @change="applyFilter">
                                <span class="ml-2 text-gray-900">Feminino</span>
                            </label>
                            <label class="flex">
                                <input type="radio" :value="'all'" v-model="sexo" class="form-radio text-blue-600"
                                    @change="applyFilter">
                                <span class="ml-2 text-gray-900">Todas as Opções</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col justify-center items-center gap-2">
                <!-- Status -->
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
                <div class="flex justify-center items-center gap-2">
                    <div class="text-center">
                        <h1 class="text-xs">Selecione o Tipo</h1>
                        <div class="flex gap-3 items-center mobile">
                            <label class="flex">
                                <input type="radio" :value="'Normal'" v-model="tipo" class="form-radio text-blue-600"
                                    @change="applyFilter">
                                <span class="ml-2 text-gray-900">Normal</span>
                            </label>
                            <label class="flex">
                                <input type="radio" :value="'Vip'" v-model="tipo" class="form-radio text-blue-600"
                                    @change="applyFilter">
                                <span class="ml-2 text-gray-900">Vip</span>
                            </label>
                            <label class="flex">
                                <input type="radio" :value="'all'" v-model="tipo" class="form-radio text-blue-600"
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
import InputAge from '../Inputs/InputAge.vue';
import InputName from '../Inputs/InputName.vue';
import InputIdade from '../Inputs/InputIdade.vue';
import InputNumber from '../Inputs/InputNumber.vue';

export default {
    name: "ClientesFilter",
    components: { AccorDion, InputName, InputAge, InputIdade, InputNumber },
    data() {
        return {
            name: '',
            status: 'all',
            dataMinima: '',
            dataMaxima: '',
            idadeMin: '',
            idadeMax: '',
            sexo: 'all',
            tipo: 'all',
            quantidadeMinima: '', // Adicionado
            quantidadeMaxima: '', // Adicionado
        };
    },
    methods: {
        applyFilter() {
            const filter = {};
            if (this.name.trim()) filter.nome = this.name.trim();
            if (this.status !== 'all') filter.status = this.status;
            if (this.sexo !== 'all') filter.sexo = this.sexo; // Corrigido de `sex` para `sexo`
            if (this.dataMinima) filter.dataMinima = this.dataMinima;
            if (this.dataMaxima) filter.dataMaxima = this.dataMaxima;
            if (this.idadeMin) filter.idadeMin = this.idadeMin;
            if (this.idadeMax) filter.idadeMax = this.idadeMax;
            if (this.tipo) filter.tipo = this.tipo;
            if (this.quantidadeMinima) filter.quantidadeMinima = this.quantidadeMinima; // Adicionado
            if (this.quantidadeMaxima) filter.quantidadeMaxima = this.quantidadeMaxima; // Adicionado
            this.$emit('applyFilter', filter);
        }
    }
};
</script>
