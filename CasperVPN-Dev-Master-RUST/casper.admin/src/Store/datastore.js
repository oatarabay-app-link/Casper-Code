import { create } from 'zustand';

export const useDatabaseStore = create(() => ({
  stats: {
    size: '2.4 GB',
    connections: 47,
    queryTime: '12ms',
    lastBackup: '2h ago',
  },

  status: {
    primary: 'Online',
    replica: 'Online',
    cache: 'Warning',
  },

  performance: {
    cpu: 34,
    memory: 67,
    disk: 23,
  },

  backups: [
    { type: 'Full Backup', time: '2h ago', status: 'Success' },
    { type: 'Incremental', time: '6h ago', status: 'Success' },
    { type: 'Full Backup', time: '1d ago', status: 'Success' },
  ],

  queries: {
    select: 12847,
    insert: 3456,
    update: 1234,
    delete: 567,
    slowQueries: false,
  },
}));
