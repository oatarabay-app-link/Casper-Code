import { create } from 'zustand';

export const useConnectStore = create((_set, get) => ({
  connectionStats: [
    { label: 'Active Connections', value: '4 Online', icon: 'Lan' },
    { label: 'Total Upload', value: '8.6 GB Today', icon: 'Upload' },
    { label: 'Total Download', value: '28.8 GB Today', icon: 'Download' },
    { label: 'Avg Session', value: '2h 47m', icon: 'AccessTime' },
  ],

  activeConnections: [
    {
      email: 'john.doe@email.com',
      server: 'US-East-1',
      location: 'New York',
      protocol: 'OpenVPN',
      session: '2h 34m',
      upload: '1.2 GB',
      download: '4.8 GB',
      status: 'Connected',
    },
    {
      email: 'jane.smith@email.com',
      server: 'EU-Central-1',
      location: 'Frankfurt',
      protocol: 'WireGuard',
      session: '45m',
      upload: '0.3 GB',
      download: '1.9 GB',
      status: 'Connected',
    },
    {
      email: 'mike.johnson@email.com',
      server: 'Asia-East-1',
      location: 'Tokyo',
      protocol: 'IKEv2',
      session: '1h 12m',
      upload: '0.8 GB',
      download: '2.1 GB',
      status: 'Disconnected',
    },
    {
      email: 'sarah.wilson@email.com',
      server: 'US-West-1',
      location: 'Los Angeles',
      protocol: 'OpenVPN',
      session: '3h 21m',
      upload: '2.1 GB',
      download: '7.2 GB',
      status: 'Connected',
    },
    {
      email: 'david.brown@email.com',
      server: 'UK-South-1',
      location: 'London',
      protocol: 'WireGuard',
      session: '6h 45m',
      upload: '4.2 GB',
      download: '12.8 GB',
      status: 'Connected',
    },
  ],
  downloadConnectionLogs: () => {
    if (typeof document === 'undefined') {
      return;
    }

    const connections = get().activeConnections ?? [];

    const headers = ['Email', 'Server', 'Location', 'Protocol', 'Session', 'Upload', 'Download', 'Status'];

    const rows = connections.map((conn) => [
      conn.email,
      conn.server,
      conn.location,
      conn.protocol,
      conn.session,
      conn.upload,
      conn.download,
      conn.status,
    ]);

    const escapeCell = (value) => {
      if (value == null) {
        return '';
      }

      const stringValue = String(value);

      if (stringValue.includes('"') || stringValue.includes(',') || stringValue.includes('\n')) {
        return `"${stringValue.replace(/"/g, '""')}"`;
      }

      return stringValue;
    };

    const csvContent = ['\ufeff' + headers.map(escapeCell).join(',')]
      .concat(rows.map((row) => row.map(escapeCell).join(',')))
      .join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);

    const timestamp = new Date()
      .toISOString()
      .replace(/[:.]/g, '-')
      .slice(0, 19);

    const link = document.createElement('a');
    link.href = url;
    link.download = `connection-logs-${timestamp}.csv`;
    link.style.display = 'none';

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    URL.revokeObjectURL(url);
  },
}));
