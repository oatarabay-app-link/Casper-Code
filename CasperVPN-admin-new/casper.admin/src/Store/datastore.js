import { create } from 'zustand';

const clampPercent = (value) => Math.min(100, Math.max(0, value));

let backupTimerRef = null;

export const useDatabaseStore = create((set, get) => ({
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
    { id: 1, type: 'Full Backup', time: '2h ago', status: 'Success' },
    { id: 2, type: 'Incremental', time: '6h ago', status: 'Success' },
    { id: 3, type: 'Full Backup', time: '1d ago', status: 'Success' },
  ],

  queries: {
    select: 12847,
    insert: 3456,
    update: 1234,
    delete: 567,
    slowQueries: false,
  },

  isBackupQueued: false,

  refreshStats: () =>
    set((state) => {
      const currentSize = parseFloat(state.stats.size) || 2.4;
      const sizeDelta = (Math.random() * 0.3 - 0.1).toFixed(2);
      const newSize = Math.max(1.5, currentSize + parseFloat(sizeDelta));

      const connectionDelta = Math.floor(Math.random() * 11) - 5;
      const queryTimeCurrent = parseInt(state.stats.queryTime, 10) || 12;
      const queryTimeDelta = Math.floor(Math.random() * 5) - 2;

      const randomPercentShift = () => Math.floor(Math.random() * 11) - 5;

      return {
        stats: {
          ...state.stats,
          size: `${newSize.toFixed(2)} GB`,
          connections: Math.max(0, state.stats.connections + connectionDelta),
          queryTime: `${Math.max(4, queryTimeCurrent + queryTimeDelta)}ms`,
        },
        performance: {
          cpu: clampPercent(state.performance.cpu + randomPercentShift()),
          memory: clampPercent(state.performance.memory + randomPercentShift()),
          disk: clampPercent(state.performance.disk + randomPercentShift()),
        },
        queries: {
          ...state.queries,
          select: Math.max(0, state.queries.select + Math.floor(Math.random() * 501) - 250),
          insert: Math.max(0, state.queries.insert + Math.floor(Math.random() * 151) - 75),
          update: Math.max(0, state.queries.update + Math.floor(Math.random() * 101) - 50),
          delete: Math.max(0, state.queries.delete + Math.floor(Math.random() * 51) - 25),
          slowQueries: Math.random() < 0.1 ? true : state.queries.slowQueries && Math.random() < 0.5,
        },
      };
    }),

  queueBackup: (type = 'Manual Backup') => {
    if (get().isBackupQueued) {
      return;
    }

    const timestamp = new Date();
    const formattedTime = timestamp.toLocaleString(undefined, {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
    });
    const entryId = timestamp.getTime();

    set((state) => ({
      backups: [
        { id: entryId, type, time: `${formattedTime}`, status: 'Queued' },
        ...state.backups,
      ].slice(0, 6),
      stats: {
        ...state.stats,
        lastBackup: 'Queued just now',
      },
      isBackupQueued: true,
    }));

    if (backupTimerRef) {
      clearTimeout(backupTimerRef);
    }

    backupTimerRef = setTimeout(() => {
      set((state) => {
        const updatedBackups = state.backups.map((backup) =>
          backup.id === entryId ? { ...backup, status: 'Success' } : backup,
        );

        return {
          backups: updatedBackups,
          stats: {
            ...state.stats,
            lastBackup: `${formattedTime} (Success)`,
          },
          isBackupQueued: false,
        };
      });
      backupTimerRef = null;
    }, 5000);
  },
}));
