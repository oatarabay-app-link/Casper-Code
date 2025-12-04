import React, { useEffect, useMemo, useRef, useState } from 'react';
import { useGeoStore } from '../../Store/geostore';
import { useUiStore } from '../../Store/uiStore';
import * as topojson from 'topojson-client';
import { geoPath, geoEqualEarth } from 'd3-geo';
import { scaleQuantile, scaleSequential } from 'd3-scale';
import { select } from 'd3-selection';
import { zoom as d3Zoom, zoomIdentity } from 'd3-zoom';
import Globe3D from './Globe3D';

// Minimal mapping from world-atlas country names → ISO2/ISO3 for our dummy dataset
const NAME_TO_CODES = {
  'United States of America': { iso2: 'US', iso3: 'USA' },
  'Canada': { iso2: 'CA', iso3: 'CAN' },
  'Mexico': { iso2: 'MX', iso3: 'MEX' },
  'Brazil': { iso2: 'BR', iso3: 'BRA' },
  'Argentina': { iso2: 'AR', iso3: 'ARG' },
  'Chile': { iso2: 'CL', iso3: 'CHL' },
  'Colombia': { iso2: 'CO', iso3: 'COL' },
  'United Kingdom': { iso2: 'GB', iso3: 'GBR' },
  'Ireland': { iso2: 'IE', iso3: 'IRL' },
  'France': { iso2: 'FR', iso3: 'FRA' },
  'Germany': { iso2: 'DE', iso3: 'DEU' },
  'Spain': { iso2: 'ES', iso3: 'ESP' },
  'Italy': { iso2: 'IT', iso3: 'ITA' },
  'Netherlands': { iso2: 'NL', iso3: 'NLD' },
  'Belgium': { iso2: 'BE', iso3: 'BEL' },
  'Switzerland': { iso2: 'CH', iso3: 'CHE' },
  'Austria': { iso2: 'AT', iso3: 'AUT' },
  'Sweden': { iso2: 'SE', iso3: 'SWE' },
  'Norway': { iso2: 'NO', iso3: 'NOR' },
  'Finland': { iso2: 'FI', iso3: 'FIN' },
  'Poland': { iso2: 'PL', iso3: 'POL' },
  'Czechia': { iso2: 'CZ', iso3: 'CZE' },
  'Czech Republic': { iso2: 'CZ', iso3: 'CZE' },
  'Romania': { iso2: 'RO', iso3: 'ROU' },
  'Greece': { iso2: 'GR', iso3: 'GRC' },
  'Portugal': { iso2: 'PT', iso3: 'PRT' },
  'Russia': { iso2: 'RU', iso3: 'RUS' },
  'Russian Federation': { iso2: 'RU', iso3: 'RUS' },
  'Turkey': { iso2: 'TR', iso3: 'TUR' },
  'Türkiye': { iso2: 'TR', iso3: 'TUR' },
  'Saudi Arabia': { iso2: 'SA', iso3: 'SAU' },
  'United Arab Emirates': { iso2: 'AE', iso3: 'ARE' },
  'Israel': { iso2: 'IL', iso3: 'ISR' },
  'Egypt': { iso2: 'EG', iso3: 'EGY' },
  'South Africa': { iso2: 'ZA', iso3: 'ZAF' },
  'Nigeria': { iso2: 'NG', iso3: 'NGA' },
  'Kenya': { iso2: 'KE', iso3: 'KEN' },
  'Ethiopia': { iso2: 'ET', iso3: 'ETH' },
  'India': { iso2: 'IN', iso3: 'IND' },
  'Pakistan': { iso2: 'PK', iso3: 'PAK' },
  'Bangladesh': { iso2: 'BD', iso3: 'BGD' },
  'China': { iso2: 'CN', iso3: 'CHN' },
  'Japan': { iso2: 'JP', iso3: 'JPN' },
  'South Korea': { iso2: 'KR', iso3: 'KOR' },
  'Republic of Korea': { iso2: 'KR', iso3: 'KOR' },
  'Vietnam': { iso2: 'VN', iso3: 'VNM' },
  'Viet Nam': { iso2: 'VN', iso3: 'VNM' },
  'Thailand': { iso2: 'TH', iso3: 'THA' },
  'Malaysia': { iso2: 'MY', iso3: 'MYS' },
  'Singapore': { iso2: 'SG', iso3: 'SGP' },
  'Indonesia': { iso2: 'ID', iso3: 'IDN' },
  'Australia': { iso2: 'AU', iso3: 'AUS' },
  'New Zealand': { iso2: 'NZ', iso3: 'NZL' },
};

