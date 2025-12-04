import React from 'react';
import { usePackageStore } from '../../Store/packagestore';

const currency = (amount, code = 'USD') =>
  new Intl.NumberFormat('en-US', { style: 'currency', currency: code }).format(amount);

const Subscriptions = () => {
  const plans = usePackageStore((s) => s.plans);

  return (
    <div className="min-h-screen bg-gray-900 p-6">
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-3xl font-bold text-white">Subscription Management</h1>
        <button className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Create Plan</button>
      </div>

      <div className="bg-gray-800 border border-gray-700 rounded-lg p-6 text-gray-300">
        <p>Manage customer subscriptions, billing cycles, and plan upgrades.</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        {plans.map((plan) => (
          <div
            key={plan.id}
            className={`relative bg-gray-800 border ${plan.popular ? 'border-blue-500 ring-2 ring-blue-500/40' : 'border-gray-700'} rounded-xl p-6 flex flex-col`}
          >
            {plan.popular && (
              <div className="absolute -top-3 right-4 bg-blue-600 text-white text-xs px-2 py-1 rounded">Most Popular</div>
            )}
            <h2 className="text-xl font-semibold text-white">{plan.name}</h2>
            <div className="mt-2 text-3xl font-bold text-white">{currency(plan.price, plan.currency)}</div>
            <div className="text-sm text-gray-400">{plan.cycleLabel}</div>

            <ul className="mt-4 space-y-2 text-gray-300 text-sm">
              {plan.features.map((f, idx) => (
                <li key={idx} className="flex items-start gap-2">
                  <span className="mt-1 h-1.5 w-1.5 rounded-full bg-blue-500" />
                  <span>{f}</span>
                </li>
              ))}
            </ul>

            <button className="mt-6 w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
              Select {plan.name}
            </button>
          </div>
        ))}
      </div>
    </div>
  );
};

export default Subscriptions;
