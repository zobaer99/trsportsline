import { NextResponse } from "next/server";

export const dynamic = "force-dynamic";
export async function GET() {
  const api = process.env.NEXT_PUBLIC_API_URL;
  if (api) {
    try {
      const response = await fetch(`${api.replace(/\/?/, "")}/settings/tracking`, { next: { revalidate: 300 } });
      if (response.ok) { const payload = await response.json(); const settings = payload.data || payload; return NextResponse.json({ pixelId: settings.pixel_id || settings.pixelId || null, gtmId: settings.gtm_id || settings.gtmId || null }, { headers: { "cache-control": "public, s-maxage=300, stale-while-revalidate=3600" } }); }
    } catch { /* Use public build-time settings if the Laravel API is offline. */ }
  }
  return NextResponse.json({ pixelId: process.env.NEXT_PUBLIC_FACEBOOK_PIXEL_ID || null, gtmId: process.env.NEXT_PUBLIC_GTM_ID || null }, { headers: { "cache-control": "public, s-maxage=300" } });
}
