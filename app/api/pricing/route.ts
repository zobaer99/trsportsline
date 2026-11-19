import { NextResponse } from "next/server";
import { fallbackPriceGroups } from "@/lib/pricing";

export const revalidate = 60;

export async function GET() {
  const api = process.env.NEXT_PUBLIC_API_URL;
  if (!api) return NextResponse.json({ data: fallbackPriceGroups });
  try {
    const response = await fetch(`${api.replace(/\/$/, "")}/pricing`, { next: { revalidate: 60 } });
    if (response.ok) return NextResponse.json(await response.json(), { headers: { "cache-control": "public, s-maxage=60, stale-while-revalidate=300" } });
  } catch { /* Use the editable demo chart until the API is online. */ }
  return NextResponse.json({ data: fallbackPriceGroups });
}
