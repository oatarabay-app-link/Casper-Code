import { useUserStore } from '../../Store/userstore';
import { useTheme } from '../../hooks/useTheme.jsx';
import { PeopleAlt, CloudDownload, PersonAdd } from '@mui/icons-material';

const User = () => {
  const { getThemeClasses } = useTheme();
  const themeClasses = getThemeClasses();
  const {
    userStats,
    users,
    searchQuery,
    selectedPlan,
    setSearchQuery,
    setSelectedPlan,
  } = useUserStore();

  const query = searchQuery || '';
  const filteredUsers = users.filter((user) => {
    const matchesSearch = user.email.toLowerCase().includes(query.toLowerCase());
    const matchesPlan = selectedPlan === 'All' || user.plan === selectedPlan;
    return matchesSearch && matchesPlan;
  });

  return (
      <div className="p-6">
      {/* Header */}
      <div className="flex items-center justify-between mb-8 flex-wrap gap-4">
        <div>
          <h1 className={`text-3xl font-bold ${themeClasses.textPrimary}`}>User Management</h1>
          <p className={`text-sm mt-1 ${themeClasses.textTertiary}`}>Manage users, plans, and data usage</p>
        </div>
        <div className="flex gap-3">
          <button className={`flex items-center gap-2 px-4 py-2 text-sm rounded-lg transition-colors duration-200 ${themeClasses.buttonSecondary}`}>
            <CloudDownload fontSize="small" />
            Export Users
          </button>
          <button className={`flex items-center gap-2 px-4 py-2 text-sm rounded-lg transition-colors duration-200 ${themeClasses.buttonPrimary}`}>
            <PersonAdd fontSize="small" />
            Add User
          </button>
        </div>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <StatCard label="Total Users" value={userStats.total} />
        <StatCard label="Active Users" value={userStats.active} />
        <StatCard label="Premium Users" value={userStats.premium} />
        <StatCard label="Data Usage" value={userStats.usage} />
      </div>

      {/* Search & Filter */}
      <div className="flex justify-between items-center mb-4 flex-wrap gap-4">
        <h2 className="text-lg font-semibold text-white">User Directory</h2>
        <div className="flex gap-3">
          <input
            type="text"
            placeholder="Search users..."
            value={searchQuery || ''}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 placeholder-gray-400"
          />
          <select
            value={selectedPlan}
            onChange={(e) => setSelectedPlan(e.target.value)}
            className="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
          >
            <option value="All">Filter</option>
            <option value="Basic">Basic</option>
            <option value="Premium">Premium</option>
            <option value="Enterprise">Enterprise</option>
          </select>
        </div>
      </div>

      {/* User Table */}
      <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6">
        <table className="w-full text-sm text-left text-white">
          <thead className="bg-gray-700 text-xs uppercase text-gray-300">
            <tr>
              <th className="px-4 py-3">Email</th>
              <th className="px-4 py-3">Plan</th>
              <th className="px-4 py-3">Status</th>
              <th className="px-4 py-3">Server</th>
              <th className="px-4 py-3">Joined</th>
              <th className="px-4 py-3">Usage</th>
            </tr>
          </thead>
          <tbody>
            {filteredUsers.map((user, i) => (
              <tr key={i} className="border-t border-gray-700 hover:bg-gray-750 transition-colors duration-200">
                <td className="px-4 py-3 text-gray-300">{user.email}</td>
                <td className="px-4 py-3 text-gray-300">{user.plan}</td>
                <td className="px-4 py-3">
                  <span className={`text-xs font-semibold px-2 py-1 rounded-full ${
                    user.status === 'Active'
                      ? 'bg-green-900 text-green-300 border border-green-700'
                      : 'bg-red-900 text-red-300 border border-red-700'
                  }`}>
                    {user.status}
                  </span>
                </td>
                <td className="px-4 py-3 text-gray-300">{user.server}</td>
                <td className="px-4 py-3 text-gray-300">{user.joined}</td>
                <td className="px-4 py-3 text-gray-300">{user.usage}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      </div>
  );
};

const StatCard = ({ label, value }) => (
  <div className="bg-gray-800 border border-gray-700 shadow-lg rounded-lg p-4 text-center">
    <div className="text-sm text-gray-400">{label}</div>
    <div className="text-2xl font-semibold text-white">{value}</div>
  </div>
);

export default User;