const getCodes = (name, props) => {
  const byProps = {
    iso2: props?.iso_a2,
    iso3: props?.iso_a3,
  };
  if (byProps.iso3 || byProps.iso2) return byProps;
  return NAME_TO_CODES[name] || { iso2: undefined, iso3: undefined };
};
// Using a lightweight world-topojson from a CDN
const GEO_URL = 'https://cdn.jsdelivr.net/npm/world-atlas@2/countries-110m.json';

const blueRamp = (t) => {
  const start = [37, 99, 235]; // #2563eb
  const end = [30, 64, 175]; // #1d4ed8
  const r = Math.round(start[0] * (1 - t) + end[0] * t);
  const g = Math.round(start[1] * (1 - t) + end[1] * t);
  const b = Math.round(start[2] * (1 - t) + end[2] * t);
  return `rgb(${r}, ${g}, ${b})`;
};

const GeoMap = () => {
  const { countryUsers, min, max, details } = useGeoStore();
  const total = useMemo(() => Object.values(countryUsers).reduce((a, b) => a + b, 0), [countryUsers]);
  // Sidebar/UI state for responsive shrinking when sidebar overlays
  const isSidebarOpen = useUiStore((s) => s.isSidebarOpen);
  const sidebarCollapsed = useUiStore((s) => s.sidebarCollapsed);
  const [sidebarWidthPx, setSidebarWidthPx] = useState(0);
  useEffect(() => {
    const readSidebarWidth = () => {
      const el = document.getElementById('app-sidebar');
      if (el) {
        setSidebarWidthPx(el.offsetWidth || 0);
      } else {
        // fallback to typical widths if element not ready
        setSidebarWidthPx(isSidebarOpen ? 256 : (sidebarCollapsed ? 64 : 0));
      }
    };
    readSidebarWidth();
    const ro = new ResizeObserver(() => readSidebarWidth());
    const el = document.getElementById('app-sidebar');
    if (el) ro.observe(el);
    // also update on window resize
    const onW = () => readSidebarWidth();
    window.addEventListener('resize', onW);
    return () => {
      ro.disconnect();
      window.removeEventListener('resize', onW);
    };
  }, [isSidebarOpen, sidebarCollapsed]);
  const [vw, setVw] = useState(typeof window !== 'undefined' ? window.innerWidth : 1200);
  useEffect(() => {
    const onR = () => setVw(window.innerWidth);
    window.addEventListener('resize', onR);
    return () => window.removeEventListener('resize', onR);
  }, []);
  const isLgAndUp = vw >= 1024; // Tailwind's lg breakpoint
  // We only need to shrink explicitly on small screens where sidebar overlays content
  const shouldShrinkForSidebar = isSidebarOpen && !isLgAndUp;

  const [features, setFeatures] = useState([]);
  const [hover, setHover] = useState(null);
  const [selected, setSelected] = useState(null);
  const [scaleMode, setScaleMode] = useState('sequential'); // 'sequential' | 'quantile'
  const [viewMode, setViewMode] = useState('2d'); // '2d' | '3d'
  const [query, setQuery] = useState('');
  const svgRef = useRef(null);
  const gRef = useRef(null);
  const zoomBehaviorRef = useRef(null);

  useEffect(() => {
    let active = true;
    fetch(GEO_URL)
      .then((r) => r.json())
      .then((world) => {
        if (!active) return;
        const countries = topojson.feature(world, world.objects.countries);
        setFeatures(countries.features || []);
      })
      .catch(() => setFeatures([]));
    return () => {
      active = false;
    };
  }, []);

  const width = 900;
  const height = 420;
  const projection = geoEqualEarth().fitSize([width, height], { type: 'Sphere' });
  const path = geoPath(projection);
  const values = useMemo(() => Object.values(countryUsers).filter((v) => typeof v === 'number'), [countryUsers]);
  const quantile = useMemo(() => scaleQuantile(values, ['#dbeafe','#bfdbfe','#93c5fd','#60a5fa','#3b82f6','#2563eb','#1d4ed8']), [values]);
  const sequential = useMemo(() => (v) => (v == null ? '#1f2937' : blueRamp((v - min) / (max - min || 1))), [min, max]);
  const getFill = (v) => {
    if (v == null) return '#1f2937';
    return scaleMode === 'quantile' ? quantile(v) : sequential(v);
  };

  // Zoom & pan for 2D map
  useEffect(() => {
    if (!svgRef.current || !gRef.current) return;
    const svg = select(svgRef.current);
    const g = select(gRef.current);
    const z = d3Zoom()
      .scaleExtent([1, 8])
      .on('zoom', (event) => {
        g.attr('transform', event.transform);
      });
    svg.call(z);
    zoomBehaviorRef.current = z;
    // Reset zoom on mode change to 2D
    svg.on('dblclick.zoom', null); // optional: disable dblclick to zoom
    return () => {
      svg.on('.zoom', null);
    };
  }, [viewMode]);

  // Programmatic zoom-to feature
  const zoomToFeature = (f) => {
    if (!svgRef.current || !gRef.current || !f) return;
    const svg = select(svgRef.current);
    const g = select(gRef.current);
    const bounds = path.bounds(f);
    const dx = bounds[1][0] - bounds[0][0];
    const dy = bounds[1][1] - bounds[0][1];
    const x = (bounds[0][0] + bounds[1][0]) / 2;
    const y = (bounds[0][1] + bounds[1][1]) / 2;
    const scale = Math.max(1, Math.min(8, 0.9 / Math.max(dx / width, dy / height)));
    const translate = [width / 2 - scale * x, height / 2 - scale * y];
    const t = zoomIdentity.translate(translate[0], translate[1]).scale(scale);
    svg.transition().duration(700).call(d3Zoom().transform, t);
  };

  const onSearch = (e) => {
    e.preventDefault();
    const q = query.trim().toLowerCase();
    if (!q) return;
    const f = features.find((f) => (f.properties?.name || '').toLowerCase().includes(q));
    if (f) {
      const iso3 = f.properties?.iso_a3 || f.id;
      const iso2 = f.properties?.iso_a2 || '';
      const val = countryUsers[iso3];
      setSelected({ iso3, iso2, name: f.properties?.name || iso3, val });
      zoomToFeature(f);
    }
  };

  const resetView = () => {
    if (!svgRef.current || !zoomBehaviorRef.current) return;
    const svg = select(svgRef.current);
    svg.transition().duration(500).call(zoomBehaviorRef.current.transform, zoomIdentity);
  };

  return (
  <div className="min-h-screen bg-gray-900 space-y-6 overflow-x-hidden">
  <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold text-white">Geo Map</h1>
        <div className="text-gray-300 text-sm">Total users mapped: <span className="text-white font-semibold">{total.toLocaleString()}</span></div>
      </div>

  <div className="bg-gray-800 border border-gray-700 rounded-lg p-4 overflow-hidden"
    style={shouldShrinkForSidebar ? { width: `calc(100% - ${sidebarWidthPx}px)`, marginLeft: `${sidebarWidthPx}px`, transition: 'width 300ms ease, margin 300ms ease' } : undefined}>
        <div className="w-full overflow-x-auto relative">
          {/* Controls */}
          <div className="absolute right-4 top-4 z-10 bg-gray-900/70 backdrop-blur rounded border border-gray-700 p-2 flex items-center gap-2 text-xs text-gray-300">
            <span>View:</span>
            <button onClick={() => setViewMode('2d')} className={`px-2 py-1 rounded ${viewMode==='2d' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-300'}`}>2D</button>
            <button onClick={() => setViewMode('3d')} className={`px-2 py-1 rounded ${viewMode==='3d' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-300'}`}>3D</button>
            {viewMode === '2d' && (
              <>
                <span className="ml-3">Scale:</span>
                <button onClick={() => setScaleMode('sequential')} className={`px-2 py-1 rounded ${scaleMode==='sequential' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-300'}`}>Sequential</button>
                <button onClick={() => setScaleMode('quantile')} className={`px-2 py-1 rounded ${scaleMode==='quantile' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-300'}`}>Quantile</button>
              </>
            )}
          </div>

          {/* Search */}
          <form onSubmit={onSearch} className="absolute left-4 top-4 z-10 bg-gray-900/70 backdrop-blur rounded border border-gray-700 p-2 flex items-center gap-2 text-xs text-gray-300">
            <input value={query} onChange={(e) => setQuery(e.target.value)} placeholder="Search country" className="bg-gray-800 border border-gray-700 rounded px-2 py-1 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-600" />
            <button type="submit" className="px-2 py-1 rounded bg-blue-600 text-white">Go</button>
          </form>

          {viewMode === '2d' ? (
          <svg ref={svgRef} viewBox={`0 0 ${width} ${height}`} className="w-full h-[420px]"
               style={shouldShrinkForSidebar ? { width: `calc(100% - ${sidebarWidthPx}px)`, marginLeft: `${sidebarWidthPx}px`, transition: 'width 300ms ease, margin 300ms ease' } : undefined}>
            <rect width={width} height={height} fill="#0b1220" />
            <g ref={gRef}>
              {features.map((f) => {
                const name = f.properties?.name || '';
                const codes = getCodes(name, f.properties);
                const iso3 = codes.iso3 || f.properties?.iso_a3 || f.id;
                const iso2 = codes.iso2 || f.properties?.iso_a2 || '';
                const val = countryUsers[iso3];
                const isSel = selected && selected.iso3 === iso3;
                return (
                  <path
                    key={iso3}
                    d={path(f)}
                    fill={getFill(val)}
                    stroke={isSel ? '#93c5fd' : '#111827'}
                    strokeWidth={isSel ? 1.2 : 0.5}
                    onMouseEnter={(e) => {
                      setHover({ x: e.clientX, y: e.clientY, name, iso2, val });
                    }}
                    onMouseMove={(e) => {
                      setHover((h) => (h ? { ...h, x: e.clientX, y: e.clientY } : h));
                    }}
                    onMouseLeave={() => setHover(null)}
                    onClick={() => setSelected({ iso3, iso2, name, val })}
                  >
                    <title>{`${name || iso3}: ${val != null ? val.toLocaleString() : 'No data'}`}</title>
                  </path>
                );
              })}
            </g>
          </svg>
          ) : (
            <div className="w-full flex items-center justify-center">
              <div className="w-full" style={{ maxWidth: 'clamp(420px, 48vw, 780px)', transition: 'max-width 300ms ease' }}>
                <Globe3D onSelect={(sel) => setSelected(sel)} />
              </div>
            </div>
          )}

          {/* Hover tooltip */}
          {hover && (
            <div
              className="pointer-events-none fixed z-50 bg-gray-900/90 text-gray-100 text-xs rounded shadow border border-gray-700 px-2 py-1"
              style={{ left: hover.x + 12, top: hover.y + 12 }}
            >
              <div className="flex items-center gap-2">
                {hover.iso2 && (
                  <img
                    alt="flag"
                    width="18"
                    height="12"
                    className="rounded-sm"
                    src={`https://flagcdn.com/24x18/${hover.iso2.toLowerCase()}.png`}
                    onError={(e) => { e.currentTarget.style.display = 'none'; }}
                  />
                )}
                <div className="font-semibold">{hover.name}</div>
              </div>
              <div>{hover.val != null ? hover.val.toLocaleString() : 'No data'} users</div>
            </div>
          )}
        </div>

        <div className="mt-4 flex items-center gap-3 text-xs text-gray-400">
          <span>No data</span>
          <div className="h-3 w-8 rounded" style={{ background: '#1f2937' }} />
          <span className="ml-4">Low</span>
          <div className="h-3 w-24 rounded" style={{ background: 'linear-gradient(90deg, #2563eb 0%, #1d4ed8 100%)' }} />
          <span>High</span>
        </div>
        {/* Quantile legend if enabled */}
        {scaleMode === 'quantile' && viewMode === '2d' && (
          <div className="mt-4 flex items-center gap-2 text-xs text-gray-400">
            <span>Low</span>
            {quantile.range().map((c, i) => (
              <div key={i} className="h-3 w-8 rounded" style={{ background: c }} />
            ))}
            <span>High</span>
          </div>
        )}
        {viewMode === '2d' && (
          <div className="mt-3">
            <button onClick={resetView} className="px-3 py-1.5 text-xs rounded bg-gray-700 text-gray-200 border border-gray-600 hover:bg-gray-600">Reset view</button>
          </div>
        )}
      </div>

      {/* Drilldown panel */}
      {selected && (
        <div className="bg-gray-800 border border-gray-700 rounded-lg p-4">
          <div className="flex items-center justify-between mb-3">
            <div>
              <h3 className="text-xl text-white font-semibold">{selected.name}</h3>
              <p className="text-gray-400 text-sm">{selected.val?.toLocaleString() || 'No'} users</p>
            </div>
            <button onClick={() => setSelected(null)} className="px-3 py-1.5 rounded text-sm bg-gray-700 text-gray-200 border border-gray-600 hover:bg-gray-600">Close</button>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="bg-gray-900/50 border border-gray-700 rounded p-3">
              <h4 className="text-white font-semibold mb-2">Top Servers</h4>
              <ul className="text-sm text-gray-300 space-y-1">
                {(details[selected.iso3]?.topServers || []).map((s, idx) => (
                  <li key={idx} className="flex justify-between">
                    <span>{s.name} — {s.city}</span>
                    <span className="text-blue-400">{s.load}</span>
                  </li>
                ))}
                {!details[selected.iso3]?.topServers?.length && <li className="text-gray-500">No data</li>}
              </ul>
            </div>
            <div className="bg-gray-900/50 border border-gray-700 rounded p-3">
              <h4 className="text-white font-semibold mb-2">User Segments</h4>
              <ul className="text-sm text-gray-300 space-y-1">
                {(details[selected.iso3]?.topUsers || []).map((u, idx) => (
                  <li key={idx} className="flex justify-between">
                    <span>{u.segment}</span>
                    <span className="text-blue-400">{u.count}</span>
                  </li>
                ))}
                {!details[selected.iso3]?.topUsers?.length && <li className="text-gray-500">No data</li>}
              </ul>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default GeoMap;
