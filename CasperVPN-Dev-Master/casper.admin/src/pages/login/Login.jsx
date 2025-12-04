
import { useAuth, useAuthForm } from '../../hooks/useAuth';
import { useNavigate } from 'react-router-dom';
import { useEffect, useState } from 'react';

export default function Login({ onLogin }) {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    email: '',
    password: ''
  });
  const [formErrors, setFormErrors] = useState({});

  const {
    error,
    isAuthenticated,
    isLoading,
    login,
    clearError
  } = useAuth();

  const { validateLoginForm } = useAuthForm();

  // Auth is auto-initialized by the useAuth hook

  useEffect(() => {
    if (isAuthenticated) {
      navigate('/dashboard', { replace: true });
    }
  }, [isAuthenticated, navigate]);

  // Clear error when component unmounts or form changes
  useEffect(() => {
    return () => clearError();
  }, [clearError]);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
    // Clear error when user starts typing
    if (error) {
      clearError();
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    // Validate form
    const errors = validateLoginForm(formData);
    if (Object.keys(errors).length > 0) {
      setFormErrors(errors);
      return;
    }

    setFormErrors({});
    
    const result = await login({
      email: formData.email,
      password: formData.password
    });

    if (result.success ) {
      if (onLogin) onLogin();
      navigate('/dashboard', { replace: true });
    }
  };

  return (
    <div className="flex items-center justify-center min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
      <form onSubmit={handleSubmit} className="bg-[#111827] p-8 rounded-xl shadow-2xl w-full max-w-sm border border-gray-700">
        <h1 className="text-3xl font-extrabold text-center mb-2 text-white tracking-wide drop-shadow-lg">Casper Vpn</h1>
        <h2 className="text-lg font-semibold mb-6 text-center text-blue-400">Sign in to your account</h2>
        <div className="mb-4">
          <label className="block text-gray-300 mb-2 font-medium">Email or Username</label>
          <input
            type="email"
            name="email"
            className={`w-full px-3 py-2 border rounded bg-gray-800 text-white focus:outline-none focus:ring-2 focus:border-blue-500 placeholder-gray-400 ${
              formErrors.email ? 'border-red-500 focus:ring-red-500' : 'border-gray-700 focus:ring-blue-500'
            }`}
            value={formData.email}
            onChange={handleInputChange}
            placeholder="admin@example.com"
            required
          />
          {formErrors.email && <p className="text-red-500 text-sm mt-1">{formErrors.email}</p>}
        </div>
        <div className="mb-6">
          <label className="block text-gray-300 mb-2 font-medium">Password</label>
          <input
            type="password"
            name="password"
            className={`w-full px-3 py-2 border rounded bg-gray-800 text-white focus:outline-none focus:ring-2 focus:border-blue-500 placeholder-gray-400 ${
              formErrors.password ? 'border-red-500 focus:ring-red-500' : 'border-gray-700 focus:ring-blue-500'
            }`}
            value={formData.password}
            onChange={handleInputChange}
            placeholder="Enter your password"
            required
          />
          {formErrors.password && <p className="text-red-500 text-sm mt-1">{formErrors.password}</p>}
        </div>
        {error && <div className="text-red-500 mb-4 text-center font-semibold">{error}</div>}
        <button
          type="submit"
          disabled={isLoading}
          className="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700 transition-all shadow-lg tracking-wide text-lg disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {isLoading ? 'Logging in...' : 'Login'}
        </button>
      </form>
    </div>
  );
}
