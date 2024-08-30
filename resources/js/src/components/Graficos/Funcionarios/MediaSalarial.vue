<template>
  <Bar :data="chartData" :options="chartOptions" />
</template>

<script>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
  name: 'BarChart',
  components: { Bar },
  props: {
      body: {
          type: Array,
          required: true
      }
  },
  computed: {
      chartData() {
          // Define colors for each sex
          const colorMap = {
              'Masculino': '#36A2EB', // Color for 'Masculino'
              'Feminino': '#FF6384',  // Color for 'Feminino'
              'Outro': '#FFCE56'     // Color for 'Outro'
          };

          // Calculate salaries by sex
          const salariesBySex = this.body.reduce((acc, item) => {
              const sexo = item.sexo.trim(); // Remove extra spaces
              const salario = parseFloat(item.salario); // Convert salary to number

              if (!acc[sexo]) {
                  acc[sexo] = { total: 0, count: 0 };
              }
              acc[sexo].total += salario;
              acc[sexo].count += 1;
              return acc;
          }, {});

          // Prepare chart labels and data
          const labels = [];
          const data = [];
          const backgroundColor = [];
          const borderColor = [];

          for (const [sex, { total, count }] of Object.entries(salariesBySex)) {
              labels.push(sex);
              data.push(total / count); // Average salary
              backgroundColor.push(colorMap[sex] || '#CCCCCC'); // Use default color if not defined
              borderColor.push(colorMap[sex] || '#CCCCCC'); // Use default color if not defined
          }

          return {
              labels: labels,
              datasets: [{
                  label: 'Média Salarial por Sexo',
                  data: data,
                  backgroundColor: backgroundColor, // Background color of the bars
                  borderColor: borderColor, // Border color of the bars
                  borderWidth: 1 // Width of the border
              }]
          };
      },
      chartOptions() {
          return {
              responsive: true,
              plugins: {
                  legend: {
                      position: 'top'
                  },
                  tooltip: {
                      callbacks: {
                          label: function(context) {
                              return `R$ ${context.raw.toFixed(2)}`; // Format tooltip labels
                          }
                      }
                  },
                  title: {
                      display: true,
                      text: 'Média Salarial por Sexo',
                      font: {
                          size: 16 // Font size for the title
                      },
                      padding: {
                          top: 10,
                          bottom: 10 // Padding around the title
                      }
                  }
              },
              scales: {
                  x: {
                      beginAtZero: true,
                      title: {
                          display: true,
                          text: 'Sexo'
                      }
                  },
                  y: {
                      beginAtZero: true,
                      title: {
                          display: true,
                          text: 'Média Salarial'
                      },
                      ticks: {
                          callback: function(value) {
                              return `R$ ${value}`;
                          }
                      }
                  }
              }
          };
      }
  }
}
</script>
