<template>
  <div>
      <Pie :data="chartData" :options="chartOptions" />
  </div>
</template>

<script>
import { Pie } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

export default {
  name: 'PieChart',
  components: { Pie },
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
          };

          // Calculate average age by sex
          const ageBySex = this.body.reduce((acc, item) => {
              const sexo = item.sexo.trim(); // Remove extra spaces
              const idade = parseFloat(item.idade); // Convert age to number

              if (!acc[sexo]) {
                  acc[sexo] = { totalAge: 0, count: 0 };
              }
              acc[sexo].totalAge += idade;
              acc[sexo].count += 1;
              return acc;
          }, {});

          const labels = [];
          const data = [];
          const backgroundColor = [];
          const borderColor = [];

          for (const [sex, { totalAge, count }] of Object.entries(ageBySex)) {
              labels.push(sex);
              data.push(totalAge / count);
              backgroundColor.push(colorMap[sex] || '#CCCCCC'); // Use default color if not defined
              borderColor.push(colorMap[sex] || '#CCCCCC'); // Use default color if not defined
          }

          return {
              labels: labels,
              datasets: [{
                  label: 'Média de Idade por Sexo',
                  data: data,
                  backgroundColor: backgroundColor, // Colors for the slices
                  borderColor: borderColor, // Border colors of the slices
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
                              return `${context.label}: ${context.raw.toFixed(2)} anos`; // Format tooltip labels
                          }
                      }
                  },
                  title: {
                      display: true,
                      text: 'Média de Idade por Sexo',
                      font: {
                          size: 16 // Font size for the title
                      },
                      padding: {
                          top: 10,
                          bottom: 10 // Padding around the title
                      }
                  }
              }
          };
      }
  }
}
</script>

