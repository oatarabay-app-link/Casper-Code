/* promotionstore.js */
import { create } from "zustand";
import {
createCouponService,
fetchCouponsService,
updateCouponService,
deleteCouponService,
createDiscountService,
fetchDiscountsService,
updateDiscountService,
deleteDiscountService,
createSeasoanalDealService,
fetchSeasonalDealsService,
updateSeasonalDealService,
deleteSeasonalDealService,
activeSeasonalDealService,
} from "../services/promotions-services";

export const usePromotionStore = create((set, get) => ({
// consistent names expected by UI
coupons: [],
discounts: [],
seasonalDeals: [],
loading: false,
error: null,

/* ------- LOADERS ------- */
loadCoupons: async () => {
set({ loading: true, error: null });
try {
const coupons = await fetchCouponsService();
set({ coupons: coupons || [], loading: false });
return coupons;
} catch (err) {
set({ loading: false, error: err });
throw err;
}
},

loadDiscounts: async () => {
set({ loading: true, error: null });
try {
const discounts = await fetchDiscountsService();
set({ discounts: discounts || [], loading: false });
return discounts;
} catch (err) {
set({ loading: false, error: err });
throw err;
}
},

loadSeasonalDeals: async () => {
set({ loading: true, error: null });
try {
const seasonalDeals = await fetchSeasonalDealsService();
set({ seasonalDeals: seasonalDeals || [], loading: false });
return seasonalDeals;
} catch (err) {
set({ loading: false, error: err });
throw err;
}
},

/* ------- COUPONS CRUD ------- */
addCoupon: async (coupon) => {
set({ loading: true, error: null });
try {
const created = await createCouponService(coupon);
set((state) => ({ coupons: [created, ...state.coupons], loading: false }));
return created;
} catch (err) {
set({ loading: false, error: err });
throw err;
}
},

updateCoupon: async (idOrData, maybeData) => {
set({ loading: true, error: null });
try {
const payload = typeof idOrData === "string" ? { id: idOrData, ...(maybeData || {}) } : idOrData;
const updated = await updateCouponService(payload);
set((state) => ({ coupons: state.coupons.map((c) => (c.id === updated.id ? updated : c)), loading: false }));
return updated;
} catch (err) {
set({ loading: false, error: err });
throw err;
}
},

removeCoupon: async (id) => {
set({ loading: true, error: null });
try {
await deleteCouponService(id);
set((state) => ({ coupons: state.coupons.filter((c) => c.id !== id), loading: false }));
return true;
} catch (err) {
set({ loading: false, error: err });
throw err;
}
},

/* ------- DISCOUNTS CRUD ------- */
addDiscount: async (discount) => {
set({ loading: true, error: null });
try {
const created = await createDiscountService(discount);
set((state) => ({ discounts: [created, ...state.discounts], loading: false }));
return created;
} catch (err) {
set({ loading: false, error: err });
throw err;
}
},

updateDiscount: async (idOrData, maybeData) => {
set({ loading: true, error: null });
try {
const payload = typeof idOrData === "string" ? { id: idOrData, ...(maybeData || {}) } : idOrData;
const updated = await updateDiscountService(payload);
set((state) => ({ discounts: state.discounts.map((d) => (d.id === updated.id ? updated : d)), loading: false }));
return updated;
} catch (err) {
set({ loading: false, error: err });
throw err;
}
},

removeDiscount: async (id) => {
set({ loading: true, error: null });
try {
await deleteDiscountService(id);
set((state) => ({ discounts: state.discounts.filter((d) => d.id !== id), loading: false }));
return true;
} catch (err) {
set({ loading: false, error: err });
throw err;
}
},

/* ------- SEASONAL DEALS CRUD ------- */
addSeasonalDeal: async (deal) => {
set({ loading: true, error: null });
try {
const created = await createSeasoanalDealService(deal);
set((state) => ({ seasonalDeals: [created, ...state.seasonalDeals], loading: false }));
return created;
} catch (err) {
set({ loading: false, error: err });
throw err;
}
},

updateSeasonalDeal: async (idOrData, maybeData) => {
set({ loading: true, error: null });
try {
const payload = typeof idOrData === "string" ? { id: idOrData, ...(maybeData || {}) } : idOrData;
const updated = await updateSeasonalDealService(payload);
set((state) => ({ seasonalDeals: state.seasonalDeals.map((s) => (s.id === updated.id ? updated : s)), loading: false }));
return updated;
} catch (err) {
set({ loading: false, error: err });
throw err;
}
},

removeSeasonalDeal: async (id) => {
set({ loading: true, error: null });
try {
await deleteSeasonalDealService(id);
set((state) => ({ seasonalDeals: state.seasonalDeals.filter((s) => s.id !== id), loading: false }));
return true;
} catch (err) {
set({ loading: false, error: err });
throw err;
}
},

activateSeasonalDeal: async (id) => {
set({ loading: true, error: null });
try {
const updated = await activeSeasonalDealService(id);
set((state) => ({ seasonalDeals: state.seasonalDeals.map((s) => (s.id === updated.id ? updated : s)), loading: false }));
return updated;
} catch (err) {
set({ loading: false, error: err });
throw err;
}
},
}));
