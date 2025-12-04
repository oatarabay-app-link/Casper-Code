import React, { useEffect, useMemo, useRef, useState } from 'react';
import Globe from 'globe.gl';
import { feature } from 'topojson-client';
import { useGeoStore } from '../../Store/geostore';

// Minimal mapping from world-atlas country names → ISO2/ISO3 for our dummy dataset
const NAME_TO_CODES = {
  'United States of America': { iso2: 'US', iso3: 'USA' },
  Canada: { iso2: 'CA', iso3: 'CAN' },
  Mexico: { iso2: 'MX', iso3: 'MEX' },
  Brazil: { iso2: 'BR', iso3: 'BRA' },
  Argentina: { iso2: 'AR', iso3: 'ARG' },
  Chile: { iso2: 'CL', iso3: 'CHL' },
  Colombia: { iso2: 'CO', iso3: 'COL' },
  'United Kingdom': { iso2: 'GB', iso3: 'GBR' },
  Ireland: { iso2: 'IE', iso3: 'IRL' },
  France: { iso2: 'FR', iso3: 'FRA' },
  Germany: { iso2: 'DE', iso3: 'DEU' },
  Spain: { iso2: 'ES', iso3: 'ESP' },
  Italy: { iso2: 'IT', iso3: 'ITA' },
  Netherlands: { iso2: 'NL', iso3: 'NLD' },
  Belgium: { iso2: 'BE', iso3: 'BEL' },
  Switzerland: { iso2: 'CH', iso3: 'CHE' },
  Austria: { iso2: 'AT', iso3: 'AUT' },
  Sweden: { iso2: 'SE', iso3: 'SWE' },
  Norway: { iso2: 'NO', iso3: 'NOR' },
  Finland: { iso2: 'FI', iso3: 'FIN' },
  Poland: { iso2: 'PL', iso3: 'POL' },
  Czechia: { iso2: 'CZ', iso3: 'CZE' },
  'Czech Republic': { iso2: 'CZ', iso3: 'CZE' },
  Romania: { iso2: 'RO', iso3: 'ROU' },
  Greece: { iso2: 'GR', iso3: 'GRC' },
  Portugal: { iso2: 'PT', iso3: 'PRT' },
  Russia: { iso2: 'RU', iso3: 'RUS' },
  'Russian Federation': { iso2: 'RU', iso3: 'RUS' },
  Turkey: { iso2: 'TR', iso3: 'TUR' },
  Türkiye: { iso2: 'TR', iso3: 'TUR' },
  'Saudi Arabia': { iso2: 'SA', iso3: 'SAU' },
  'United Arab Emirates': { iso2: 'AE', iso3: 'ARE' },
  Israel: { iso2: 'IL', iso3: 'ISR' },
  Egypt: { iso2: 'EG', iso3: 'EGY' },
  'South Africa': { iso2: 'ZA', iso3: 'ZAF' },
  Nigeria: { iso2: 'NG', iso3: 'NGA' },
  Kenya: { iso2: 'KE', iso3: 'KEN' },
  Ethiopia: { iso2: 'ET', iso3: 'ETH' },
  India: { iso2: 'IN', iso3: 'IND' },
  Pakistan: { iso2: 'PK', iso3: 'PAK' },
  Bangladesh: { iso2: 'BD', iso3: 'BGD' },
  China: { iso2: 'CN', iso3: 'CHN' },
  Japan: { iso2: 'JP', iso3: 'JPN' },
  'South Korea': { iso2: 'KR', iso3: 'KOR' },
  'Republic of Korea': { iso2: 'KR', iso3: 'KOR' },
  Vietnam: { iso2: 'VN', iso3: 'VNM' },
  'Viet Nam': { iso2: 'VN', iso3: 'VNM' },
  Thailand: { iso2: 'TH', iso3: 'THA' },
  Malaysia: { iso2: 'MY', iso3: 'MYS' },
  Singapore: { iso2: 'SG', iso3: 'SGP' },
  Indonesia: { iso2: 'ID', iso3: 'IDN' },
  Australia: { iso2: 'AU', iso3: 'AUS' },
  'New Zealand': { iso2: 'NZ', iso3: 'NZL' },
};

const getCodes = (name, props) => {
  const byProps = { iso2: props?.iso_a2, iso3: props?.iso_a3 };
  if (byProps.iso3 || byProps.iso2) return byProps;
  return NAME_TO_CODES[name] || { iso2: undefined, iso3: undefined };
};

