import { create } from 'zustand';

const TB = 1024 ** 4;

const usersSeed = [
    {
        id: 'user-alice',
        name: 'Alice Robertson',
        email: 'alice@example.com',
        phone: '+1 415-555-1010',
        country: 'United States',
        kycStatus: 'Verified',
        kycTier: 'Level 2',
        lastLogin: '2025-10-08T13:42:00Z',
        mfaEnabled: true,
        blocked: false,
        totalDataBytes: 2.6 * TB,
        hasActiveSubscription: true,
        lastPasswordResetAt: '2025-07-12T08:45:00Z',
        mfaResetAt: null,
        subscriptions: [
            {
                id: 'sub-alice-pro',
                planName: 'Pro Shield',
                status: 'Active',
                startedAt: '2024-11-01',
                renewsOn: '2025-11-01',
                link: '#subscription-sub-alice-pro',
            },
        ],
        devices: [
            { id: 'device-alice-mac', name: 'MacBook Pro', type: 'Desktop', lastSeen: '2025-10-08T12:58:00Z', ip: '104.193.18.24' },
            { id: 'device-alice-ios', name: 'iPhone 15', type: 'Mobile', lastSeen: '2025-10-07T21:14:00Z', ip: '198.21.55.12' },
        ],
        recentConnections: [
            { id: 'conn-alice-1', location: 'Los Angeles, US', ip: '185.45.32.10', connectedAt: '2025-10-08T11:02:00Z', durationMinutes: 48 },
            { id: 'conn-alice-2', location: 'Tokyo, JP', ip: '103.54.22.90', connectedAt: '2025-10-07T23:18:00Z', durationMinutes: 22 },
        ],
    },
    {
        id: 'user-bob',
        name: 'Bob Hernandez',
        email: 'bob@example.com',
        phone: '+44 20 7946 0700',
        country: 'United Kingdom',
        kycStatus: 'Pending',
        kycTier: 'Level 1',
        lastLogin: '2025-10-05T09:30:00Z',
        mfaEnabled: false,
        blocked: false,
        totalDataBytes: 1.1 * TB,
        hasActiveSubscription: false,
        lastPasswordResetAt: '2025-04-02T17:12:00Z',
        mfaResetAt: '2025-08-18T10:02:00Z',
        subscriptions: [
            {
                id: 'sub-bob-lite',
                planName: 'Secure Lite',
                status: 'Expired',
                startedAt: '2024-08-20',
                renewsOn: '2025-08-20',
                link: '#subscription-sub-bob-lite',
            },
        ],
        devices: [
            { id: 'device-bob-laptop', name: 'Surface Laptop', type: 'Laptop', lastSeen: '2025-09-30T16:40:00Z', ip: '91.208.77.14' },
        ],
        recentConnections: [
            { id: 'conn-bob-1', location: 'London, UK', ip: '77.115.66.20', connectedAt: '2025-09-28T21:45:00Z', durationMinutes: 13 },
        ],
    },
    {
        id: 'user-carol',
        name: 'Carol Wong',
        email: 'carol@example.com',
        phone: '+65 3163 2500',
        country: 'Singapore',
        kycStatus: 'Verified',
        kycTier: 'Level 3',
        lastLogin: '2025-10-08T02:15:00Z',
        mfaEnabled: true,
        blocked: true,
        totalDataBytes: 3.4 * TB,
        hasActiveSubscription: true,
        lastPasswordResetAt: '2025-09-10T06:12:00Z',
        mfaResetAt: null,
        subscriptions: [
            {
                id: 'sub-carol-enterprise',
                planName: 'Enterprise Vanguard',
                status: 'Suspended',
                startedAt: '2024-04-15',
                renewsOn: '2025-10-15',
                link: '#subscription-sub-carol-enterprise',
            },
            {
                id: 'sub-carol-lite',
                planName: 'Secure Lite',
                status: 'Active',
                startedAt: '2025-01-01',
                renewsOn: '2025-11-01',
                link: '#subscription-sub-carol-lite',
            },
        ],
        devices: [
            { id: 'device-carol-work', name: 'ThinkPad X1', type: 'Laptop', lastSeen: '2025-10-08T01:55:00Z', ip: '203.116.12.45' },
            { id: 'device-carol-home', name: 'Home Router', type: 'Router', lastSeen: '2025-10-06T19:26:00Z', ip: '14.201.88.33' },
        ],
        recentConnections: [
            { id: 'conn-carol-1', location: 'Singapore, SG', ip: '203.116.12.45', connectedAt: '2025-10-08T01:40:00Z', durationMinutes: 67 },
            { id: 'conn-carol-2', location: 'Sydney, AU', ip: '101.167.55.70', connectedAt: '2025-10-07T13:22:00Z', durationMinutes: 54 },
        ],
    },
    {
        id: 'user-dave',
        name: 'Dave Osei',
        email: 'dave@example.com',
        phone: '+233 302 555 021',
        country: 'Ghana',
        kycStatus: 'Manual Review',
        kycTier: 'Level 1',
        lastLogin: '2025-09-30T18:10:00Z',
        mfaEnabled: true,
        blocked: false,
        totalDataBytes: 0.6 * TB,
        hasActiveSubscription: true,
        lastPasswordResetAt: '2025-05-22T15:30:00Z',
        mfaResetAt: '2025-07-01T10:00:00Z',
        subscriptions: [
            {
                id: 'sub-dave-pro',
                planName: 'Pro Shield',
                status: 'Active',
                startedAt: '2024-12-04',
                renewsOn: '2025-12-04',
                link: '#subscription-sub-dave-pro',
            },
        ],
        devices: [
            { id: 'device-dave-android', name: 'Pixel 9', type: 'Mobile', lastSeen: '2025-10-02T07:22:00Z', ip: '41.242.33.50' },
        ],
        recentConnections: [
            { id: 'conn-dave-1', location: 'Accra, GH', ip: '41.242.33.50', connectedAt: '2025-10-02T06:55:00Z', durationMinutes: 34 },
        ],
    },
];

