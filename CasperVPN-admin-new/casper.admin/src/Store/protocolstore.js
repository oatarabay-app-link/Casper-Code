import { create } from 'zustand';


// Helper to create URL-safe ids from names
const slugify = (s) => s.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');

// Dummy counts + state for users/servers using each protocol
export const ALLOWED_PROTOCOLS = ['OpenVPN', 'WireGuard', 'IKEv2', 'L2TP/IPsec'];
const allowedSet = new Set(ALLOWED_PROTOCOLS.map((n) => n.toLowerCase()));

export const useProtocolStore = create((set, get) => ({
  protocols: [
    { id: 'openvpn', name: 'OpenVPN', usersUsing: 2200, serversUsing: 18, isSuspended: false },
    { id: 'wireguard', name: 'WireGuard', usersUsing: 1300, serversUsing: 14, isSuspended: false },
    { id: 'ikev2', name: 'IKEv2', usersUsing: 180, serversUsing: 5, isSuspended: false },
    { id: 'l2tp', name: 'L2TP/IPsec', usersUsing: 60, serversUsing: 2, isSuspended: false },
  ],

  addProtocol: (name) =>
    set((state) => {
      const raw = (name || '').trim();
      const norm = raw.toLowerCase();
      if (!raw || !allowedSet.has(norm)) {
        // Not allowed, no change
        return { protocols: state.protocols };
      }
      // Prevent duplicates by name (case-insensitive)
      if (state.protocols.some((p) => p.name.toLowerCase() === norm)) {
        return { protocols: state.protocols };
      }
      const baseId = slugify(raw);
      const existing = new Set(state.protocols.map((p) => p.id));
      let id = baseId || `protocol-${state.protocols.length + 1}`;
      let i = 1;
      while (existing.has(id)) id = `${baseId}-${i++}`;
      const newProtocol = { id, name: raw, usersUsing: 0, serversUsing: 0, isSuspended: false };
      return { protocols: [...state.protocols, newProtocol] };
    }),

  toggleSuspend: (id) =>
    set((state) => ({
      protocols: state.protocols.map((p) => (p.id === id ? { ...p, isSuspended: !p.isSuspended } : p)),
    })),

  updateCounts: (id, counts) =>
    set((state) => ({
      protocols: state.protocols.map((p) => (p.id === id ? { ...p, ...counts } : p)),
    })),

  removeProtocol: (id) =>
    set((state) => ({ protocols: state.protocols.filter((p) => p.id !== id) })),
}));
