import React, { useState, useEffect } from 'react';
import { useAuth, usePermissions } from '../hooks/useAuth';

/**
 * Example component showing how to use authentication for user management
 * This demonstrates admin-only functionality
 */
const UserManagement = () => {
  const { getUsers, deleteUser, revokeUserToken, isLoading, error } = useAuth();
  const { isAdmin, canManageUsers } = usePermissions();
  const [users, setUsers] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const pageSize = 10;

  // Redirect if not admin
  if (!canManageUsers) {
    return (
      <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <strong className="font-bold">Access Denied!</strong>
        <span className="block sm:inline"> You don't have permission to access user management.</span>
      </div>
    );
  }

  useEffect(() => {
    loadUsers();
  }, [currentPage]);

  const loadUsers = async () => {
    const result = await getUsers(currentPage, pageSize);
    if (result.success) {
      setUsers(result.data.data || []);
      setTotalPages(Math.ceil((result.data.pagination?.total || 0) / pageSize));
    }
  };

  const handleDeleteUser = async (userId, userName) => {
    if (window.confirm(`Are you sure you want to delete user: ${userName}?`)) {
      const result = await deleteUser(userId);
      if (result.success) {
        loadUsers(); // Refresh the list
        alert('User deleted successfully');
      } else {
        alert(`Failed to delete user: ${result.error}`);
      }
    }
  };

  const handleRevokeToken = async (userId, userName) => {
    if (window.confirm(`Are you sure you want to revoke tokens for user: ${userName}?`)) {
      const result = await revokeUserToken(userId);
      if (result.success) {
        alert('User tokens revoked successfully');
      } else {
        alert(`Failed to revoke tokens: ${result.error}`);
      }
    }
  };

  if (isLoading) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
      </div>
    );
  }

  return (
    <div className="p-6">
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-2xl font-bold text-white">User Management</h2>
        <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
          Add New User
        </button>
      </div>

      {error && (
        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          {error}
        </div>
      )}

      <div className="bg-gray-800 rounded-lg overflow-hidden">
        <table className="w-full">
          <thead className="bg-gray-700">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                User
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                Email
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                Status
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-600">
            {users.map((user) => (
              <tr key={user.id} className="hover:bg-gray-700">
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="text-sm font-medium text-white">{user.userName}</div>
                  <div className="text-sm text-gray-400">{user.id}</div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                  {user.email}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                    user.emailConfirmed 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-yellow-100 text-yellow-800'
                  }`}>
                    {user.emailConfirmed ? 'Verified' : 'Pending'}
                  </span>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                  <button
                    onClick={() => handleRevokeToken(user.id, user.userName)}
                    className="text-yellow-400 hover:text-yellow-300"
                  >
                    Revoke Tokens
                  </button>
                  <button
                    onClick={() => handleDeleteUser(user.id, user.userName)}
                    className="text-red-400 hover:text-red-300"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {users.length === 0 && (
          <div className="text-center py-8 text-gray-400">
            No users found
          </div>
        )}
      </div>

      {/* Pagination */}
      {totalPages > 1 && (
        <div className="flex justify-center mt-6 space-x-2">
          <button
            onClick={() => setCurrentPage(prev => Math.max(1, prev - 1))}
            disabled={currentPage === 1}
            className="px-3 py-1 bg-gray-700 text-white rounded disabled:opacity-50"
          >
            Previous
          </button>
          
          <span className="px-3 py-1 text-white">
            Page {currentPage} of {totalPages}
          </span>
          
          <button
            onClick={() => setCurrentPage(prev => Math.min(totalPages, prev + 1))}
            disabled={currentPage === totalPages}
            className="px-3 py-1 bg-gray-700 text-white rounded disabled:opacity-50"
          >
            Next
          </button>
        </div>
      )}
    </div>
  );
};

export default UserManagement;