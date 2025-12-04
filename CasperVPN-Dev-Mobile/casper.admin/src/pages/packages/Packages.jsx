import { useMemo, useState, useEffect } from 'react';
import {
  Add,
  Edit,
  Star,
  Archive,
  Undo,
  Bolt,
  Layers,
  CheckCircle,
  HighlightOff,
} from '@mui/icons-material';
import { usePackageStore } from '../../Store/packagestore';

const currency = (amount, code = "USD") =>
  new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: (code || 'USD').replace(/[^A-Z]/gi, '') // Remove any non-letter characters
  }).format(amount);




const defaultForm = {
  id: '',
  name: '',
  price: '',
  currency: 'USD',
  billingInterval: 'Monthly',
  featureMatrixText: '',
  deviceLimit: '1',
  dataPolicy: '',
  protocolsText: 'WireGuard®, OpenVPN',
  addOnEligible: false,
  popular: false,
  archived: false,
};

const slugify = (value) =>
  value
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)+/g, '') || `plan-${Date.now()}`;

const parseFeatureMatrix = (text) =>
  text
    .split('\n')
    .map((line) => line.trim())
    .filter(Boolean)
    .map((line) => {
      const [label, detail] = line.split('|');
      return {
        label: label?.trim() || 'Feature',
        detail: detail?.trim() || '',
      };
    });

const parseProtocols = (text) =>
  text
    .split(',')
    .map((item) => item.trim())
    .filter(Boolean);

