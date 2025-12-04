// packagestore.js
import { create } from "zustand";
import {
  createPackageService,
  fetchPackagesService,
  updatePackageService,
  deletePackageService,
} from "../services/package-services";

const defaultPlans = []; // now empty; store will load from API

export const usePackageStore = create((set, get) => ({
  plans: defaultPlans,
  loading: false,
  error: null,

  loadPackages: async () => {
    set({ loading: true, error: null });
    try {
      const packages = await fetchPackagesService();
      set({ plans: packages, loading: false });
    } catch (err) {
      set({ loading: false, error: err });
      // bubble up if caller wants to handle
      throw err;
    }
  },

  addPlan: async (formData) => {
    set({ loading: true, error: null });
    try {
      const created = await createPackageService(formData);
      set((state) => ({ plans: [...state.plans, created], loading: false }));
      return created;
    } catch (err) {
      set({ loading: false, error: err });
      throw err;
    }
  },

  updatePlan: async (idOrData, maybeData) => {
    set({ loading: true, error: null });
    try {
      // Support both updatePlan(id, updates) and updatePlan(updatesObjectWithId)
      const packageData = typeof idOrData === "string" ? { id: idOrData, ...(maybeData || {}) } : idOrData;
      const updated = await updatePackageService(packageData);
      set((state) => ({
        plans: state.plans.map((p) => (p.id === updated.id ? updated : p)),
        loading: false,
      }));
      return updated;
    } catch (err) {
      set({ loading: false, error: err });
      throw err;
    }
  },

  removePlan: async (id) => {
    set({ loading: true, error: null });
    try {
      await deletePackageService(id);
      set((state) => ({ plans: state.plans.filter((p) => p.id !== id), loading: false }));
    } catch (err) {
      set({ loading: false, error: err });
      throw err;
    }
  },
}));
