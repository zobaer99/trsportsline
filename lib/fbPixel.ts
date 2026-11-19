"use client";

declare global { interface Window { fbq?: (...args: unknown[]) => void; _fbq?: (...args: unknown[]) => void; dataLayer?: unknown[]; } }
let pixelId: string | null = null;
let initialized = false;
const pendingEvents: Array<{ event: string; data: Record<string, unknown>; eventId: string }> = [];

export async function initializePixel() {
  if (typeof window === "undefined" || initialized) return;
  try {
    const response = await fetch("/api/settings/tracking", { cache: "no-store" });
    const settings = await response.json();
    const gtmId = String(settings.gtmId || "");
    const gtmLoaded = gtmId && Array.from(document.querySelectorAll<HTMLScriptElement>("[data-gtm-id]")).some((script) => script.dataset.gtmId === gtmId);
    if (gtmId && !gtmLoaded) {
      window.dataLayer = window.dataLayer || []; window.dataLayer.push({ "gtm.start": Date.now(), event: "gtm.js" });
      const gtm = document.createElement("script"); gtm.async = true; gtm.dataset.gtmId = gtmId; gtm.src = `https://www.googletagmanager.com/gtm.js?id=${encodeURIComponent(gtmId)}`; document.head.appendChild(gtm);
    }
    initialized = true;
    if (!settings.pixelId) return;
    pixelId = String(settings.pixelId);
    if (!window.fbq) {
      type FBQ = (...args: unknown[]) => void;
      const fbq: FBQ = function (...args: unknown[]) { if ((fbq as unknown as { callMethod?: (...args: unknown[]) => void }).callMethod) (fbq as unknown as { callMethod: (...args: unknown[]) => void }).callMethod(...args); else ((fbq as unknown as { queue: unknown[][] }).queue ||= []).push(args); };
      (fbq as unknown as { push: FBQ; loaded: boolean; version: string; queue: unknown[][] }).push = fbq;
      (fbq as unknown as { loaded: boolean }).loaded = true;
      (fbq as unknown as { version: string }).version = "2.0";
      window.fbq = fbq; window._fbq = fbq;
      const script = document.createElement("script"); script.async = true; script.src = "https://connect.facebook.net/en_US/fbevents.js"; document.head.appendChild(script);
    }
    window.fbq?.("init", pixelId);
    trackPixel("PageView", {}, crypto.randomUUID());
    for (const queued of pendingEvents.splice(0)) trackPixel(queued.event, queued.data, queued.eventId);
  } catch { /* Tracking is optional; storefront remains usable when it is unavailable. */ }
}

export function trackPixel(event: string, data: Record<string, unknown> = {}, eventId = crypto.randomUUID()) {
  if (typeof window === "undefined") return eventId;
  if (!pixelId || !window.fbq) { if (!initialized) pendingEvents.push({ event, data, eventId }); return eventId; }
  window.fbq("track", event, data, { eventID: eventId });
  return eventId;
}

export async function trackServerEvent(eventName: string, eventId: string, userData: Record<string, string>, customData: Record<string, unknown>) {
  try { await fetch("/api/fb-capi", { method: "POST", headers: { "content-type": "application/json" }, body: JSON.stringify({ eventName, eventId, userData, customData, eventSourceUrl: window.location.href }) }); } catch { /* Do not interrupt checkout for ad tracking. */ }
}
