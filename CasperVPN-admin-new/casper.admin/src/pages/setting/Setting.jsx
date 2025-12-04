import { useUiStore } from '../../Store/uiStore';
import { useSettingStore } from '../../Store/settingstore';

// Component imports (case-sensitive paths for Linux builds)
import General from './components/General';
import Security from './components/Security';
import Servers from './components/Servers';
import Notifications from './components/Notifications';
import SaveOutlinedIcon from '@mui/icons-material/SaveOutlined';
import Users from './components/Users';
import Api from './components/Api';



const TabRenderer = ({ tab }) => {
  switch (tab) {
    case 'General':
      return <General />;
    case 'Security':
      return <Security />;
    case 'Servers':
      return <Servers />;
    case 'Notifications':
      return <Notifications />;
    case 'Users':
      return <Users />;
          case 'API':
      return <Api />;

      

    // You can add: case 'Servers': return <Servers /> etc.
    default:
      return (
        <div className="text-sm text-gray-400">
          No content available for <span className="font-semibold text-white">{tab}</span>
        </div>
      );
  }
};

const Setting = () => {
  const isSidebarOpen = useUiStore((state) => state.isSidebarOpen);
  const { tabs, currentTab, setCurrentTab } = useSettingStore();

  return (
    <div className="min-h-screen bg-gray-900">
      {/* 🧾 Header with Save Button */}
      <div className="flex items-start justify-between mb-8 flex-wrap gap-4">
        <div>
          <h1 className="text-3xl font-bold text-white">System Settings</h1>
          <p className="text-sm text-gray-400 mt-1">
            Configure administrative preferences and tab-specific options
          </p>
        </div>
        <button className="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200">
          <SaveOutlinedIcon fontSize="small" />
          Save Changes
        </button>
      </div>

      {/* 🧭 Tab Navigation */}
      <div className="flex gap-1 text-sm border-b border-gray-700 mb-6">
        {tabs.map((tab) => (
          <button
            key={tab.key}
            onClick={() => setCurrentTab(tab.key)}
            className={`px-4 py-3 rounded-t-lg font-medium border-b-2 transition-colors duration-200 ${currentTab === tab.key
              ? 'text-blue-400 border-blue-400 bg-gray-800'
              : 'text-gray-400 border-transparent hover:text-blue-300 hover:bg-gray-800'
              }`}
          >
            {tab.label}
          </button>
        ))}
      </div>

      {/* 📄 Dynamic Page Loader */}
      <TabRenderer tab={currentTab} />
    </div>
  );
};

export default Setting;