const computeStats = (users) => {
    const total = users.length;
    const verified = users.filter((user) => user.kycStatus === 'Verified').length;
    const mfaSecured = users.filter((user) => user.mfaEnabled).length;
    const blocked = users.filter((user) => user.blocked).length;
    const activeSubscriptions = users.filter((user) => user.hasActiveSubscription).length;

    return {
        total,
        verified,
        mfaSecured,
        blocked,
        activeSubscriptions,
    };
};

const formatCsvValue = (value) => {
    const stringValue = value ?? '';
    if (/[",\n]/.test(String(stringValue))) {
        return `"${String(stringValue).replace(/"/g, '""')}"`;
    }
    return String(stringValue);
};

export const useUserStore = create((set, get) => ({
    users: usersSeed,
    userStats: computeStats(usersSeed),
    searchQuery: '',

    setSearchQuery: (query) => set({ searchQuery: query }),

    downloadUsersCsv: () => {
        const { users } = get();
        const headers = [
            'Name',
            'Email',
            'Phone',
            'Country',
            'KYC Status',
            'KYC Tier',
            'Last Login',
            'MFA Enabled',
            'Blocked',
            'Total Data (TB)',
            'Has Active Subscription',
        ];

        const rows = users.map((user) => [
            user.name,
            user.email,
            user.phone,
            user.country,
            user.kycStatus,
            user.kycTier,
            user.lastLogin,
            user.mfaEnabled ? 'Yes' : 'No',
            user.blocked ? 'Yes' : 'No',
            (user.totalDataBytes / TB).toFixed(2),
            user.hasActiveSubscription ? 'Yes' : 'No',
        ]);

        const csvContent = `\uFEFF${[headers.join(','), ...rows.map((row) => row.map(formatCsvValue).join(','))].join('\n')}`;
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const downloadLink = document.createElement('a');
        const timestamp = new Date().toISOString().slice(0, 10);
        downloadLink.href = URL.createObjectURL(blob);
        downloadLink.download = `caspervpn-users-${timestamp}.csv`;
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
        URL.revokeObjectURL(downloadLink.href);
    },

    exportUserData: (id) => {
        const user = get().users.find((candidate) => candidate.id === id);
        if (!user) return;

        const payload = {
            id: user.id,
            identity: {
                name: user.name,
                email: user.email,
                phone: user.phone,
                country: user.country,
            },
            compliance: {
                kycStatus: user.kycStatus,
                kycTier: user.kycTier,
            },
            security: {
                mfaEnabled: user.mfaEnabled,
                blocked: user.blocked,
                lastPasswordResetAt: user.lastPasswordResetAt,
                mfaResetAt: user.mfaResetAt,
            },
            usage: {
                totalDataBytes: user.totalDataBytes,
            },
            subscriptions: user.subscriptions,
            devices: user.devices,
            recentConnections: user.recentConnections,
        };

        const blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json' });
        const downloadLink = document.createElement('a');
        downloadLink.href = URL.createObjectURL(blob);
        downloadLink.download = `${user.id}-profile.json`;
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
        URL.revokeObjectURL(downloadLink.href);
    },

    resetUserPassword: (id) =>
        set((state) => ({
            users: state.users.map((user) =>
                user.id === id
                    ? {
                            ...user,
                            lastPasswordResetAt: new Date().toISOString(),
                        }
                    : user,
            ),
        })),

    resetUserMfa: (id) =>
        set((state) => ({
            users: state.users.map((user) =>
                user.id === id
                    ? {
                            ...user,
                            mfaEnabled: false,
                            mfaResetAt: new Date().toISOString(),
                        }
                    : user,
            ),
        })),

    toggleBlockUser: (id) =>
        set((state) => {
            const updatedUsers = state.users.map((user) =>
                user.id === id
                    ? {
                            ...user,
                            blocked: !user.blocked,
                        }
                    : user,
            );

            return {
                users: updatedUsers,
                userStats: computeStats(updatedUsers),
            };
        }),

    deleteUser: (id) =>
        set((state) => {
            const filtered = state.users.filter((user) => user.id !== id);
            return {
                users: filtered,
                userStats: computeStats(filtered),
            };
        }),

    markSubscriptionActivity: (id, hasActive) =>
        set((state) => {
            const updatedUsers = state.users.map((user) =>
                user.id === id
                    ? {
                            ...user,
                            hasActiveSubscription: hasActive,
                        }
                    : user,
            );

            return {
                users: updatedUsers,
                userStats: computeStats(updatedUsers),
            };
        }),
}));
