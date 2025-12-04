/* eslint-disable object-shorthand */

/* global Chart, CustomTooltips, getStyle, hexToRgba */

/**
 * --------------------------------------------------------------------------
 * CoreUI Free Boostrap Admin Template (v2.1.12): main.js
 * Licensed under MIT (https://coreui.io/license)
 * --------------------------------------------------------------------------
 */

/* eslint-disable no-magic-numbers */
// Disable the on-canvas tooltip
Chart.defaults.global.pointHitDetectionRadius = 1;
Chart.defaults.global.tooltips.enabled = false;
Chart.defaults.global.tooltips.mode = 'index';
Chart.defaults.global.tooltips.position = 'nearest';
Chart.defaults.global.tooltips.custom = CustomTooltips; // eslint-disable-next-line no-unused-vars

//First Card Chart

var cardChart1 = new Chart($('#card-chart1'), {
    type: 'bar',
    data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [{
            label: 'My First dataset',
            backgroundColor: getStyle('--primary'),
            borderColor: 'rgba(255,255,255,.55)',
            data: [65, 59, 84, 84, 51, 55, 40]
        }]
    },
    options: {
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        scales: {
            xAxes: [{
                gridLines: {
                    color: 'transparent',
                    zeroLineColor: 'transparent'
                },
                ticks: {
                    fontSize: 2,
                    fontColor: 'transparent'
                }
            }],
            yAxes: [{
                display: false,
                ticks: {
                    display: false,
                    min: 35,
                    max: 89
                }
            }]
        },
        elements: {
            line: {
                borderWidth: 1
            },
            point: {
                radius: 4,
                hitRadius: 10,
                hoverRadius: 4
            }
        }
    }
}); // eslint-disable-next-line no-unused-vars