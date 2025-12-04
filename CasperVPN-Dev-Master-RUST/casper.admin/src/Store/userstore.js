import { create } from 'zustand';

export const useUserStore = create(() => ({
    userStats: {
        total: 5,
        active: 4,
        premium: 3,
        usage: '332GB',
    },

    users: [
        {
            email: 'alice@example.com',
            plan: 'Premium',
            status: 'Active',
            server: 'US-East',
            joined: '2024-06-01',
            usage: '120GB',
        },
        {
            email: 'bob@example.com',
            plan: 'Basic',
            status: 'Active',
            server: 'EU-West',
            joined: '2024-05-15',
            usage: '80GB',
        },
        {
            email: 'carol@example.com',
            plan: 'Premium',
            status: 'Inactive',
            server: 'Asia-SG',
            joined: '2024-04-20',
            usage: '60GB',
        },
        {
            email: 'dave@example.com',
            plan: 'Premium',
            status: 'Active',
            server: 'US-West',
            joined: '2024-03-10',
            usage: '50GB',
        },
        {
            email: 'eve@example.com',
            plan: 'Basic',
            status: 'Active',
            server: 'EU-Central',
            joined: '2024-02-05',
            usage: '22GB',
        },
    ],
    searchQuery: '',
    selectedPlan: 'All',
    setSearchQuery: (query) => set({ searchQuery: query }),
    setSelectedPlan: (plan) => set({ selectedPlan: plan }),
}));
