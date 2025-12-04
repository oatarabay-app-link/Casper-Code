import { create } from 'zustand';

let scanTimerRef = null;
let scanMessagesQueue = [];
let scanMessageStageSize = 1;

export const useSecurityStore = create((set, get) => ({
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
  scanStatus: 'idle',
  scanProgress: 0,
  scanMessage: '',
  startSecurityScan: () => {
    const { scanStatus } = get();
    if (scanStatus === 'running') {
      return;
    }

    if (scanTimerRef) {
      clearInterval(scanTimerRef);
      scanTimerRef = null;
    }

    scanMessagesQueue = [
      'Contacting VPN edge nodes…',
      'Authenticating secure channel…',
      'Checking firewall policies…',
      'Validating IP tables and NAT routing…',
      'Scanning open ports for anomalies…',
      'Verifying intrusion detection signatures…',
      'Tracing VPN tunnels for leaks…',
      'Inspecting TLS certificates…',
      'Re-indexing security baselines…',
      'Generating compliance summary…',
    ];

    set({
      scanStatus: 'running',
      scanProgress: 0,
      scanMessage: scanMessagesQueue.shift() ?? 'Security scan initiated. Monitoring system integrity...',
    });

    const totalDurationMs = 12000;
    const tickInterval = 500;
    const totalTicks = Math.ceil(totalDurationMs / tickInterval);
    scanMessageStageSize = Math.max(1, Math.floor(totalTicks / (scanMessagesQueue.length || 1)));
    let tickCount = 0;

  scanTimerRef = setInterval(() => {
      tickCount += 1;

      set((state) => {
        const nextProgress = Math.min(Math.round((tickCount / totalTicks) * 100), 100);
        let message = state.scanMessage;
        if (scanMessagesQueue.length > 0 && tickCount % scanMessageStageSize === 0) {
          message = scanMessagesQueue.shift();
        }

        if (nextProgress >= 100) {
          if (scanTimerRef) {
            clearInterval(scanTimerRef);
            scanTimerRef = null;
          }
          return {
            scanProgress: 100,
            scanStatus: 'completed',
            scanMessage: 'Security scan completed successfully. No critical issues detected.',
          };
        }

        return {
          scanProgress: nextProgress,
          scanMessage: message ?? 'Continuing deep scan…',
        };
      });
    }, tickInterval);
  },
  resetSecurityScan: () => {
    if (scanTimerRef) {
      clearInterval(scanTimerRef);
      scanTimerRef = null;
    }

    set({
      scanStatus: 'idle',
      scanProgress: 0,
      scanMessage: '',
    });
    scanMessagesQueue = [];
    scanMessageStageSize = 1;
  },
  downloadSecurityReport: () => {
    if (typeof document === 'undefined') {
      return;
    }

    const { securityStats, recentEvents, securityPolicies } = get();

    const lines = [
      'CasperVPN - Security Report',
      `Generated: ${new Date().toISOString()}`,
      '',
      'Summary Metrics:',
      ...securityStats.map((stat) => `- ${stat.label}: ${stat.value}`),
      '',
      'Active Security Policies:',
      ...securityPolicies.map((policy) => `- ${policy.policy}: ${policy.status}`),
      '',
      'Recent Events:',
      ...recentEvents.map((event) =>
        `- [${event.time}] ${event.type} | User: ${event.user} | IP: ${event.ip} | Severity: ${event.severity}`,
      ),
      '',
      'End of report.',
    ];

    const blob = new Blob([lines.join('\n')], { type: 'text/plain;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const timestamp = new Date()
      .toISOString()
      .replace(/[:.]/g, '-')
      .slice(0, 19);

    const link = document.createElement('a');
    link.href = url;
    link.download = `security-report-${timestamp}.txt`;
    link.style.display = 'none';

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    URL.revokeObjectURL(url);
  },
}));
