// package-services.js
import { API_ROUTES } from "../router/api-routes";
import api from "../utils/axios";

/**
 * Service layer responsibilities:
 * - Validate incoming UI data
 * - Normalize / prepare payload for backend
 * - Call API endpoints
 * - Normalize responses for the UI (ensure featureMatrix/protocols arrays exist)
 *
 * All validation errors are thrown as an Error with a `details` object:
 *   const err = new Error("Validation failed");
 *   err.details = { field: "message", ... }
 *   throw err;
 */

const logError = (label, error) => {
  console.error(`${label}:`, {
    message: error?.response?.data?.message || error.message,
    status: error?.response?.status,
    data: error?.response?.data,
  });
};

/* ---------- Helpers ---------- */

const cleanCurrencyCode = (raw) => {
  if (!raw) return "USD";
  // keep only letters and convert to upper-case (USD)
  const cleaned = String(raw).replace(/[^A-Za-z]/g, "").toUpperCase();
  return cleaned.length === 3 ? cleaned : "USD";
};

const parseFeatureMatrixText = (text) => {
  if (!text) return [];
  return String(text)
    .split("\n")
    .map((line) => line.trim())
    .filter(Boolean)
    .map((line) => {
      const [labelRaw, ...rest] = line.split("|");
      const label = (labelRaw || "").trim() || "Feature";
      const detail = rest.join("|").trim();
      return { label, detail };
    });
};

const parseProtocolsText = (text) => {
  if (!text) return [];
  return String(text)
    .split(",")
    .map((p) => p.trim())
    .filter(Boolean);
};

const ensureNumber = (val, fallback = 0) => {
  const n = Number(val);
  return Number.isFinite(n) ? n : fallback;
};

const validationError = (details) => {
  const err = new Error("Validation failed");
  err.details = details;
  throw err;
};

const normalizeResponsePackage = (pkg) => {
  // Defensive mapping : ensure arrays exist and fields are normalized
  return {
    id: pkg.id ?? pkg._id ?? pkg.slug ?? undefined,
    name: pkg.name ?? "",
    price: ensureNumber(pkg.price, 0),
    currency: cleanCurrencyCode(pkg.currency ?? "USD"),
    billingInterval: pkg.billingInterval ?? "",
    deviceLimit: ensureNumber(pkg.deviceLimit, 0),
    dataPolicy: pkg.dataPolicy ?? "",
    eligibleForAddons: Boolean(pkg.eligibleForAddons ?? pkg.addOnEligible ?? false),
    isPopular: Boolean(pkg.isPopular ?? pkg.popular ?? false),
    archived: Boolean(pkg.archived ?? false),
    featureMatrix: Array.isArray(pkg.featureMatrix) ? pkg.featureMatrix : [],
    protocols: Array.isArray(pkg.protocols) ? pkg.protocols : [],
    // include raw original payload for debugging if needed
    _raw: pkg,
  };
};

/* ---------- Validation + Prepare ---------- */

