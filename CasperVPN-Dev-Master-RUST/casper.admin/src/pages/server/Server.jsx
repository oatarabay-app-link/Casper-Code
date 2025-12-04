import {
  PowerSettingsNew,
  Settings,
  Cloud as CloudIcon,
  People as PeopleIcon,
  Speed as SpeedIcon,
} from '@mui/icons-material';
import SettingsOutlinedIcon from '@mui/icons-material/SettingsOutlined';
import AddOutlinedIcon from '@mui/icons-material/AddOutlined';

import { useUiStore } from '../../Store/uiStore';
import { useServerStore } from '../../Store/serverstore.js';

const IconMap = {
  Cloud: <CloudIcon fontSize="large" className="text-blue-500" />,
  People: <PeopleIcon fontSize="large" className="text-blue-500" />,
  Speed: <SpeedIcon fontSize="large" className="text-blue-500" />,
};

const Server = () => {
  const isSidebarOpen = useUiStore((state) => state.isSidebarOpen);
  const { serverStats, serverList } = useServerStore();

  return (
    <div className="min-h-screen bg-gray-900">
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-3xl font-bold text-white">Server Management</h1>
          <p className="text-sm text-gray-400 mt-1">Monitor and manage your VPN servers</p>
        </div>
          <div className="flex gap-3">
           <button className="px-4 gap-2 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200">
                   <AddOutlinedIcon fontSize="small" />
                    Add Server
                  </button>
                  <button className="px-4 gap-2 py-2 bg-gray-800 border border-gray-700 text-gray-300 text-sm rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200">
                     <SettingsOutlinedIcon fontSize="small" />
                    Server Settings
                    
                  </button>
                </div>
      </div>

      {/* 📊 Metric Cards with Icons */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
        {serverStats.map((stat, i) => (
          <div
            key={i}
            className="bg-gray-800 border border-gray-700 shadow-lg rounded-lg p-6 flex items-center justify-between hover:bg-gray-750 transition-colors duration-200"
          >
            <div>
              <div className="text-sm text-gray-400">{stat.label}</div>
              <div className="text-2xl font-semibold text-white">{stat.value}</div>
            </div>
            <div>{IconMap[stat.icon]}</div>
          </div>
        ))}
      </div>

      {/* 🖥️ Server Status Table */}
      <div className="bg-gray-800 border border-gray-700 p-6 shadow-lg rounded-lg overflow-x-auto">
        <h2 className="text-lg font-semibold text-white mb-4">Server Status</h2>
        <table className="w-full text-sm text-left text-gray-300">
          <thead className="text-xs uppercase bg-gray-700 text-gray-400">
            <tr>
              <th className="py-3 px-4">Name</th>
              <th className="py-3 px-4">Location</th>
              <th className="py-3 px-4">IP</th>
              <th className="py-3 px-4">Status</th>
              <th className="py-3 px-4">Users</th>
              <th className="py-3 px-4">Load</th>
              <th className="py-3 px-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            {serverList.map((server, i) => (
              <tr key={i} className="border-t border-gray-700 hover:bg-gray-750 transition-colors duration-200">
                <td className="py-3 px-4 text-white font-medium">{server.name}</td>
                <td className="py-3 px-4 text-gray-300">{server.location}</td>
                <td className="py-3 px-4 font-mono text-gray-300">{server.ip}</td>
                <td className="py-3 px-4">
                  <span
                    className={`px-3 py-1 rounded-full text-xs font-semibold ${
                      server.status === 'Online'
                        ? 'bg-green-900 text-green-300 border border-green-700'
                        : server.status === 'Maintenance'
                        ? 'bg-yellow-900 text-yellow-300 border border-yellow-700'
                        : 'bg-red-900 text-red-300 border border-red-700'
                    }`}
                  >
                    {server.status}
                  </span>
                </td>
                <td className="py-3 px-4 text-gray-300">{server.users}</td>
                <td className="py-3 px-4 text-gray-300">{server.load}</td>
                <td className="py-3 px-4 flex space-x-2">
                  <button className="text-blue-400 hover:text-blue-300 p-1 rounded hover:bg-gray-700 transition-colors duration-200">
                    <PowerSettingsNew fontSize="small" />
                  </button>
                  <button className="text-blue-400 hover:text-blue-300 p-1 rounded hover:bg-gray-700 transition-colors duration-200">
                    <Settings fontSize="small" />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default Server;
