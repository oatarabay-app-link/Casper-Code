import { useSettingStore } from '../../../Store/settingstore.js';

const Api = () => {
  const { apiConfig, updateApiConfig } = useSettingStore();

  return (
    <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6">
      <h2 className="text-lg font-semibold text-white mb-4">API Configuration</h2>
      <form className="space-y-6">

        {/* API Access Toggle */}
        <div className="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
          <label className="text-sm text-gray-300">Enable API Access</label>
          <input
            type="checkbox"
            checked={apiConfig.apiAccess}
            onChange={(e) => updateApiConfig('apiAccess', e.target.checked)}
            className="h-5 w-5 text-blue-600 bg-gray-600 border-gray-500 rounded focus:ring-blue-500"
          />
        </div>

        {/* API Key (read-only) */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">API Key</label>
          <input
            type="text"
            value={apiConfig.apiKey}
            readOnly
            className="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-sm text-gray-400 cursor-not-allowed"
          />
        </div>

        {/* Rate Limit */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">Rate Limit (requests/minute)</label>
          <input
            type="number"
            value={apiConfig.rateLimit}
            onChange={(e) => updateApiConfig('rateLimit', parseInt(e.target.value))}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
            min={1}
          />
        </div>

        {/* API Version */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">API Version</label>
          <select
            value={apiConfig.apiVersion}
            onChange={(e) => updateApiConfig('apiVersion', e.target.value)}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
          >
            <option value="v1">v1</option>
            <option value="v2">v2</option>
          </select>
        </div>

        {/* Webhook URL */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">Webhook URL</label>
          <input
            type="text"
            value={apiConfig.webhookUrl}
            onChange={(e) => updateApiConfig('webhookUrl', e.target.value)}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
          />
        </div>

        {/* API Logging Toggle */}
        <div className="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
          <label className="text-sm text-gray-300">Enable API Logging</label>
          <input
            type="checkbox"
            checked={apiConfig.apiLogging}
            onChange={(e) => updateApiConfig('apiLogging', e.target.checked)}
            className="h-5 w-5 text-blue-600 bg-gray-600 border-gray-500 rounded focus:ring-blue-500"
          />
        </div>

        {/* Save Button */}
        <div className="pt-4 border-t border-gray-600">
          <button type="submit" className="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200">
            Save API Settings
          </button>
        </div>
      </form>
    </div>
  );
};

export default Api;
