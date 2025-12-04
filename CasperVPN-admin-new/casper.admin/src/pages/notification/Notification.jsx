import { useNotificationStore } from '../../Store/notificationstore';
import { DoneAll, CheckCircle } from '@mui/icons-material';

const Notification = () => {
  const summary = useNotificationStore((state) => state.summary);
  const recent = useNotificationStore((state) => state.recent);
  const settings = useNotificationStore((state) => state.settings);
  const thresholds = useNotificationStore((state) => state.thresholds);
  const updateSetting = useNotificationStore((state) => state.updateSetting);
  const updateThreshold = useNotificationStore((state) => state.updateThreshold);
  const markAllRead = useNotificationStore((state) => state.markAllRead);
  const allRead = useNotificationStore((state) => state.allRead);

  const handleMarkAllRead = () => {
    if (!allRead) {
      markAllRead();
    }
  };

  const markAllClasses = allRead
    ? 'bg-green-600 hover:bg-green-700 cursor-default'
    : 'bg-blue-600 hover:bg-blue-700';

  const settingEntries = Object.entries(settings).filter(([key]) => key !== 'allRead');

  return (
    <div className="bg-gray-900 min-h-screen transition-all duration-300">
      {/* Header */}
      <div className="flex items-center justify-between mb-8 flex-wrap gap-4">
        <div>
          <h1 className="text-3xl font-bold text-white">Notification Center</h1>
          <p className="text-sm text-gray-400 mt-1">View alerts, configure settings, and manage thresholds</p>
        </div>
        <div className="flex gap-3">
          <button
            type="button"
            onClick={handleMarkAllRead}
            className={`flex items-center gap-2 px-4 py-2 text-white text-sm rounded-lg transition-colors duration-200 ${markAllClasses}`}
          >
            <DoneAll fontSize="small" />
            {allRead ? 'All Read' : 'Mark All Read'}
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
                <div className="flex items-start gap-3">
                  <CheckCircle
                    fontSize="small"
                    className={`mt-1 transition-all duration-200 ${n.read ? 'text-green-400 opacity-100 scale-100' : 'text-transparent opacity-0 scale-75'}`}
                  />
                  <div>
                    <div className="font-medium text-white">{n.title}</div>
                    <div className="text-sm text-gray-300">{n.detail}</div>
                    <div className="text-xs text-gray-400 mt-1">{n.time}</div>
                  </div>
                </div>
              </li>
            ))}
          </ul>
        </Section>

        {/* Notification Settings */}
        <Section title="Notification Settings">
          <div className="space-y-4">
            {settingEntries.map(([key, value]) => (
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
