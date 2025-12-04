import { create } from 'zustand';

export const usePackageStore = create((set, get) => ({
  plans: [
    {
      id: 'monthly',
      name: 'Monthly',
      price: 9.99,
      currency: 'USD',
      cycleLabel: 'per month',
      popular: false,
      features: [
        'Unlimited bandwidth',
        'All protocols (WG / OpenVPN / IKEv2 / L2TP/IPsec)',
        'No logs',
        '1 device',
        'Basic support',
      ],
    },
    {
      id: 'quarterly',
      name: 'Quarterly',
      price: 24.99,
      currency: 'USD',
      cycleLabel: 'every 3 months',
      popular: true,
      features: [
        'Unlimited bandwidth',
        'All protocols (WG / OpenVPN / IKEv2 / L2TP/IPsec)',
        'No logs',
        '3 devices',
        'Priority support',
        'Best value quarterly',
      ],
    },
    {
      id: 'yearly',
      name: 'Yearly',
      price: 79.99,
      currency: 'USD',
      cycleLabel: 'per year',
      popular: false,
      features: [
        'Unlimited bandwidth',
        'All protocols (WG / OpenVPN / IKEv2 / L2TP/IPsec)',
        'No logs',
        '5 devices',
        'Priority support',
        'Save more annually',
      ],
    },
  ],

  addPlan: (plan) => set((state) => ({ plans: [...state.plans, plan] })),
  updatePlan: (id, updates) =>
    set((state) => ({ plans: state.plans.map((p) => (p.id === id ? { ...p, ...updates } : p)) })),
  removePlan: (id) => set((state) => ({ plans: state.plans.filter((p) => p.id !== id) })),

  getPlanById: (id) => get().plans.find((p) => p.id === id),
}));
