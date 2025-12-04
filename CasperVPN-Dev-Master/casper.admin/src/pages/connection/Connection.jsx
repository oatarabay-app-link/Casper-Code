import {
  Lan,
  CloudUpload,
  CloudDownload,
  AccessTime,
} from '@mui/icons-material';
import ShowChartOutlinedIcon from '@mui/icons-material/ShowChartOutlined';
import FileDownloadOutlinedIcon from '@mui/icons-material/FileDownloadOutlined';

import { useConnectStore } from '../../Store/connectstore';

const IconMap = {
  Lan: <Lan fontSize="large" className="text-blue-500" />,
  Upload: <CloudUpload fontSize="large" className="text-blue-500" />,
  Download: <CloudDownload fontSize="large" className="text-blue-500" />,
  AccessTime: <AccessTime fontSize="large" className="text-blue-500" />,
};

const Connection = () => {
  const connectionStats = useConnectStore((state) => state.connectionStats);
  const activeConnections = useConnectStore((state) => state.activeConnections);
  const downloadConnectionLogs = useConnectStore((state) => state.downloadConnectionLogs);

  return (
    <div className="min-h-screen bg-gray-900">
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-3xl font-bold text-white">Connection Management</h1>
          <p className="text-sm text-gray-400 mt-1">Monitor active VPN connections and usage</p>
        </div>

         <div className="flex gap-3">
                  <button
                    type="button"
                    onClick={downloadConnectionLogs}
                    className="gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center"
                  >
                   <FileDownloadOutlinedIcon fontSize="small" />
                    Export Logs
                  </button>
                  <button className="px-4 gap-2 py-2 bg-gray-800 border border-gray-700 text-gray-300 text-sm rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200">
                     <ShowChartOutlinedIcon fontSize="small" />
                    Real-time Monitor
                    
                  </button>
                </div>
      </div>

      {/* 🔢 Stat Cards with Icons */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        {connectionStats.map((stat, i) => (
          <div
            key={i}
            className="bg-gray-800 border border-gray-700 shadow-lg rounded-lg p-6 flex items-center justify-between hover:bg-gray-750 transition-colors duration-200"
          >
            <div>
              <div className="text-sm text-gray-400">{stat.label}</div>
              <div className="text-xl font-semibold text-white">{stat.value}</div>
            </div>
            <div>{IconMap[stat.icon]}</div>
          </div>
        ))}
      </div>

      {/* 🧑‍💻 Active Connections Table */}
      <div className="bg-gray-800 border border-gray-700 p-6 shadow-lg rounded-lg overflow-x-auto">
        <h2 className="text-lg font-semibold text-white mb-4">Active Connections</h2>
        <table className="w-full text-sm text-left text-gray-300">
          <thead className="text-xs uppercase bg-gray-700 text-gray-400">
            <tr>
              <th className="py-3 px-4">Email</th>
              <th className="py-3 px-4">Server</th>
              <th className="py-3 px-4">Location</th>
              <th className="py-3 px-4">Protocol</th>
              <th className="py-3 px-4">Session</th>
              <th className="py-3 px-4">Upload</th>
              <th className="py-3 px-4">Download</th>
              <th className="py-3 px-4">Status</th>
            </tr>
          </thead>
          <tbody>
            {activeConnections.map((conn, i) => (
              <tr key={i} className="border-t border-gray-700 hover:bg-gray-750 transition-colors duration-200">
                <td className="py-3 px-4 text-white">{conn.email}</td>
                <td className="py-3 px-4 text-gray-300">{conn.server}</td>
                <td className="py-3 px-4 text-gray-300">{conn.location}</td>
                <td className="py-3 px-4 text-gray-300">{conn.protocol}</td>
                <td className="py-3 px-4 text-gray-300">{conn.session}</td>
                <td className="py-3 px-4 text-gray-300">{conn.upload}</td>
                <td className="py-3 px-4 text-blue-400">{conn.download}</td>
                <td className="py-3 px-4">
                  <span className={`px-3 py-1 rounded-full text-xs font-semibold ${
                    conn.status === 'Connected'
                      ? 'bg-green-900 text-green-300 border border-green-700'
                      : 'bg-red-900 text-red-300 border border-red-700'
                  }`}>
                    {conn.status}
                  </span>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default Connection;
