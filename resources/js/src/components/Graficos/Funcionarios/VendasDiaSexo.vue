<template>
  <div class="flex text-center bg-[#dbdbdb] w-1/2 p-2 rounded-lg mb-2 shadow-2xl border-[1px] border-gray-400">
    <div class="flex-1">
      <div v-if="series.length > 0">
        <h2 class="text-xl font-bold mb-2">Valor das {{ cliente ? 'Compras' : 'Vendas' }} por Dia Separado por Sexo</h2>
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
    cliente: {
      type: Boolean,
      default: false,
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
            text: this.cliente? 'Valor das Compras (R$)' : "Valor das Vendas (R$)",
          },
          labels: {
            formatter: (value) => `R$ ${value.toFixed(2)}`, // Limita o eixo Y a 2 casas decimais
          },
        },
        dataLabels: {
          enabled: true,
          formatter: (value) => `R$ ${value.toFixed(2)}`, // Mostra valores com 2 casas decimais
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
            formatter: (value) => `R$ ${value.toFixed(2)}`, // Formata o tooltip para 2 casas decimais
          },
        },
        colors: ["#007bff", "#FF69B4"],
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
      // Agrupando os dados por data e sexo
      const groupedData = data.reduce((acc, pedido) => {
        const date = new Date(pedido.created_at).toISOString().split("T")[0]; // Formata a data como YYYY-MM-DD
        const sexo = this.cliente ? pedido.cliente_sexo : pedido.funcionario_sexo;
        const total = parseFloat(pedido.total);

        if (!acc[date]) {
          acc[date] = { Masculino: 0, Feminino: 0 };
        }

        acc[date][sexo] += total;

        return acc;
      }, {});

      const dates = Object.keys(groupedData).sort(); // Ordena as datas

      this.series = [
        {
          name: "Masculino",
          data: dates.map((date) => ({
            x: new Date(date).getTime(),
            y: parseFloat(groupedData[date].Masculino.toFixed(2)), // Limita a 2 casas decimais
          })),
        },
        {
          name: "Feminino",
          data: dates.map((date) => ({
            x: new Date(date).getTime(),
            y: parseFloat(groupedData[date].Feminino.toFixed(2)), // Limita a 2 casas decimais
          })),
        },
      ];

      this.chartOptions.xaxis.categories = dates.map((date) => new Date(date).getTime());
    },
  },
};
</script>
