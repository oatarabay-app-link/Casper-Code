import { useSettingStore } from '../../../Store/settingstore';

const Security = () => {
  const { securityConfig, updateSecurityConfig } = useSettingStore();

  return (
    <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6">
      <h2 className="text-lg font-semibold text-white mb-4">Security Configuration</h2>
      <form className="space-y-6">

        {/* Two-Factor Auth Toggle */}
        <div className="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
          <label className="text-sm text-gray-300">Enable Two-Factor Authentication</label>
          <input
            type="checkbox"
            checked={securityConfig.twoFactorAuth}
            onChange={(e) =>
              updateSecurityConfig('twoFactorAuth', e.target.checked)
            }
            className="h-5 w-5 text-blue-600 bg-gray-600 border-gray-500 rounded focus:ring-blue-500"
          />
        </div>

        {/* IP Whitelist Toggle */}
        <div className="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
          <label className="text-sm text-gray-300">Enable IP Whitelist</label>
          <input
            type="checkbox"
            checked={securityConfig.ipWhitelist}
            onChange={(e) =>
              updateSecurityConfig('ipWhitelist', e.target.checked)
            }
            className="h-5 w-5 text-blue-600 bg-gray-600 border-gray-500 rounded focus:ring-blue-500"
          />
        </div>

        {/* Session Timeout */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">Session Timeout (minutes)</label>
          <input
            type="number"
            value={securityConfig.sessionTimeout}
            onChange={(e) =>
              updateSecurityConfig('sessionTimeout', parseInt(e.target.value))
            }
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
            min={1}
          />
        </div>

        {/* Max Login Attempts */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">Max Login Attempts</label>
          <input
            type="number"
            value={securityConfig.maxLoginAttempts}
            onChange={(e) =>
              updateSecurityConfig('maxLoginAttempts', parseInt(e.target.value))
            }
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
            min={1}
          />
        </div>

        {/* Allowed IP Addresses */}
        <div>
          <label className="block text-sm text-gray-300 mb-2">Allowed IP Addresses</label>
          <textarea
            rows={3}
            value={securityConfig.allowedIPs.join('\n')}
            onChange={(e) =>
              updateSecurityConfig(
                'allowedIPs',
                e.target.value.split('\n').map((ip) => ip.trim())
              )
            }
            className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none transition-colors duration-200"
            placeholder="One IP per line"
          />
        </div>

        {/* Save Button */}
        <div className="pt-4">
          <button type="submit" className="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200">
            Save Security Settings
          </button>
        </div>
      </form>
    </div>
  );
};

export default Security;