const Packages = () => {
  const plans = usePackageStore((state) => state.plans);
  const addPlan = usePackageStore((state) => state.addPlan);
  const updatePlan = usePackageStore((state) => state.updatePlan);
  const removePlan = usePackageStore((state) => state.removePlan);
  const markMostPopular = usePackageStore((state) => state.markMostPopular);
  const toggleArchive = usePackageStore((state) => state.toggleArchive);

  const [isModalOpen, setIsModalOpen] = useState(false);
  const [form, setForm] = useState(defaultForm);
  const [mode, setMode] = useState('create');
  const [errors, setErrors] = useState({});
  const loadPackages = usePackageStore((state) => state.loadPackages);
  const loading = usePackageStore((state) => state.loading);

  useEffect(() => {
  loadPackages();
}, []);


  const modalTitle = mode === 'edit' ? 'Edit Package' : 'New Package';

  const resetForm = () => {
    setForm(defaultForm);
    setErrors({});
  };

  const openCreateModal = () => {
    setMode('create');
    resetForm();
    setIsModalOpen(true);
  };

  const openEditModal = (plan) => {
    setMode('edit');
    setForm({
      id: plan.id,
      name: plan.name,
      price: plan.price.toString(),
      currency: plan.currency,
      billingInterval: plan.billingInterval,
      featureMatrixText: plan.featureMatrix.map((item) => `${item.label} | ${item.detail}`).join('\n'),
      deviceLimit: plan.deviceLimit?.toString() ?? '1',
      dataPolicy: plan.dataPolicy,
      protocolsText: plan.protocols.join(', '),
      addOnEligible: Boolean(plan.addOnEligible),
      popular: Boolean(plan.popular),
      archived: Boolean(plan.archived),
    });
    setErrors({});
    setIsModalOpen(true);
  };

  const closeModal = () => {
    setIsModalOpen(false);
    resetForm();
  };

  const handleChange = (event) => {
    const { name, value, type, checked } = event.target;
    setForm((prev) => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value,
    }));
  };

  const validateForm = () => {
    const nextErrors = {};
    if (!form.name.trim()) {
      nextErrors.name = 'Name is required.';
    }
    const priceNumber = Number(form.price);
    if (form.price === '' || Number.isNaN(priceNumber) || priceNumber < 0) {
      nextErrors.price = 'Enter a valid, non-negative price.';
    }
    if (!form.billingInterval.trim()) {
      nextErrors.billingInterval = 'Billing interval is required.';
    }
    if (!form.dataPolicy.trim()) {
      nextErrors.dataPolicy = 'Data policy summary is required.';
    }
    const featureMatrix = parseFeatureMatrix(form.featureMatrixText);
    if (featureMatrix.length === 0) {
      nextErrors.featureMatrixText = 'Provide at least one feature line.';
    }
    const protocols = parseProtocols(form.protocolsText);
    if (protocols.length === 0) {
      nextErrors.protocolsText = 'List at least one protocol.';
    }
    const deviceLimitNumber = Number(form.deviceLimit);
    if (form.deviceLimit === '' || Number.isNaN(deviceLimitNumber) || deviceLimitNumber <= 0) {
      nextErrors.deviceLimit = 'Device limit must be greater than zero.';
    }

    return {
      nextErrors,
      featureMatrix,
      protocols,
      priceNumber,
      deviceLimitNumber,
    };
  };

  const handleSubmit = (event) => {
    event.preventDefault();
    const { nextErrors, featureMatrix, protocols, priceNumber, deviceLimitNumber } = validateForm();
    if (Object.keys(nextErrors).length > 0) {
      setErrors(nextErrors);
      return;
    }

    const payload = {
      name: form.name,
      price: priceNumber,
      currency: form.currency,
      billingInterval: form.billingInterval,
      featureMatrix,
      deviceLimit: deviceLimitNumber,
      dataPolicy: form.dataPolicy,
      protocols,
      eligibleForAddons: form.addOnEligible,
      isPopular: form.popular,
      archived: Boolean(form.archived),
    };

    if (mode === 'edit') {
      updatePlan(form.id, payload);
    } else {
      const proposedId = slugify(payload.name);
      const exists = plans.some((plan) => plan.id === proposedId);
      const uniqueId = exists ? `${proposedId}-${Date.now()}` : proposedId;
      addPlan(payload);
    }

    closeModal();
  };

  const handleArchive = (plan) => {
    toggleArchive(plan.id);
  };

  const handleMarkPopular = (plan) => {
    if (plan.archived) {
      window.alert('Restore the package before marking it as most popular.');
      return;
    }
    markMostPopular(plan.id);
  };

  const handleRemove = (plan) => {
    const confirmation = window.confirm(`Permanently delete the ${plan.name} package?`);
    if (confirmation) {
      removePlan(plan.id);
    }
  };

  const sortedPlans = useMemo(
    () =>
      [...plans].sort((a, b) => {
        if (a.archived && !b.archived) return 1;
        if (!a.archived && b.archived) return -1;
        if (a.popular && !b.popular) return -1;
        if (!a.popular && b.popular) return 1;
        return a.price - b.price;
      }),
    [plans],
  );

  return (
    <div className="min-h-screen bg-gray-900 pb-12">
      <div className="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 className="text-3xl font-bold text-white">Packages</h1>
          <p className="text-sm text-gray-400 mt-1 max-w-2xl">
            Curate VPN packages for marketing and growth teams. Keep messaging clear—no user balances here,
            just positioning.
          </p>
        </div>
        <div className="flex items-center gap-2">
          <button
            type="button"
            onClick={openCreateModal}
            className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors"
          >
            <Add fontSize="small" />
            Create Package
          </button>
        </div>
      </div>

      <div className="mt-8 grid grid-cols-1 xl:grid-cols-3 gap-6">
        {sortedPlans.map((plan) => (
          <div
            key={plan.id}
            className={`relative bg-gray-800 border ${plan.popular ? 'border-blue-500 ring-2 ring-blue-500/40' : 'border-gray-700'} rounded-xl p-6 flex flex-col gap-5 transition-opacity`}
            style={{ opacity: plan.archived ? 0.65 : 1 }}
          >
            <div className="flex items-start justify-between gap-4">
              <div>
                <div className="flex items-center gap-3">
                  <h2 className="text-xl font-semibold text-white">{plan.name}</h2>
                  {plan.popular && (
                    <span className="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded bg-blue-600 text-white">
                      <Star fontSize="inherit" /> Most Popular
                    </span>
                  )}
                  {plan.archived && (
                    <span className="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded bg-red-600/20 text-red-300 border border-red-500/40">
                      <HighlightOff fontSize="inherit" /> Archived
                    </span>
                  )}
                </div>
                <div className="mt-3 text-4xl font-bold text-white">
                  {currency(plan.price, plan.currency)}
                </div>
                <div className="text-sm text-gray-400">{plan.billingInterval}</div>
              </div>
              <div className="flex flex-col gap-2">
                <button
                  type="button"
                  onClick={() => openEditModal(plan)}
                  className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-500/15 text-blue-200 hover:bg-blue-500/25 text-xs"
                >
                  <Edit fontSize="inherit" /> Edit
                </button>
                <button
                  type="button"
                  onClick={() => handleMarkPopular(plan)}
                  className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-500/15 text-emerald-200 hover:bg-emerald-500/25 text-xs"
                  disabled={plan.popular}
                >
                  <Bolt fontSize="inherit" /> {plan.popular ? 'Pinned' : 'Mark Popular'}
                </button>
                <button
                  type="button"
                  onClick={() => handleArchive(plan)}
                  className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-amber-500/15 text-amber-200 hover:bg-amber-500/25 text-xs"
                >
                  {plan.archived ? <Undo fontSize="inherit" /> : <Archive fontSize="inherit" />}
                  {plan.archived ? 'Restore' : 'Archive'}
                </button>
                <button
                  type="button"
                  onClick={() => handleRemove(plan)}
                  className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-500/15 text-red-200 hover:bg-red-500/25 text-xs"
                >
                  <HighlightOff fontSize="inherit" /> Delete
                </button>
              </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-300">
              <div className="bg-gray-900/40 border border-gray-700 rounded-lg p-4 flex flex-col gap-1">
                <span className="text-xs uppercase tracking-wide text-gray-400">Device Limit</span>
                <span className="text-lg font-semibold text-white">{plan.deviceLimit} devices</span>
              </div>
              <div className="bg-gray-900/40 border border-gray-700 rounded-lg p-4 flex flex-col gap-1">
                <span className="text-xs uppercase tracking-wide text-gray-400">Add-on Eligibility</span>
                <span className="text-lg font-semibold text-white flex items-center gap-2">
                  {plan.addOnEligible ? <CheckCircle fontSize="small" className="text-emerald-400" /> : <HighlightOff fontSize="small" className="text-red-400" />}
                  {plan.addOnEligible ? 'Supports add-ons' : 'Core package only'}
                </span>
              </div>
              <div className="sm:col-span-2 bg-gray-900/40 border border-gray-700 rounded-lg p-4">
                <span className="text-xs uppercase tracking-wide text-gray-400">Data Policy</span>
                <p className="mt-2 text-sm text-gray-300 leading-relaxed">{plan.dataPolicy}</p>
              </div>
            </div>

            <div>
              <div className="text-xs uppercase tracking-wide text-gray-400 mb-2">Protocols</div>
              <div className="flex flex-wrap gap-2">
                {plan.protocols.map((protocol) => (
                  <span key={`${plan.id}-${protocol}`} className="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-900/60 border border-gray-700 text-xs text-blue-200">
                    <Layers fontSize="inherit" /> {protocol}
                  </span>
                ))}
              </div>
            </div>

            <div>
              <div className="text-xs uppercase tracking-wide text-gray-400 mb-3">Feature Matrix</div>
              <ul className="space-y-2 text-sm text-gray-200">
                {plan.featureMatrix.map((feature, index) => (
                  <li key={`${plan.id}-feature-${index}`} className="flex items-start gap-3">
                    <span className="mt-1 h-1.5 w-1.5 rounded-full bg-blue-500" />
                    <div>
                      <div className="font-medium text-white">{feature.label}</div>
                      {feature.detail && <div className="text-gray-400 text-xs mt-0.5">{feature.detail}</div>}
                    </div>
                  </li>
                ))}
              </ul>
            </div>
          </div>
        ))}
      </div>

      {isModalOpen && (
        <div className="modal-backdrop">
          <div className="modal-panel">
            <div className="flex items-center justify-between px-6 py-4 border-b border-gray-700">
              <h2 className="text-xl font-semibold text-white">{modalTitle}</h2>
              <button
                type="button"
                onClick={closeModal}
                className="text-gray-400 hover:text-white transition-colors"
              >
                ✕
              </button>
            </div>

            <form
              onSubmit={handleSubmit}
              className="flex-1 overflow-y-auto px-6 py-6 grid grid-cols-1 md:grid-cols-2 gap-4"
            >
              <div>
                <label htmlFor="name" className="block text-sm font-medium text-gray-300 mb-1">
                  Package Name
                </label>
                <input
                  id="name"
                  name="name"
                  type="text"
                  value={form.name}
                  onChange={handleChange}
                  className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Secure Lite"
                  required
                />
                {errors.name && <p className="mt-1 text-xs text-red-400">{errors.name}</p>}
              </div>

              <div>
                <label htmlFor="price" className="block text-sm font-medium text-gray-300 mb-1">
                  Price
                </label>
                <input
                  id="price"
                  name="price"
                  type="number"
                  min="0"
                  step="0.01"
                  value={form.price}
                  onChange={handleChange}
                  className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="12"
                  required
                />
                {errors.price && <p className="mt-1 text-xs text-red-400">{errors.price}</p>}
              </div>

              <div>
                <label htmlFor="currency" className="block text-sm font-medium text-gray-300 mb-1">
                  Currency
                </label>
                <input
                  id="currency"
                  name="currency"
                  type="text"
                  value={form.currency}
                  onChange={handleChange}
                  className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="USD"
                  maxLength={3}
                  required
                />
              </div>

              <div>
                <label htmlFor="billingInterval" className="block text-sm font-medium text-gray-300 mb-1">
                  Billing Interval
                </label>
                <input
                  id="billingInterval"
                  name="billingInterval"
                  type="text"
                  value={form.billingInterval}
                  onChange={handleChange}
                  className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Monthly"
                  required
                />
                {errors.billingInterval && <p className="mt-1 text-xs text-red-400">{errors.billingInterval}</p>}
              </div>

              <div>
                <label htmlFor="deviceLimit" className="block text-sm font-medium text-gray-300 mb-1">
                  Device Limit
                </label>
                <input
                  id="deviceLimit"
                  name="deviceLimit"
                  type="number"
                  min="1"
                  value={form.deviceLimit}
                  onChange={handleChange}
                  className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
                {errors.deviceLimit && <p className="mt-1 text-xs text-red-400">{errors.deviceLimit}</p>}
              </div>

              <div className="md:col-span-2">
                <label htmlFor="dataPolicy" className="block text-sm font-medium text-gray-300 mb-1">
                  Data Policy Summary
                </label>
                <textarea
                  id="dataPolicy"
                  name="dataPolicy"
                  rows={2}
                  value={form.dataPolicy}
                  onChange={handleChange}
                  className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Unlimited data with fair-use after 5 TB"
                  required
                />
                {errors.dataPolicy && <p className="mt-1 text-xs text-red-400">{errors.dataPolicy}</p>}
              </div>

              <div className="md:col-span-2">
                <label htmlFor="featureMatrixText" className="block text-sm font-medium text-gray-300 mb-1">
                  Feature Matrix (one per line — "Label | Detail")
                </label>
                <textarea
                  id="featureMatrixText"
                  name="featureMatrixText"
                  rows={5}
                  value={form.featureMatrixText}
                  onChange={handleChange}
                  className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder={'Global Servers | 45 countries / 89 cities\nPrivacy | Strict no-logs policy'}
                  required
                />
                {errors.featureMatrixText && <p className="mt-1 text-xs text-red-400">{errors.featureMatrixText}</p>}
              </div>

              <div className="md:col-span-2">
                <label htmlFor="protocolsText" className="block text-sm font-medium text-gray-300 mb-1">
                  Protocols (comma separated)
                </label>
                <input
                  id="protocolsText"
                  name="protocolsText"
                  type="text"
                  value={form.protocolsText}
                  onChange={handleChange}
                  className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="WireGuard®, OpenVPN, IKEv2"
                  required
                />
                {errors.protocolsText && <p className="mt-1 text-xs text-red-400">{errors.protocolsText}</p>}
              </div>

              <div className="flex items-center gap-2">
                <input
                  id="addOnEligible"
                  name="addOnEligible"
                  type="checkbox"
                  checked={form.addOnEligible}
                  onChange={handleChange}
                  className="h-5 w-5 rounded border border-gray-600 bg-gray-700 text-blue-500 focus:ring-blue-500"
                />
                <label htmlFor="addOnEligible" className="text-sm text-gray-300">
                  Eligible for add-ons
                </label>
              </div>

              <div className="flex items-center gap-2">
                <input
                  id="popular"
                  name="popular"
                  type="checkbox"
                  checked={form.popular}
                  onChange={handleChange}
                  className="h-5 w-5 rounded border border-gray-600 bg-gray-700 text-blue-500 focus:ring-blue-500"
                />
                <label htmlFor="popular" className="text-sm text-gray-300">
                  Mark as most popular on create
                </label>
              </div>

              {mode === 'edit' && (
                <div className="flex items-center gap-2">
                  <input
                    id="archived"
                    name="archived"
                    type="checkbox"
                    checked={form.archived}
                    onChange={handleChange}
                    className="h-5 w-5 rounded border border-gray-600 bg-gray-700 text-blue-500 focus:ring-blue-500"
                  />
                  <label htmlFor="archived" className="text-sm text-gray-300">
                    Start archived
                  </label>
                </div>
              )}

              <div className="md:col-span-2 flex justify-end gap-3 pt-4">
                <button
                  type="button"
                  onClick={closeModal}
                  className="px-4 py-2 rounded-lg border border-gray-600 text-gray-300 hover:bg-gray-800 transition-colors"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="px-5 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition-colors"
                >
                  Save Package
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

export default Packages;
