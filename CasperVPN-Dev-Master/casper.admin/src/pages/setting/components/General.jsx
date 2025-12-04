import { useSettingStore } from '../../../Store/settingstore';

const General = () => {
  const { generalConfig } = useSettingStore();

  return (
    <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6">
      <h2 className="text-lg font-semibold text-white mb-4">General Settings</h2>
      <form className="space-y-6">
        {/* Company Name */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">Company Name</label>
          <input
            type="text"
            defaultValue={generalConfig.companyName}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
          />
        </div>

        {/* System Name */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">System Name</label>
          <input
            type="text"
            defaultValue={generalConfig.systemName}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
          />
        </div>

        {/* Admin Email */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">Admin Email</label>
          <input
            type="email"
            defaultValue={generalConfig.adminEmail}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
          />
        </div>

        {/* Maintenance Mode Toggle */}
        <div className="flex items-center justify-between p-4 bg-gray-700 rounded-lg">
          <label className="text-sm text-gray-300">Maintenance Mode</label>
          <input
            type="checkbox"
            defaultChecked={generalConfig.maintenanceMode}
            className="h-4 w-4 text-blue-600 bg-gray-600 border-gray-500 rounded focus:ring-blue-500"
          />
        </div>

        {/* Debug Logging Toggle */}
        <div className="flex items-center justify-between p-4 bg-gray-700 rounded-lg">
          <label className="text-sm text-gray-300">Enable Debug Logging</label>
          <input
            type="checkbox"
            defaultChecked={generalConfig.debugLogging}
            className="h-4 w-4 text-blue-600 bg-gray-600 border-gray-500 rounded focus:ring-blue-500"
          />
        </div>

        {/* Save Button */}
        <div className="pt-4">
          <button className="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200">
            Update Settings
          </button>
        </div>
      </form>
    </div>
  );
};

export default General;
