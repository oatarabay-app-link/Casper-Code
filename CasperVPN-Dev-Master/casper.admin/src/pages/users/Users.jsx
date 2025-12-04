import { useMemo, useState, useEffect } from 'react';
import {
  Download,
  LockReset,
  VpnKey,
  Block,
  Undo,
  Delete,
  Visibility,
  TaskAlt,
} from '@mui/icons-material';
import { useUserStore } from '../../Store/userstore';

const TB = 1024 ** 4;

const formatDateTime = (value) => {
  if (!value) return '—';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return value;
  return `${date.toLocaleDateString()} ${date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;
};

const formatRelativeDate = (value) => {
  if (!value) return '—';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return value;
  return date.toLocaleDateString();
};

const formatDataUsage = (bytes) => {
  if (!bytes || bytes <= 0) return 'Unlimited';
  const tb = bytes / TB;
  if (tb < 0.1) {
    return `${(tb * 1024).toFixed(1)} GB`;
  }
  return `${tb.toFixed(2)} TB`;
};

const statusBadge = (status) => {
  const base = 'inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full border';
  switch (status) {
    case 'Verified':
      return `${base} border-emerald-500/40 bg-emerald-500/15 text-emerald-200`;
    case 'Pending':
      return `${base} border-yellow-500/40 bg-yellow-500/15 text-yellow-200`;
    case 'Manual Review':
      return `${base} border-orange-500/40 bg-orange-500/15 text-orange-200`;
    default:
      return `${base} border-gray-500/40 bg-gray-500/15 text-gray-200`;
  }
};

const Users = () => {
  const {
    userStats,
    users,
    searchQuery,
    setSearchQuery,
    downloadUsersCsv,
    toggleBlockUser,
    resetUserPassword,
    resetUserMfa,
    deleteUser,
    exportUserData,
  } = useUserStore();

  const [selectedUserId, setSelectedUserId] = useState(users[0]?.id ?? null);

  useEffect(() => {
    if (!selectedUserId && users.length > 0) {
      setSelectedUserId(users[0].id);
    }
  }, [selectedUserId, users]);

  const filteredUsers = useMemo(() => {
    const query = (searchQuery || '').toLowerCase();
    if (!query) return users;
    return users.filter((user) =>
      [user.name, user.email, user.country].some((field) => field.toLowerCase().includes(query)),
    );
  }, [users, searchQuery]);

  useEffect(() => {
    if (filteredUsers.length === 0) {
      setSelectedUserId(null);
      return;
    }
    const exists = filteredUsers.some((user) => user.id === selectedUserId);
    if (!exists) {
      setSelectedUserId(filteredUsers[0].id);
    }
  }, [filteredUsers, selectedUserId]);

  const selectedUser = filteredUsers.find((user) => user.id === selectedUserId) || users.find((user) => user.id === selectedUserId) || null;

  const handleBlockToggle = (user) => {
    toggleBlockUser(user.id);
  };

  const handleResetPassword = (user) => {
    resetUserPassword(user.id);
    window.alert(`Password reset workflow queued for ${user.email}.`);
  };

  const handleResetMfa = (user) => {
    resetUserMfa(user.id);
    window.alert(`MFA reset email sent to ${user.email}.`);
  };

  const handleExportUser = (user) => {
    exportUserData(user.id);
  };

  const handleDelete = (user) => {
    if (window.confirm(`Delete ${user.email} and purge profile data? This cannot be undone.`)) {
      deleteUser(user.id);
      if (selectedUserId === user.id) {
        setSelectedUserId(null);
      }
    }
  };

  const handleSelectUser = (user) => {
    setSelectedUserId(user.id);
  };

  return (
    <div className="min-h-screen bg-gray-900 pb-12">
      <header className="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 className="text-3xl font-bold text-white">Users</h1>
          <p className="text-sm text-gray-400 mt-1 max-w-2xl">
            Identity, compliance, and device visibility for every customer. Marketing only—no billing totals leak here.
          </p>
        </div>
        <div className="flex items-center gap-2">
          <button
            type="button"
            onClick={downloadUsersCsv}
            className="flex items-center gap-2 px-4 py-2 text-sm rounded-lg bg-gray-800 border border-gray-700 text-gray-300 hover:bg-gray-700"
          >
            <Download fontSize="small" />
            Export Directory (CSV)
          </button>
        </div>
      </header>

  <section className="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard label="Total Profiles" value={userStats.total} badge="all" />
        <StatCard label="Verified KYC" value={userStats.verified} badge="compliance" theme="emerald" />
        <StatCard label="MFA Secured" value={userStats.mfaSecured} badge="security" theme="blue" />
        <StatCard label="Active Subscriptions" value={userStats.activeSubscriptions} badge="subscriptions" theme="indigo" />
      </section>

  <section className="mt-8">
        <div className="flex flex-wrap items-center justify-between gap-4 mb-4">
          <h2 className="text-lg font-semibold text-white">Directory</h2>
          <input
            type="search"
            value={searchQuery}
            onChange={(event) => setSearchQuery(event.target.value)}
            placeholder="Search by name, email, or country"
            className="w-full md:w-72 px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

  <div className="overflow-x-auto bg-gray-800 border border-gray-700 rounded-xl">
          <table className="min-w-full text-sm text-gray-200">
            <thead className="bg-gray-750 text-xs uppercase tracking-wide text-gray-400">
              <tr>
                <th className="px-4 py-3 text-left">Identity</th>
                <th className="px-4 py-3 text-left">Contact</th>
                <th className="px-4 py-3 text-left">KYC</th>
                <th className="px-4 py-3 text-left">Last Login</th>
                <th className="px-4 py-3 text-left">MFA</th>
                <th className="px-4 py-3 text-left">Total Data</th>
                <th className="px-4 py-3 text-left">Actions</th>
              </tr>
            </thead>
            <tbody>
              {filteredUsers.length === 0 ? (
                <tr>
                  <td colSpan={7} className="px-4 py-6 text-center text-gray-400">
                    No users match the current filters.
                  </td>
                </tr>
              ) : (
                filteredUsers.map((user) => (
                  <tr key={user.id} className={`border-t border-gray-700 ${selectedUserId === user.id ? 'bg-gray-800/80' : 'hover:bg-gray-800/60 transition-colors'}`}>
                    <td className="px-4 py-4">
                      <div className="flex flex-col">
                        <span className="font-semibold text-white">{user.name}</span>
                        <span className="text-xs text-gray-400">{user.email}</span>
                        {user.hasActiveSubscription && (
                          <span className="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-emerald-200 bg-emerald-500/15 border border-emerald-500/30 px-2 py-0.5 rounded-full">
                            <TaskAlt fontSize="inherit" /> Active subscription
                          </span>
                        )}
                      </div>
                    </td>
                    <td className="px-4 py-4">
                      <div className="flex flex-col gap-1 text-sm">
                        <span>{user.phone}</span>
                        <span className="text-gray-400">{user.country}</span>
                      </div>
                    </td>
                    <td className="px-4 py-4">
                      <span className={statusBadge(user.kycStatus)}>{user.kycStatus}</span>
                      <div className="text-xs text-gray-400 mt-1">{user.kycTier}</div>
                    </td>
                    <td className="px-4 py-4 text-sm text-gray-300">{formatRelativeDate(user.lastLogin)}</td>
                    <td className="px-4 py-4">
                      <span
                        className={`inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full border ${
                          user.mfaEnabled
                            ? 'border-emerald-500/40 bg-emerald-500/15 text-emerald-200'
                            : 'border-red-500/40 bg-red-500/15 text-red-200'
                        }`}
                      >
                        {user.mfaEnabled ? 'Enabled' : 'Disabled'}
                      </span>
                    </td>
                    <td className="px-4 py-4 text-sm text-gray-300">{formatDataUsage(user.totalDataBytes)}</td>
                    <td className="px-4 py-4">
                      <div className="flex flex-wrap gap-2 text-xs">
                        <button
                          type="button"
                          onClick={() => handleResetPassword(user)}
                          className="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-gray-900/60 border border-gray-700 text-gray-200 hover:bg-gray-800"
                        >
                          <LockReset fontSize="inherit" /> Reset Password
                        </button>
                        <button
                          type="button"
                          onClick={() => handleResetMfa(user)}
                          className="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-gray-900/60 border border-gray-700 text-gray-200 hover:bg-gray-800"
                        >
                          <VpnKey fontSize="inherit" /> Reset MFA
                        </button>
                        <button
                          type="button"
                          onClick={() => handleBlockToggle(user)}
                          className={`inline-flex items-center gap-1 px-2.5 py-1 rounded border ${
                            user.blocked
                              ? 'bg-emerald-500/15 border-emerald-500/40 text-emerald-200'
                              : 'bg-red-500/15 border-red-500/40 text-red-200'
                          }`}
                        >
                          {user.blocked ? <Undo fontSize="inherit" /> : <Block fontSize="inherit" />}
                          {user.blocked ? 'Unblock' : 'Block'}
                        </button>
                        <button
                          type="button"
                          onClick={() => handleExportUser(user)}
                          className="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-gray-900/60 border border-gray-700 text-gray-200 hover:bg-gray-800"
                        >
                          <Download fontSize="inherit" /> Export Data
                        </button>
                        <button
                          type="button"
                          onClick={() => handleDelete(user)}
                          className="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-red-600/20 border border-red-600/40 text-red-200 hover:bg-red-600/30"
                        >
                          <Delete fontSize="inherit" /> Delete
                        </button>
                        <button
                          type="button"
                          onClick={() => handleSelectUser(user)}
                          className={`inline-flex items-center gap-1 px-2.5 py-1 rounded border ${
                            selectedUserId === user.id
                              ? 'bg-blue-600/20 border-blue-500/60 text-blue-200'
                              : 'bg-gray-900/60 border-gray-700 text-gray-200 hover:bg-gray-800'
                          }`}
                        >
                          <Visibility fontSize="inherit" /> View
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </section>

      {selectedUser && (
        <section className="mt-10 grid grid-cols-1 xl:grid-cols-3 gap-6">
          <div className="xl:col-span-1 bg-gray-800 border border-gray-700 rounded-xl p-6 space-y-4">
            <div>
              <h3 className="text-lg font-semibold text-white">Identity & Compliance</h3>
              <dl className="mt-4 space-y-3 text-sm text-gray-300">
                <div className="flex justify-between gap-2">
                  <dt className="text-gray-400">Name</dt>
                  <dd>{selectedUser.name}</dd>
                </div>
                <div className="flex justify-between gap-2">
                  <dt className="text-gray-400">Email</dt>
                  <dd>{selectedUser.email}</dd>
                </div>
                <div className="flex justify-between gap-2">
                  <dt className="text-gray-400">Country</dt>
                  <dd>{selectedUser.country}</dd>
                </div>
                <div className="flex justify-between gap-2">
                  <dt className="text-gray-400">Phone</dt>
                  <dd>{selectedUser.phone}</dd>
                </div>
                <div className="flex justify-between gap-2">
                  <dt className="text-gray-400">KYC</dt>
                  <dd>
                    <span className={statusBadge(selectedUser.kycStatus)}>{selectedUser.kycStatus}</span>
                  </dd>
                </div>
                <div className="flex justify-between gap-2">
                  <dt className="text-gray-400">Last Login</dt>
                  <dd>{formatRelativeDate(selectedUser.lastLogin)}</dd>
                </div>
                <div className="flex justify-between gap-2">
                  <dt className="text-gray-400">Password Reset</dt>
                  <dd>{formatDateTime(selectedUser.lastPasswordResetAt)}</dd>
                </div>
                <div className="flex justify-between gap-2">
                  <dt className="text-gray-400">Total Data</dt>
                  <dd>{formatDataUsage(selectedUser.totalDataBytes)}</dd>
                </div>
              </dl>
            </div>
          </div>

          <div className="xl:col-span-2 space-y-6">
            <div className="bg-gray-800 border border-gray-700 rounded-xl p-6">
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-lg font-semibold text-white">Subscriptions</h3>
                <span className="text-xs text-gray-400">Linked out to billing workspace</span>
              </div>
              {selectedUser.subscriptions.length === 0 ? (
                <p className="text-sm text-gray-400">No subscriptions on file.</p>
              ) : (
                <ul className="divide-y divide-gray-700">
                  {selectedUser.subscriptions.map((subscription) => (
                    <li key={subscription.id} className="py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                      <div>
                        <div className="font-semibold text-white">{subscription.planName}</div>
                        <div className="text-xs text-gray-400">{subscription.status} — since {subscription.startedAt}</div>
                      </div>
                      <div className="text-sm text-gray-300 flex items-center gap-2">
                        Renews {subscription.renewsOn}
                        <a
                          href={subscription.link}
                          className="text-blue-300 hover:text-blue-200 text-xs underline"
                        >
                          Open in billing
                        </a>
                      </div>
                    </li>
                  ))}
                </ul>
              )}
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <h3 className="text-lg font-semibold text-white mb-3">Devices</h3>
                {selectedUser.devices.length === 0 ? (
                  <p className="text-sm text-gray-400">No devices currently registered.</p>
                ) : (
                  <ul className="space-y-3 text-sm text-gray-200">
                    {selectedUser.devices.map((device) => (
                      <li key={device.id} className="flex flex-col gap-1">
                        <span className="font-semibold text-white">{device.name}</span>
                        <span className="text-gray-400 text-xs">{device.type} • Last seen {formatDateTime(device.lastSeen)}</span>
                        <span className="text-gray-500 text-xs">Exit IP {device.ip}</span>
                      </li>
                    ))}
                  </ul>
                )}
              </div>

              <div className="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <h3 className="text-lg font-semibold text-white mb-3">Recent Connections</h3>
                {selectedUser.recentConnections.length === 0 ? (
                  <p className="text-sm text-gray-400">No recent connections captured.</p>
                ) : (
                  <ul className="space-y-3 text-sm text-gray-200">
                    {selectedUser.recentConnections.map((connection) => (
                      <li key={connection.id} className="flex flex-col gap-1">
                        <span className="font-semibold text-white">{connection.location}</span>
                        <span className="text-gray-400 text-xs">{formatDateTime(connection.connectedAt)}</span>
                        <span className="text-gray-500 text-xs">Exit IP {connection.ip} • {connection.durationMinutes} min session</span>
                      </li>
                    ))}
                  </ul>
                )}
              </div>
            </div>
          </div>
        </section>
      )}
    </div>
  );
};

const StatCard = ({ label, value, badge, theme = 'slate' }) => {
  const themeClasses = {
    slate: 'bg-gray-800 border-gray-700 text-white',
    emerald: 'bg-emerald-500/10 border-emerald-500/40 text-emerald-100',
    blue: 'bg-blue-500/10 border-blue-500/40 text-blue-100',
    indigo: 'bg-indigo-500/10 border-indigo-500/40 text-indigo-100',
  };

  return (
    <div className={`border rounded-xl p-5 ${themeClasses[theme] ?? themeClasses.slate}`}>
      <div className="text-xs uppercase tracking-wide opacity-80 mb-2">{badge}</div>
      <div className="text-3xl font-semibold">{value}</div>
      <div className="text-sm opacity-80 mt-1">{label}</div>
    </div>
  );
};

export default Users;
