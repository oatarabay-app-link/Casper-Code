import { useState } from 'react';
import { usePromotionStore } from '../../Store/promotionstore';
import { Edit, Delete, Close } from '@mui/icons-material';

const badgeStyles = {
  blue: 'bg-blue-600/20 text-blue-300 border border-blue-600/30',
  green: 'bg-green-600/20 text-green-300 border border-green-600/30',
  yellow: 'bg-yellow-600/20 text-yellow-200 border border-yellow-600/30',
  gray: 'bg-gray-600/20 text-gray-300 border border-gray-600/30',
};

const Badge = ({ children, color = 'blue' }) => (
  <span className={`inline-flex items-center px-2 py-0.5 text-xs rounded ${badgeStyles[color] ?? badgeStyles.blue}`}>
    {children}
  </span>
);

const statusToColor = (status) => {
  switch (status) {
    case 'Active':
      return 'green';
    case 'Scheduled':
      return 'yellow';
    case 'Expired':
    default:
      return 'gray';
  }
};

const today = () => new Date().toISOString().slice(0, 10);
const addDays = (days) => new Date(Date.now() + days * 86400000).toISOString().slice(0, 10);

const generateId = (prefix) => {
  if (typeof crypto !== 'undefined' && crypto.randomUUID) {
    return crypto.randomUUID();
  }
  return `${prefix}-${Date.now()}-${Math.floor(Math.random() * 1000)}`;
};

const initialCouponForm = {
  code: '',
  description: '',
  type: 'percent',
  value: '10',
  currency: '',
  validFrom: today(),
  validTo: addDays(30),
  usageLimit: '100',
  used: '0',
  status: 'Scheduled',
};

const initialDiscountForm = {
  name: '',
  planId: 'monthly',
  type: 'percent',
  value: '10',
  currency: '',
  status: 'Scheduled',
};

const initialSeasonalForm = {
  name: '',
  description: '',
  type: 'percent',
  value: '25',
  currency: '',
  startDate: today(),
  endDate: addDays(7),
  applyTo: 'all',
  status: 'Scheduled',
};

