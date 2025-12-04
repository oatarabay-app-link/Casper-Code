import { useSettingStore } from '../../../Store/settingstore';

const Users = () => {
  const { usersConfig, updateUsersConfig } = useSettingStore();

  return (
    <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6">
      <h2 className="text-lg font-semibold text-white mb-4">User Management Settings</h2>
      <form className="space-y-6">

        {/* Auto Registration Toggle */}
        <div className="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
          <label className="text-sm text-gray-300">Auto User Registration</label>
          <input
            type="checkbox"
            checked={usersConfig.autoRegistration}
            onChange={(e) => updateUsersConfig('autoRegistration', e.target.checked)}
            className="h-5 w-5 text-blue-600 bg-gray-600 border-gray-500 rounded focus:ring-blue-500"
          />
        </div>

        {/* Email Verification Toggle */}
        <div className="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
          <label className="text-sm text-gray-300">Email Verification Required</label>
          <input
            type="checkbox"
            checked={usersConfig.emailVerification}
            onChange={(e) => updateUsersConfig('emailVerification', e.target.checked)}
            className="h-5 w-5 text-blue-600 bg-gray-600 border-gray-500 rounded focus:ring-blue-500"
          />
        </div>

        {/* Default Plan Dropdown */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">Default Plan</label>
          <select
            value={usersConfig.defaultPlan}
            onChange={(e) => updateUsersConfig('defaultPlan', e.target.value)}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
          >
            <option>Basic</option>
            <option>Pro</option>
            <option>Enterprise</option>
          </select>
        </div>

        {/* Max Devices */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">Max Devices per User</label>
          <input
            type="number"
            value={usersConfig.maxDevices}
            onChange={(e) => updateUsersConfig('maxDevices', parseInt(e.target.value))}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
            min={1}
          />
        </div>

        {/* Trial Period */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">Trial Period (days)</label>
          <input
            type="number"
            value={usersConfig.trialPeriod}
            onChange={(e) => updateUsersConfig('trialPeriod', parseInt(e.target.value))}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
            min={0}
          />
        </div>

        {/* Monthly Data Limit */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">Monthly Data Limit (GB)</label>
          <input
            type="number"
            value={usersConfig.monthlyLimit}
            onChange={(e) => updateUsersConfig('monthlyLimit', parseInt(e.target.value))}
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
            min={0}
          />
        </div>

        {/* Save Button */}
        <div className="pt-4 border-t border-gray-600">
          <button type="submit" className="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200">
            Save User Settings
          </button>
        </div>
      </form>
    </div>
  );
};

export default Users;
