/* promotions-services.js */
import { API_ROUTES } from "../router/api-routes";
import api from "../utils/axios";

/* central logger that preserves the original error */
const logError = (label, error) => {
console.error(`${label}:`, {
message: error?.response?.data?.message ?? error.message,
status: error?.response?.status,
data: error?.response?.data ?? error,
stack: error?.stack,
});
};

/* small helpers */
const pickPossible = (obj, keys) => {
if (!obj) return undefined;
for (const k of keys) {
if (typeof obj[k] !== "undefined") return obj[k];
}
return undefined;
};

const normalizeCoupon = (raw) => ({
id: raw.id ?? raw._id ?? raw.code ?? undefined,
code: raw.code ?? "",
description: raw.description ?? "",
type: raw.type ?? "Percent",
value: raw.value ?? 0,
currency: raw.currency ?? raw.currencyCode ?? undefined,
validFrom: raw.validFrom ?? raw.from ?? undefined,
validTo: raw.validTo ?? raw.to ?? undefined,
usageLimit: raw.usageLimit ?? 0,
used: raw.used ?? 0,
status: raw.status ?? "Scheduled",
_raw: raw,
});

const normalizeDiscount = (raw) => ({
id: raw.id ?? raw._id ?? undefined,
name: raw.name ?? "",
plan: raw.planId ?? raw.targetPlan ?? "",
type: raw.type ?? "Percent",
value: Number(raw.value ?? 0),
currency: raw.currency ?? undefined,
status: raw.status ?? "Scheduled",
_raw: raw,
});

const normalizeSeasonal = (raw) => ({
id: raw.id ?? raw._id ?? undefined,
name: raw.name ?? "",
description: raw.description ?? "",
type: raw.type ?? "Percent",
value: Number(raw.value ?? 0),
currency: raw.currency ?? undefined,
startDate: raw.startDate ?? raw.from ?? undefined,
endDate: raw.endDate ?? raw.to ?? undefined,
applyTo: Array.isArray(raw.applyTo) ? raw.applyTo : raw.applyTo ?? "all",
status: raw.status ?? "Scheduled",
_raw: raw,
});

/* ===== Coupons ===== */

export const createCouponService = async (couponData) => {
try {
// Do not send client-generated id to server (server should generate)
const { id, ...payload } = couponData;
const response = await api.post(API_ROUTES.COUPON.CREATE_COUPON, payload);
// Try to find coupon in response in several places
const coupon = pickPossible(response.data, ["coupon", "data", "result"]) ?? response.data;
return normalizeCoupon(coupon);
} catch (err) {
logError("❌ Create Coupon Error", err);
throw err;
}
};

export const fetchCouponsService = async () => {
try {
const response = await api.get(API_ROUTES.COUPON.GET_COUPONS);
// Defensive extraction: backend might return { coupons: [...] } or { data: { coupons: [...] } } etc.
const list =
pickPossible(response.data, ["coupons", "data", "results"]) ??
response.data?.coupons ??
response.data ??
[];
const arr = Array.isArray(list) ? list : list.items ?? [];
return (arr || []).map(normalizeCoupon);
} catch (err) {
logError("❌ Fetch Coupons Error", err);
throw err;
}
};

export const updateCouponService = async (couponData) => {
try {
// Backend PUT /admin/coupon expects body with id + updates (according to your API_ROUTES)
const response = await api.put(API_ROUTES.COUPON.UPDATE_COUPON, couponData);
const coupon = pickPossible(response.data, ["coupon", "data"]) ?? response.data;
return normalizeCoupon(coupon);
} catch (err) {
logError("❌ Update Coupon Error", err);
throw err;
}
};

export const deleteCouponService = async (id) => {
try {
const response = await api.delete(API_ROUTES.COUPON.DELETE_COUPON(id));
return response.data;
} catch (err) {
logError("❌ Delete Coupon Error", err);
throw err;
}
};

export const validateCouponService = async (code) => {
try {
const response = await api.get(API_ROUTES.COUPON.VALIDATE_COUPON(code));
return response.data;
} catch (err) {
logError("❌ Validate Coupon Error", err);
throw err;
}
};

