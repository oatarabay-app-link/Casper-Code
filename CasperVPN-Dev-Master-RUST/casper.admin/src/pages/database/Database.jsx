import {
  Storage,
  PeopleAlt,
  Speed,
  CloudDone,
  Backup,
  Sync,
  WarningAmber,
} from '@mui/icons-material';

import { useUiStore } from '../../Store/uiStore';
import { useDatabaseStore } from '../../Store/datastore';

const Database = () => {
  const isSidebarOpen = useUiStore((state) => state.isSidebarOpen);
  const { stats, status, performance, backups, queries } = useDatabaseStore();

  return (
    <div className="min-h-screen bg-gray-900">
      {/* Header */}
      <div className="flex items-start justify-between mb-8 flex-wrap gap-4">
        <div>
          <h1 className="text-3xl font-bold text-white">Database Management</h1>
          <p className="text-sm text-gray-400 mt-1">System status, backups and performance overview</p>
        </div>
        <div className="flex gap-3">
          <button className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200">
            <Sync fontSize="small" />
            Refresh Stats
          </button>
          <button className="flex items-center gap-2 px-4 py-2 bg-gray-800 border border-gray-700 text-gray-300 text-sm rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200">
            <Backup fontSize="small" />
            Create Backup
          </button>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <InfoCard label="Database Size" value={stats.size} detail="+180 MB this month" icon={<Storage />} />
        <InfoCard label="Active Connections" value={stats.connections} detail="Max 100 connections" icon={<PeopleAlt />} />
        <InfoCard label="Query Performance" value={stats.queryTime} detail="Avg response time" icon={<Speed />} />
        <InfoCard label="Last Backup" value={stats.lastBackup} detail="Automated backup" icon={<CloudDone />} />
      </div>

      {/* Status Section */}
      <Section title="Database Status">
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <StatusCard label="Primary DB" version="PostgreSQL 15.2" status={status.primary} />
          <StatusCard label="Read Replica" version="PostgreSQL 15.2" status={status.replica} />
          <StatusCard label="Cache Server" version="Redis 7.0" status={status.cache} />
        </div>
      </Section>

      {/* Performance Section */}
      <Section title="Performance Metrics">
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
          <UsageCard label="CPU Usage" value={performance.cpu} />
          <UsageCard label="Memory Usage" value={performance.memory} />
          <UsageCard label="Disk Usage" value={performance.disk} />
        </div>
      </Section>

      {/* Backups Table */}
      <Section title="Recent Backups">
        <div className="overflow-x-auto">
          <table className="w-full text-sm text-left text-gray-300">
            <thead className="bg-gray-700 text-xs uppercase text-gray-400">
              <tr>
                <th className="px-4 py-3">Type</th>
                <th className="px-4 py-3">Time</th>
                <th className="px-4 py-3">Status</th>
              </tr>
            </thead>
            <tbody>
              {backups.map((b, i) => (
                <tr key={i} className="border-t border-gray-700 hover:bg-gray-750 transition-colors duration-200">
                  <td className="px-4 py-3 text-white">{b.type}</td>
                  <td className="px-4 py-3 text-gray-300">{b.time}</td>
                  <td className="px-4 py-3">
                    <span className={`text-xs font-semibold px-3 py-1 rounded-full ${
                      b.status === 'Success' ? 'bg-green-900 text-green-300 border border-green-700' : 'bg-red-900 text-red-300 border border-red-700'
                    }`}>
                      {b.status}
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </Section>

      {/* Query Stats */}
      <Section title="Query Statistics">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-4">
          <QueryCard label="SELECT" value={queries.select} />
          <QueryCard label="INSERT" value={queries.insert} />
          <QueryCard label="UPDATE" value={queries.update} />
          <QueryCard label="DELETE" value={queries.delete} />
        </div>
        {!queries.slowQueries ? (
          <div className="text-sm text-green-300 bg-green-900 border border-green-700 px-4 py-2 rounded-lg font-medium inline-block">
            ✅ All queries performing well
          </div>
        ) : (
          <div className="text-sm text-red-300 bg-red-900 border border-red-700 px-4 py-2 rounded-lg inline-flex items-center gap-2 font-medium">
            <WarningAmber fontSize="small" />
            Slow queries detected — performance degraded
          </div>
        )}
      </Section>
    </div>
  );
};

// 🔧 Reusable components
const Section = ({ title, children }) => (
  <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6 mb-6">
    <h2 className="text-lg font-semibold text-white mb-4">{title}</h2>
    {children}
  </div>
);

const InfoCard = ({ label, value, detail, icon }) => (
  <div className="bg-gray-800 border border-gray-700 shadow-lg rounded-lg p-6 flex items-center justify-between hover:bg-gray-750 transition-colors duration-200">
    <div>
      <div className="text-sm text-gray-400">{label}</div>
      <div className="text-2xl font-semibold text-white">{value}</div>
      {detail && <div className="text-xs text-gray-500 mt-1">{detail}</div>}
    </div>
    <div className="text-blue-500">{icon}</div>
  </div>
);

const StatusCard = ({ label, version, status }) => {
  const color =
    status === 'Online' ? 'bg-green-900 text-green-300 border border-green-700' :
    status === 'Warning' ? 'bg-yellow-900 text-yellow-300 border border-yellow-700' :
    'bg-red-900 text-red-300 border border-red-700';

  return (
    <div className="p-4 bg-gray-700 rounded-lg border border-gray-600 flex flex-col gap-2">
      <div className="text-sm text-gray-300">{label}</div>
      <div className="text-sm font-semibold text-white">{version}</div>
      <span className={`text-xs font-semibold px-3 py-1 rounded-full w-fit ${color}`}>
        {status}
      </span>
    </div>
  );
};

const UsageCard = ({ label, value }) => (
  <div className="bg-gray-700 rounded-lg p-4 border border-gray-600">
    <div className="text-sm text-gray-300">{label}</div>
    <div className="text-xl font-bold text-blue-400">{value}%</div>
  </div>
);

const QueryCard = ({ label, value }) => (
  <div className="bg-gray-800 border border-gray-700 shadow-lg rounded-lg p-4 text-center hover:bg-gray-750 transition-colors duration-200">
    <div className="text-sm text-gray-400">{label}</div>
    <div className="text-xl font-semibold text-white">{value}</div>
  </div>
);

export default Database;
