import {
  ShowChart,
  DataUsage,
  Timer,
  SignalCellularAlt,
  Public,
} from '@mui/icons-material';
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

import { useAnalyticStore } from '../../Store/analyticstore';

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
  'Total Sessions': <ShowChart fontSize="large" className="text-blue-500" />,
  'Data Transferred': <DataUsage fontSize="large" className="text-blue-500" />,
  'Avg Session Time': <Timer fontSize="large" className="text-blue-500" />,
  'Uptime': <SignalCellularAlt fontSize="large" className="text-blue-500" />,
};

const Analytics = () => {
  const {
    analyticsStats,
    chartData,
    chartOptions,
    topServers,
    protocolDistribution,
    geoDistribution,
  } = useAnalyticStore();

  return (
    <div className="min-h-screen bg-gray-900">
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-3xl font-bold text-white">Analytics Overview</h1>
          <p className="text-sm text-gray-400 mt-1">Detailed metrics and performance insights</p>
        </div>

      </div>

      {/* 🔢 Summary Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        {analyticsStats.map((stat, i) => (
          <div
            key={i}
            className="bg-gray-800 border border-gray-700 shadow-lg rounded-lg p-6 flex items-center justify-between hover:bg-gray-750 transition-colors duration-200"
          >
            <div>
              <div className="text-sm text-gray-400">{stat.label}</div>
              <div className="text-2xl font-semibold text-white">{stat.value}</div>
              <div className="text-xs text-green-400 italic mt-1">{stat.change}</div>
            </div>
            <div>{IconMap[stat.label]}</div>
          </div>
        ))}
      </div>

      {/* 📈 Real-Time Chart */}
      <div className="bg-gray-800 border border-gray-700 p-6 shadow-lg rounded-lg mb-6 h-[400px]">
        <h2 className="text-lg font-semibold text-white mb-4">Real-Time Metrics</h2>
        <div className="h-[320px]">
          <Line data={chartData} options={chartOptions} />
        </div>
      </div>

      {/* 📊 Analytics Breakdown */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* 🗂 Servers */}
        <div className="bg-gray-800 border border-gray-700 p-6 shadow-lg rounded-lg">
          <h2 className="text-lg font-semibold text-white mb-4">Usage by Server</h2>
          <ul className="text-sm space-y-3">
            {topServers.map((srv, i) => (
              <li key={i} className="flex justify-between items-center p-2 bg-gray-700 rounded">
                <span className="text-gray-300">{srv.name} – {srv.location}</span>
                <span className="font-semibold text-blue-400">{srv.usage}</span>
              </li>
            ))}
          </ul>
        </div>

        {/* ⚙️ Protocols */}
        <div className="bg-gray-800 border border-gray-700 p-6 shadow-lg rounded-lg">
          <h2 className="text-lg font-semibold text-white mb-4">Protocol Distribution</h2>
          <ul className="text-sm space-y-3">
            {protocolDistribution.map((item, i) => (
              <li key={i} className="flex justify-between items-center p-2 bg-gray-700 rounded">
                <span className="text-gray-300">{item.protocol}</span>
                <span className="font-semibold text-blue-400">{item.percent}</span>
              </li>
            ))}
          </ul>
        </div>

        {/* 🌍 Regions */}
        <div className="bg-gray-800 border border-gray-700 p-6 shadow-lg rounded-lg">
          <h2 className="text-lg font-semibold text-white mb-4">Geo Distribution</h2>
          <ul className="text-sm space-y-3">
            {geoDistribution.map((item, i) => (
              <li key={i} className="flex justify-between items-center p-2 bg-gray-700 rounded">
                <span className="flex items-center gap-2 text-gray-300">
                  <Public fontSize="small" className="text-blue-400" /> {item.region}
                </span>
                <span className="font-semibold text-blue-400">{item.percent}</span>
              </li>
            ))}
          </ul>
        </div>
      </div>
    </div>
  );
};

export default Analytics;
