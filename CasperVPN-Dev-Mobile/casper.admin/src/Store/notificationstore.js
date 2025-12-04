import { create } from 'zustand';

export const useNotificationStore = create((set) => ({
  summary: {
    unread: 2,
    emailSent: 247,
    systemMessages: 12,
    responseTime: '2.3 min',
  },

  recent: [
    { title: 'Server US-East-1 High Load', detail: 'CPU usage at 89% for the last 10 minutes', time: '5 min ago', read: false },
    { title: 'Scheduled Maintenance', detail: 'EU-Central-1 will undergo maintenance tonight at 2 AM UTC', time: '2 hours ago', read: false },
    { title: 'Backup Completed', detail: 'Daily backup completed successfully', time: '3 hours ago', read: false },
    { title: 'Failed Login Attempts', detail: 'Multiple failed login attempts detected from IP 192.168.1.100', time: '4 hours ago', read: false },
    { title: 'New User Registration', detail: '50 new users registered in the last 24 hours', time: '1 day ago', read: false },
  ],

  settings: {
  allRead: false,

  markAllRead: () =>
    set((state) => ({
      summary: {
        ...state.summary,
        unread: 0,
      },
      recent: state.recent.map((notification) => ({
        ...notification,
        read: true,
      })),
      allRead: true,
    })),
    email: true,
    sms: true,
    push: false,
    slack: true,
    maintenance: true,
  },

  thresholds: {
    server: {
      cpu: 85,
      memory: 90,
      disk: 80,
    },
    security: {
      loginAttempts: 85,
      traffic: 90,
      newIPs: 80,
    },
    user: {
      drops: 5,
      spikeReq: 1000,
      spikeGB: 500,
      registrationsHour: 50,
      registrationsDay: 100,
    },
  },

  updateSetting: (key, value) =>
    set((state) => ({
      settings: {
        ...state.settings,
        [key]: value,
      },
    })),

  updateThreshold: (section, key, value) =>
    set((state) => ({
      thresholds: {
        ...state.thresholds,
        [section]: {
          ...state.thresholds[section],
          [key]: value,
        },
      },
    })),
}));
