import { API_ROUTES } from "../router/api-routes";
import api from "../utils/axios";

const logError = (label, error) => {
  console.error(` ${label}:`, {
    message: error?.response?.data?.message || error.message,
    status: error?.response?.status,
    data: error?.response?.data,
  });
}

export const fetchRolesSummaryService = async () => {
    try {
        const response = await api.get(API_ROUTES.ROLES.GET_ALL_ROLES);
        return response.data.roles || [];
    } catch (err) {
        logError("❌ Fetch Roles Summary Error:", { err });
        throw err;
    }
};

export const fetchRolesService = async () => {
    try {
        const response = await api.get(API_ROUTES.ROLES.GET_ROLES);
        return response.data.roles || [];
    } catch (err) {
        logError("❌ Fetch Roles Error:", { err });
        throw err;
    }
};

export const createRoleService = async (roleData) => {
    try {
        const response = await api.post(API_ROUTES.ROLES.CREATE_ROLE, roleData);
        return response.data;
    } catch (err) {
        logError("❌ Create Role Error:", { err });
        throw err;
    }
};

export const fetchRoleByIdService = async (roleId) => {
    try {
        const response = await api.get(API_ROUTES.ROLES.GET_ROLE_BY_ID(roleId));
        return response.data.role || null;
    } catch (err) {
        logError("❌ Fetch Role By ID Error:", { err });
        throw err;
    }
};

export const updateRoleByIdService = async (roleId, roleData) => {
    try {
        const response = await api.put(API_ROUTES.ROLES.UPDATE_ROLE_BY_ID(roleId), roleData);
        return response.data;
    } catch (err) {
        logError("❌ Update Role By ID Error:", { err });
        throw err;
    }
};

export const deleteRoleService = async (roleId) => {
    try {
        const response = await api.delete(API_ROUTES.ROLES.DELETE_ROLE_BY_ID(roleId));
        return response.data;
    } catch (err) {
        logError("❌ Delete Role Error:", { err });
        throw err;
    }
};

export const createRolePermissionService = async (roleId, permissionData) => {
    try {
        const response = await api.post(API_ROUTES.ROLES.CREATE_ROLE_PERMISSION(roleId), permissionData);
        return response.data;
    } catch (err) {
        logError("❌ Create Role Permission Error:", { err });
        throw err;
    }
};

export const fetchRolePermissionsService = async (roleId) => {
    try {
        const response = await api.get(API_ROUTES.ROLES.GET_ROLE_PERMISSIONS(roleId));
        return response.data.permissions || [];
    } catch (err) {
        logError("❌ Fetch Role Permissions Error:", { err });
        throw err;
    }
};

export const deleteRolePermissionService = async (roleId, permissionData) => {
    try {
        const response = await api.delete(API_ROUTES.ROLES.DELETE_ROLE_PERMISSION(roleId), { data: permissionData });
        return response.data;
    } catch (err) {
        logError("❌ Delete Role Permission Error:", { err });
        throw err;
    }
};

export const createRoleUserService = async (roleId, userId) => {
    try {
        const response = await api.post(API_ROUTES.ROLES.CREATE_ROLE_USER_BY_ID(roleId, userId));
        return response.data;
    } catch (err) { 
        logError("❌ Create Role User Error:", { err });
        throw err;
    }
};
export const deleteRoleUserService = async (roleId, userId) => {
    try {
        const response = await api.delete(API_ROUTES.ROLES.DELETE_ROLE_USER_BY_ID(roleId, userId));
        return response.data;
    } catch (err) {
        logError("❌ Delete Role User Error:", { err });
        throw err;
    }
};

export const fetchRoleUsersService = async (roleId) => {
    try {
        const response = await api.get(API_ROUTES.ROLES.GET_ROLE_USERS(roleId));
        return response.data.users || [];
    } catch (err) {
        logError("❌ Fetch Role Users Error:", { err });
        throw err;
    }
};