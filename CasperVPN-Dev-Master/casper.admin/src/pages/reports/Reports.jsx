import { useMemo } from 'react';

const downloadBlob = ({ content, filename, mimeType }) => {
  if (typeof window === 'undefined') return;

  const blob = new Blob([content], { type: mimeType });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = filename;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
};

const formatDate = (date) => date.toISOString().slice(0, 10);

const Reports = () => {
  const today = useMemo(() => new Date(), []);

  const reportDefinitions = useMemo(
    () => [
      {
        id: 'network-usage',
        name: 'Network Usage Summary',
        description: 'Bandwidth consumption and active device counts per plan for the last 24 hours.',
        format: 'CSV',
        tags: ['Usage', 'Operations'],
        generate: () => {
          const header = 'Plan,Bandwidth (GB),Sessions,Active Devices,Peak Hour';
          const sample = [
            ['Secure Lite', 482, 1213, 654, '19:00 UTC'],
            ['Pro Shield', 1134, 2144, 992, '20:00 UTC'],
            ['Enterprise Vanguard', 2450, 534, 420, '18:00 UTC'],
          ];
          const rows = sample
            .map((row) =>
              row
                .map((value) => {
                  const str = String(value ?? '');
                  return /[",\n]/.test(str) ? `"${str.replace(/"/g, '""')}"` : str;
                })
                .join(','),
            )
            .join('\n');
          return {
            content: `\uFEFF${header}\n${rows}`,
            filename: `network-usage-${formatDate(today)}.csv`,
            mimeType: 'text/csv;charset=utf-8;',
          };
        },
      },
      {
        id: 'revenue-snapshot',
        name: 'Revenue Snapshot',
        description: 'Subscription revenue, refunds, and upgrades for the current billing month.',
        format: 'CSV',
        tags: ['Finance', 'Revenue'],
        generate: () => {
          const header = 'Date,New Subscriptions,MRR (USD),ARR (USD),Refunds (USD),Upgrades';
          const baseDate = new Date(today);
          const entries = Array.from({ length: 5 }).map((_, idx) => {
            const date = new Date(baseDate);
            date.setDate(today.getDate() - idx);
            return [
              formatDate(date),
              42 + idx * 3,
              74650 + idx * 780,
              74650 * 12 + idx * 1000,
              1200 - idx * 50,
              15 + idx,
            ];
          });
          const rows = entries
            .map((row) => row.map((value) => value.toString()).join(','))
            .join('\n');
          return {
            content: `\uFEFF${header}\n${rows}`,
            filename: `revenue-snapshot-${formatDate(today)}.csv`,
            mimeType: 'text/csv;charset=utf-8;',
          };
        },
      },
      {
        id: 'security-audit-log',
        name: 'Security Audit Log',
        description: 'Recent high-priority security events with remediation status.',
        format: 'CSV',
        tags: ['Security', 'Compliance'],
        generate: () => {
          const header = 'Alert,Severity,Date Detected,Resolved,Owner';
          const events = [
            ['Elevated login failures', 'Medium', formatDate(new Date(today.getTime() - 3600 * 1000 * 12)), 'Yes', 'SOC-02'],
            ['Outdated TLS ciphers on edge-4', 'High', formatDate(new Date(today.getTime() - 3600 * 1000 * 24)), 'In Progress', 'NetOps'],
            ['New device type fingerprint', 'Low', formatDate(new Date(today.getTime() - 3600 * 1000 * 30)), 'Yes', 'Security Research'],
          ];
          const rows = events
            .map((row) =>
              row
                .map((value) => {
                  const str = String(value ?? '');
                  return /[",\n]/.test(str) ? `"${str.replace(/"/g, '""')}"` : str;
                })
                .join(','),
            )
            .join('\n');
          return {
            content: `\uFEFF${header}\n${rows}`,
            filename: `security-audit-${formatDate(today)}.csv`,
            mimeType: 'text/csv;charset=utf-8;',
          };
        },
      },
    ],
    [today],
  );

  const handleDownload = (definition) => {
    const payload = definition.generate();
    downloadBlob(payload);
  };

  return (
    <div className="min-h-screen bg-gray-900">
  <div className="flex items-center justify-between mb-8 flex-wrap gap-4">
        <div>
          <h1 className="text-3xl font-bold text-white">Reports</h1>
          <p className="text-sm text-gray-400 mt-1">Download finance, usage, and security snapshots any time.</p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {reportDefinitions.map((report) => (
          <div key={report.id} className="bg-gray-800 border border-gray-700 rounded-xl p-6 flex flex-col justify-between shadow-lg">
            <div>
              <h2 className="text-xl font-semibold text-white mb-2">{report.name}</h2>
              <p className="text-sm text-gray-300 leading-relaxed">{report.description}</p>
              <div className="mt-4 flex flex-wrap gap-2">
                {report.tags.map((tag) => (
                  <span key={tag} className="text-xs uppercase tracking-wide bg-gray-700/70 text-gray-200 px-2 py-1 rounded-full border border-gray-600">
                    {tag}
                  </span>
                ))}
                <span className="text-xs uppercase tracking-wide bg-blue-600/20 text-blue-300 px-2 py-1 rounded-full border border-blue-600/40">
                  {report.format}
                </span>
              </div>
            </div>
            <button
              type="button"
              onClick={() => handleDownload(report)}
              className="mt-6 inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
            >
              Download {report.format}
            </button>
          </div>
        ))}
      </div>
    </div>
  );
};

export default Reports;
