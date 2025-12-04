import { create } from 'zustand';
import {
  createPackageService,
  fetchPackagesService,
  updatePackageService,
  deletePackageService
} from '../services/package-services';




const defaultPlans = [
  {
    id: 'secure-lite',
    name: 'Secure Lite',
    price: 8,
    currency: 'USD',
    billingInterval: 'Monthly',
    featureMatrix: [
      { label: 'Global Servers', detail: '45 countries / 89 cities' },
      { label: 'Streaming', detail: 'Optimised HD streaming routes' },
      { label: 'Privacy', detail: 'Strict no-logs & RAM-only servers' },
    ],
    deviceLimit: 3,
    dataPolicy: 'Unlimited data with fair-use after 5 TB',
    protocols: ['WireGuard®', 'OpenVPN'],
    addOnEligible: false,
    popular: false,
    archived: false,
  },
  {
    id: 'pro-shield',
    name: 'Pro Shield',
    price: 12,
    currency: 'USD',
    billingInterval: 'Monthly',
    featureMatrix: [
      { label: 'Global Servers', detail: '88 countries / 145 cities' },
      { label: 'Streaming', detail: '4K streaming + smart routing' },
      { label: 'Privacy', detail: 'Dedicated IP vault + no logs' },
      { label: 'Support', detail: '24/7 live engineering desk' },
    ],
    deviceLimit: 7,
    dataPolicy: 'Unlimited data with performance analytics',
    protocols: ['WireGuard®', 'OpenVPN', 'IKEv2'],
    addOnEligible: true,
    popular: true,
    archived: false,
  },
  {
    id: 'enterprise-vanguard',
    name: 'Enterprise Vanguard',
    price: 28,
    currency: 'USD',
    billingInterval: 'Monthly (min. 10 seats)',
    featureMatrix: [
      { label: 'Global Servers', detail: 'Custom PoP selection + private exit' },
      { label: 'Privacy', detail: 'Hardware HSM keys, audit trails' },
      { label: 'Automation', detail: 'SCIM / Terraform integrations' },
      { label: 'Support', detail: 'Dedicated TAM & uptime SLA' },
    ],
    deviceLimit: 50,
    dataPolicy: 'Unlimited data, priority backbone with burst credits',
    protocols: ['WireGuard®', 'OpenVPN', 'IKEv2', 'L2TP/IPsec'],
    addOnEligible: true,
    popular: false,
    archived: false,
  },
];

const normalisePlans = (plans) =>
  plans.map((plan) => ({
    ...plan,
    featureMatrix: Array.isArray(plan.featureMatrix) ? plan.featureMatrix : [],
    protocols: Array.isArray(plan.protocols) ? plan.protocols : [],
  }));

export const usePackageStore = create((set, get) => ({
  plans: [],
  loading: false,

  // fetchapi///

  loadPackages: async () => {
    set({ loading: true });
    try {
      const packages = await fetchPackagesService();
      set({ plans: normalisePlans(packages), loading: false });
    } catch (err) {
      set({ loading: false });
      throw err;
    }
  },

  // craetePackage///

  addPlan: async (packageData) => {
    set({ loading: true });
    try {
      const newPackage = await createPackageService(packageData);
      set((state) => ({
        plans: [...state.plans, ...normalisePlans([newPackage])],
        loading: false,
      }));
    } catch (err) {
      set({ loading: false });
      throw err;
    }
  },


updatePlan: async (packageData) => {
    set({ loading: true });
    try {
      const updatedPackage = await updatePackageService(packageData);
      set((state) => ({
        plans: state.plans.map((plan) =>
          plan.id === updatedPackage.id
            ? { ...plan, ...normalisePlans([updatedPackage])[0] }
            : plan
        ),
        loading: false,
      }));
    } catch (err) {
      set({ loading: false });
      throw err;
    }
  },

  removePlan: async (id) => {
    try {
      await deletePackageService(id);
      set((state) => ({
        plans: state.plans.filter((pkg) => pkg.id !== id),
      }));
    } catch (error) {
      console.error("Delete error:", error);
    }
  },

  // plans: normalisePlans(defaultPlans),

  // addPlan: (plan) =>
  //   set((state) => {
  //     const preparedPlan = {
  //       ...plan,
  //       featureMatrix: normalisePlans([plan])[0].featureMatrix,
  //       protocols: normalisePlans([plan])[0].protocols,
  //     };

  //     const plans = preparedPlan.popular
  //       ? state.plans.map((p) => ({ ...p, popular: p.id === preparedPlan.id ? true : false }))
  //       : state.plans;

  //     return {
  //       plans: plans.concat({ ...preparedPlan, archived: Boolean(preparedPlan.archived) }),
  //     };
  //   }),

  // updatePlan: (id, updates) =>
  //   set((state) => ({
  //     plans: state.plans.map((plan) =>
  //       plan.id === id
  //         ? {
  //             ...plan,
  //             ...updates,
  //             featureMatrix: normalisePlans([{ ...plan, ...updates }])[0].featureMatrix,
  //             protocols: normalisePlans([{ ...plan, ...updates }])[0].protocols,
  //           }
  //         : plan,
  //     ),
  //   })),

  // removePlan: (id) =>
  //   set((state) => ({
  //     plans: state.plans.filter((plan) => plan.id !== id),
  //   })),

  // markMostPopular: (id) =>
  //   set((state) => ({
  //     plans: state.plans.map((plan) => ({
  //       ...plan,
  //       popular: plan.id === id && !plan.archived,
  //     })),
  //   })),

  // toggleArchive: (id) =>
  //   set((state) => ({
  //     plans: state.plans.map((plan) => {
  //       if (plan.id !== id) {
  //         return plan;
  //       }
  //       const archived = !plan.archived;
  //       return {
  //         ...plan,
  //         archived,
  //         popular: archived ? false : plan.popular,
  //       };
  //     }),
  //   })),

  // getPlanById: (id) => get().plans.find((plan) => plan.id === id),
}));
