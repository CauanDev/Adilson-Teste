<template>
    <AccorDion title="Filtros Disponíveis">

        <div class="flex flex-col gap-2 w-full justify-center items-center whitespace-nowrap">
            <div>
                <h1 class="text-xs font-bold mb-2">Data de Inserção</h1>
                <div class="flex gap-2">
                    <InputAge v-model="dataMinima" small @input="applyFilter" placeholder="Data Mínima" />
                    <InputAge v-model="dataMaxima" small @input="applyFilter" placeholder="Data Máxima" />
                </div>
            </div>
            <div class="flex justify-center gap-2">
                <InputName v-model="selectedMarca" @input="applyFilter" small placeholder="Digite a Marca" />
                <InputName v-model="selectedSegmento" @input="applyFilter" small placeholder="Digite o Segmento" />
            </div>

            <div class="flex justify-center gap-2">
                <InputName v-model="fornecedor" @input="applyFilter" small placeholder="Digite a Fornecedor" />
                <InputName v-model="name" @input="applyFilter" small placeholder="Digite o Produto" />
            </div>

            <div class="flex justify-center gap-2">
                <InputNumber v-model="quantidadeMin" small @input="applyFilter" placeholder="Quantidade Mínima" />
                <InputNumber v-model="quantidadeMax" small @input="applyFilter" placeholder="Quantidade Máxima" />
            </div>

            <div class="flex justify-center gap-2">
                <InputSalario v-model="valorMin" small @input="applyFilter" placeholder="Valor Mínimo" />
                <InputSalario v-model="valorMax" small @input="applyFilter" placeholder="Valor Máximo" />
            </div>
        </div>
    </AccorDion>

</template>

<script>
import InputAge from "../Inputs/InputAge.vue"
import InputNumber from "../Inputs/InputNumber.vue";
import InputSalario from "../Inputs/InputSalario.vue";
import InputName from "../Inputs/InputName.vue";
import AccorDion from "../Accordion/AccorDion.vue";

export default {
    name: "FuncionarioFilter",
    components: { InputAge, InputNumber, InputSalario, InputName, AccorDion },
    emits: ['applyFilter'],

    data() {
        return {
            name: '',
            selectedSegmento: '',
            status: '',
            dataMinima: '',
            dataMaxima: '',
            valorMin: '',
            valorMax: '',
            quantidadeMin: '',
            quantidadeMax: '',
            selectedMarca: '',
            marcas: [],
            segmentos: [],
            fornecedor: ''
        };
    },

    methods: {

        applyFilter() {
            const filter={}
            if (this.name) filter.name = this.name
            if (this.status !== 'all') filter.status = this.status;
            if (this.quantidadeMinima) filter.quantidadeMinima = this.quantidadeMinima;
            if (this.quantidadeMaxima) filter.quantidadeMaxima = this.quantidadeMaxima;
            if (this.dataMinima) filter.dataMinima = this.dataMinima;
            if (this.dataMaxima) filter.dataMaxima = this.dataMaxima;
            if (this.selectedSegmento) filter.segmento = this.selectedSegmento
            if (this.fornecedor.trim()) filter.fornecedor = this.fornecedor
            if (this.selectedMarca.trim()) filter.marca = this.marca
            if (this.valorMax) filter.valorMax = this.valorMax
            if (this.valorMin) filter.valorMin = this.valorMin
            // this.$emit('applyFilter', {
            //     name: this.name || null,
            //     status: this.status || null,
            //     dataMinima: this.dataMinima || null,
            //     dataMaxima: this.dataMaxima || null,
            //     valorMin: this.valorMin || null,
            //     valorMax: this.valorMax || null,
            //     quantidadeMin: this.quantidadeMin || null,
            //     quantidadeMax: this.quantidadeMax || null,
            //     marca: this.selectedMarca || null,
            //     segmento: this.selectedSegmento || null,
            //     fornecedor: this.fornecedor.trim()
            // });
            this.$emit('applyFilter',filter)
        }
    },

}
</script>
