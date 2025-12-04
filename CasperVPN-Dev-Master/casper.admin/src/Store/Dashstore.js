import { create } from 'zustand';

export const useDashStore = create((set) => ({
  // ✅ Stat Cards
  dashboardStats: [
    { label: 'Total Users', value: '1,260', icon: 'Storage' },
    { label: 'Active Servers', value: '18', icon: 'Dns' },
    { label: 'Total Connections', value: '5.2K', icon: 'Wifi' },
    { label: 'Security Alerts', value: '4', icon: 'Warning' },
  ],

  // 📈 Real-Time Chart
  chartData: {
    labels: ['00h', '04h', '08h', '12h', '16h', '20h', '24h'],
    datasets: [
      {
        label: 'Connections',
        data: [120, 150, 180, 100, 250, 300, 280],
        borderColor: '#6366f1',
        backgroundColor: 'rgba(99, 102, 241, 0.2)',
        tension: 0.4,
      },
    ],
  },

  // 🖥️ Server Status Table
  serverStatus: [
    {
      name: 'US-East-1',
      location: 'New York',
      ip: '198.51.100.1',
      status: 'Online',
      users: 1247,
      load: '78%',
      uptime: '99.9%',
    },
    {
      name: 'EU-West-1',
      location: 'London',
      ip: '198.51.100.2',
      status: 'Online',
      users: 892,
      load: '45%',
      uptime: '99.8%',
    },
    {
      name: 'Asia-Pacific',
      location: 'Singapore',
      ip: '198.51.100.3',
      status: 'Maintenance',
      users: 0,
      load: '0%',
      uptime: '98.5%',
    },
    {
      name: 'US-West-1',
      location: 'California',
      ip: '198.51.100.4',
      status: 'Online',
      users: 567,
      load: '34%',
      uptime: '99.7%',
    },
    {
      name: 'EU-Central',
      location: 'Frankfurt',
      ip: '198.51.100.5',
      status: 'Offline',
      users: 0,
      load: '0%',
      uptime: '95.2%',
    },
  ],

  // 🕵️ Recent Activity
  recentActivity: [
    {
      type: 'Security Alert',
      detail: 'Suspicious login attempt from IP 192.168.1.100',
      time: '5 minutes ago',
      color: 'red',
    },
    {
      type: 'Server Start',
      detail: 'Server US-East-1 started successfully',
      time: '15 minutes ago',
      color: 'green',
    },
    {
      type: 'Connected',
      detail: 'User connected to EU-West-1 (john.doe@example.com)',
      time: '30 minutes ago',
      color: 'blue',
    },
    {
      type: 'New User',
      detail: 'User registration completed (jane.smith@example.com)',
      time: '1 hour ago',
      color: 'indigo',
    },
    {
      type: 'Disconnected',
      detail: 'User disconnected from Asia-Pacific (mike.wilson@example.com)',
      time: '1 hour ago',
      color: 'yellow',
    },
  ],
}));