/* ===== Discounts ===== */

export const createDiscountService = async (discountData) => {
try {
const { id, ...payload } = discountData;
const response = await api.post(API_ROUTES.DISCOUNT.CREATE_DISCOUNT, payload);
const discount = pickPossible(response.data, ["discount", "data"]) ?? response.data;
return normalizeDiscount(discount);
} catch (err) {
logError("❌ Create Discount Error", err);
throw err;
}
};

export const fetchDiscountsService = async () => {
try {
const response = await api.get(API_ROUTES.DISCOUNT.GET_DISCOUNTS);
const list =
pickPossible(response.data, ["discounts", "data", "results"]) ??
response.data?.discounts ??
response.data ??
[];
const arr = Array.isArray(list) ? list : list.items ?? [];
return (arr || []).map(normalizeDiscount);
} catch (err) {
logError("❌ Fetch Discounts Error", err);
throw err;
}
};

export const updateDiscountService = async (discountData) => {
try {
const response = await api.put(API_ROUTES.DISCOUNT.UPDATE_DISCOUNT, discountData);
const discount = pickPossible(response.data, ["discount", "data"]) ?? response.data;
return normalizeDiscount(discount);
} catch (err) {
logError("❌ Update Discount Error", err);
throw err;
}
};

export const deleteDiscountService = async (id) => {
try {
const response = await api.delete(API_ROUTES.DISCOUNT.DELETE_DISCOUNT(id));
return response.data;
} catch (err) {
logError("❌ Delete Discount Error", err);
throw err;
}
};

/* ===== Seasonal Deals ===== */

export const createSeasoanalDealService = async (dealData) => {
try {
const { id, ...payload } = dealData;
const response = await api.post(API_ROUTES.SEASONAL_DEAL.CREATE_SEASONAL_DEAL, payload);
const item = pickPossible(response.data, ["seasonalDeal", "seasonaldeal", "data"]) ?? response.data;
return normalizeSeasonal(item);
} catch (err) {
logError("❌ Create Seasonal Deal Error", err);
throw err;
}
};

export const fetchSeasonalDealsService = async () => {
try {
const response = await api.get(API_ROUTES.SEASONAL_DEAL.GET_SEASONAL_DEALS);
// check many possible keys
const list =
pickPossible(response.data, ["seasonalDeals", "seasonaldeals", "data", "results"]) ??
response.data?.seasonaldeals ??
response.data ??
[];
const arr = Array.isArray(list) ? list : list.items ?? [];
return (arr || []).map(normalizeSeasonal);
} catch (err) {
logError("❌ Fetch Seasonal Deals Error", err);
throw err;
}
};

export const updateSeasonalDealService = async (dealData) => {
try {
const response = await api.put(API_ROUTES.SEASONAL_DEAL.UPDATE_SEASONAL_DEAL, dealData);
const item = pickPossible(response.data, ["seasonalDeal", "data"]) ?? response.data;
return normalizeSeasonal(item);
} catch (err) {
logError("❌ Update Seasonal Deal Error", err);
throw err;
}
};

export const deleteSeasonalDealService = async (id) => {
try {
const response = await api.delete(API_ROUTES.SEASONAL_DEAL.DELETE_SEASONAL_DEAL(id));
return response.data;
} catch (err) {
logError("❌ Delete Seasonal Deal Error", err);
throw err;
}
};

/* Activation: POST /admin/seasonaldeal/active
According to your API structure this is a POST endpoint (no id in URL),
so we pass { id } in body.
*/
export const activeSeasonalDealService = async (id) => {
try {
const response = await api.post(API_ROUTES.SEASONAL_DEAL.ACTIVE_SEASONAL_DEAL, { id });
const item = pickPossible(response.data, ["seasonalDeal", "data"]) ?? response.data;
return normalizeSeasonal(item);
} catch (err) {
logError("❌ Activate Seasonal Deal Error", err);
throw err;
}
};