const blueRamp = (t) => {
  const start = [99, 139, 255];
  const end = [37, 99, 235];
  const r = Math.round(start[0] * (1 - t) + end[0] * t);
  const g = Math.round(start[1] * (1 - t) + end[1] * t);
  const b = Math.round(start[2] * (1 - t) + end[2] * t);
  return `rgb(${r}, ${g}, ${b})`;
};

const Globe3D = ({ onSelect, style }) => {
  const containerRef = useRef(null);
  const globeRef = useRef(null);
  const { countryUsers, min, max: storeMax } = useGeoStore();
  const [countries, setCountries] = useState([]);
  const [size, setSize] = useState({ width: 0, height: 0 });

  useEffect(() => {
    let active = true;
    fetch('https://cdn.jsdelivr.net/npm/world-atlas@2/countries-110m.json')
      .then((r) => r.json())
      .then((topology) => {
        if (!active) return;
        const geojson = feature(topology, topology.objects.countries);
        setCountries(geojson.features || []);
      })
      .catch(() => setCountries([]));
    return () => { active = false; };
  }, []);

  const maxVal = useMemo(() => Math.max(...Object.values(countryUsers)), [countryUsers]);

  // Resize observer to fit globe to container
  useEffect(() => {
    if (!containerRef.current) return;
    const el = containerRef.current;
    const ro = new ResizeObserver((entries) => {
      const cr = entries[0]?.contentRect;
      if (!cr) return;
      setSize({ width: Math.floor(cr.width), height: Math.floor(cr.height) });
    });
    ro.observe(el);
    return () => ro.disconnect();
  }, []);

  useEffect(() => {
    if (!containerRef.current) return;
    if (!globeRef.current) {
      globeRef.current = Globe()(containerRef.current)
        .globeImageUrl('//unpkg.com/three-globe/example/img/earth-dark.jpg')
        .backgroundColor('rgba(0,0,0,0)')
        .showAtmosphere(true)
        .atmosphereColor('#3b82f6')
        .atmosphereAltitude(0.2);

      // enable user controls
      const ctrls = globeRef.current.controls();
      ctrls.enableZoom = true;
      ctrls.enablePan = true;
  ctrls.minDistance = 150;
  ctrls.maxDistance = 800;

      // set a reasonable default point of view to fit the globe
      globeRef.current.pointOfView({ altitude: 2.4 }, 0);
    }

    const g = globeRef.current;
  g.polygonsData(countries)
      .polygonCapColor((d) => {
        const name = d.properties?.name || '';
        const { iso3 } = getCodes(name, d.properties);
        const v = countryUsers[iso3];
        if (v == null) return '#1f2937';
  const t = Math.max(0, Math.min(1, (v - min) / (storeMax - min || 1)));
        return blueRamp(t);
      })
      .polygonAltitude(0.006)
      .polygonsTransitionDuration(300)
      .polygonSideColor(() => 'rgba(3, 7, 18, 0.6)')
      .polygonStrokeColor(() => '#111827')
      .polygonLabel((d) => {
        const name = d.properties?.name || '';
        const { iso3 } = getCodes(name, d.properties);
        const v = countryUsers[iso3];
        return `${name}: ${v != null ? v.toLocaleString() : 'No data'}`;
      })
      .onPolygonClick((d) => {
        if (!onSelect) return;
        const name = d.properties?.name || '';
        const { iso3, iso2 } = getCodes(name, d.properties);
        const v = countryUsers[iso3];
        onSelect({ iso3, iso2, name, val: v });
      });
  }, [countries, countryUsers, storeMax, min]);

  // Apply width/height when container resizes
  useEffect(() => {
    if (!globeRef.current || !size.width || !size.height) return;
    globeRef.current.width(size.width).height(size.height);
  }, [size]);

  return (
    <div className="relative w-full" style={{ height: '60vh', maxHeight: '820px', ...(style || {}) }}>
      <div
        ref={containerRef}
        className="w-full h-full"
        style={{ maxWidth: 'clamp(420px, 48vw, 780px)', aspectRatio: '1 / 1', margin: '0 auto' }}
      />
      <div className="absolute bottom-3 right-3 text-xs text-gray-300 bg-gray-900/70 border border-gray-700 rounded px-2 py-1">Drag to rotate, scroll to zoom</div>
    </div>
  );
};

export default Globe3D;
