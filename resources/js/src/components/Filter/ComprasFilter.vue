<template>
    <AccorDion title="Filtros Disponveis">

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
import http from "../../services/http.js"
import InputAge from "../Inputs/InputAge.vue"
import InputNumber from "../Inputs/InputNumber.vue";
import InputSalario from "../Inputs/InputSalario.vue";
import InputName from "../Inputs/InputName.vue";
import AccorDion from "../Accordion/AccorDion.vue";

export default {
    name: "FuncionarioFilter",
    components: { InputAge, InputNumber, InputSalario, InputName, AccorDion },
    data() {
        return {
            name: '',
            status: '',
            dataMinima: '',
            dataMaxima: '',
            valorMin: '',
            valorMax: '',
            quantidadeMin: '',
            quantidadeMax: '',
            selectedMarca: '',
            selectedSegmento: '',
            marcas: [],
            segmentos: [],
            fornecedor: ''
        };
    },
    computed: {
        isFormValid() {
            const valorMin = Number(this.valorMin);
            const valorMax = Number(this.valorMax);
            const quantidadeMin = Number(this.quantidadeMin);
            const quantidadeMax = Number(this.quantidadeMax);

            const valorRangeValid = !this.valorMin || !this.valorMax || valorMin <= valorMax;
            const quantidadeRangeValid = !this.quantidadeMin || !this.quantidadeMax || quantidadeMin <= quantidadeMax;
            const dataRangeValid = !this.dataMinima || !this.dataMaxima || new Date(this.dataMinima) <= new Date(this.dataMaxima);

            return (this.name.trim() !== '' ||
                this.status !== '' ||
                this.dataMinima !== null ||
                this.dataMaxima !== '' ||
                this.selectedMarca !== '' ||
                this.selectedSegmento !== '' ||
                this.fornecedor.trim() !== '' ||  // Incluindo a validação do fornecedor
                (this.valorMin || this.valorMax) ||
                (this.quantidadeMin || this.quantidadeMax)) &&
                valorRangeValid &&
                quantidadeRangeValid &&
                dataRangeValid;
        }
    },

    methods: {
        validateName(field) {
            if (field === 'name' && this.name.trim() === '') {
                this.name = '';
            }
        },
        validateNumericInput(field) {
            // Remove caracteres não numéricos
            this[field] = this[field].replace(/[^0-9]/g, '');
        },
        validateName(field) {
            if (this[field].trim() === '') {
                this[field] = '';
            }
        },

        applyFilter() {
            this.$emit('applyFilter', {
                name: this.name.trim() || null,
                status: this.status || null,
                dataMinima: this.dataMinima || null,
                dataMaxima: this.dataMaxima || null,
                valorMin: this.valorMin || null,
                valorMax: this.valorMax || null,
                quantidadeMin: this.quantidadeMin || null,
                quantidadeMax: this.quantidadeMax || null,
                marca: this.selectedMarca || null,
                segmento: this.selectedSegmento || null,
                fornecedor: this.fornecedor.trim()
            });
        },
        async getMarca() {
            try {
                const { data } = await http.get('/marcas');
                this.marcas = data.marcas.map((marca) => marca.nome);
                this.segmentos = [...new Set(data.marcas.map((marca) => marca.segmento))]; // Assumindo que segmentos estão no mesmo endpoint
            } catch (error) {
                console.error("Erro ao buscar marcas e segmentos", error);
            }
        }
    },
    created() {
        this.getMarca();
    }
}
</script>
