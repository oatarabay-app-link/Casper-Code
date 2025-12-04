import { useSettingStore } from '../../../Store/settingstore.js';

const Notifications = () => {
  const { notificationsConfig, updateNotificationsConfig } = useSettingStore();

  return (
    <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6">
      <h2 className="text-lg font-semibold text-white mb-4">Notification Preferences</h2>
      <form className="space-y-6">

        {/* Email Notifications Toggle */}
        <div className="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
          <label className="text-sm text-gray-300">Enable Email Notifications</label>
          <input
            type="checkbox"
            checked={notificationsConfig.emailEnabled}
            onChange={(e) => updateNotificationsConfig('emailEnabled', e.target.checked)}
            className="h-5 w-5 text-blue-600 bg-gray-600 border-gray-500 rounded focus:ring-blue-500"
          />
        </div>

        {/* SMTP Server */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">SMTP Server</label>
          <input
            type="text"
            value={notificationsConfig.smtpServer}
            onChange={(e) => updateNotificationsConfig('smtpServer', e.target.value)}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
          />
        </div>

        {/* SMTP Username */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">SMTP Username</label>
          <input
            type="email"
            value={notificationsConfig.smtpUsername}
            onChange={(e) => updateNotificationsConfig('smtpUsername', e.target.value)}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
          />
        </div>

        {/* SMTP Port */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">SMTP Port</label>
          <input
            type="number"
            value={notificationsConfig.smtpPort}
            onChange={(e) => updateNotificationsConfig('smtpPort', parseInt(e.target.value))}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
          />
        </div>

        {/* Slack Integration Toggle */}
        <div className="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
          <label className="text-sm text-gray-300">Enable Slack Integration</label>
          <input
            type="checkbox"
            checked={notificationsConfig.slackEnabled}
            onChange={(e) => updateNotificationsConfig('slackEnabled', e.target.checked)}
            className="h-5 w-5 text-blue-600 bg-gray-600 border-gray-500 rounded focus:ring-blue-500"
          />
        </div>

        {/* Slack Webhook URL */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">Slack Webhook URL</label>
          <input
            type="text"
            value={notificationsConfig.slackWebhook}
            onChange={(e) => updateNotificationsConfig('slackWebhook', e.target.value)}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
          />
        </div>

        {/* Save Button */}
        <div className="pt-4 border-t border-gray-600">
          <button type="submit" className="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200">
            Save Notification Settings
          </button>
        </div>
      </form>
    </div>
  );
};

export default Notifications;
