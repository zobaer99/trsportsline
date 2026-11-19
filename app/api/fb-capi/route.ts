import { createHash, randomUUID } from "node:crypto";
import { headers } from "next/headers";
import { NextResponse } from "next/server";

export const runtime = "nodejs";
const allowed = new Set(["Purchase", "InitiateCheckout", "AddToCart"]);
const sha256 = (value: string) => createHash("sha256").update(value).digest("hex");
const normalized = (value: unknown) => typeof value === "string" ? value.trim().toLowerCase() : "";

export async function POST(request: Request) {
  try {
    const body = await request.json();
    const eventName = String(body.eventName || "");
    if (!allowed.has(eventName)) return NextResponse.json({ error: "Unsupported event" }, { status: 400 });
    const eventId = String(body.eventId || randomUUID()).slice(0, 128);
    const headerList = await headers();
    const forwarded = headerList.get("x-forwarded-for")?.split(",")[0]?.trim();
    const userAgent = headerList.get("user-agent") || undefined;
    const rawUser = body.userData || {};
    const userData: Record<string, string> = {};
    const phoneRaw = normalized(rawUser.ph).replace(/\D/g, "");
    const phone = phoneRaw.startsWith("0") ? `88${phoneRaw}` : phoneRaw;
    const name = normalized(rawUser.fn).split(/\s+/)[0].replace(/[^a-z]/g, "");
    if (phone) userData.ph = sha256(phone);
    if (name) userData.fn = sha256(name);
    if (forwarded) userData.client_ip_address = forwarded;
    if (userAgent) userData.client_user_agent = userAgent.slice(0, 500);
    const cookies: Record<string, string> = {};
    for (const part of (headerList.get("cookie") || "").split(";")) { const separator = part.indexOf("="); if (separator > 0) cookies[part.slice(0, separator).trim()] = part.slice(separator + 1).trim(); }
    if (cookies._fbp) userData.fbp = cookies._fbp;
    if (cookies._fbc) userData.fbc = cookies._fbc;
    const settingsServiceUrl = process.env.TRACKING_SETTINGS_URL;
    const serviceToken = process.env.TRACKING_SETTINGS_SERVICE_TOKEN;
    let token = process.env.FB_CAPI_ACCESS_TOKEN;
    let pixelId = process.env.NEXT_PUBLIC_FACEBOOK_PIXEL_ID;
    let testEventCode = process.env.FB_TEST_EVENT_CODE;
    if (settingsServiceUrl && serviceToken) {
      try {
        const settingResponse = await fetch(settingsServiceUrl, { headers: { authorization: `Bearer ${serviceToken}` }, cache: "no-store" });
        if (settingResponse.ok) { const settings = await settingResponse.json(); token = settings.capi_access_token || token; pixelId = settings.pixel_id || pixelId; testEventCode = settings.test_event_code || testEventCode; }
      } catch { /* Environment fallback keeps the tracking endpoint resilient. */ }
    }
    if (!token || !pixelId) return NextResponse.json({ received: true, skipped: "CAPI is not configured" });
    const event = {
      event_name: eventName,
      event_time: Math.floor(Date.now() / 1000),
      event_id: eventId,
      action_source: "website",
      event_source_url: String(body.eventSourceUrl || request.headers.get("referer") || "").slice(0, 2048),
      user_data: userData,
      custom_data: body.customData && typeof body.customData === "object" ? body.customData : {},
    };
    const graphVersion = process.env.FACEBOOK_GRAPH_VERSION || "v27.0";
    const params = new URLSearchParams({ access_token: token });
    const payload: Record<string, unknown> = { data: [event] };
    if (testEventCode) payload.test_event_code = testEventCode;
    const response = await fetch(`https://graph.facebook.com/${graphVersion}/${encodeURIComponent(pixelId)}/events?${params}`, { method: "POST", headers: { "content-type": "application/json" }, body: JSON.stringify(payload), cache: "no-store" });
    if (!response.ok) { const detail = await response.text(); console.error("Meta CAPI request failed", response.status, detail.slice(0, 300)); return NextResponse.json({ received: false }, { status: 502 }); }
    return NextResponse.json({ received: true, eventId });
  } catch { return NextResponse.json({ error: "Invalid tracking payload" }, { status: 400 }); }
}
