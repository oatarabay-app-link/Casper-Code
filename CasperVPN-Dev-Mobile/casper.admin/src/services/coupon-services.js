import { API_ROUTES } from "../router/api-routes";
import api from "../utils/axios";

const logError = (label, error) => {
  console.error(` ${label}:`, {
    message: error?.response?.data?.message || error.message,
    status: error?.response?.status,
    data: error?.response?.data,
  });
};
export const createCouponService = async (couponData) => {
  try {
    const response = await api.post(API_ROUTES.COUPON.CREATE_COUPON, couponData);
    return response.data;
    } catch (err) {
        logError("❌ Create Cupon Error:", { err });
        throw err;
    }
};
export const fetchCouponsService = async () => {
    try {
        const response = await api.get(API_ROUTES.COUPON.GET_COUPONS);
        return response.data.coupons || [];
    } catch (err) {
        logError("❌ Fetch Coupons Error:", { err });
        throw err;
    }
};
export const updateCouponService = async (couponData) => {
    try {
        const response = await api.put(API_ROUTES.COUPON.UPDATE_COUPON, couponData);
        return response.data;
    } catch (err) {
        logError("❌ Update Coupon Error:", { err });
        throw err;
    }
};

export const deleteCouponService = async (Id) => {
    try {
        const response = await api.delete(API_ROUTES.COUPON.DELETE_COUPON(Id));
        return response.data;
    } catch (err) {
        logError("❌ Delete Coupon Error:", { err });
        throw err;
    }
};

export const validateCouponService = async (code) => {
    try {
        const response = await api.get(API_ROUTES.COUPON.VALIDATE_COUPON(code));
        return response.data;
    } catch (err) {
        logError("❌ Validate Coupon Error:", { err });
        throw err;
    }
};