import { create } from 'zustand';

export const useServerStore = create(() => ({
  serverStats: [
    { label: 'Total Servers', value: '5', icon: 'Cloud' },
    { label: 'Total Users', value: '3,907', icon: 'People' },
    { label: 'Average Load', value: '48%', icon: 'Speed' },
  ],
  serverList: [
    {
      name: 'US-East-1',
      location: 'New York',
      ip: '198.51.100.1',
      status: 'Online',
      users: 1247,
      load: '65%',
    },
    {
      name: 'US-West-1',
      location: 'Los Angeles',
      ip: '198.51.100.2',
      status: 'Online',
      users: 892,
      load: '45%',
    },
    {
      name: 'EU-Central-1',
      location: 'Frankfurt',
      ip: '198.51.100.3',
      status: 'Maintenance',
      users: 0,
      load: '0%',
    },
    {
      name: 'Asia-East-1',
      location: 'Tokyo',
      ip: '198.51.100.4',
      status: 'Online',
      users: 634,
      load: '78%',
    },
    {
      name: 'UK-South-1',
      location: 'London',
      ip: '198.51.100.5',
      status: 'Online',
      users: 1134,
      load: '52%',
    },
  ],
}));
