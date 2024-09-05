<template>
    <div class="flex-1 text-center bg-[#dbdbdb] shadow-2xl rounded-lg p-2 border-[1px] border-gray-400">
        <div v-if="chartSeries.length > 0">
            <h2 class="text-xl font-bold mb-2">Quantidade de Produtos por Status</h2>
            <apexchart ref="donutChart" type="donut" width="100%" :options="chartOptions" :series="chartSeries" />
        </div>
        <div v-else class="flex justify-center items-center h-full">
            <h2 class="text-xl font-bold mb-2 rotate-12">Sem Dados Disponíveis</h2>
        </div>
    </div>
</template>

<script>
import VueApexCharts from 'vue3-apexcharts';

export default {
    components: {
        apexchart: VueApexCharts,
    },
    props: {
        body: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            chartOptions: {
                chart: {
                    type: 'donut',
                },
                labels: ['Ativo', 'Suspenso'], // Labels fixos, pois sabemos que são apenas esses dois tipos
                legend: {
                    position: 'bottom',
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '16px',
                                    color: '#373d3f',
                                    formatter: () => this.getTotalInfo(), // Função para mostrar o total de produtos e marcas
                                },
                            },
                        },
                    },
                },
                colors: ['#FF4560', '#00E396'], // Cores personalizadas para 'Ativo' e 'Suspenso'
            },
            chartSeries: [], // Será preenchido dinamicamente
            totalMarcas: 0, // Total de marcas
            totalProdutos: 0, // Total de produtos
        };
    },
    watch: {
        body: {
            handler(newData) {
                this.updateDonutChart(newData);
            },
            deep: true,
        },
    },
    mounted() {
        this.updateDonutChart(this.body);
    },
    methods: {
        updateDonutChart(data) {
            // Inicializar a contagem de produtos por status
            const statusCount = {
                Ativo: 0,
                Suspenso: 0,
            };

            // Contagem de marcas exclusivas
            const uniqueBrands = new Set();

            // Contar a quantidade de produtos por status e marcas exclusivas
            data.forEach(product => {
                if (product.status === 'Ativo') {
                    statusCount['Ativo']++;
                } else {
                    statusCount['Suspenso']++;
                }

                // Adicionar marca ao conjunto para garantir que não haja duplicatas
                uniqueBrands.add(product.marca);
            });

            // Atualizar as séries do gráfico
            this.chartSeries = [statusCount['Ativo'], statusCount['Suspenso']];

            // Atualizar o total de produtos e marcas
            this.totalProdutos = statusCount['Ativo'] + statusCount['Suspenso'];
            this.totalMarcas = uniqueBrands.size;
        },
        getTotalInfo() {
            return `Produtos: ${this.totalProdutos}`;
        },
    },
};
</script>
