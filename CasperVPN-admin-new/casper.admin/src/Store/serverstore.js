import { create } from "zustand";
import {
  fetchServersService,
  createServerService,
  updateServerService,
  deleteServerService,
} from "../services/server-services";

export const useServerStore = create((set, get) => ({
  serverList: [],
  serverStats: [
    { label: "Total Servers", value: "0", icon: "Cloud" },
    { label: "Total Users", value: "0", icon: "People" },
    { label: "Average Load", value: "0%", icon: "Speed" },
  ],
  loading: false,
  error: null,

  // ✅ Fetch servers
  fetchServers: async () => {
    set({ loading: true, error: null });
    try {
      const servers = await fetchServersService();
      set({
        serverList: servers,
        serverStats: [
          { label: "Total Servers", value: String(servers.length), icon: "Cloud" },
          {
            label: "Total Users",
            value: servers.reduce((sum, s) => sum + (s.maxUsers || 0), 0).toString(),
            icon: "People",
          },
          { label: "Average Load", value: "0%", icon: "Speed" },
        ],
      });
    } catch (error) {
      set({ error: error.message });
    } finally {
      set({ loading: false });
    }
  },

  // ✅ Create server
  createServer: async (serverData) => {
    set({ loading: true, error: null });
    try {
      const newServer = await createServerService(serverData);
      // Normalize keys to PascalCase for table compatibility
      const formattedServer = {
        id: newServer.id,
        serverName: newServer.serverName,
        location: newServer.location,
        ipAddress: newServer.ipAddress,
        serverStatus: newServer.serverStatus || "Queued",
        users: newServer.users ?? 0,
        load: newServer.load ?? 0,
      };

      set((state) => {
        const updatedServerList = [...state.serverList, formattedServer];
        return {
          serverList: updatedServerList,
          serverStats: [
            { label: "Total Servers", value: String(updatedServerList.length), icon: "Cloud" },
            {
              label: "Total Users",
              value: updatedServerList.reduce((sum, s) => sum + (s.Users || 0), 0).toString(),
              icon: "People",
            },
            { label: "Average Load", value: "0%", icon: "Speed" },
          ],
        };
      });
    } catch (error) {
      set({ error: error.message });
    } finally {
      set({ loading: false });
    }
  },

  // ✅ Update server
  updateServer: async (id, body) => {
    set({ loading: true, error: null });
    try {
      const updatedServer = await updateServerService(id, body);
      set((state) => {
        const updatedList = state.serverList.map((server) =>
          server.id === updatedServer.id || server._id === updatedServer._id
            ? updatedServer
            : server
        );
        return {
          serverList: updatedList,
          serverStats: [
            { label: "Total Servers", value: String(updatedList.length), icon: "Cloud" },
            {
              label: "Total Users",
              value: updatedList.reduce((sum, s) => sum + (s.maxUsers || 0), 0).toString(),
              icon: "People",
            },
            { label: "Average Load", value: "0%", icon: "Speed" },
          ],
        };
      });
      return updatedServer;
    } catch (error) {
      set({ error: error.message });
    } finally {
      set({ loading: false });
    }
  },

  // ✅ Delete server
  deleteServer: async (id) => {
    set({ loading: true, error: null });
    try {
      await deleteServerService(id);
      set((state) => {
        const updatedList = state.serverList.filter(
          (server) => server.id !== id && server._id !== id
        );
        return {
          serverList: updatedList,
          serverStats: [
            { label: "Total Servers", value: String(updatedList.length), icon: "Cloud" },
            {
              label: "Total Users",
              value: updatedList.reduce((sum, s) => sum + (s.maxUsers || 0), 0).toString(),
              icon: "People",
            },
            { label: "Average Load", value: "0%", icon: "Speed" },
          ],
        };
      });
    } catch (error) {
      set({ error: error.message });
    } finally {
      set({ loading: false });
    }
  },
}));
