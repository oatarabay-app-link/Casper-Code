import { create } from 'zustand';

export const useSettingStore = create((set) => ({
  currentTab: 'General',
  setCurrentTab: (tab) => set({ currentTab: tab }),

  tabs: [
    { label: 'General', key: 'General' },
    { label: 'Security', key: 'Security' },
    { label: 'Servers', key: 'Servers' },
    { label: 'Notifications', key: 'Notifications' },
    { label: 'Users', key: 'Users' },
    { label: 'API', key: 'API' },
  ],

  generalConfig: {
    companyName: 'VPN Corp',
    systemName: 'VPN Admin Panel',
    adminEmail: 'admin@vpncorp.com',
    maintenanceMode: false,
    debugLogging: true,
  },

  securityConfig: {
    twoFactorAuth: true,
    ipWhitelist: true,
    sessionTimeout: 30,
    maxLoginAttempts: 5,
    allowedIPs: ['192.168.1.1', '10.0.0.1'],
  },

  updateSecurityConfig: (key, value) =>
    set((state) => ({
      securityConfig: {
        ...state.securityConfig,
        [key]: value,
      },
    })),

  serverConfig: {
    defaultProtocol: 'OpenVPN',
    maxConnections: 1000,
    autoSelection: true,
    loadBalancing: true,
    healthCheckInterval: 60,
    connectionTimeout: 30,
  },

  updateServerConfig: (key, value) =>
    set((state) => ({
      serverConfig: {
        ...state.serverConfig,
        [key]: value,
      },
    })),
  notificationsConfig: {
    emailEnabled: true,
    smtpServer: 'smtp.gmail.com',
    smtpUsername: 'your-email@gmail.com',
    smtpPort: 587,

    slackEnabled: true,
    slackWebhook: 'https://hooks.slack.com/services/...',
  },
  updateNotificationsConfig: (key, value) =>
  set((state) => ({
    notificationsConfig: {
      ...state.notificationsConfig,
      [key]: value,
    },
  })),
  usersConfig: {
  autoRegistration: true,
  emailVerification: true,
  defaultPlan: 'Basic',
  maxDevices: 5,
  trialPeriod: 7,
  monthlyLimit: 100,
},

updateUsersConfig: (key, value) =>
  set((state) => ({
    usersConfig: {
      ...state.usersConfig,
      [key]: value,
    },
  })),
apiConfig: {
  apiAccess: true,
  apiKey: 'vpn_sk_test_1234567890abcdef',
  rateLimit: 100,
  apiVersion: 'v1',
  webhookUrl: 'https://your-app.com/webhook',
  apiLogging: true,
},

updateApiConfig: (key, value) =>
  set((state) => ({
    apiConfig: {
      ...state.apiConfig,
      [key]: value,
    },
  })),
}));
