import { useNotificationStore } from '../../Store/notificationstore';
import { Settings, DoneAll } from '@mui/icons-material';

const Notification = () => {
  const {
    summary,
    recent,
    settings,
    thresholds,
    updateSetting,
    updateThreshold,
  } = useNotificationStore();

  return (
    <div className="p-6 bg-gray-900 min-h-screen transition-all duration-300">
      {/* Header */}
      <div className="flex items-center justify-between mb-8 flex-wrap gap-4">
        <div>
          <h1 className="text-3xl font-bold text-white">Notification Center</h1>
          <p className="text-sm text-gray-400 mt-1">View alerts, configure settings, and manage thresholds</p>
        </div>
        <div className="flex gap-3">
          <button className="flex items-center gap-2 px-4 py-2 bg-gray-700 text-gray-300 text-sm rounded-lg hover:bg-gray-600 transition-colors duration-200">
            <Settings fontSize="small" />
            Settings
          </button>
          <button className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200">
            <DoneAll fontSize="small" />
            Mark All Read
          </button>
        </div>
      </div>

      {/* Stat Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <StatCard label="Unread" value={`${summary.unread} new`} />
        <StatCard label="Email Alerts" value={`${summary.emailSent} sent`} />
        <StatCard label="System Messages" value={`${summary.systemMessages} active`} />
        <StatCard label="Response Time" value={`${summary.responseTime} avg`} />
      </div>

      {/* Main Grid: Notifications + Settings */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {/* Recent Notifications */}
        <Section title="Recent Notifications" className="lg:col-span-2">
          <ul className="space-y-4">
            {recent.map((n, i) => (
              <li key={i} className="border-b border-gray-600 pb-3">
                <div className="font-medium text-white">{n.title}</div>
                <div className="text-sm text-gray-300">{n.detail}</div>
                <div className="text-xs text-gray-400 mt-1">{n.time}</div>
              </li>
            ))}
          </ul>
        </Section>

        {/* Notification Settings */}
        <Section title="Notification Settings">
          <div className="space-y-4">
            {Object.entries(settings).map(([key, value]) => (
              <div key={key} className="flex justify-between items-center border-b border-gray-600 pb-3">
                <span className="text-sm text-gray-300 capitalize">{key.replace(/([A-Z])/g, ' $1')}</span>
                <input
                  type="checkbox"
                  checked={value}
                  onChange={(e) => updateSetting(key, e.target.checked)}
                  className="h-5 w-5 text-blue-600 bg-gray-600 border-gray-500 rounded focus:ring-blue-500"
                />
              </div>
            ))}
          </div>
        </Section>
      </div>

      {/* Alert Thresholds */}
      <Section title="Alert Thresholds">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <ThresholdGroup title="Server Performance" data={thresholds.server} section="server" update={updateThreshold} />
          <ThresholdGroup title="Security" data={thresholds.security} section="security" update={updateThreshold} />
          <ThresholdGroup title="User Activity" data={thresholds.user} section="user" update={updateThreshold} />
        </div>
      </Section>
    </div>
  );
};

// 🔧 Reusable Components
const StatCard = ({ label, value }) => (
  <div className="bg-gray-800 border border-gray-700 shadow-lg rounded-lg p-4 text-center">
    <div className="text-sm text-gray-400">{label}</div>
    <div className="text-xl font-semibold text-white">{value}</div>
  </div>
);

const Section = ({ title, children, className = '' }) => (
  <div className={`bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6 ${className}`}>
    <h2 className="text-lg font-semibold text-white mb-4">{title}</h2>
    {children}
  </div>
);

const ThresholdGroup = ({ title, data, section, update }) => (
  <div>
    <h3 className="text-sm font-medium text-gray-300 mb-2">{title}</h3>
    <div className="space-y-3">
      {Object.entries(data).map(([key, value]) => (
        <div key={key} className="flex justify-between items-center">
          <span className="text-sm text-gray-300 capitalize">{key.replace(/([A-Z])/g, ' $1')}</span>
          <input
            type="number"
            value={value}
            onChange={(e) => update(section, key, parseInt(e.target.value))}
            className="w-20 px-2 py-1 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
          />
        </div>
      ))}
    </div>
  </div>
);

export default Notification;
