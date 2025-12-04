import { create } from 'zustand';

// Dummy per-country user counts using ISO3 codes
const countryUsers = {
  USA: 1240,
  CAN: 320,
  MEX: 280,
  BRA: 410,
  ARG: 160,
  CHL: 90,
  COL: 140,
  GBR: 450,
  IRL: 70,
  FRA: 540,
  DEU: 600,
  ESP: 380,
  ITA: 420,
  NLD: 130,
  BEL: 95,
  CHE: 110,
  AUT: 85,
  SWE: 150,
  NOR: 80,
  FIN: 75,
  POL: 170,
  CZE: 60,
  ROU: 65,
  GRC: 55,
  PRT: 50,
  RUS: 500,
  TUR: 230,
  SAU: 210,
  ARE: 120,
  ISR: 90,
  EGY: 140,
  ZAF: 120,
  NGA: 160,
  KEN: 80,
  ETH: 70,
  IND: 980,
  PAK: 260,
  BGD: 190,
  CHN: 1100,
  JPN: 520,
  KOR: 340,
  VNM: 220,
  THA: 200,
  MYS: 180,
  SGP: 240,
  IDN: 360,
  AUS: 230,
  NZL: 60,
  JOR: 40,
  LBN: 35,
  QAT: 22,
  KWT: 28,
  OMN: 18,
};

export const useGeoStore = create(() => ({
  countryUsers,
  min: Object.values(countryUsers).reduce((m, v) => Math.min(m, v), Infinity),
  max: Object.values(countryUsers).reduce((m, v) => Math.max(m, v), -Infinity),
  details: {
    USA: {
      topServers: [
        { name: 'US-East-1', city: 'New York', load: '65%' },
        { name: 'US-West-1', city: 'Los Angeles', load: '48%' },
        { name: 'US-Central-1', city: 'Dallas', load: '55%' },
      ],
      topUsers: [
        { segment: 'Premium', count: 520 },
        { segment: 'Standard', count: 430 },
        { segment: 'Trial', count: 290 },
      ],
    },
    DEU: {
      topServers: [
        { name: 'EU-Central-1', city: 'Frankfurt', load: '58%' },
        { name: 'EU-Central-2', city: 'Berlin', load: '44%' },
      ],
      topUsers: [
        { segment: 'Premium', count: 220 },
        { segment: 'Standard', count: 280 },
      ],
    },
    IND: {
      topServers: [
        { name: 'IN-West-1', city: 'Mumbai', load: '72%' },
        { name: 'IN-South-1', city: 'Bangalore', load: '61%' },
      ],
      topUsers: [
        { segment: 'Premium', count: 360 },
        { segment: 'Standard', count: 420 },
        { segment: 'Trial', count: 200 },
      ],
    },
    CHN: {
      topServers: [
        { name: 'AP-East-1', city: 'Hong Kong', load: '69%' },
        { name: 'AP-North-1', city: 'Tokyo (proxy)', load: '54%' },
      ],
      topUsers: [
        { segment: 'Premium', count: 420 },
        { segment: 'Standard', count: 510 },
      ],
    },
    BRA: {
      topServers: [
        { name: 'SA-East-1', city: 'São Paulo', load: '57%' },
      ],
      topUsers: [
        { segment: 'Premium', count: 120 },
        { segment: 'Standard', count: 200 },
      ],
    },
    GBR: {
      topServers: [
        { name: 'UK-South-1', city: 'London', load: '52%' },
      ],
      topUsers: [
        { segment: 'Premium', count: 180 },
        { segment: 'Standard', count: 210 },
      ],
    },
    AUS: {
      topServers: [
        { name: 'AU-East-1', city: 'Sydney', load: '49%' },
      ],
      topUsers: [
        { segment: 'Premium', count: 90 },
        { segment: 'Standard', count: 140 },
      ],
    },
  },
}));