const Promotions = () => {
  const {
    coupons,
    discounts,
    seasonalDeals,
    addCoupon,
    updateCoupon,
    removeCoupon,
    addDiscount,
    updateDiscount,
    removeDiscount,
    addSeasonalDeal,
    updateSeasonalDeal,
    removeSeasonalDeal,
  } = usePromotionStore();

  const [modalState, setModalState] = useState({ open: false, type: null, mode: 'create', targetId: null });
  const [formData, setFormData] = useState(initialCouponForm);

  const closeModal = () => {
    setModalState({ open: false, type: null, mode: 'create', targetId: null });
    setFormData(initialCouponForm);
  };

  const openCouponModal = (mode, coupon) => {
    if (mode === 'edit' && coupon) {
      setFormData({
        code: coupon.code,
        description: coupon.description,
        type: coupon.type,
        value: String(coupon.value ?? ''),
        currency: coupon.currency ?? '',
        validFrom: coupon.validFrom,
        validTo: coupon.validTo,
        usageLimit: String(coupon.usageLimit ?? ''),
        used: String(coupon.used ?? '0'),
        status: coupon.status ?? 'Active',
      });
      setModalState({ open: true, type: 'coupon', mode: 'edit', targetId: coupon.id });
    } else {
      setFormData(initialCouponForm);
      setModalState({ open: true, type: 'coupon', mode: 'create', targetId: null });
    }
  };

  const openDiscountModal = (mode, discount) => {
    if (mode === 'edit' && discount) {
      setFormData({
        name: discount.name,
        planId: discount.planId,
        type: discount.type,
        value: String(discount.value ?? ''),
        currency: discount.currency ?? '',
        status: discount.status ?? 'Active',
      });
      setModalState({ open: true, type: 'discount', mode: 'edit', targetId: discount.id });
    } else {
      setFormData(initialDiscountForm);
      setModalState({ open: true, type: 'discount', mode: 'create', targetId: null });
    }
  };

  const openSeasonalModal = (mode, deal) => {
    if (mode === 'edit' && deal) {
      setFormData({
        name: deal.name,
        description: deal.description,
        type: deal.type,
        value: String(deal.value ?? ''),
        currency: deal.currency ?? '',
        startDate: deal.startDate,
        endDate: deal.endDate,
        applyTo: Array.isArray(deal.applyTo) ? deal.applyTo.join(', ') : deal.applyTo ?? 'all',
        status: deal.status ?? 'Scheduled',
      });
      setModalState({ open: true, type: 'seasonal', mode: 'edit', targetId: deal.id });
    } else {
      setFormData(initialSeasonalForm);
      setModalState({ open: true, type: 'seasonal', mode: 'create', targetId: null });
    }
  };

  const handleChange = (event) => {
    const { name, value } = event.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const normaliseCurrency = (type, currency) => {
    if (type === 'percent') {
      return undefined;
    }
    return currency?.trim() || 'USD';
  };

  const handleSubmit = (event) => {
    event.preventDefault();

    if (modalState.type === 'coupon') {
      const payload = {
        code: formData.code.trim(),
        description: formData.description.trim(),
        type: formData.type,
        value: Number(formData.value) || 0,
        currency: normaliseCurrency(formData.type, formData.currency),
        validFrom: formData.validFrom,
        validTo: formData.validTo,
        usageLimit: Number(formData.usageLimit) || 0,
        used: Number(formData.used) || 0,
        status: formData.status,
      };

      if (payload.currency === undefined) {
        delete payload.currency;
      }

      if (modalState.mode === 'edit' && modalState.targetId) {
        updateCoupon(modalState.targetId, payload);
      } else {
        addCoupon({ id: generateId('coupon'), ...payload });
      }
    }

    if (modalState.type === 'discount') {
      const payload = {
        name: formData.name.trim(),
        planId: formData.planId,
        type: formData.type,
        value: Number(formData.value) || 0,
        currency: normaliseCurrency(formData.type, formData.currency),
        status: formData.status,
      };

      if (payload.currency === undefined) {
        delete payload.currency;
      }

      if (modalState.mode === 'edit' && modalState.targetId) {
        updateDiscount(modalState.targetId, payload);
      } else {
        addDiscount({ id: generateId('discount'), ...payload });
      }
    }

    if (modalState.type === 'seasonal') {
      const applyToInput = formData.applyTo?.trim();
      let applyToValue;
      if (!applyToInput || applyToInput.toLowerCase() === 'all') {
        applyToValue = 'all';
      } else {
        applyToValue = applyToInput
          .split(',')
          .map((entry) => entry.trim())
          .filter(Boolean);
      }

      const payload = {
        name: formData.name.trim(),
        description: formData.description.trim(),
        type: formData.type,
        value: Number(formData.value) || 0,
        currency: normaliseCurrency(formData.type, formData.currency),
        startDate: formData.startDate,
        endDate: formData.endDate,
        applyTo: applyToValue,
        status: formData.status,
      };

      if (payload.currency === undefined) {
        delete payload.currency;
      }

      if (modalState.mode === 'edit' && modalState.targetId) {
        updateSeasonalDeal(modalState.targetId, payload);
      } else {
        addSeasonalDeal({ id: generateId('seasonal'), ...payload });
      }
    }

    closeModal();
  };

  const confirmDelete = (type, id) => {
    if (typeof window !== 'undefined' && !window.confirm('Are you sure you want to delete this item?')) {
      return;
    }

    if (type === 'coupon') {
      removeCoupon(id);
    }

    if (type === 'discount') {
      removeDiscount(id);
    }

    if (type === 'seasonal') {
      removeSeasonalDeal(id);
    }
  };

  const modalTitle = (() => {
    if (modalState.type === 'coupon') {
      return modalState.mode === 'edit' ? 'Edit Coupon' : 'Create Coupon';
    }
    if (modalState.type === 'discount') {
      return modalState.mode === 'edit' ? 'Edit Discount' : 'Create Discount';
    }
    if (modalState.type === 'seasonal') {
      return modalState.mode === 'edit' ? 'Edit Seasonal Deal' : 'Create Seasonal Deal';
    }
    return 'Create Promotion';
  })();

  return (
    <div className="min-h-screen bg-gray-900 pb-10">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold text-white">Promotions</h1>
        <div className="flex gap-2">
          <button
            type="button"
            onClick={() => openCouponModal('create')}
            className="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700"
          >
            New Coupon
          </button>
          <button
            type="button"
            onClick={() => openDiscountModal('create')}
            className="px-3 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700"
          >
            New Discount
          </button>
          <button
            className="px-3 py-2 bg-emerald-600 text-white text-sm rounded hover:bg-emerald-700"
            type="button"
            onClick={() => openSeasonalModal('create')}
          >
            New Seasonal Deal
          </button>
        </div>
      </div>

      <div className="mt-8 space-y-8">
        {/* Coupons */}
        <section className="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
            <h2 className="text-xl text-white font-semibold">Coupons</h2>
          </div>
          <div className="overflow-x-auto">
            <table className="min-w-full text-sm text-gray-300">
              <thead className="bg-gray-750">
                <tr>
                  <th className="text-left px-6 py-3">Code</th>
                  <th className="text-left px-6 py-3">Description</th>
                  <th className="text-left px-6 py-3">Type</th>
                  <th className="text-left px-6 py-3">Value</th>
                  <th className="text-left px-6 py-3">Valid</th>
                  <th className="text-left px-6 py-3">Usage</th>
                  <th className="text-left px-6 py-3">Status</th>
                  <th className="text-left px-6 py-3">Actions</th>
                </tr>
              </thead>
              <tbody>
                {coupons.map((coupon) => (
                  <tr key={coupon.id} className="border-t border-gray-700">
                    <td className="px-6 py-3 font-mono text-white">{coupon.code}</td>
                    <td className="px-6 py-3">{coupon.description}</td>
                    <td className="px-6 py-3 capitalize">{coupon.type}</td>
                    <td className="px-6 py-3">
                      {coupon.type === 'percent' ? `${coupon.value}%` : `$${coupon.value} ${coupon.currency || ''}`}
                    </td>
                    <td className="px-6 py-3">
                      {coupon.validFrom} → {coupon.validTo}
                    </td>
                    <td className="px-6 py-3">
                      {coupon.used}/{coupon.usageLimit}
                    </td>
                    <td className="px-6 py-3">
                      <Badge color={statusToColor(coupon.status)}>{coupon.status}</Badge>
                    </td>
                    <td className="px-6 py-3">
                      <div className="flex items-center gap-2">
                        <button
                          type="button"
                          onClick={() => openCouponModal('edit', coupon)}
                          className="px-2 py-1 rounded bg-gray-700 text-gray-200 hover:bg-gray-600"
                        >
                          <Edit fontSize="small" />
                        </button>
                        <button
                          type="button"
                          onClick={() => confirmDelete('coupon', coupon.id)}
                          className="px-2 py-1 rounded bg-red-600/80 text-white hover:bg-red-600"
                        >
                          <Delete fontSize="small" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </section>

        {/* Discounts */}
        <section className="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
            <h2 className="text-xl text-white font-semibold">Plan Discounts</h2>
          </div>
          <div className="overflow-x-auto">
            <table className="min-w-full text-sm text-gray-300">
              <thead className="bg-gray-750">
                <tr>
                  <th className="text-left px-6 py-3">Name</th>
                  <th className="text-left px-6 py-3">Plan</th>
                  <th className="text-left px-6 py-3">Type</th>
                  <th className="text-left px-6 py-3">Value</th>
                  <th className="text-left px-6 py-3">Status</th>
                  <th className="text-left px-6 py-3">Actions</th>
                </tr>
              </thead>
              <tbody>
                {discounts.map((discount) => (
                  <tr key={discount.id} className="border-t border-gray-700">
                    <td className="px-6 py-3 text-white">{discount.name}</td>
                    <td className="px-6 py-3 uppercase">{discount.planId}</td>
                    <td className="px-6 py-3 capitalize">{discount.type}</td>
                    <td className="px-6 py-3">
                      {discount.type === 'percent' ? `${discount.value}%` : `$${discount.value} ${discount.currency || ''}`}
                    </td>
                    <td className="px-6 py-3">
                      <Badge color={statusToColor(discount.status)}>{discount.status}</Badge>
                    </td>
                    <td className="px-6 py-3">
                      <div className="flex items-center gap-2">
                        <button
                          type="button"
                          onClick={() => openDiscountModal('edit', discount)}
                          className="px-2 py-1 rounded bg-gray-700 text-gray-200 hover:bg-gray-600"
                        >
                          <Edit fontSize="small" />
                        </button>
                        <button
                          type="button"
                          onClick={() => confirmDelete('discount', discount.id)}
                          className="px-2 py-1 rounded bg-red-600/80 text-white hover:bg-red-600"
                        >
                          <Delete fontSize="small" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </section>

        {/* Seasonal Deals */}
  <section className="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
            <h2 className="text-xl text-white font-semibold">Seasonal Deals</h2>
          </div>
          <div className="overflow-x-auto">
            <table className="min-w-full text-sm text-gray-300">
              <thead className="bg-gray-750">
                <tr>
                  <th className="text-left px-6 py-3">Name</th>
                  <th className="text-left px-6 py-3">Description</th>
                  <th className="text-left px-6 py-3">Type</th>
                  <th className="text-left px-6 py-3">Value</th>
                  <th className="text-left px-6 py-3">Window</th>
                  <th className="text-left px-6 py-3">Applies To</th>
                  <th className="text-left px-6 py-3">Status</th>
                  <th className="text-left px-6 py-3">Actions</th>
                </tr>
              </thead>
              <tbody>
                {seasonalDeals.map((deal) => (
                  <tr key={deal.id} className="border-t border-gray-700">
                    <td className="px-6 py-3 text-white">{deal.name}</td>
                    <td className="px-6 py-3">{deal.description}</td>
                    <td className="px-6 py-3 capitalize">{deal.type}</td>
                    <td className="px-6 py-3">
                      {deal.type === 'percent' ? `${deal.value}%` : `$${deal.value} ${deal.currency || ''}`}
                    </td>
                    <td className="px-6 py-3">{deal.startDate} → {deal.endDate}</td>
                    <td className="px-6 py-3">{Array.isArray(deal.applyTo) ? deal.applyTo.join(', ') : deal.applyTo}</td>
                    <td className="px-6 py-3">
                      <Badge color={statusToColor(deal.status)}>{deal.status}</Badge>
                    </td>
                    <td className="px-6 py-3">
                      <div className="flex items-center gap-2">
                        <button
                          type="button"
                          onClick={() => openSeasonalModal('edit', deal)}
                          className="px-2 py-1 rounded bg-gray-700 text-gray-200 hover:bg-gray-600"
                        >
                          <Edit fontSize="small" />
                        </button>
                        <button
                          type="button"
                          onClick={() => confirmDelete('seasonal', deal.id)}
                          className="px-2 py-1 rounded bg-red-600/80 text-white hover:bg-red-600"
                        >
                          <Delete fontSize="small" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </section>
      </div>

      {modalState.open && (
        <div className="modal-backdrop">
          <div className="modal-panel">
            <div className="flex items-center justify-between px-6 py-4 border-b border-gray-700">
              <h3 className="text-lg font-semibold text-white">{modalTitle}</h3>
              <button type="button" onClick={closeModal} className="text-gray-400 hover:text-white">
                <Close fontSize="small" />
              </button>
            </div>
            <form onSubmit={handleSubmit} className="flex-1 overflow-y-auto p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
              {modalState.type === 'coupon' && (
                <>
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="code">Code</label>
                    <input
                      id="code"
                      name="code"
                      value={formData.code}
                      onChange={handleChange}
                      required
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="type">Type</label>
                    <select
                      id="type"
                      name="type"
                      value={formData.type}
                      onChange={handleChange}
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="percent">Percent</option>
                      <option value="amount">Amount</option>
                    </select>
                  </div>
                  <div className="md:col-span-2">
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="description">Description</label>
                    <textarea
                      id="description"
                      name="description"
                      value={formData.description}
                      onChange={handleChange}
                      rows={2}
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="value">Value</label>
                    <input
                      id="value"
                      name="value"
                      type="number"
                      min="0"
                      step="0.01"
                      value={formData.value}
                      onChange={handleChange}
                      required
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  {formData.type === 'amount' && (
                    <div>
                      <label className="block text-sm text-gray-300 mb-1" htmlFor="currency">Currency</label>
                      <input
                        id="currency"
                        name="currency"
                        value={formData.currency}
                        onChange={handleChange}
                        placeholder="USD"
                        className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                      />
                    </div>
                  )}
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="validFrom">Valid From</label>
                    <input
                      id="validFrom"
                      name="validFrom"
                      type="date"
                      value={formData.validFrom}
                      onChange={handleChange}
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="validTo">Valid To</label>
                    <input
                      id="validTo"
                      name="validTo"
                      type="date"
                      value={formData.validTo}
                      onChange={handleChange}
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="usageLimit">Usage Limit</label>
                    <input
                      id="usageLimit"
                      name="usageLimit"
                      type="number"
                      min="0"
                      value={formData.usageLimit}
                      onChange={handleChange}
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="used">Used</label>
                    <input
                      id="used"
                      name="used"
                      type="number"
                      min="0"
                      value={formData.used}
                      onChange={handleChange}
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="status">Status</label>
                    <select
                      id="status"
                      name="status"
                      value={formData.status}
                      onChange={handleChange}
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="Active">Active</option>
                      <option value="Scheduled">Scheduled</option>
                      <option value="Expired">Expired</option>
                    </select>
                  </div>
                </>
              )}

              {modalState.type === 'discount' && (
                <>
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="name">Name</label>
                    <input
                      id="name"
                      name="name"
                      value={formData.name}
                      onChange={handleChange}
                      required
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="planId">Plan</label>
                    <select
                      id="planId"
                      name="planId"
                      value={formData.planId}
                      onChange={handleChange}
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="monthly">Monthly</option>
                      <option value="quarterly">Quarterly</option>
                      <option value="yearly">Yearly</option>
                      <option value="lifetime">Lifetime</option>
                    </select>
                  </div>
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="type">Type</label>
                    <select
                      id="type"
                      name="type"
                      value={formData.type}
                      onChange={handleChange}
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="percent">Percent</option>
                      <option value="amount">Amount</option>
                    </select>
                  </div>
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="value">Value</label>
                    <input
                      id="value"
                      name="value"
                      type="number"
                      min="0"
                      step="0.01"
                      value={formData.value}
                      onChange={handleChange}
                      required
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  {formData.type === 'amount' && (
                    <div>
                      <label className="block text-sm text-gray-300 mb-1" htmlFor="currency">Currency</label>
                      <input
                        id="currency"
                        name="currency"
                        value={formData.currency}
                        onChange={handleChange}
                        placeholder="USD"
                        className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                      />
                    </div>
                  )}
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="status">Status</label>
                    <select
                      id="status"
                      name="status"
                      value={formData.status}
                      onChange={handleChange}
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="Active">Active</option>
                      <option value="Scheduled">Scheduled</option>
                      <option value="Expired">Expired</option>
                    </select>
                  </div>
                </>
              )}

              {modalState.type === 'seasonal' && (
                <>
                  <div className="md:col-span-2">
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="name">Name</label>
                    <input
                      id="name"
                      name="name"
                      value={formData.name}
                      onChange={handleChange}
                      required
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <div className="md:col-span-2">
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="description">Description</label>
                    <textarea
                      id="description"
                      name="description"
                      value={formData.description}
                      onChange={handleChange}
                      rows={2}
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="type">Type</label>
                    <select
                      id="type"
                      name="type"
                      value={formData.type}
                      onChange={handleChange}
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="percent">Percent</option>
                      <option value="amount">Amount</option>
                    </select>
                  </div>
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="value">Value</label>
                    <input
                      id="value"
                      name="value"
                      type="number"
                      min="0"
                      step="0.01"
                      value={formData.value}
                      onChange={handleChange}
                      required
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  {formData.type === 'amount' && (
                    <div>
                      <label className="block text-sm text-gray-300 mb-1" htmlFor="currency">Currency</label>
                      <input
                        id="currency"
                        name="currency"
                        value={formData.currency}
                        onChange={handleChange}
                        placeholder="USD"
                        className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                      />
                    </div>
                  )}
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="startDate">Start Date</label>
                    <input
                      id="startDate"
                      name="startDate"
                      type="date"
                      value={formData.startDate}
                      onChange={handleChange}
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="endDate">End Date</label>
                    <input
                      id="endDate"
                      name="endDate"
                      type="date"
                      value={formData.endDate}
                      onChange={handleChange}
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <div className="md:col-span-2">
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="applyTo">Applies To</label>
                    <input
                      id="applyTo"
                      name="applyTo"
                      value={formData.applyTo}
                      onChange={handleChange}
                      placeholder="all or comma separated plan IDs"
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <p className="mt-1 text-xs text-gray-400">Use "all" for every plan or comma separate plan identifiers (e.g., monthly, yearly).</p>
                  </div>
                  <div>
                    <label className="block text-sm text-gray-300 mb-1" htmlFor="status">Status</label>
                    <select
                      id="status"
                      name="status"
                      value={formData.status}
                      onChange={handleChange}
                      className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="Active">Active</option>
                      <option value="Scheduled">Scheduled</option>
                      <option value="Expired">Expired</option>
                    </select>
                  </div>
                </>
              )}

              <div className="md:col-span-2 flex justify-end gap-3 pt-2">
                <button
                  type="button"
                  onClick={closeModal}
                  className="px-4 py-2 rounded-lg border border-gray-600 text-gray-300 hover:bg-gray-800 transition-colors duration-200"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="px-5 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition-colors duration-200"
                >
                  Save
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

export default Promotions;
