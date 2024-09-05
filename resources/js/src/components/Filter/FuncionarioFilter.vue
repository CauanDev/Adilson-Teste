<template>
    <AccorDion title="Filtros Disponíveis">
        <div class="flex flex-col gap-2 w-full justify-center items-center whitespace-nowrap mt-2">
            <div class="flex flex-col gap-2 mobile">
                <div class="flex justify-center gap-2 ">
                    <InputName v-model="name" small placeholder="Digite o Nome" @input="applyFilter"/>


                </div> 
                <div class="flex justify-center items-center gap-2">
                    <div class="text-center">
                        <h1 class="text-xs">Selecione o Sexo</h1>
                        <div class="flex gap-3 items-center mobile">
                            <label class="flex">
                                <input type="radio" :value="'Masculino'" v-model="sexo"
                                    class="form-radio text-blue-600" @change="applyFilter">
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

            <div class="flex flex-col justify-center items-center gap-2 w-[50%] mobile">
                <div>
                    <h1 class="text-xs font-bold mb-2">Data de Inserção</h1>
                    <div class="flex gap-2">
                        <InputAge v-model="dataMinima" small @input="applyFilter" placeholder="Data Mínima" />
                        <InputAge v-model="dataMaxima" small @input="applyFilter" placeholder="Data Máxima" />
                    </div>
                </div>
                <div>
                    <h1 class="text-xs font-bold mb-2"> Quantidades de Pedidos </h1>
                    <div class="flex gap-2">
                        <InputNumber v-model="quantidadeMin" small @input="applyFilter" placeholder="Quantidade Mínima" />
                        <InputNumber v-model="quantidadeMax" small @input="applyFilter" placeholder="Quantidade Máxima" />
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2 mobile">
                <div class="flex gap-2 ">
                    <InputSalario v-model="salarioMin" small @input="applyFilter" placeholder="Salário Mínimo" />
                    <InputSalario v-model="salarioMax" small @input="applyFilter" placeholder="Salário Máximo" />
                </div>
                
                <div class="flex gap-2 ">
                    <InputIdade v-model="idadeMin" small @input="applyFilter" placeholder="Idade Mínima" />
                    <InputIdade v-model="idadeMax" small @input="applyFilter" placeholder="Idade Máxima" />
                </div>
            </div>
        </div>
    </AccorDion>
</template>

<script>
import AccorDion from "../Accordion/AccorDion.vue"
import InputName from "../Inputs/InputName.vue";
import InputAge from "../Inputs/InputAge.vue";
import InputSalario from "../Inputs/InputSalario.vue";
import InputIdade from "../Inputs/InputIdade.vue";
import InputNumber from "../Inputs/InputNumber.vue";
export default {
    name: "FuncionarioFilter",
    components: { AccorDion,InputName,InputAge,InputSalario,InputIdade,InputNumber},
    data() {
        return {
            ordem: '',
            name: '',
            status: 'all',
            dataMinima: '',
            dataMaxima: '',
            idadeMin: '',
            idadeMax: '',
            sexo: 'all',
            salarioMin: '',
            salarioMax: '',
            quantidadeMax:'',
            quantidadeMin:''
        };
    },
    methods: {
        applyFilter() {
            this.$emit('applyFilter', {
                name: this.name.trim() || null,
                status: this.status || null,
                dataMinima: this.dataMinima || null,
                dataMaxima: this.dataMaxima || null,
                idadeMin: this.idadeMin || null,
                idadeMax: this.idadeMax || null,
                salarioMinimo: this.salarioMin.replace('R$','').trim() || null,
                salarioMaximo: this.salarioMax.replace('R$','').trim() || null,
                sexo: this.sexo || null,
                quantidadeMax: this.quantidadeMax || null,
                quantidadeMin: this.quantidadeMin || null
            });
        }
    }
};
</script>
