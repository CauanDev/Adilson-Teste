<template>
    <ProdutoFilter @applyFilter="applyFilter" />
    <div class="flex flex-col gap-2 p-2">
        <div class="w-full flex gap-2 mobile">
            <div class="flex sm:w-1/2 gap-2 mobile">
                <ProdutoSegmentoMarca :body="produtos" />
                <ProdutosStatus :body="produtos" />
            </div>
            <div class="flex justify-center items-center">
                <ProdutoDashboardTable :body="produtos" />

            </div>
        </div>

        <div class="flex w-full gap-2 mobile">
            <div class="flex sm:w-1/2 gap-2 mobile">
                <MediaPrecoMarca :body="produtos" />
                <EstoqueProdutos :body="produtos" />
            </div>
            <div class="sm:w-1/2">
                <ProdutosMaisVendidos :body="produtos" />
            </div>

        </div>

    </div>

</template>
<script>
import ProdutoFilter from "../../Filter/ProdutoFilter.vue"
import http from "../../../services/http"

import ProdutosStatus from "../../Graficos/Produtos/ProdutosStatus.vue";
import ProdutoSegmentoMarca from "../../Graficos/Produtos/ProdutoSegmentoMarca.vue";
import MediaPrecoMarca from "../../Graficos/Produtos/MediaPrecoMarca.vue";
import EstoqueProdutos from "../../Graficos/Produtos/EstoqueProdutos.vue";
import ProdutosMaisVendidos from "../../Graficos/Produtos/ProdutosMaisVendidos.vue";
import ProdutoDashboardTable from "../../Tables/ProdutosTable/ProdutoDashboardTable.vue";
import { format } from 'date-fns';

export default {
    name: "ProdutosDashboard",
    components: { ProdutoDashboardTable, ProdutosMaisVendidos, EstoqueProdutos, MediaPrecoMarca, ProdutoFilter, ProdutosStatus, ProdutoSegmentoMarca },
    data() {
        return {
            produtos: [],
            allProdutos: []
        }
    },
    methods: {
        converterData(dataStr) {
            const dia = dataStr.substring(0, 2);
            const mes = dataStr.substring(2, 4);
            const ano = dataStr.substring(4, 8);
            return `${dia}-${mes}-${ano}`;
        },
        async getProdutos() {
            const data = await http.get('/produtos')
            this.allProdutos = data.data.produtos.map(produto => {
                return {
                    ...produto,
                    created_at: format(new Date(produto.created_at), 'dd-MM-yyyy')

                }
            })
            this.produtos = this.allProdutos
            console.log(this.produtos)
        },
        applyFilter(filter) {
            console.log(filter.valorMax)
            const filteredProdutos = this.allProdutos.filter(produto => {
                let isValid = true
                console.log(filter.name)
                if (filter.name) if (!produto.name.toLowerCase().includes(filter.name.toLowerCase())) isValid = false

                if (filter.marca) if (!produto.marca.toLowerCase().includes(filter.marca.toLowerCase())) isValid = false


                if (filter.segmento) if (!produto.segmento.toLowerCase().includes(filter.segmento.toLowerCase())) isValid = false


                if (filter.quantidadeMin) if (produto.quantidade > filter.quantidadeMin) isValid = false


                if (filter.quantidadeMax) if (produto.quantidade < filter.quantidadeMax) isValid = false


                if (filter.status && filter.status !== 'all') if (filter.status !== produto.status) isValid = false

               
                if (filter.dataMinima) if (produto.created_at < this.converterData(filter.dataMinima)) isValid = false


                if (filter.dataMaxima) if (produto.created_at > this.converterData(filter.dataMaxima)) isValid = false


                if (filter.valorMax) if (produto.preco > filter.valorMin.replace('R$', '').trim()) isValid = false


                if (filter.valorMin) if (produto.preco < filter.valorMin.replace('R$', '').trim()) isValid = false


                if (filter.pedidosMin) if (produto.total_vendas < filter.pedidosMax) isValid = false


                if (filter.pedidosMax) if (produto.total_vendas > filter.pedidosMax) isValid = false



                return isValid

            })

            this.produtos = filteredProdutos
        }
    },
    mounted() {
        this.getProdutos()
    }
}
</script>