import type { MetadataRoute } from "next";
import { products } from "@/lib/catalog";

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const base = process.env.NEXT_PUBLIC_SITE_URL || "http://localhost:3000";
  let liveProducts: Array<{ slug: string; updated_at?: string }> = products;
  try { const api = process.env.NEXT_PUBLIC_API_URL; if (api) { const response = await fetch(`${api.replace(/\/?/, "")}/products`, { next: { revalidate: 3600 } }); if (response.ok) { const result = await response.json(); const rows = result.data || result; if (Array.isArray(rows) && rows.length) liveProducts = rows; } } } catch { /* Keep sitemap generation independent of API availability. */ }
  const staticRoutes = ["", "/custom-jersey", "/track-order", "/category/player-edition", "/category/fan-edition", "/category/custom-team-jerseys", "/category/retro-classics", "/category/sports-gear"];
  return [...staticRoutes.map((route) => ({ url: `${base}${route}`, lastModified: new Date(), changeFrequency: route ? "weekly" as const : "daily" as const, priority: route ? 0.7 : 1 })), ...liveProducts.map((product) => ({ url: `${base}/product/${product.slug}`, lastModified: product.updated_at ? new Date(product.updated_at) : new Date(), changeFrequency: "weekly" as const, priority: 0.8 }))];
}
