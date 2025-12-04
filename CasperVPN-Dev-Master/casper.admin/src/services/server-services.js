import { API_ROUTES } from "../router/api-routes";
import api from "../utils/axios";

// ✅ Validate server data before sending to API
// const validateServerData = (data) => {
//   if (!data.name || data.name.trim() === "") {
//     throw new Error("Server name is required");
//   }

//   if (!data.location || data.location.trim() === "") {
//     throw new Error("Server location is required");
//   }

//   if (!data.ip || data.ip.trim() === "") {
//     throw new Error("Server IP address is required");
//   }

//   if (!data.username || data.username.trim() === "") {
//     throw new Error("Username is required");
//   }

//   // At least one of password or sshKey must be provided
//   if ((!data.password || data.password.trim() === "") &&
//       (!data.sshKey || data.sshKey.trim() === "")) {
//     throw new Error("Provide either a password or an SSH key");
//   }

//   if (data.maxUsers !== undefined) {
//     if (typeof data.maxUsers !== "number" || data.maxUsers < 0) {
//       throw new Error("Max users must be a positive number");
//     }
//   }

//   return true;
// };

export const fetchServersService = async () => {
  try {
    const response = await api.get(API_ROUTES.SERVER.GET_SERVERS);

    // Extract servers from the nested structure
    if (response?.data && Array.isArray(response.data.servers)) {
      return response.data.servers;
    }

    return response?.data?.servers || [];
  } catch (error) {
    console.error("❌ Error fetching servers:", {
      message: error?.response?.data?.message || error.message,
      status: error?.response?.status,
      data: error?.response?.data,
    });

    // Throw a user-friendly error to the store/component
    throw new Error(error?.response?.data?.message || "Failed to fetch servers");
  }
};


export const createServerService = async (serverData) => {
  try{
  const response = await api.post(API_ROUTES.SERVER.CREATE_SERVER, serverData);
  
  return response.data;
} catch(err){
  console.error("❌ Create Server Error:", {
      message: err.response?.data?.message,
      error: err.response?.data,
      status: err.response?.status,
      serverData
    });
    throw err;

}


};
export const updateServerService = async (id, serverData) => {
  try {
    const response = await api.put(API_ROUTES.SERVER.UPDATE_SERVER, {
      ...serverData,
      id,
    });

    return response.data?.data || response.data;
} catch (error) {
    console.error("Error updating server:", {
      message: error?.message,
      status: error?.response?.status,
      data: error?.response?.data
    });

    throw error; // keep throwing so caller can handle it
  }
};


export const deleteServerService = async (id) => {
  try {
    if (!id) throw new Error("Server ID is required for deletion");

    const response = await api.delete(API_ROUTES.SERVER.DELETE_SERVER(id));

    return response?.data || id;
  } catch (error) {
    console.error("❌ Error deleting server:", {
      message: error?.response?.data?.message || error.message,
      status: error?.response?.status,
      data: error?.response?.data,
    });

    // rethrow so the component/store can handle it
    throw new Error(error?.response?.data?.message || "Failed to delete server");
  }
};
