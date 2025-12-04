import React from 'react';
import { useAuth } from '../hooks/useAuth';

/**
 * Test component to verify authentication setup is working
 * You can add this to your router temporarily to test the auth system
 */
const AuthTest = () => {
  const { 
    user, 
    isAuthenticated, 
    isLoading, 
    error, 
    login, 
    logout 
  } = useAuth();

  const testLogin = async () => {
    const result = await login({
      email: 'test@example.com',
      password: 'password123'
    });
    
    console.log('Login result:', result);
  };

  const testLogout = async () => {
    const result = await logout();
    console.log('Logout result:', result);
  };

  return (
    <div className="p-6 bg-gray-900 min-h-screen text-white">
      <h1 className="text-2xl font-bold mb-4">Authentication Test</h1>
      
      <div className="space-y-4">
        <div className="bg-gray-800 p-4 rounded">
          <h2 className="text-lg font-semibold mb-2">Current State</h2>
          <p><strong>Authenticated:</strong> {isAuthenticated ? 'Yes' : 'No'}</p>
          <p><strong>Loading:</strong> {isLoading ? 'Yes' : 'No'}</p>
          <p><strong>User:</strong> {user ? JSON.stringify(user, null, 2) : 'None'}</p>
          <p><strong>Error:</strong> {error || 'None'}</p>
        </div>

        <div className="bg-gray-800 p-4 rounded">
          <h2 className="text-lg font-semibold mb-2">Test Actions</h2>
          <div className="space-x-2">
            <button
              onClick={testLogin}
              disabled={isLoading}
              className="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded disabled:opacity-50"
            >
              Test Login
            </button>
            <button
              onClick={testLogout}
              disabled={isLoading}
              className="bg-red-600 hover:bg-red-700 px-4 py-2 rounded disabled:opacity-50"
            >
              Test Logout
            </button>
          </div>
        </div>

        <div className="bg-gray-800 p-4 rounded">
          <h2 className="text-lg font-semibold mb-2">API Configuration</h2>
          <p><strong>Base URL:</strong> https://localhost:5006/api</p>
          <p><strong>Token Storage:</strong> localStorage('token')</p>
          <p><strong>Auth Store:</strong> Zustand with persistence</p>
        </div>
      </div>
    </div>
  );
};

export default AuthTest;