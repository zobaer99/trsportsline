import { NextResponse } from "next/server";

export const revalidate = 300;
export async function GET() {
  const defaults = { partial_advance_enabled: true, customization_fee: 3, edition_surcharge: 5, patch_fee: 2 };
  const api = process.env.NEXT_PUBLIC_API_URL;
  if (!api) return NextResponse.json(defaults);
  try { const response = await fetch(`${api.replace(/\/$/, "")}/settings/storefront`, { next: { revalidate: 300 } }); if (response.ok) { const payload = await response.json(); return NextResponse.json(payload.data || payload, { headers: { "cache-control": "public, s-maxage=300, stale-while-revalidate=3600" } }); } } catch { /* Checkout can fall back to the safe default rules. */ }
  return NextResponse.json(defaults);
}
