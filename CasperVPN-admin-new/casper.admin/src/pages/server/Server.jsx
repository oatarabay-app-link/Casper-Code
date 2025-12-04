import { useEffect, useState } from 'react';
import {
  PowerSettingsNew,
  Settings,
  Delete as DeleteIcon,
  Cloud as CloudIcon,
  People as PeopleIcon,
  Speed as SpeedIcon,
} from '@mui/icons-material';
import SettingsOutlinedIcon from '@mui/icons-material/SettingsOutlined';
import AddOutlinedIcon from '@mui/icons-material/AddOutlined';
import { useServerStore } from '../../Store/serverstore.js';

const IconMap = {
  Cloud: <CloudIcon fontSize="large" className="text-blue-500" />,
  People: <PeopleIcon fontSize="large" className="text-blue-500" />,
  Speed: <SpeedIcon fontSize="large" className="text-blue-500" />,
};

const Server = () => {
  const [isAddModalOpen, setIsAddModalOpen] = useState(false);
  const [formData, setFormData] = useState({
    name: '',
    location: '',
    ip: '',
    username: '',
    password: '',
    sshKey: '',
  });
  const [errors, setErrors] = useState({});
  const {
    serverList,
    serverStats,
    fetchServers,
    createServer,
    updateServer,
    deleteServer,
    loading,
    error,
  } = useServerStore();
  const [updateLoading, setUpdateLoading] = useState(false);
  const [updateError, setUpdateError] = useState(null);


  useEffect(() => {
    fetchServers();
  }, [fetchServers]);
  const toggleServerStatus = async (server) => {
    setUpdateLoading(true);
    setUpdateError(null);

    const newStatus = server.serverStatus === "Online" ? "Offline" : "Online";

    try {
      await updateServer(server.id, {
        serverName: server.serverName,
        location: server.location,
        ipAddress: server.ipAddress,
        username: server.username,
        password: server.password,
        sshkey: server.sshkey,
        serverStatus: newStatus,
      });

      await fetchServers();
    } catch (err) {
      console.error("Update failed:", err);
      setUpdateError("Failed to update server status");

      // Auto hide after 3 seconds
      setTimeout(() => setUpdateError(null), 3000);
    }

    setUpdateLoading(false);
  };



  const handleInputChange = (event) => {
    const { name, value } = event.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const resetForm = () => {
    setFormData({
      name: '',
      location: '',
      ip: '',
      username: '',
      password: '',
      sshKey: '',
    });
    setErrors({});
  };



  const handleCloseModal = () => {
    resetForm();
    setIsAddModalOpen(false);
  };

  const handleSubmit = (event) => {
    event.preventDefault();

    const validationErrors = {};

    if (!formData.name.trim()) {
      validationErrors.name = 'Server name is required.';
    }

    if (!formData.location.trim()) {
      validationErrors.location = 'Location is required.';
    }

    if (!formData.ip.trim()) {
      validationErrors.ip = 'IP address is required.';
    }

    if (!formData.username.trim()) {
      validationErrors.username = 'Username is required.';
    }

    if (!formData.password.trim() && !formData.sshKey.trim()) {
      validationErrors.credentials = 'Provide either a password or an SSH key.';
    }

    setErrors(validationErrors);

    if (Object.keys(validationErrors).length > 0) {
      return;
    }

    const payload = {
      serverName: formData.name,
      location: formData.location,
      ipAddress: formData.ip,
      serverStatus: "Online", // or "Online"
      username: formData.username,
      password: formData.password || null,
      sshkey: formData.sshKey || null,
    };
    createServer(payload).then(() => {
      fetchServers();
      resetForm();
      setIsAddModalOpen(false);
    });
  };

  {/* POPUP for updating server */ }
  {
    updateLoading && (
      <div className="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
        <div className="bg-gray-800 p-6 rounded-xl border border-gray-700 text-center w-[300px]">
          <div className="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
          <p className="mt-4 text-gray-200 text-sm">Updating server status…</p>
        </div>
      </div>
    )
  }

  {/* POPUP for update error */ }
  {
    updateError && (
      <div className="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
        <div className="bg-gray-800 p-6 rounded-xl border border-red-600 text-center w-[300px]">
          <p className="text-red-400 font-semibold">{updateError}</p>
        </div>
      </div>
    )
  }


  return (

    <div className="min-h-screen bg-gray-900">
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-3xl font-bold text-white">Server Management</h1>
          <p className="text-sm text-gray-400 mt-1">Monitor and manage your VPN servers</p>
        </div>
        <div className="flex gap-3">
          <button
            type="button"
            onClick={() => setIsAddModalOpen(true)}
            className="px-4 gap-2 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center"
          >
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

      {/* LOADING UI */}
      {loading && (
        <div className="w-full flex flex-col items-center justify-center py-20 text-gray-300">
          <div className="w-10 h-10 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
          <p className="mt-4 text-sm">Loading servers…</p>
        </div>
      )}

      {/* ERROR UI */}
      {!loading && error && (
        <div className="w-full flex flex-col items-center justify-center py-20 text-red-400">
          <p className="text-lg font-semibold">Failed to fetch the server</p>
          <p className="text-sm opacity-70 mt-1">{error}</p>
        </div>
      )}


      {/* 🖥️ Server Status Table */}
      {/* 🖥️ Server Status Table */}
      {!loading && !error && (
        <div className="bg-gray-800 border border-gray-700 p-6 shadow-lg rounded-lg overflow-x-auto">
          <h2 className="text-lg font-semibold text-white mb-4">Server Status</h2>

          {serverList.length === 0 ? (
            <p className="text-gray-400 text-sm py-6 text-center">No servers found.</p>
          ) : (
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
                    <td className="py-3 px-4 text-white font-medium">{server.serverName}</td>
                    <td className="py-3 px-4 text-gray-300">{server.location}</td>
                    <td className="py-3 px-4 font-mono text-gray-300">{server.ipAddress}</td>
                    <td className="py-3 px-4">
                      {(() => {
                        const statusClassMap = {
                          Online: 'bg-green-900 text-green-300 border border-green-700',
                          Maintenance: 'bg-yellow-900 text-yellow-300 border border-yellow-700',
                          Queued: 'bg-blue-900 text-blue-300 border border-blue-700',
                        };

                        const statusStyle =
                          statusClassMap[server.serverStatus] ??
                          'bg-red-900 text-red-300 border border-red-700';

                        return (
                          <span
                            className={`px-3 py-1 rounded-full text-xs font-semibold ${statusStyle}`}
                          >
                            {server.serverStatus}
                          </span>
                        );
                      })()}
                    </td>

                    <td className="py-3 px-4 text-gray-300">{server.users}</td>
                    <td className="py-3 px-4 text-gray-300">{server.load}</td>

                    <td className="py-3 px-4 flex space-x-2">
                      <button className="text-blue-400 hover:text-blue-300 p-1 rounded hover:bg-gray-700 transition-colors duration-200">
                        <PowerSettingsNew fontSize="small"
                          onClick={() => toggleServerStatus(server)}
                        />
                      </button>

                      <button className="text-blue-400 hover:text-blue-300 p-1 rounded hover:bg-gray-700 transition-colors duration-200">
                        <Settings fontSize="small" />
                      </button>

                      <button
                        className="text-blue-400 hover:text-blue-300 p-1 rounded hover:bg-gray-700 transition-colors duration-200"
                        onClick={() => deleteServer(server.id)}
                      >
                        <DeleteIcon fontSize="small" />
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          )}
        </div>
      )}


      {/* <div className="bg-gray-800 border border-gray-700 p-6 shadow-lg rounded-lg overflow-x-auto">
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
                <td className="py-3 px-4 text-white font-medium">{server.serverName}</td>
                <td className="py-3 px-4 text-gray-300">{server.location}</td>
                <td className="py-3 px-4 font-mono text-gray-300">{server.ipAddress}</td>
                <td className="py-3 px-4">
                  {(() => {
                    const statusClassMap = {
                      Online: 'bg-green-900 text-green-300 border border-green-700',
                      Maintenance: 'bg-yellow-900 text-yellow-300 border border-yellow-700',
                      Queued: 'bg-blue-900 text-blue-300 border border-blue-700',
                    };

                    const statusStyle = statusClassMap[server.serverStatus] ??
                      'bg-red-900 text-red-300 border border-red-700';

                    return (
                      <span
                        className={`px-3 py-1 rounded-full text-xs font-semibold ${statusStyle}`}
                      >
                        {server.serverStatus}
                      </span>
                    );
                  })()}
                </td>
                <td className="py-3 px-4 text-gray-300">{server.users}</td>
                <td className="py-3 px-4 text-gray-300">{server.load}</td>
                <td className="py-3 px-4 flex space-x-2">
                  <button className="text-blue-400 hover:text-blue-300 p-1 rounded hover:bg-gray-700 transition-colors duration-200">
                    <PowerSettingsNew fontSize="small" 
                    onClick={() => toggleServerStatus(server)}
                    />
                  </button>
                  <button className="text-blue-400 hover:text-blue-300 p-1 rounded hover:bg-gray-700 transition-colors duration-200">
                    <Settings fontSize="small" />
                  </button>
                  <button className="text-blue-400 hover:text-blue-300 p-1 rounded hover:bg-gray-700 transition-colors duration-200"
                    onClick={() => deleteServer(server.id)}>
                    <DeleteIcon fontSize="small" />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div> */}

      {isAddModalOpen && (
        <div className="modal-backdrop">
          <div className="modal-panel modal-panel--narrow">
            <div className="flex items-center justify-between border-b border-gray-800 px-6 py-4">
              <h3 className="text-lg font-semibold text-white">Add Server</h3>
              <button
                type="button"
                onClick={handleCloseModal}
                className="text-gray-400 transition-colors duration-150 hover:text-white"
                aria-label="Close add server modal"
              >
                ✕
              </button>
            </div>
            <form onSubmit={handleSubmit} className="flex-1 overflow-y-auto space-y-5 px-6 py-6">
              <div>
                <label className="block text-sm font-medium text-gray-300" htmlFor="server-name">
                  Server Name
                </label>
                <input
                  id="server-name"
                  name="name"
                  value={formData.name}
                  onChange={handleInputChange}
                  className="mt-1 w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                  placeholder="e.g. US-East-2"
                />
                {errors.name && <p className="mt-1 text-xs text-red-400">{errors.name}</p>}
              </div>


              <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <label className="block text-sm font-medium text-gray-300" htmlFor="server-location">
                    Location
                  </label>
                  <input
                    id="server-location"
                    name="location"
                    value={formData.location}
                    onChange={handleInputChange}
                    className="mt-1 w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                    placeholder="City or region"
                  />
                  {errors.location && <p className="mt-1 text-xs text-red-400">{errors.location}</p>}
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-300" htmlFor="server-ip">
                    IP Address
                  </label>
                  <input
                    id="server-ip"
                    name="ip"
                    value={formData.ip}
                    onChange={handleInputChange}
                    className="mt-1 w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                    placeholder="198.51.100.10"
                  />
                  {errors.ip && <p className="mt-1 text-xs text-red-400">{errors.ip}</p>}
                </div>
              </div>

              <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <label className="block text-sm font-medium text-gray-300" htmlFor="server-username">
                    Username
                  </label>
                  <input
                    id="server-username"
                    name="username"
                    value={formData.username}
                    onChange={handleInputChange}
                    className="mt-1 w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                    placeholder="root"
                  />
                  {errors.username && <p className="mt-1 text-xs text-red-400">{errors.username}</p>}
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-300" htmlFor="server-password">
                    Password (optional)
                  </label>
                  <input
                    id="server-password"
                    name="password"
                    type="password"
                    value={formData.password}
                    onChange={handleInputChange}
                    className="mt-1 w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                    placeholder="••••••••"
                  />
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-300" htmlFor="server-ssh-key">
                  SSH Key (optional)
                </label>
                <textarea
                  id="server-ssh-key"
                  name="sshKey"
                  rows="3"
                  value={formData.sshKey}
                  onChange={handleInputChange}
                  className="mt-1 w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                  placeholder="Paste public key"
                />
                {errors.credentials && <p className="mt-1 text-xs text-red-400">{errors.credentials}</p>}
              </div>

              <div className="flex flex-col gap-2 rounded-lg border border-blue-500/40 bg-blue-500/5 px-4 py-3 text-xs text-blue-200">
                <span>
                  Provide at least one authentication method: either a password or an SSH key. New servers will appear in the
                  table with a <span className="font-semibold text-blue-300">Queued</span> status while provisioning.
                </span>
              </div>

              <div className="flex items-center justify-end gap-3 border-t border-gray-800 pt-4">
                <button
                  type="button"
                  onClick={handleCloseModal}
                  className="rounded-lg border border-gray-700 px-4 py-2 text-sm text-gray-300 transition-colors duration-150 hover:border-gray-500 hover:text-white"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition-colors duration-150 hover:bg-blue-700"
                >
                  Queue Server
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

export default Server;
