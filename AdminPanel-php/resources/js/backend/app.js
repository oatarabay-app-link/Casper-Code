import '@coreui/coreui'
import 'chart.js'
import '@coreui/coreui-plugin-chartjs-custom-tooltips'
/* eslint-disable object-shorthand */

/* global Chart, CustomTooltips, getStyle, hexToRgba */

/**
 * --------------------------------------------------------------------------
 * CoreUI Free Boostrap Admin Template (v2.1.12): main.js
 * Licensed under MIT (https://coreui.io/license)
 * --------------------------------------------------------------------------
 */

/* eslint-disable no-magic-numbers */
//Disable the on-canvas tooltip
// Chart.defaults.global.pointHitDetectionRadius = 1;
// Chart.defaults.global.tooltips.enabled = false;
// Chart.defaults.global.tooltips.mode = 'index';
// Chart.defaults.global.tooltips.position = 'nearest';
// Chart.defaults.global.tooltips.custom = CustomTooltips; // eslint-disable-next-line no-unused-vars

//First Card Chart

var labels = app_signups_chart_data.map(function(e) {
    return e.DT;
});
var data = app_signups_chart_data.map(function(e) {
    return e.total;
});

var cardChart1 = new Chart($('#card-signups'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'User SignUp',
            backgroundColor: 'rgba(255,255,255,.2)',
            borderColor: 'rgba(255,255,255,.55)',
            data: data
        }]
    },
    options: {
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        scales: {
            xAxes: [{
                display: false,
                barPercentage: 0.8
            }],
            yAxes: [{
                display: false
            }]
        }
    }
});


var labels_singedin = app_signedins_chart_data.map(function(e) {
    return e.DT;
});
var data_singedin = app_signedins_chart_data.map(function(e) {
    return e.total;
});
var cardChart2 = new Chart($('#card-signedins'), {
    type: 'bar',
    data: {
        labels: labels_singedin,
        datasets: [{
            label: 'User Signed In',
            backgroundColor: 'rgba(255,255,255,.2)',
            borderColor: 'rgba(255,255,255,.55)',
            data: data_singedin
        }]
    },
    options: {
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        scales: {
            xAxes: [{
                display: false,
                barPercentage: 0.8
            }],
            yAxes: [{
                display: false
            }]
        }
    }
});


var labels_first_time =app_first_time_connections_chart_data.map(function(e) {
    return e.DT;
});
var data_first_time = app_first_time_connections_chart_data.map(function(e) {
    return e.total;
});
var cardChart3 = new Chart($('#card-first_time'), {
    type: 'bar',
    data: {
        labels: labels_first_time,
        datasets: [{
            label: 'First time Connections',
            backgroundColor: 'rgba(255,255,255,.2)',
            borderColor: 'rgba(255,255,255,.55)',
            data: data_first_time
        }]
    },
    options: {
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        scales: {
            xAxes: [{
                display: false,
                barPercentage: 0.8
            }],
            yAxes: [{
                display: false
            }]
        }
    }
});

//IN App Purchases

var labels_in_app_purchases =in_app_purchases_chart_data.map(function(e) {
    return e.DT;
});
var data_in_app_purchases = in_app_purchases_chart_data.map(function(e) {
    return e.total;
});
var cardChart4 = new Chart($('#card-in_app_purchases'), {
    type: 'bar',
    data: {
        labels: labels_in_app_purchases,
        datasets: [{
            label: 'In App Purchases',
            backgroundColor: 'rgba(255,255,255,.2)',
            borderColor: 'rgba(255,255,255,.55)',
            data: data_in_app_purchases
        }]
    },
    options: {
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        scales: {
            xAxes: [{
                display: false,
                barPercentage: 0.8
            }],
            yAxes: [{
                display: false
            }]
        }
    }
});


//Sales Data

var labels_sales =sales_chart_data.map(function(e) {
    return e.DT;
});
var data_sales = sales_chart_data.map(function(e) {
    return e.total;
});
var cardChart5 = new Chart($('#card-sales'), {
    type: 'bar',
    data: {
        labels: labels_sales,
        datasets: [{
            label: 'Sales',
            backgroundColor: 'rgba(255,255,255,.2)',
            borderColor: 'rgba(255,255,255,.55)',
            data: data_sales
        }]
    },
    options: {
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        scales: {
            xAxes: [{
                display: false,
                barPercentage: 0.8
            }],
            yAxes: [{
                display: false
            }]
        }
    }
});
 //Sales Free Premium_trial

var labels_sales_free_premium_trial =sales_free_premium_trial_chart_data.map(function(e) {
    return e.DT;
});
var data_sales_free_premium_trial = sales_free_premium_trial_chart_data.map(function(e) {
    return e.total;
});
var cardChart5 = new Chart($('#card-sales_free_premium_trial'), {
    type: 'bar',
    data: {
        labels: labels_sales_free_premium_trial,
        datasets: [{
            label: 'Free Premium/ Trails',
            backgroundColor: 'rgba(255,255,255,.2)',
            borderColor: 'rgba(255,255,255,.55)',
            data: data_sales_free_premium_trial
        }]
    },
    options: {
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        scales: {
            xAxes: [{
                display: false,
                barPercentage: 0.8
            }],
            yAxes: [{
                display: false
            }]
        }
    }
});


//EMAIL Delivered

var labels_emails_delivered =emails_delivered_chart_data.map(function(e) {
    return e.DT;
});
var data_emails_delivered = emails_delivered_chart_data.map(function(e) {
    return e.total;
});
var cardChart6 = new Chart($('#card-emails_delivered'), {
    type: 'bar',
    data: {
        labels: labels_emails_delivered,
        datasets: [{
            label: 'Email Delivered',
            backgroundColor: 'rgba(255,255,255,.2)',
            borderColor: 'rgba(255,255,255,.55)',
            data: data_emails_delivered
        }]
    },
    options: {
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        scales: {
            xAxes: [{
                display: false,
                barPercentage: 0.8
            }],
            yAxes: [{
                display: false
            }]
        }
    }
});


//EMAIL Delivered

var labels_emails_not_delivered = emails_not_delivered_chart_data.map(function(e) {
    return e.DT;
});
var data_emails_not_delivered = emails_not_delivered_chart_data.map(function(e) {
    return e.total;
});
var cardChart7 = new Chart($('#card-emails_not_delivered'), {
    type: 'bar',
    data: {
        labels: labels_emails_not_delivered,
        datasets: [{
            label: 'Email Issues',
            backgroundColor: 'rgba(255,255,255,.2)',
            borderColor: 'rgba(255,255,255,.55)',
            data: data_emails_not_delivered
        }]
    },
    options: {
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        scales: {
            xAxes: [{
                display: false,
                barPercentage: 0.8
            }],
            yAxes: [{
                display: false
            }]
        }
    }
});



//EMAIL Delivered

var labels_subscriptions = subscriptions_chart_data.map(function(e) {
    return e.DT;
});
var data_subscriptions = subscriptions_chart_data.map(function(e) {
    return e.total;
});
var cardChart7 = new Chart($('#card-subscriptions'), {
    type: 'bar',
    data: {
        labels: labels_subscriptions,
        datasets: [{
            label: 'Subscriptions',
            backgroundColor: 'rgba(255,255,255,.2)',
            borderColor: 'rgba(255,255,255,.55)',
            data: data_subscriptions
        }]
    },
    options: {
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        scales: {
            xAxes: [{
                display: false,
                barPercentage: 0.8
            }],
            yAxes: [{
                display: false
            }]
        }
    }
});