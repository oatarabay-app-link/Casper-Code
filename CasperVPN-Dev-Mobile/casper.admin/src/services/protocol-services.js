import { API_ROUTES } from "../router/api-routes";
import api from "../utils/axios";

const logError = (label, error) => {
  console.error(` ${label}:`, {
    message: error?.response?.data?.message || error.message,
    status: error?.response?.status,
    data: error?.response?.data,
  });
};

export const createProtocolService = async (protocolData) => {
  try {
    const response = await api.post(API_ROUTES.VPNPROTOCOL.CREATE_VPNPROTOCOL, protocolData);
    return response.data;
    } catch (err) {
        logError("❌ Create Protocol Error:", { err });
        throw err;
    }
};
export const fetchProtocolsService = async () => {
    try {
        const response = await api.get(API_ROUTES.VPNPROTOCOL.GET_VPNPROTOCOLS);
        return response.data.protocols || [];
    } catch (err) {
        logError("❌ Fetch Protocols Error:", { err });
        throw err;
    }
}

export const fetchProtocolByIdService = async (protocolId) => {
    try {
        const response = await api.get(API_ROUTES.VPNPROTOCOL.GET_VPNPROTOCOLS_BY_ID(protocolId));
        return response.data.protocol || null;
    } catch (err) {
        logError("❌ Fetch Protocol By ID Error:", { err });
        throw err;
    }
}

export const updateProtocolService = async (protocolData) => {
    try {
        const response = await api.put(API_ROUTES.VPNPROTOCOL.UPDATE_VPNPROTOCOL, protocolData);
        return response.data;
    } catch (err) { 
        logError("❌ Update Protocol Error:", { err });
        throw err;
    }
}
export const deleteProtocolService = async (protocolId) => {
    try {
        const response = await api.delete(API_ROUTES.VPNPROTOCOL.DELETE_VPNPROTOCOL(protocolId));
        return response.data;
    } catch (err) {
        logError("❌ Delete Protocol Error:", { err });
        throw err;
    }
};
