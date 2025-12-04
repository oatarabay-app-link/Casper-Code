import React from 'react';

const Reports = () => {
  return (
    <div className="min-h-screen bg-gray-900 p-6">
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-3xl font-bold text-white">Reports</h1>
        <button className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Export</button>
      </div>

      <div className="bg-gray-800 border border-gray-700 rounded-lg p-6 text-gray-300">
        <p>Generate usage, revenue, and security reports.</p>
      </div>
    </div>
  );
};

export default Reports;
