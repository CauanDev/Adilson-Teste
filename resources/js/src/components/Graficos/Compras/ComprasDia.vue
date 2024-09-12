<template>
  <div class="flex text-center bg-[#dbdbdb] w-1/2 p-2 rounded-lg mb-2 shadow-2xl border-[1px] border-gray-400">
      <div class="flex-1">
          <div v-if="series.length > 0">
              <h2 class="text-xl font-bold mb-2">Compras por Dia</h2>
              <apexchart type="line" width="99%" height="300" :options="chartOptions" :series="series"></apexchart>
          </div>
          <div v-else class="flex justify-center items-center h-full">
              <h2 class="text-xl font-bold mb-2 rotate-12">Sem Dados Disponíveis</h2>
          </div>
      </div>
  </div>
</template>

  
  <script>
  import VueApexCharts from "vue3-apexcharts";
  
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
        series: [],
        chartOptions: {
          chart: {
            type: "line",
          },
          xaxis: {
            type: "datetime",
            title: {
              text: "Data",
            },
            labels: {
              format: "dd/MM/yyyy",
            },
          },
          yaxis: {
            title: {
              text: "Valor Total das Vendas (R$)",
            },
            labels: {
              formatter: (value) => `R$ ${value.toFixed(2)}`,
            },
          },
          dataLabels: {
            enabled: true,
            formatter: (value) => `R$ ${value.toFixed(2)}`,
          },
          stroke: {
            curve: "smooth",
          },
          grid: {
            borderColor: "#e7e7e7",
          },
          legend: {
            position: "bottom",
          },
          tooltip: {
            x: {
              format: "dd/MM/yyyy",
            },
            y: {
              formatter: (value) => `R$ ${value.toFixed(2)}`,
            },
          },
          colors: ["#007bff"],
        },
      };
    },
    watch: {
      body: {
        handler(newData) {
          this.updateChart(newData);
        },
        deep: true,
      },
    },
    mounted() {
      this.updateChart(this.body);
    },
    methods: {
      updateChart(data) {
        // Agrupando os dados por data
        const groupedData = data.reduce((acc, pedido) => {
          const [day, month, year] = pedido.created_at.split("-"); // Divide dd-mm-yyyy
          const date = `${year}-${month}-${day}`; // Formata yyyy-mm-dd
  
          const total = parseFloat(pedido.total);
  
          if (!acc[date]) {
            acc[date] = 0;
          }
  
          acc[date] += total;
  
          return acc;
        }, {});
  
        const dates = Object.keys(groupedData).sort(); // Ordena as datas
  
        // Prepara os dados para o gráfico de linha
        this.series = [
          {
            name: "Total de Vendas",
            data: dates.map((date) => ({
              x: new Date(date).getTime(),
              y: parseFloat(groupedData[date].toFixed(2)),
            })),
          },
        ];
  
        this.chartOptions.xaxis.categories = dates.map((date) => new Date(date).getTime());
      },
    },
  };
  </script>
  