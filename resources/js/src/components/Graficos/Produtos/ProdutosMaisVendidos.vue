<template>
    <div class="flex mobile text-center bg-[#dbdbdb] shadow-2xl rounded-lg p-2 border-[1px] border-gray-400">
        <!-- Produtos Mais Vendidos -->
        <div class="chart-container" v-if="topProductsSeries.length > 0">
            <h2 class="text-xl font-bold mb-2">Produtos Mais<br> Vendidos</h2>
            <apexchart ref="topProductsChart" type="bar" width="100%" :options="topProductsOptions" :series="topProductsSeries" />
        </div>
        <div class="chart-container flex justify-center items-center h-full" v-else>
            <h2 class="text-xl font-bold mb-2 rotate-12">Sem Dados Disponíveis</h2>
        </div>

        <!-- Produtos com Total Vendas Zero -->
        <div class="chart-container" v-if="zeroSalesProducts.length > 0">
            <h2 class="text-xl font-bold mb-2">Produtos Menos<br> Vendas</h2>
            <table class="min-w-full bg-white border border-gray-400 rounded-lg">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border-b">Nome do Produto</th>
                        <th class="px-4 py-2 border-b">Total de Vendas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in zeroSalesProducts" :key="product.id">
                        <td class="px-4 py-2 border-b">{{ product.name }}</td>
                        <td class="px-4 py-2 border-b">0</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="chart-container flex justify-center items-center h-full" v-else>
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
            topProductsOptions: {
                chart: {
                    type: 'bar',
                    toolbar: {
                        show: true,
                    },
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: false,
                        columnWidth: '60%',
                    },
                },
                xaxis: {
                    categories: [],
                    labels: {
                        rotate: 0,
                        style: {
                            fontSize: '7px',
                        },
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                colors: ['#008FFB', '#00E396', '#FEB019'],
                title: {
                    align: 'center',
                    style: {
                        fontSize: '16px',
                        fontWeight: 'bold',
                    },
                },
                height: 300,
            },
            topProductsSeries: [
                {
                    name: 'Quantidade',
                    data: [],
                },
            ],
            zeroSalesProducts: [],
        };
    },
    watch: {
        body: {
            handler(newData) {
                this.updateCharts(newData);
            },
            deep: true,
        },
    },
    mounted() {
        this.updateCharts(this.body);
    },
    methods: {
        updateCharts(data) {
            // Produtos mais vendidos
            const sortedProducts = data
                .filter(product => product.total_vendas > 0)
                .sort((a, b) => b.total_vendas - a.total_vendas);

            const topProducts = sortedProducts.slice(0, 3);
            this.topProductsOptions.xaxis.categories = topProducts.map(product => product.name);
            this.topProductsSeries[0].data = topProducts.map(product => product.total_vendas);

            // Produtos com total vendas igual a 0
            this.zeroSalesProducts = data.filter(product => product.total_vendas === 0);

            this.$nextTick(() => {
                if (this.$refs.topProductsChart) {
                    this.$refs.topProductsChart.updateOptions(this.topProductsOptions, false);
                    this.$refs.topProductsChart.updateSeries(this.topProductsSeries, false);
                }
            });
        },
    },
};
</script>

<style scoped>
.chart-container {
    flex: 1;
    margin-right: 10px;
    max-width: 50%;
}
.chart-container:last-child {
    margin-right: 0;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    text-align: left;
    padding: 8px;
}
th {
    background-color: #f4f4f4;
}
</style>
