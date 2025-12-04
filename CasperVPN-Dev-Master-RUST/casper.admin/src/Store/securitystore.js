import { create } from 'zustand';

export const useSecurityStore = create(() => ({
  // 📊 Summary Stats
  securityStats: [
    { label: 'Critical Alerts', value: 1 },
    { label: 'High Priority', value: 2 },
    { label: 'Blocked Threats', value: 2 },
    { label: 'Security Score', value: '94%' },
  ],

  // 🧠 Event Logs
  recentEvents: [
    {
      type: 'Failed Login',
      user: 'unknown@hacker.com',
      ip: '192.168.1.100',
      severity: 'High',
      time: '2 min ago',
    },
    {
      type: 'Suspicious Traffic',
      user: 'john.doe@email.com',
      ip: '10.0.0.45',
      severity: 'Medium',
      time: '15 min ago',
    },
    {
      type: 'Certificate Expired',
      user: 'system',
      ip: 'N/A',
      severity: 'High',
      time: '1 hour ago',
    },
    {
      type: 'DDoS Attempt',
      user: 'multiple sources',
      ip: 'various',
      severity: 'Critical',
      time: '2 hours ago',
    },
    {
      type: 'Unusual Location',
      user: 'jane.smith@email.com',
      ip: '203.45.67.89',
      severity: 'Low',
      time: '3 hours ago',
    },
  ],

  // 🔐 Policy Status
  securityPolicies: [
    { policy: 'Multi-factor Auth', status: 'Enabled' },
    { policy: 'IP Whitelisting', status: 'Active' },
    { policy: 'Rate Limiting', status: 'Disabled' },
    { policy: 'DDoS Protection', status: 'Active' },
    { policy: 'Session Timeout', status: '30 min' },
  ],
}));