export const validateAndPreparePackagePayload = (input = {}, options = { fromUI: true, isUpdate: false }) => {
  // Accept either UI form shape (featureMatrixText/protocolsText) or already prepared arrays.
  const errors = {};
  const {
    name,
    price,
    currency,
    billingInterval,
    deviceLimit,
    dataPolicy,
    addOnEligible,
    eligibleForAddons,
    isPopular,
    popular,
    archived,
    featureMatrixText,
    protocolsText,
    featureMatrix,
    protocols,
  } = input;

  // Basic validations
  if (!name || String(name).trim().length === 0) {
    errors.name = "Name is required.";
  }

  const priceNum = ensureNumber(price, NaN);
  if (!Number.isFinite(priceNum) || priceNum < 0) {
    errors.price = "Price must be a non-negative number.";
  }

  if (!billingInterval || String(billingInterval).trim().length === 0) {
    errors.billingInterval = "Billing interval is required.";
  }

  const deviceLimitNum = ensureNumber(deviceLimit, NaN);
  if (!Number.isFinite(deviceLimitNum) || deviceLimitNum <= 0) {
    errors.deviceLimit = "Device limit must be greater than zero.";
  }

  if (!dataPolicy || String(dataPolicy).trim().length === 0) {
    errors.dataPolicy = "Data policy is required.";
  }

  // Parse featureMatrix & protocols (UI might send text fields)
  const finalFeatureMatrix =
    Array.isArray(featureMatrix) && featureMatrix.length > 0
      ? featureMatrix
      : parseFeatureMatrixText(featureMatrixText);

  const finalProtocols =
    Array.isArray(protocols) && protocols.length > 0 ? protocols : parseProtocolsText(protocolsText);

  // If you want to require at least one feature/protocol, enforce here:
  // (keeping permissive but allow empty arrays)
  // if (finalFeatureMatrix.length === 0) errors.featureMatrix = "Provide at least one feature line.";
  // if (finalProtocols.length === 0) errors.protocols = "Provide at least one protocol.";

  if (Object.keys(errors).length > 0) {
    validationError(errors);
  }

  // Prepare final payload matching backend contract
  const payload = {
    // core
    name: String(name).trim(),
    price: Number(priceNum),
    currency: cleanCurrencyCode(currency),
    billingInterval: String(billingInterval).trim(),
    deviceLimit: Number(deviceLimitNum),
    dataPolicy: String(dataPolicy).trim(),
    // flags - accept either naming convention
    eligibleForAddons: Boolean(typeof eligibleForAddons !== "undefined" ? eligibleForAddons : addOnEligible),
    isPopular: Boolean(typeof isPopular !== "undefined" ? isPopular : popular),
    archived: Boolean(archived ?? false),
    // arrays
    featureMatrix: finalFeatureMatrix,
    protocols: finalProtocols,
  };

  // If update, allow id passed (leave id if present)
  if (options.isUpdate && input.id) {
    payload.id = input.id;
  }

  return payload;
};

/* ---------- API calls ---------- */

export const createPackageService = async (packageData) => {
  try {
    const prepared = validateAndPreparePackagePayload(packageData, { fromUI: true, isUpdate: false });
    const response = await api.post(API_ROUTES.PACKAGE.CREATE_PACKAGE, prepared);
    // Normalize response to UI-friendly shape
    return normalizeResponsePackage(response.data?.package ?? response.data);
  } catch (err) {
    logError("❌ Create Package Error", err);
    // rethrow to let UI handle errors; attach validation details if present
    throw err;
  }
};

export const fetchPackagesService = async () => {
  try {
    const response = await api.get(API_ROUTES.PACKAGE.GET_PACKAGES);
    const packages = response.data?.packages ?? response.data ?? [];
    return (Array.isArray(packages) ? packages : []).map(normalizeResponsePackage);
  } catch (err) {
    logError("❌ Fetch Packages Error", err);
    throw err;
  }
};

export const updatePackageService = async (packageData) => {
  try {
    const prepared = validateAndPreparePackagePayload(packageData, { fromUI: true, isUpdate: true });
    // For update endpoint, backend may expect id in URL or body; adapt as needed.
    // Assuming API_ROUTES.PACKAGE.UPDATE_PACKAGE is a function or a route accepting body with id:
    const response = await api.put(API_ROUTES.PACKAGE.UPDATE_PACKAGE, prepared);
    return normalizeResponsePackage(response.data?.package ?? response.data);
  } catch (err) {
    logError("❌ Update Package Error", err);
    throw err;
  }
};

export const deletePackageService = async (packageId) => {
  try {
    const response = await api.delete(API_ROUTES.PACKAGE.DELETE_PACKAGE_BY_ID(packageId));
    return response.data;
  } catch (err) {
    logError("❌ Delete Package Error", err);
    throw err;
  }
};
