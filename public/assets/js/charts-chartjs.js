/**
 * Charts ChartsJS
 */
'use strict';

(function () {
  // Color Variables
  const purpleColor = '#836AF9',
    yellowColor = '#ffe800',
    cyanColor = '#28dac6',
    orangeColor = '#FF8132',
    orangeLightColor = '#FDAC34',
    oceanBlueColor = '#299AFF',
    greyColor = '#4F5D70',
    greyLightColor = '#EDF1F4',
    blueColor = '#2B9AFF',
    blueLightColor = '#84D0FF';

  let cardColor, headingColor, labelColor, borderColor, legendColor;

  if (isDarkStyle) {
    cardColor = config.colors_dark.cardColor;
    headingColor = config.colors_dark.headingColor;
    labelColor = config.colors_dark.textMuted;
    legendColor = config.colors_dark.bodyColor;
    borderColor = config.colors_dark.borderColor;
  } else {
    cardColor = config.colors.cardColor;
    headingColor = config.colors.headingColor;
    labelColor = config.colors.textMuted;
    legendColor = config.colors.bodyColor;
    borderColor = config.colors.borderColor;
  }

  // Set height according to their data-height
  // --------------------------------------------------------------------
  const chartList = document.querySelectorAll('.chartjs');
  chartList.forEach(function (chartListItem) {
    chartListItem.height = chartListItem.dataset.height;
  });

  // Bar Chart
  // --------------------------------------------------------------------
  const barChart = document.getElementById('barChart');
  if (barChart) {
    const barChartVar = new Chart(barChart, {
      type: 'bar',
      data: {
        labels: [
          '7/12',
          '8/12',
          '9/12',
          '10/12',
          '11/12',
          '12/12',
          '13/12',
          '14/12',
          '15/12',
          '16/12',
          '17/12',
          '18/12',
          '19/12'
        ],
        datasets: [
          {
            data: [275, 90, 190, 205, 125, 85, 55, 87, 127, 150, 230, 280, 190],
            backgroundColor: cyanColor,
            borderColor: 'transparent',
            maxBarThickness: 15,
            borderRadius: {
              topRight: 15,
              topLeft: 15
            }
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 500
        },
        plugins: {
          tooltip: {
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          },
          legend: {
            display: false
          }
        },
        scales: {
          x: {
            grid: {
              color: borderColor,
              drawBorder: false,
              borderColor: borderColor
            },
            ticks: {
              color: labelColor
            }
          },
          y: {
            min: 0,
            max: 400,
            grid: {
              color: borderColor,
              drawBorder: false,
              borderColor: borderColor
            },
            ticks: {
              stepSize: 100,
              color: labelColor
            }
          }
        }
      }
    });
  }

  // Horizontal Bar Chart
  // --------------------------------------------------------------------

  const horizontalBarChart = document.getElementById('horizontalBarChart');
  if (horizontalBarChart) {
    const horizontalBarChartVar = new Chart(horizontalBarChart, {
      type: 'bar',
      data: {
        labels: ['MON', 'TUE', 'WED ', 'THU', 'FRI', 'SAT', 'SUN'],
        datasets: [
          {
            data: [710, 350, 470, 580, 230, 460, 120],
            backgroundColor: config.colors.info,
            borderColor: 'transparent',
            maxBarThickness: 15
          }
        ]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 500
        },
        elements: {
          bar: {
            borderRadius: {
              topRight: 15,
              bottomRight: 15
            }
          }
        },
        plugins: {
          tooltip: {
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          },
          legend: {
            display: false
          }
        },
        scales: {
          x: {
            min: 0,
            grid: {
              color: borderColor,
              borderColor: borderColor
            },
            ticks: {
              color: labelColor
            }
          },
          y: {
            grid: {
              borderColor: borderColor,
              display: false,
              drawBorder: false
            },
            ticks: {
              color: labelColor
            }
          }
        }
      }
    });
  }

  // Line Chart
  // --------------------------------------------------------------------

  const lineChart = document.getElementById('lineChart');
  if (lineChart) {
    const lineChartVar = new Chart(lineChart, {
      type: 'line',
      data: {
        labels: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140],
        datasets: [
          {
            data: [80, 150, 180, 270, 210, 160, 160, 202, 265, 210, 270, 255, 290, 360, 375],
            label: 'Europe',
            borderColor: config.colors.danger,
            tension: 0.5,
            pointStyle: 'circle',
            backgroundColor: config.colors.danger,
            fill: false,
            pointRadius: 1,
            pointHoverRadius: 5,
            pointHoverBorderWidth: 5,
            pointBorderColor: 'transparent',
            pointHoverBorderColor: cardColor,
            pointHoverBackgroundColor: config.colors.danger
          },
          {
            data: [80, 125, 105, 130, 215, 195, 140, 160, 230, 300, 220, 170, 210, 200, 280],
            label: 'Asia',
            borderColor: config.colors.primary,
            tension: 0.5,
            pointStyle: 'circle',
            backgroundColor: config.colors.primary,
            fill: false,
            pointRadius: 1,
            pointHoverRadius: 5,
            pointHoverBorderWidth: 5,
            pointBorderColor: 'transparent',
            pointHoverBorderColor: cardColor,
            pointHoverBackgroundColor: config.colors.primary
          },
          {
            data: [80, 99, 82, 90, 115, 115, 74, 75, 130, 155, 125, 90, 140, 130, 180],
            label: 'Africa',
            borderColor: yellowColor,
            tension: 0.5,
            pointStyle: 'circle',
            backgroundColor: yellowColor,
            fill: false,
            pointRadius: 1,
            pointHoverRadius: 5,
            pointHoverBorderWidth: 5,
            pointBorderColor: 'transparent',
            pointHoverBorderColor: cardColor,
            pointHoverBackgroundColor: yellowColor
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            grid: {
              color: borderColor,
              drawBorder: false,
              borderColor: borderColor
            },
            ticks: {
              color: labelColor
            }
          },
          y: {
            scaleLabel: {
              display: true
            },
            min: 0,
            max: 400,
            ticks: {
              color: labelColor,
              stepSize: 100
            },
            grid: {
              color: borderColor,
              drawBorder: false,
              borderColor: borderColor
            }
          }
        },
        plugins: {
          tooltip: {
            // Updated default tooltip UI
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          },
          legend: {
            position: 'top',
            align: 'start',
            rtl: isRtl,
            labels: {
              usePointStyle: true,
              padding: 35,
              boxWidth: 6,
              boxHeight: 6,
              color: legendColor
            }
          }
        }
      }
    });
  }

  // Radar Chart
  // --------------------------------------------------------------------

  const radarChart = document.getElementById('radarChart');
  if (radarChart) {
    // For radar gradient color
    const gradientBlue = radarChart.getContext('2d').createLinearGradient(0, 0, 0, 150);
    gradientBlue.addColorStop(0, 'rgba(85, 85, 255, 0.9)');
    gradientBlue.addColorStop(1, 'rgba(151, 135, 255, 0.8)');

    const gradientRed = radarChart.getContext('2d').createLinearGradient(0, 0, 0, 150);
    gradientRed.addColorStop(0, 'rgba(255, 85, 184, 0.9)');
    gradientRed.addColorStop(1, 'rgba(255, 135, 135, 0.8)');

    const radarChartVar = new Chart(radarChart, {
      type: 'radar',
      data: {
        labels: ['STA', 'STR', 'AGI', 'VIT', 'CHA', 'INT'],
        datasets: [
          {
            label: 'Donté Panlin',
            data: [25, 59, 90, 81, 60, 82],
            fill: true,
            pointStyle: 'dash',
            backgroundColor: gradientRed,
            borderColor: 'transparent',
            pointBorderColor: 'transparent'
          },
          {
            label: 'Mireska Sunbreeze',
            data: [40, 100, 40, 90, 40, 90],
            fill: true,
            pointStyle: 'dash',
            backgroundColor: gradientBlue,
            borderColor: 'transparent',
            pointBorderColor: 'transparent'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 500
        },
        scales: {
          r: {
            ticks: {
              maxTicksLimit: 1,
              display: false,
              color: labelColor
            },
            grid: {
              color: borderColor
            },
            angleLines: { color: borderColor },
            pointLabels: {
              color: labelColor
            }
          }
        },
        plugins: {
          legend: {
            rtl: isRtl,
            position: 'top',
            labels: {
              padding: 25,
              color: legendColor
            }
          },
          tooltip: {
            // Updated default tooltip UI
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          }
        }
      }
    });
  }

  // Polar Chart
  // --------------------------------------------------------------------

  const polarChart = document.getElementById('polarChart');
  if (polarChart) {
    const polarChartVar = new Chart(polarChart, {
      type: 'polarArea',
      data: {
        labels: ['Africa', 'Asia', 'Europe', 'America', 'Antarctica', 'Australia'],
        datasets: [
          {
            label: 'Population (millions)',
            backgroundColor: [purpleColor, yellowColor, orangeColor, oceanBlueColor, greyColor, cyanColor],
            data: [19, 17.5, 15, 13.5, 11, 9],
            borderWidth: 0
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 500
        },
        scales: {
          r: {
            ticks: {
              display: false,
              color: labelColor
            },
            grid: {
              display: false
            }
          }
        },
        plugins: {
          tooltip: {
            // Updated default tooltip UI
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          },
          legend: {
            rtl: isRtl,
            position: 'right',
            labels: {
              usePointStyle: true,
              padding: 25,
              boxWidth: 8,
              boxHeight: 8,
              color: legendColor
            }
          }
        }
      }
    });
  }

  // Bubble Chart
  // --------------------------------------------------------------------

  const bubbleChart = document.getElementById('bubbleChart');
  if (bubbleChart) {
    const bubbleChartVar = new Chart(bubbleChart, {
      type: 'bubble',
      data: {
        animation: {
          duration: 10000
        },
        datasets: [
          {
            label: 'Dataset 1',
            backgroundColor: purpleColor,
            borderColor: purpleColor,
            data: [
              {
                x: 20,
                y: 74,
                r: 10
              },
              {
                x: 10,
                y: 110,
                r: 5
              },
              {
                x: 30,
                y: 165,
                r: 7
              },
              {
                x: 40,
                y: 200,
                r: 20
              },
              {
                x: 90,
                y: 185,
                r: 7
              },
              {
                x: 50,
                y: 240,
                r: 7
              },
              {
                x: 60,
                y: 275,
                r: 10
              },
              {
                x: 70,
                y: 305,
                r: 5
              },
              {
                x: 80,
                y: 325,
                r: 4
              },
              {
                x: 100,
                y: 310,
                r: 5
              },
              {
                x: 110,
                y: 240,
                r: 5
              },
              {
                x: 120,
                y: 270,
                r: 7
              },
              {
                x: 130,
                y: 300,
                r: 6
              }
            ]
          },
          {
            label: 'Dataset 2',
            backgroundColor: yellowColor,
            borderColor: yellowColor,
            data: [
              {
                x: 30,
                y: 72,
                r: 5
              },
              {
                x: 40,
                y: 110,
                r: 7
              },
              {
                x: 20,
                y: 135,
                r: 6
              },
              {
                x: 10,
                y: 160,
                r: 12
              },
              {
                x: 50,
                y: 285,
                r: 5
              },
              {
                x: 60,
                y: 235,
                r: 5
              },
              {
                x: 70,
                y: 275,
                r: 7
              },
              {
                x: 80,
                y: 290,
                r: 4
              },
              {
                x: 90,
                y: 250,
                r: 10
              },
              {
                x: 100,
                y: 220,
                r: 7
              },
              {
                x: 120,
                y: 230,
                r: 4
              },
              {
                x: 110,
                y: 320,
                r: 15
              },
              {
                x: 130,
                y: 330,
                r: 7
              }
            ]
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,

        scales: {
          x: {
            min: 0,
            max: 140,
            grid: {
              color: borderColor,
              drawBorder: false,
              borderColor: borderColor
            },
            ticks: {
              stepSize: 10,
              color: labelColor
            }
          },
          y: {
            min: 0,
            max: 400,
            grid: {
              color: borderColor,
              drawBorder: false,
              borderColor: borderColor
            },
            ticks: {
              stepSize: 100,
              color: labelColor
            }
          }
        },
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            // Updated default tooltip UI
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          }
        }
      }
    });
  }

  // LineArea Chart
  // --------------------------------------------------------------------

  const lineAreaChart = document.getElementById('lineAreaChart');
  if (lineAreaChart) {
    const lineAreaChartVar = new Chart(lineAreaChart, {
      type: 'line',
      data: {
        labels: [
          '7/12',
          '8/12',
          '9/12',
          '10/12',
          '11/12',
          '12/12',
          '13/12',
          '14/12',
          '15/12',
          '16/12',
          '17/12',
          '18/12',
          '19/12',
          '20/12',
          ''
        ],
        datasets: [
          {
            label: 'Africa',
            data: [40, 55, 45, 75, 65, 55, 70, 60, 100, 98, 90, 120, 125, 140, 155],
            tension: 0,
            fill: true,
            backgroundColor: blueColor,
            pointStyle: 'circle',
            borderColor: 'transparent',
            pointRadius: 0.5,
            pointHoverRadius: 5,
            pointHoverBorderWidth: 5,
            pointBorderColor: 'transparent',
            pointHoverBackgroundColor: blueColor,
            pointHoverBorderColor: cardColor
          },
          {
            label: 'Asia',
            data: [70, 85, 75, 150, 100, 140, 110, 105, 160, 150, 125, 190, 200, 240, 275],
            tension: 0,
            fill: true,
            backgroundColor: blueLightColor,
            pointStyle: 'circle',
            borderColor: 'transparent',
            pointRadius: 0.5,
            pointHoverRadius: 5,
            pointHoverBorderWidth: 5,
            pointBorderColor: 'transparent',
            pointHoverBackgroundColor: blueLightColor,
            pointHoverBorderColor: cardColor
          },
          {
            label: 'Europe',
            data: [240, 195, 160, 215, 185, 215, 185, 200, 250, 210, 195, 250, 235, 300, 315],
            tension: 0,
            fill: true,
            backgroundColor: greyLightColor,
            pointStyle: 'circle',
            borderColor: 'transparent',
            pointRadius: 0.5,
            pointHoverRadius: 5,
            pointHoverBorderWidth: 5,
            pointBorderColor: 'transparent',
            pointHoverBackgroundColor: greyLightColor,
            pointHoverBorderColor: cardColor
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            rtl: isRtl,
            align: 'start',
            labels: {
              usePointStyle: true,
              padding: 35,
              boxWidth: 6,
              boxHeight: 6,
              color: legendColor
            }
          },
          tooltip: {
            // Updated default tooltip UI
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          }
        },
        scales: {
          x: {
            grid: {
              color: 'transparent',
              borderColor: borderColor
            },
            ticks: {
              color: labelColor
            }
          },
          y: {
            min: 0,
            max: 400,
            grid: {
              color: 'transparent',
              borderColor: borderColor
            },
            ticks: {
              stepSize: 100,
              color: labelColor
            }
          }
        }
      }
    });
  }

  // Doughnut Chart
  // --------------------------------------------------------------------

  const doughnutChart = document.getElementById('doughnutChart');
  if (doughnutChart) {
    const doughnutChartVar = new Chart(doughnutChart, {
      type: 'doughnut',
      data: {
        labels: ['Tablet', 'Mobile', 'Desktop'],
        datasets: [
          {
            data: [10, 10, 80],
            backgroundColor: [cyanColor, orangeLightColor, config.colors.primary],
            borderWidth: 0,
            pointStyle: 'rectRounded'
          }
        ]
      },
      options: {
        responsive: true,
        animation: {
          duration: 500
        },
        cutout: '68%',
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                const label = context.labels || '',
                  value = context.parsed;
                const output = ' ' + label + ' : ' + value + ' %';
                return output;
              }
            },
            // Updated default tooltip UI
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          }
        }
      }
    });
  }

  // Scatter Chart
  // --------------------------------------------------------------------

  const scatterChart = document.getElementById('scatterChart');
  if (scatterChart) {
    const scatterChartVar = new Chart(scatterChart, {
      type: 'scatter',
      data: {
        datasets: [
          {
            label: 'United States',
            data: [
              { x: 40, y: 45 }, // T. Halmkin
              { x: 20, y: 15 }, // A. Cabbell
              { x: 25, y: 10 }, // S. Anstey
              { x: 15, y: 10 }  // B. Byrom
            ],
            backgroundColor: 'blue',
            borderColor: 'transparent',
            pointRadius: 5
          },
          {
            label: 'Japan',
            data: [
              { x: 45, y: 50 }, // J. Panner
              { x: 35, y: 40 }, // A. Duckerin
              { x: 30, y: 25 }, // C. Spires
              { x: 30, y: 20 }  // L. Sprull
            ],
            backgroundColor: 'red',
            borderColor: 'transparent',
            pointRadius: 5
          },
          {
            label: 'Canada',
            data: [
              { x: 35, y: 10 }, // F. Arend
              { x: 40, y: 5 }   // M. Butson
            ],
            backgroundColor: 'yellow',
            borderColor: 'transparent',
            pointRadius: 5
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 800
        },
        plugins: {
          legend: {
            position: 'top',
            labels: {
              usePointStyle: true,
              padding: 20,
              boxWidth: 10,
              boxHeight: 10,
              color: 'black'
            }
          },
          tooltip: {
            backgroundColor: '#f8f9fa',
            titleColor: '#212529',
            bodyColor: '#495057',
            borderWidth: 1,
            borderColor: '#ced4da'
          }
        },
        scales: {
          x: {
            title: {
              display: true,
              text: 'Months of Employment'
            },
            min: 0,
            max: 50,
            ticks: {
              stepSize: 10
            }
          },
          y: {
            title: {
              display: true,
              text: 'Months with Above Average Performance'
            },
            min: 0,
            max: 50,
            ticks: {
              stepSize: 10
            }
          }
        }
      }
    });
  }

})();
