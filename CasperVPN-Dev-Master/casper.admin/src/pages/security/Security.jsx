import {
  WarningAmber,
  Shield,
  Security as SecurityIcon,
  Assessment,
} from '@mui/icons-material';
import ShieldOutlinedIcon from '@mui/icons-material/ShieldOutlined';
import SummarizeOutlinedIcon from '@mui/icons-material/SummarizeOutlined';

import { useSecurityStore } from '../../Store/securitystore';

const IconMap = {
  'Critical Alerts': <WarningAmber fontSize="large" className="text-red-500" />,
  'High Priority': <Shield fontSize="large" className="text-yellow-500" />,
  'Blocked Threats': <SecurityIcon fontSize="large" className="text-green-500" />,
  'Security Score': <Assessment fontSize="large" className="text-indigo-500" />,
};

const SeverityColors = {
  Critical: 'bg-red-100 text-red-700 border-red-500',
  High: 'bg-orange-100 text-orange-700 border-orange-500',
  Medium: 'bg-yellow-100 text-yellow-700 border-yellow-500',
  Low: 'bg-blue-100 text-blue-700 border-blue-500',
};

const SecurityDashboard = () => {
  const securityStats = useSecurityStore((state) => state.securityStats);
  const recentEvents = useSecurityStore((state) => state.recentEvents);
  const securityPolicies = useSecurityStore((state) => state.securityPolicies);
  const scanStatus = useSecurityStore((state) => state.scanStatus);
  const scanProgress = useSecurityStore((state) => state.scanProgress);
  const scanMessage = useSecurityStore((state) => state.scanMessage);
  const startSecurityScan = useSecurityStore((state) => state.startSecurityScan);
  const downloadSecurityReport = useSecurityStore((state) => state.downloadSecurityReport);

  return (
    <div className="min-h-screen bg-gray-900">
      {/* Title & Description */}
      <div className="mb-8 flex items-center justify-between">
        {/* Left: Title */}
        <div>
          <h1 className="text-3xl font-bold text-white">Security Dashboard</h1>
          <p className="text-sm text-gray-400 mt-1">
            Monitor alerts, policy status, and suspicious activity in real-time
          </p>
        </div>

        {/* Right: Fixed Buttons */}
        <div className="flex gap-3">
          <button
            type="button"
            onClick={startSecurityScan}
            disabled={scanStatus === 'running'}
            className={`relative overflow-hidden px-4 py-2 text-sm rounded-lg transition-colors duration-200 flex items-center gap-2 ${
              scanStatus === 'running'
                ? 'bg-blue-700 text-white cursor-wait'
                : 'bg-blue-600 text-white hover:bg-blue-700'
            }`}
          >
            <span
              className="absolute inset-0 bg-blue-500/60 transition-all duration-300"
              style={{ width: `${scanStatus === 'running' ? scanProgress : scanStatus === 'completed' ? 100 : 0}%` }}
            />
            <span className="relative z-10 flex items-center gap-2">
              <ShieldOutlinedIcon fontSize="small" />
              {scanStatus === 'running'
                ? `Scanning... ${scanProgress}%`
                : scanStatus === 'completed'
                ? 'Run Scan Again'
                : 'Security Scan'}
            </span>
          </button>
          <button
            type="button"
            onClick={downloadSecurityReport}
            className="px-4 py-2 bg-gray-800 border border-gray-700 text-gray-300 text-sm rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 flex items-center gap-2"
          >
            <SummarizeOutlinedIcon fontSize="small" />
            Security Report
          </button>
        </div>
      </div>

      {(scanStatus !== 'idle' || scanProgress > 0) && (
        <div className="mb-8">
          <div className="h-2 w-full max-w-xl overflow-hidden rounded-full bg-gray-800">
            <div
              className={`h-full rounded-full ${scanStatus === 'completed' ? 'bg-green-500' : 'bg-blue-500'} transition-all duration-300`}
              style={{ width: `${scanProgress}%` }}
            />
          </div>
          <div className="mt-2 flex items-center justify-between text-xs text-gray-400 max-w-xl">
            <span>{scanMessage || (scanStatus === 'completed' ? 'Security scan completed.' : 'Security scan ready.')}</span>
            <span>{scanProgress}%</span>
          </div>
        </div>
      )}


      {/* Summary Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {securityStats.map((stat, i) => (
          <div
            key={i}
            className="bg-gray-800 border border-gray-700 shadow-lg rounded-lg px-6 py-5 flex items-center justify-between hover:bg-gray-750 transition-colors duration-200"
          >
            <div>
              <div className="text-sm text-gray-400">{stat.label}</div>
              <div className="text-2xl font-semibold text-white">{stat.value}</div>
            </div>
            <div>{IconMap[stat.label]}</div>
          </div>
        ))}
      </div>

      {/* Security Policies */}
      <div className="bg-gray-800 border border-gray-700 shadow-lg rounded-lg p-6 mb-8">
        <h2 className="text-lg font-semibold text-white mb-4">Active Security Policies</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {securityPolicies.map((policy, i) => (
            <div key={i} className="flex justify-between items-center p-3 bg-gray-700 rounded border-b border-gray-600">
              <span className="text-sm text-gray-300">{policy.policy}</span>
              <span className={`text-xs font-semibold px-3 py-1 rounded-full ${policy.status === 'Enabled' || policy.status === 'Active'
                  ? 'bg-green-900 text-green-300 border border-green-700'
                  : policy.status === 'Disabled'
                    ? 'bg-red-900 text-red-300 border border-red-700'
                    : 'bg-gray-600 text-gray-300'
                }`}>
                {policy.status}
              </span>
            </div>
          ))}
        </div>
      </div>

      {/* Event Logs */}
      <div className="bg-gray-800 border border-gray-700 shadow-lg rounded-lg p-6">
        <h2 className="text-lg font-semibold text-white mb-4">Recent Security Events</h2>
        <div className="overflow-x-auto">
          <table className="w-full text-sm text-left text-gray-300">
            <thead className="text-xs uppercase bg-gray-700 text-gray-400">
              <tr>
                <th className="px-4 py-3">Event</th>
                <th className="px-4 py-3">User</th>
                <th className="px-4 py-3">IP</th>
                <th className="px-4 py-3">Severity</th>
                <th className="px-4 py-3">Timestamp</th>
              </tr>
            </thead>
            <tbody>
              {recentEvents.map((evt, i) => (
                <tr key={i} className="border-t border-gray-700 hover:bg-gray-750 transition-colors duration-200">
                  <td className="px-4 py-3 text-white">{evt.type}</td>
                  <td className="px-4 py-3 text-gray-300">{evt.user}</td>
                  <td className="px-4 py-3 text-gray-300 font-mono">{evt.ip}</td>
                  <td className="px-4 py-3">
                    <span className={`text-xs font-semibold px-3 py-1 rounded-full ${
                      evt.severity === 'Critical' ? 'bg-red-900 text-red-300 border border-red-700' :
                      evt.severity === 'High' ? 'bg-orange-900 text-orange-300 border border-orange-700' :
                      evt.severity === 'Medium' ? 'bg-yellow-900 text-yellow-300 border border-yellow-700' :
                      evt.severity === 'Low' ? 'bg-blue-900 text-blue-300 border border-blue-700' :
                      'bg-gray-600 text-gray-300'
                    }`}>
                      {evt.severity}
                    </span>
                  </td>
                  <td className="px-4 py-3 text-gray-400">{evt.time}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

export default SecurityDashboard;
