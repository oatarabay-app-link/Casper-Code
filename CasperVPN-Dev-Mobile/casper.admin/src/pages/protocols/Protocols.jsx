import React, { useMemo, useState } from 'react';
import { useProtocolStore, ALLOWED_PROTOCOLS } from '../../Store/protocolstore';
import { useAnalyticStore } from '../../Store/analyticstore';

const toPercentMap = (arr) =>
  arr.reduce((acc, cur) => {
    acc[cur.protocol?.toLowerCase()] = cur.percent;
    return acc;
  }, {});

const Protocols = () => {
  const { protocols, addProtocol, toggleSuspend, removeProtocol } = useProtocolStore();
  const protocolDistribution = useAnalyticStore((s) => s.protocolDistribution);
  const percentMap = toPercentMap(protocolDistribution);

  const [selected, setSelected] = useState('');
  const existingLower = useMemo(() => new Set(protocols.map((p) => p.name.toLowerCase())), [protocols]);

  const options = ALLOWED_PROTOCOLS.filter((name) => !existingLower.has(name.toLowerCase()));

  const handleAdd = (e) => {
    e.preventDefault();
    if (!selected) return;
    addProtocol(selected);
    setSelected('');
  };

  return (
    <div className="min-h-screen bg-gray-900 space-y-6">
  <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold text-white">VPN Protocols</h1>
        {/* Add protocol via restricted dropdown */}
        <form onSubmit={handleAdd} className="flex items-center gap-2">
          <select
            value={selected}
            onChange={(e) => setSelected(e.target.value)}
            className="px-3 py-2 bg-gray-800 border border-gray-700 rounded text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-600"
          >
            <option value="">Select protocol</option>
            {options.map((name) => (
              <option key={name} value={name}>{name}</option>
            ))}
          </select>
          <button type="submit" disabled={!selected} className={`px-4 py-2 rounded text-sm ${selected ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-700 text-gray-400 cursor-not-allowed'}`}>Add</button>
        </form>
      </div>

      <div className="bg-gray-800 border border-gray-700 rounded-lg overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-700">
          <h2 className="text-xl font-semibold text-white">Usage by Protocol</h2>
        </div>
        <div className="overflow-x-auto">
          <table className="min-w-full text-sm text-gray-300">
            <thead className="bg-gray-750">
              <tr>
                <th className="text-left px-6 py-3">Protocol</th>
                <th className="text-left px-6 py-3">Users Using</th>
                <th className="text-left px-6 py-3">Servers Using</th>
                <th className="text-left px-6 py-3">Traffic Share</th>
                <th className="text-left px-6 py-3">Status</th>
                <th className="text-left px-6 py-3">Actions</th>
              </tr>
            </thead>
            <tbody>
              {protocols.map((p) => (
                <tr key={p.id} className="border-t border-gray-700">
                  <td className="px-6 py-3 text-white">{p.name}</td>
                  <td className="px-6 py-3">{p.usersUsing.toLocaleString()}</td>
                  <td className="px-6 py-3">{p.serversUsing}</td>
                  <td className="px-6 py-3">{percentMap[p.name.toLowerCase()] || '—'}</td>
                  <td className="px-6 py-3">
                    <span className={`inline-flex items-center px-2 py-0.5 text-xs rounded border ${p.isSuspended ? 'bg-red-600/20 text-red-300 border-red-600/40' : 'bg-green-600/20 text-green-300 border-green-600/40'}`}>
                      {p.isSuspended ? 'Suspended' : 'Active'}
                    </span>
                  </td>
                  <td className="px-6 py-3 space-x-2">
                    <button
                      onClick={() => toggleSuspend(p.id)}
                      className={`px-3 py-1.5 rounded text-xs border transition-colors ${p.isSuspended ? 'bg-green-600/10 text-green-300 border-green-600 hover:bg-green-600/20' : 'bg-red-600/10 text-red-300 border-red-600 hover:bg-red-600/20'}`}
                    >
                      {p.isSuspended ? 'Activate' : 'Suspend'}
                    </button>
                    <button
                      onClick={() => removeProtocol(p.id)}
                      className="px-3 py-1.5 rounded text-xs border bg-gray-700/40 text-gray-300 border-gray-600 hover:bg-gray-700"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

export default Protocols;
