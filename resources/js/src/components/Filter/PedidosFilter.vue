<template>
    <AccorDion title="Filtros Disponíveis">
        <div class="flex flex-col gap-2 w-full justify-center items-center whitespace-nowrap mt-2">
            <!-- Linha 1 -->
            <div class="flex gap-2 mobile">
                <div class="flex justify-center gap-2">
                    <InputName small v-model="nameFuncionario" placeholder="Nome do Funcionario" @input="applyFilter" />
                    <InputName small v-model="nameCliente" placeholder="Nome do Cliente" @input="applyFilter" />

                </div>
            </div>

            <!-- Linha 2 -->
            <div class="flex justify-center items-center gap-2 w-[50%] mobile">
                <div>
                    <h1 class="text-xs font-bold mb-2">Data de Inserção</h1>
                    <div class="flex gap-2">
                        <InputAge v-model="dataMinima" small @input="applyFilter" placeholder="Data Mínima" />
                        <InputAge v-model="dataMaxima" small @input="applyFilter" placeholder="Data Máxima" />
                    </div>
                </div>
            </div>

            <div>
                <h1 class="text-xs font-bold mb-2">Valor do Produto</h1>
                <div class="flex gap-2">
                    <InputSalario v-model="valorMin" small @input="applyFilter" placeholder="Valor Mínimo" />
                    <InputSalario v-model="valorMax" small @input="applyFilter" placeholder="Valor Máximo" />
                </div>
            </div>
            <div class="flex gap-2">
                <InputName small v-model="segmento" placeholder="Segmento Produto" @input="applyFilter" />
                <InputName small v-model="nomeProduto" placeholder="Nome Produto" @input="applyFilter" />
            </div>


            <div class="flex justify-center items-center gap-2">
                <div class="text-center">
                    <h1 class="text-xs mb-2">Selecione o Status</h1>
                    <div class="flex gap-3 items-center mobile">
                        <label class="flex">
                            <input type="radio" :value="'Não Processado'" v-model="status" class="form-radio text-blue-600"
                                @change="applyFilter">
                            <span class="ml-2 text-gray-900">Não Processado</span>
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
import AccorDion from '../Accordion/AccorDion.vue';
import InputName from '../Inputs/InputName.vue';
import InputAge from '../Inputs/InputAge.vue';
import InputSalario from '../Inputs/InputSalario.vue';
export default {
    name: "PedidosFilter",
    components: { AccorDion, InputName, InputAge, InputSalario },

    data() {
        return {
            nameCliente: '',
            status: 'all',
            nameFuncionario: '',
            dataMinima: '',
            dataMaxima: '',
            valorMin: '',
            valorMax: '',
            nomeProduto:'',
            segmento: '' // Nova propriedade para o select de segmentos
        };
    },
    methods: {
        applyFilter() {
            const filters = {
                nameCliente: this.nameCliente.trim() || undefined,
                nameFuncionario: this.nameFuncionario.trim() || undefined,
                status: this.status || undefined,
                dataMinima: this.dataMinima || undefined,
                dataMaxima: this.dataMaxima || undefined,
                valorMin: this.valorMin.replace('R$', '').trim() || undefined,
                valorMax: this.valorMax.replace('R$', '').trim() || undefined,
                segmento: this.segmento || undefined,
                nomeProduto: this.nomeProduto || undefined
            };

            this.$emit('applyFilter', filters);
        }
    }
};
</script>
