import { useSettingStore } from '../../../Store/settingstore';

const Servers = () => {
  const { serverConfig, updateServerConfig } = useSettingStore();

  return (
    <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6">
      <h2 className="text-lg font-semibold text-white mb-4">Server Configuration</h2>
      <form className="space-y-6">
        {/* Default Protocol */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">Default Protocol</label>
          <select
            value={serverConfig.defaultProtocol}
            onChange={(e) => updateServerConfig('defaultProtocol', e.target.value)}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
          >
            <option>OpenVPN</option>
            <option>WireGuard</option>
            <option>IKEv2</option>
          </select>
        </div>

        {/* Max Connections */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">Max Connections per Server</label>
          <input
            type="number"
            value={serverConfig.maxConnections}
            onChange={(e) => updateServerConfig('maxConnections', parseInt(e.target.value))}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
            min={1}
          />
        </div>

        {/* Auto Server Selection Toggle */}
        <div className="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
          <label className="text-sm text-gray-300">Auto Server Selection</label>
          <input
            type="checkbox"
            checked={serverConfig.autoSelection}
            onChange={(e) => updateServerConfig('autoSelection', e.target.checked)}
            className="h-5 w-5 text-blue-600 bg-gray-600 border-gray-500 rounded focus:ring-blue-500"
          />
        </div>

        {/* Load Balancing Toggle */}
        <div className="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
          <label className="text-sm text-gray-300">Load Balancing</label>
          <input
            type="checkbox"
            checked={serverConfig.loadBalancing}
            onChange={(e) => updateServerConfig('loadBalancing', e.target.checked)}
            className="h-5 w-5 text-blue-600 bg-gray-600 border-gray-500 rounded focus:ring-blue-500"
          />
        </div>

        {/* Health Check Interval */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">Health Check Interval (seconds)</label>
          <input
            type="number"
            value={serverConfig.healthCheckInterval}
            onChange={(e) => updateServerConfig('healthCheckInterval', parseInt(e.target.value))}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
            min={10}
          />
        </div>

        {/* Connection Timeout */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">Connection Timeout (seconds)</label>
          <input
            type="number"
            value={serverConfig.connectionTimeout}
            onChange={(e) => updateServerConfig('connectionTimeout', parseInt(e.target.value))}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
            min={5}
          />
        </div>

        {/* Save Button */}
        <div className="pt-4 border-t border-gray-600">
          <button type="submit" className="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200">
            Save Server Settings
          </button>
        </div>
      </form>
    </div>
  );
};

export default Servers;
