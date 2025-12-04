import { create } from 'zustand';

// Utility helpers
const today = () => new Date().toISOString().slice(0, 10);
const plusDays = (n) => new Date(Date.now() + n * 86400000).toISOString().slice(0, 10);

export const usePromotionStore = create((set, get) => ({
  // Example data
  coupons: [
    {
      id: 'c1',
      code: 'WELCOME10',
      description: '10% off for new users',
      type: 'percent', // percent | amount
      value: 10, // 10%
      currency: 'USD',
      validFrom: today(),
      validTo: plusDays(30),
      usageLimit: 1000,
      used: 132,
      status: 'Active', // Active | Expired | Scheduled
    },
    {
      id: 'c2',
      code: 'SAVE5',
      description: '$5 off any monthly plan',
      type: 'amount',
      value: 5,
      currency: 'USD',
      validFrom: today(),
      validTo: plusDays(10),
      usageLimit: 500,
      used: 221,
      status: 'Active',
    },
  ],

  discounts: [
    {
      id: 'd1',
      name: 'Quarterly Bundle',
      planId: 'quarterly',
      type: 'percent',
      value: 15, // 15% off the quarterly plan
      status: 'Active',
    },
    {
      id: 'd2',
      name: 'Annual Saver',
      planId: 'yearly',
      type: 'amount',
      value: 10, // $10 off
      currency: 'USD',
      status: 'Scheduled',
    },
  ],

  seasonalDeals: [
    {
      id: 's1',
      name: 'Black Friday Mega Sale',
      description: 'Up to 50% off on all plans for a limited time',
      type: 'percent',
      value: 50,
      applyTo: 'all', // 'all' | string[] of planIds
      startDate: plusDays(45),
      endDate: plusDays(50),
      status: 'Scheduled',
    },
    {
      id: 's2',
      name: 'New Year Deal',
      description: 'Start the year private: extra $8 off yearly',
      type: 'amount',
      value: 8,
      currency: 'USD',
      applyTo: ['yearly'],
      startDate: '2026-01-01',
      endDate: '2026-01-07',
      status: 'Scheduled',
    },
  ],

  // CRUD actions (stubs)
  addCoupon: (coupon) => set((s) => ({ coupons: [coupon, ...s.coupons] })),
  updateCoupon: (id, u) => set((s) => ({ coupons: s.coupons.map((c) => (c.id === id ? { ...c, ...u } : c)) })),
  removeCoupon: (id) => set((s) => ({ coupons: s.coupons.filter((c) => c.id !== id) })),

  addDiscount: (d) => set((s) => ({ discounts: [d, ...s.discounts] })),
  updateDiscount: (id, u) => set((s) => ({ discounts: s.discounts.map((d) => (d.id === id ? { ...d, ...u } : d)) })),
  removeDiscount: (id) => set((s) => ({ discounts: s.discounts.filter((d) => d.id !== id) })),

  addSeasonalDeal: (deal) => set((s) => ({ seasonalDeals: [deal, ...s.seasonalDeals] })),
  updateSeasonalDeal: (id, u) => set((s) => ({ seasonalDeals: s.seasonalDeals.map((d) => (d.id === id ? { ...d, ...u } : d)) })),
  removeSeasonalDeal: (id) => set((s) => ({ seasonalDeals: s.seasonalDeals.filter((d) => d.id !== id) })),
}));
