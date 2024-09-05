<template>
  <div class="flex-1 text-center bg-[#dbdbdb] shadow-2xl rounded-lg p-2 border-[1px] border-gray-400">
    <div v-if="this.body.length > 0">
      <h2 class="text-xl font-bold mt-2">Pedidos por Sexo</h2>
      <apexchart type="bar" :options="barChartOptions" :series="barChartSeries" width="100%" />
    </div>
    <div v-else class="flex justify-center items-center h-full">
      <h2 class="text-xl font-bold mb-2 rotate-12">Sem Dados Disponíveis</h2>
    </div>
  </div>
</template>

<script>
import VueApexCharts from 'vue3-apexcharts'; // Certifique-se de que este import está correto

export default {
  components: {
    apexchart: VueApexCharts,
  },
  props: {
    body: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      barChartOptions: {},
      barChartSeries: []
    };
  },
  watch: {
    body: {
      immediate: true,
      handler() {
        this.updateChart();
      }
    }
  },
  methods: {
      updateChart() {
        const categorias = [...new Set(this.body.map(item => item.sexo))];
        const seriesData = categorias.map(sexo => {
          const totalPedidos = this.body.filter(item => item.sexo === sexo)
            .reduce((acc, item) => acc + item.pedidos_count, 0);
          return {
            name: sexo,
            data: [totalPedidos]
          };
        });

        this.barChartOptions = {
          chart: {
            id: 'bar-chart-pedidos',
            type: 'bar',
          },
          xaxis: {
            categories: categorias,
            tickPlacement: 'on',
            labels: {
              rotate: -45, // Ajuste a rotação dos rótulos conforme necessário
            },
          },
          dataLabels: {
            enabled: true,
            offsetX: 0
          },
          colors: categorias.map(sexo => sexo === 'Masculino' ? '#007bff' : '#ff69b4') // Azul para masculino, rosa para feminino
        };

        this.barChartSeries = seriesData.length > 0 ? seriesData : [{
          name: 'Sem Dados',
          data: [0]
        }];
      }
    

  }
};
</script>
