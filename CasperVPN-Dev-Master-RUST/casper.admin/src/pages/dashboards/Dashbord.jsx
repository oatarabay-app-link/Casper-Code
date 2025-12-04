import {
  Storage,
  Wifi,
  Warning,
  Dns,
  Refresh,
  Settings,
} from '@mui/icons-material';
import ShowChartOutlinedIcon from '@mui/icons-material/ShowChartOutlined';
import WarningAmberOutlinedIcon from '@mui/icons-material/WarningAmberOutlined';

import { Line } from 'react-chartjs-2';
import {
  Chart as ChartJS,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Title,
  Tooltip,
  Legend,
} from 'chart.js';

import { useDashStore } from '../../Store/Dashstore';
import { useTheme } from '../../hooks/useTheme.jsx';

ChartJS.register(
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Title,
  Tooltip,
  Legend
);

const IconMap = {
  Storage: <Storage />,
  Dns: <Dns />,
  Wifi: <Wifi />,
  Warning: <Warning />,
};

const Dashboard = () => {
  const { dashboardStats, chartData, serverStatus, recentActivity } = useDashStore();
  const { getThemeClasses } = useTheme();
  const themeClasses = getThemeClasses();

  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        labels: {
          color: '#D1D5DB', // gray-300
        }
      }
    },
    scales: {
      x: {
        ticks: {
          color: '#9CA3AF', // gray-400
        },
        grid: {
          color: '#374151', // gray-700
        }
      },
      y: {
        ticks: {
          color: '#9CA3AF', // gray-400
        },
        grid: {
          color: '#374151', // gray-700
        }
      }
    }
  };

  return (
      <div className="p-6">
        {/* Header */}
        <div className="flex items-center justify-between mb-8 flex-wrap gap-4">
        <div>
          <h1 className={`text-3xl font-bold ${themeClasses.textPrimary}`}>VPN Admin Dashboard </h1>
          <p className={`text-sm mt-1 ${themeClasses.textTertiary}`}>Monitor system performance and activity</p>
        </div>
        <div className="flex gap-3">
          <button className={`flex items-center gap-2 px-4 py-2 text-sm rounded-lg transition-colors duration-200 ${themeClasses.buttonSecondary}`}>
          <ShowChartOutlinedIcon fontSize="small" />
            System Status
          </button>
          <button className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200">
          <WarningAmberOutlinedIcon fontSize="small" />
            View Alert
          </button>
        </div>
      </div>

      {/* Stat Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        {dashboardStats.map((item, index) => (
          <div key={index} className="bg-gray-800 border border-gray-700 shadow-lg rounded-lg p-6 flex items-center justify-between hover:bg-gray-750 transition-colors duration-200">
            <div>
              <div className="text-sm text-gray-400">{item.label}</div>
              <div className="text-2xl font-semibold text-white">{item.value}</div>
            </div>
            <div className="text-blue-500">{IconMap[item.icon]}</div>
          </div>
        ))}
      </div>

      {/* Main Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {/* Left Column: Chart + Server Table */}
        <div className="flex flex-col space-y-6">
          {/* Chart */}
          <div className="bg-gray-800 border border-gray-700 p-6 shadow-lg rounded-lg h-[360px]">
            <h2 className="text-lg font-semibold text-white mb-4">Real-Time Connections</h2>
            <div className="h-[280px]">
              <Line data={chartData} options={chartOptions} />
            </div>
          </div>

          {/* Server Table */}
          <div className="bg-gray-800 border border-gray-700 p-6 shadow-lg rounded-lg h-[360px] overflow-y-auto">
            <h2 className="text-lg font-semibold text-white mb-4">Server Status</h2>
            <table className="w-full text-sm text-left text-gray-300">
              <thead className="text-xs uppercase bg-gray-700 text-gray-400 sticky top-0 z-10">
                <tr>
                  <th className="py-2 px-3">Server</th>
                  <th className="py-2 px-3">Location</th>
                  <th className="py-2 px-3">IP</th>
                  <th className="py-2 px-3">Status</th>
                  <th className="py-2 px-3">Users</th>
                  <th className="py-2 px-3">Load</th>
                  <th className="py-2 px-3">Uptime</th>
                  <th className="py-2 px-3">Actions</th>
                </tr>
              </thead>
              <tbody>
                {serverStatus.map((srv, i) => (
                  <tr key={i} className="border-t border-gray-700 hover:bg-gray-750 transition-colors duration-200">
                    <td className="py-3 px-3 text-white">{srv.name}</td>
                    <td className="py-3 px-3 text-gray-300">{srv.location}</td>
                    <td className="py-3 px-3 font-mono text-gray-300">{srv.ip}</td>
                    <td className="py-3 px-3">
                      <span className={`px-2 py-1 rounded-full text-xs font-semibold ${
                        srv.status === 'Online' ? 'bg-green-900 text-green-300 border border-green-700' :
                        srv.status === 'Maintenance' ? 'bg-yellow-900 text-yellow-300 border border-yellow-700' :
                        'bg-red-900 text-red-300 border border-red-700'
                      }`}>
                        {srv.status}
                      </span>
                    </td>
                    <td className="py-3 px-3 text-gray-300">{srv.users}</td>
                    <td className="py-3 px-3 text-gray-300">{srv.load}</td>
                    <td className="py-3 px-3 text-gray-300">{srv.uptime}</td>
                    <td className="py-3 px-3 text-blue-400 cursor-pointer hover:text-blue-300 hover:underline transition-colors duration-200">Restart</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        {/* Right Column: Performance + Activity */}
        <div className="flex flex-col space-y-6">
          {/* Performance Overview */}
          <div className="bg-gray-800 border border-gray-700 p-6 shadow-lg rounded-lg h-[360px]">
            <h2 className="text-lg font-semibold text-white mb-4">Performance Overview</h2>
            <div className="space-y-4">
              <div className="flex justify-between items-center p-3 bg-gray-700 rounded-lg">
                <span className="text-gray-300">Network Latency:</span>
                <span className="text-white font-semibold">12ms avg</span>
              </div>
              <div className="flex justify-between items-center p-3 bg-gray-700 rounded-lg">
                <span className="text-gray-300">Bandwidth Usage:</span>
                <span className="text-white font-semibold">1.2 TB/day</span>
              </div>
              <div className="flex justify-between items-center p-3 bg-gray-700 rounded-lg">
                <span className="text-gray-300">Server Load:</span>
                <span className="text-white font-semibold">68%</span>
              </div>
              <div className="flex justify-between items-center p-3 bg-gray-700 rounded-lg">
                <span className="text-gray-300">Data Transfer:</span>
                <span className="text-white font-semibold">847 GB/hr</span>
              </div>
            </div>
          </div>

          {/* Recent Activity */}
          <div className="bg-gray-800 border border-gray-700 p-6 shadow-lg rounded-lg h-[360px] overflow-y-auto">
            <h2 className="text-lg font-semibold text-white mb-4">Recent Activity</h2>
            <div className="space-y-3">
              {recentActivity.map((item, i) => (
                <div key={i} className={`border-l-4 pl-4 py-2 rounded-r-lg ${
                  item.color === 'red' ? 'border-red-500 bg-red-900/20 text-red-300' :
                  item.color === 'green' ? 'border-green-500 bg-green-900/20 text-green-300' :
                  item.color === 'blue' ? 'border-blue-500 bg-blue-900/20 text-blue-300' :
                  item.color === 'indigo' ? 'border-indigo-500 bg-indigo-900/20 text-indigo-300' :
                  item.color === 'yellow' ? 'border-yellow-500 bg-yellow-900/20 text-yellow-300' :
                  'border-gray-500 bg-gray-700/50 text-gray-300'
                }`}>
                  <div className="text-sm">
                    <span className="font-semibold">{item.type}:</span> {item.detail}
                  </div>
                  <span className="text-xs text-gray-400 italic">({item.time})</span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
      </div>
  );
};

export default Dashboard;
