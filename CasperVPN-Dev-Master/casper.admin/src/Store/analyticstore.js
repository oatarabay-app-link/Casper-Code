import { create } from 'zustand';

export const useAnalyticStore = create(() => ({
  // 📊 Top Summary Stats
  analyticsStats: [
    { label: 'Total Sessions', value: '24,891', change: '+18.2%' },
    { label: 'Data Transferred', value: '1.2 TB', change: '+12.5%' },
    { label: 'Avg Session Time', value: '2h 47m', change: '+8.1%' },
    { label: 'Uptime', value: '99.8%', change: 'Network availability' },
  ],

  // 📈 Chart Labels & Data
  chartData: {
    labels: ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11'],
    datasets: [
      {
        label: 'Active Connections',
        data: [5, 9, 12, 18, 24, 28, 30, 26, 21, 16, 12, 9],
        borderColor: '#6366f1',
        backgroundColor: 'rgba(99, 102, 241, 0.3)',
        tension: 0.4,
      },
      {
        label: 'Bandwidth Usage',
        data: [12, 18, 22, 27, 34, 41, 45, 43, 37, 31, 24, 18],
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.3)',
        tension: 0.4,
      },
    ],
  },

  chartOptions: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { position: 'top' },
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          color: '#4B5563',
        },
      },
      x: {
        ticks: {
          color: '#4B5563',
        },
      },
    },
  },

  // 🗂 Server Usage
  topServers: [
    { name: 'US-East-1', location: 'New York', usage: '32.1%' },
    { name: 'EU-Central-1', location: 'Frankfurt', usage: '24.8%' },
    { name: 'UK-South-1', location: 'London', usage: '18.7%' },
    { name: 'Asia-East-1', location: 'Tokyo', usage: '15.4%' },
    { name: 'US-West-1', location: 'Los Angeles', usage: '9.0%' },
  ],

  // ⚙️ Protocol Usage
  protocolDistribution: [
    { protocol: 'OpenVPN', percent: '60%' },
    { protocol: 'WireGuard', percent: '35%' },
    { protocol: 'IKEv2', percent: '5%' },
  ],

  // 🌍 Geo Usage
  geoDistribution: [
    { region: 'North America', percent: '45.2%' },
    { region: 'Europe', percent: '34.7%' },
    { region: 'Asia', percent: '15.8%' },
    { region: 'Other', percent: '4.3%' },
  ],
}));
