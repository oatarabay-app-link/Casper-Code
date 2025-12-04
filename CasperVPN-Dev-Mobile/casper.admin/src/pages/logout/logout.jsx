import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../hooks/useAuth';
import { Logout as LogoutIcon, Cancel } from '@mui/icons-material';

const Logout = () => {
  const navigate = useNavigate();
  const { user, logout } = useAuth();
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState(null);

  const handleLogout = async () => {
    if (submitting) return;
    setSubmitting(true);
    setError(null);
    const result = await logout();
    setSubmitting(false);
    if (result.success) {
      navigate('/login', { replace: true });
    } else {
      setError(result.error || 'Failed to logout. Please try again.');
    }
  };

  const handleCancel = () => {
    navigate('/dashboard');
  };

  return (
    <div className="p-6">
      <div className="max-w-xl mx-auto bg-gray-800 border border-gray-700 rounded-lg shadow-lg">
        <div className="p-6">
          <div className="flex items-center gap-3 mb-4">
            <LogoutIcon className="text-red-400" />
            <h1 className="text-2xl font-semibold text-white">Sign out</h1>
          </div>
          <p className="text-gray-300 mb-6">
            {`Are you sure you want to log out${user?.userName ? `, ${user.userName}` : ''}?`}
          </p>

          {error && (
            <div className="mb-4 px-4 py-3 rounded border border-red-700 bg-red-900/30 text-red-300 text-sm">
              {error}
            </div>
          )}

          <div className="flex items-center gap-3">
            <button
              onClick={handleLogout}
              disabled={submitting}
              className={`inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white bg-red-600 hover:bg-red-700 transition-colors duration-200 disabled:opacity-60 disabled:cursor-not-allowed`}
            >
              <LogoutIcon fontSize="small" />
              {submitting ? 'Signing out…' : 'Logout'}
            </button>
            <button
              onClick={handleCancel}
              disabled={submitting}
              className="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors duration-200 disabled:opacity-60 disabled:cursor-not-allowed"
            >
              <Cancel fontSize="small" />
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Logout;