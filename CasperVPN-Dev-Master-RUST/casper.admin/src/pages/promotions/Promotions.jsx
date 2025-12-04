import React from 'react';
import { usePromotionStore } from '../../Store/promotionstore';

const Badge = ({ children, color = 'blue' }) => (
  <span className={`inline-flex items-center px-2 py-0.5 text-xs rounded bg-${color}-600/20 text-${color}-300 border border-${color}-600/30`}>
    {children}
  </span>
);

const Promotions = () => {
  const { coupons, discounts, seasonalDeals } = usePromotionStore();

  return (
    <div className="min-h-screen bg-gray-900 p-6 space-y-8">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold text-white">Promotions</h1>
        <div className="flex gap-2">
          <button className="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">New Coupon</button>
          <button className="px-3 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">New Discount</button>
          <button className="px-3 py-2 bg-emerald-600 text-white text-sm rounded hover:bg-emerald-700">New Seasonal Deal</button>
        </div>
      </div>

      {/* Coupons */}
      <section className="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
          <h2 className="text-xl text-white font-semibold">Coupons</h2>
        </div>
        <div className="overflow-x-auto">
          <table className="min-w-full text-sm text-gray-300">
            <thead className="bg-gray-750">
              <tr>
                <th className="text-left px-6 py-3">Code</th>
                <th className="text-left px-6 py-3">Description</th>
                <th className="text-left px-6 py-3">Type</th>
                <th className="text-left px-6 py-3">Value</th>
                <th className="text-left px-6 py-3">Valid</th>
                <th className="text-left px-6 py-3">Usage</th>
                <th className="text-left px-6 py-3">Status</th>
              </tr>
            </thead>
            <tbody>
              {coupons.map((c) => (
                <tr key={c.id} className="border-t border-gray-700">
                  <td className="px-6 py-3 font-mono text-white">{c.code}</td>
                  <td className="px-6 py-3">{c.description}</td>
                  <td className="px-6 py-3 capitalize">{c.type}</td>
                  <td className="px-6 py-3">{c.type === 'percent' ? `${c.value}%` : `$${c.value} ${c.currency || ''}`}</td>
                  <td className="px-6 py-3">{c.validFrom} → {c.validTo}</td>
                  <td className="px-6 py-3">{c.used}/{c.usageLimit}</td>
                  <td className="px-6 py-3"><Badge color={c.status === 'Active' ? 'green' : c.status === 'Scheduled' ? 'yellow' : 'gray'}>{c.status}</Badge></td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      {/* Discounts */}
      <section className="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
          <h2 className="text-xl text-white font-semibold">Plan Discounts</h2>
        </div>
        <div className="overflow-x-auto">
          <table className="min-w-full text-sm text-gray-300">
            <thead className="bg-gray-750">
              <tr>
                <th className="text-left px-6 py-3">Name</th>
                <th className="text-left px-6 py-3">Plan</th>
                <th className="text-left px-6 py-3">Type</th>
                <th className="text-left px-6 py-3">Value</th>
                <th className="text-left px-6 py-3">Status</th>
              </tr>
            </thead>
            <tbody>
              {discounts.map((d) => (
                <tr key={d.id} className="border-t border-gray-700">
                  <td className="px-6 py-3 text-white">{d.name}</td>
                  <td className="px-6 py-3 uppercase">{d.planId}</td>
                  <td className="px-6 py-3 capitalize">{d.type}</td>
                  <td className="px-6 py-3">{d.type === 'percent' ? `${d.value}%` : `$${d.value} ${d.currency || ''}`}</td>
                  <td className="px-6 py-3"><Badge color={d.status === 'Active' ? 'green' : d.status === 'Scheduled' ? 'yellow' : 'gray'}>{d.status}</Badge></td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      {/* Seasonal Deals */}
      <section className="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
          <h2 className="text-xl text-white font-semibold">Seasonal Deals</h2>
        </div>
        <div className="overflow-x-auto">
          <table className="min-w-full text-sm text-gray-300">
            <thead className="bg-gray-750">
              <tr>
                <th className="text-left px-6 py-3">Name</th>
                <th className="text-left px-6 py-3">Description</th>
                <th className="text-left px-6 py-3">Type</th>
                <th className="text-left px-6 py-3">Value</th>
                <th className="text-left px-6 py-3">Window</th>
                <th className="text-left px-6 py-3">Applies To</th>
                <th className="text-left px-6 py-3">Status</th>
              </tr>
            </thead>
            <tbody>
              {seasonalDeals.map((s) => (
                <tr key={s.id} className="border-t border-gray-700">
                  <td className="px-6 py-3 text-white">{s.name}</td>
                  <td className="px-6 py-3">{s.description}</td>
                  <td className="px-6 py-3 capitalize">{s.type}</td>
                  <td className="px-6 py-3">{s.type === 'percent' ? `${s.value}%` : `$${s.value} ${s.currency || ''}`}</td>
                  <td className="px-6 py-3">{s.startDate} → {s.endDate}</td>
                  <td className="px-6 py-3">{Array.isArray(s.applyTo) ? s.applyTo.join(', ') : s.applyTo}</td>
                  <td className="px-6 py-3"><Badge color={s.status === 'Active' ? 'green' : s.status === 'Scheduled' ? 'yellow' : 'gray'}>{s.status}</Badge></td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>
    </div>
  );
};

export default Promotions;
