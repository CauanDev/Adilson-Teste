<template>
    <ComprasFilter @applyFilter="applyFilter"/>
    <div class="flex">
        <ComprasTableDashboard :body="compras"/>
        <ComprasDia :body="compras"/>
    </div>
</template>
<script>
import ComprasFilter from "../../Filter/ComprasFilter.vue";
import ComprasDia from "../../Graficos/Compras/ComprasDia.vue"
import ComprasTableDashboard from "../../Tables/Compras/ComprasTableDashboard.vue"
import http from "../../../services/http"
import { format } from 'date-fns';

export default {
    name: "PedidosDashboard",
    components: {ComprasFilter,ComprasTableDashboard,ComprasDia},
    data() {
        return {
            allCompras: [],
            compras: []
        }
    },
    methods: {
        formatDate(dateString) {
            // Verifica se a string tem 8 caracteres
            if (dateString.length !== 8) {
                throw new Error("A string deve ter 8 caracteres no formato 'ddmmyyyy'.");
            }

            // Extrair o dia, mês e ano da string
            let day = dateString.substring(0, 2);
            let month = dateString.substring(2, 4);
            let year = dateString.substring(4, 8);

            // Retorna a data formatada
            return `${day}-${month}-${year}`;
        },
        formatDate(dateString) {
            // Verifica se a string tem 8 caracteres
            if (dateString.length !== 8) {
                throw new Error("A string deve ter 8 caracteres no formato 'ddmmyyyy'.");
            }

            // Extrair o dia, mês e ano da string
            let day = dateString.substring(0, 2);
            let month = dateString.substring(2, 4);
            let year = dateString.substring(4, 8);

            // Retorna a data formatada
            return `${day}-${month}-${year}`;
        },
        applyFilter(filter) {
            this.loading = true;

            // Aplicar filtro no array allCompras
            const filteredCompras = this.allCompras.filter(compra => {
                let isValid = true;

                if (filter.fornecedor) {
                    const nameFornecedor = compra.produto.marca.fornecedor.name.toLowerCase();
                    const nameFornecedorFilter = filter.fornecedor.toLowerCase();
                    if (!nameFornecedor.includes(nameFornecedorFilter)) isValid = false;
                }

                // Verificar nome do produto
                if (filter.name) {
                    console.log(filter.name)
                    console.log(compra.produto.name)
                    if (!compra.produto.name.toLowerCase().includes(filter.name.toLowerCase())) isValid = false;
                }

                // Verificar data mínima
                if (filter.dataMinima) {
                    const dataMinima = this.formatDate(filter.dataMinima)
                    const createdAt = format(new Date(compra.created_at), 'dd-MM-yyyy')
                    if (createdAt >= dataMinima) isValid = false;
                }

                // Verificar data máxima
                if (filter.dataMaxima) {
                    const dataMaxima = this.formatDate(filter.dataMaxima)
                    const createdAt = format(new Date(compra.created_at), 'dd-MM-yyyy')
                    if (createdAt <= dataMaxima) isValid = false;
                }
                if (filter.valorMin) {
                    const valorMinNum = parseFloat(filter.valorMin.replace('R$', '').trim().replace(',', '.'));
                    if (compra.total < valorMinNum) isValid = false;
                }

                // Verificar valor máximo
                if (filter.valorMax) {
                    const valorMaxNum = parseFloat(filter.valorMax.replace('R$', '').trim().replace(',', '.'));
                    if (compra.total > valorMaxNum) isValid = false;
                }


                // Verificar quantidade mínima
                if (filter.quantidadeMin) {
                    if (compra.quantidade < filter.quantidadeMin) isValid = false;
                }

                // Verificar quantidade máxima
                if (filter.quantidadeMax) {
                    if (compra.quantidade > filter.quantidadeMax) isValid = false;
                }

                // Verificar marca
                if (filter.marca) {
                    const nameMarca = compra.produto.marca.nome.toLowerCase();
                    const filterMarca = filter.marca.toLowerCase();

                    if (!nameMarca.includes(filterMarca)) isValid = false;
                }
                // Verificar segmento
                if (filter.segmento) {
                    const nameSegmento = compra.produto.marca.segmento.toLowerCase();
                    const filterSegmento = filter.segmento.toLowerCase();
                    if (!nameSegmento.includes(filterSegmento)) isValid = false;
                }

                return isValid;
            });
            this.compras = filteredCompras
            },
        async getCompras() {
            try {
                const data = await http.get('/compras')
                this.allCompras = data.data.compras.map(compra => {
                    return {
                        ...compra,
                        created_at: format(new Date(compra.created_at), 'dd-MM-yyyy')
                    }
                })
                this.compras = data.data.compras.map(compra => {
                    return {
                        ...compra,
                        created_at: format(new Date(compra.created_at), 'dd-MM-yyyy')
                    }
                })
            }
            catch (error) {
                console.log(error)
            }
        },
    },
    mounted() {
        this.getCompras()
    }
}
</script>