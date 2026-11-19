export type ProductImage = { url: string; label: string };
export type Product = { slug: string; name: string; club: string; category: string; price: number; oldPrice?: number; image: string; images?: ProductImage[]; badge?: string; edition: "Fan Edition" | "Player Edition"; description: string; metaTitle?: string; metaDescription?: string; ratingValue?: number; reviewCount?: number };

export const products: Product[] = [
  { slug: "custom-team-kit", name: "Custom Team Performance Kit", club: "Made for your squad", category: "Custom Team Jerseys", price: 18, oldPrice: 24, image: "/products/custom-team-kit.webp", badge: "FROM $18", edition: "Fan Edition", description: "Your colors, your crest, your team. Custom performance jerseys start at $18 per piece for team orders." },
  { slug: "legacy-cross-kit", name: "TR Legacy Cross Jersey", club: "TR Football Club", category: "Retro Classics", price: 24, oldPrice: 29, image: "/products/legacy-cross-kit.webp", badge: "TR SIGNATURE", edition: "Fan Edition", description: "A crisp heritage look with a comfortable fan fit, made for the stands and the street." },
  { slug: "blue-heritage-kit", name: "TR Blue Heritage Jersey", club: "TR Football Club", category: "Player Edition", price: 28, image: "/products/blue-heritage-kit.webp", badge: "NEW DROP", edition: "Player Edition", description: "A vivid blue player fit with lightweight, breathable performance fabric." },
  { slug: "midnight-performance-kit", name: "TR Midnight Performance Jersey", club: "TR Cricket Club", category: "Fan Edition", price: 26, image: "/products/midnight-performance-kit.webp", badge: "MATCH READY", edition: "Fan Edition", description: "Deep teal performance fabric and an easy regular fit for training or match day." },
];

export const getProduct = (slug: string) => products.find((product) => product.slug === slug);
export const money = (amount: number) => new Intl.NumberFormat("en-US", { style: "currency", currency: "USD", minimumFractionDigits: Number.isInteger(amount) ? 0 : 2, maximumFractionDigits: 2 }).format(amount);

function normalizeProduct(input: Record<string, unknown>): Product {
  const image = String(input.image_url || input.image || "");
  const backImage = typeof input.back_image_url === "string" ? input.back_image_url : "";
  const galleryImages = Array.isArray(input.gallery_images) ? input.gallery_images.filter((url): url is string => typeof url === "string" && url.trim().length > 0) : [];
  const images: ProductImage[] = [
    ...(image ? [{ url: image, label: "Front" }] : []),
    ...(backImage ? [{ url: backImage, label: "Back" }] : []),
    ...galleryImages.map((url, index) => ({ url, label: `Photo ${index + 1}` })),
  ];
  return { slug: String(input.slug || ""), name: String(input.name || ""), club: String(input.club || ""), category: String(input.category || "Fan Edition"), price: Number(input.price || 0), oldPrice: input.compare_at_price ? Number(input.compare_at_price) : input.oldPrice ? Number(input.oldPrice) : undefined, image, images, badge: input.badge ? String(input.badge) : undefined, edition: input.edition === "Player Edition" ? "Player Edition" : "Fan Edition", description: String(input.description || ""), metaTitle: input.meta_title ? String(input.meta_title) : undefined, metaDescription: input.meta_description ? String(input.meta_description) : undefined, ratingValue: input.rating_value ? Number(input.rating_value) : undefined, reviewCount: input.review_count ? Number(input.review_count) : undefined };
}

export async function getCatalogProducts(): Promise<Product[]> {
  const api = process.env.NEXT_PUBLIC_API_URL;
  if (!api) return products;
  try { const response = await fetch(`${api.replace(/\/$/, "")}/products`, { next: { revalidate: 300 } }); if (!response.ok) return products; const payload = await response.json(); const rows = payload.data || payload; return Array.isArray(rows) && rows.length ? rows.map(normalizeProduct) : products; }
  catch { return products; }
}

export async function getCatalogProduct(slug: string): Promise<Product | undefined> {
  const api = process.env.NEXT_PUBLIC_API_URL;
  if (api) { try { const response = await fetch(`${api.replace(/\/$/, "")}/products/${encodeURIComponent(slug)}`, { next: { revalidate: 300 } }); if (response.ok) { const payload = await response.json(); return normalizeProduct(payload.data || payload); } } catch { /* Static catalog fallback below. */ } }
  return getProduct(slug);
}

export type HeroBanner = { title: string; subtitle?: string; desktop_image: string; mobile_image?: string; button_label?: string; button_url?: string };
export async function getHeroBanners(): Promise<HeroBanner[]> {
  const api = process.env.NEXT_PUBLIC_API_URL;
  if (!api) return [];
  try { const response = await fetch(`${api.replace(/\/$/, "")}/banners`, { next: { revalidate: 300 } }); if (!response.ok) return []; const payload = await response.json(); const rows = payload.data || payload; return Array.isArray(rows) ? rows : []; }
  catch { return []; }
}

export type CustomerReview = { id: number; customer_name: string; district: string; review: string; photo_url?: string; rating: number; is_verified: boolean };
export async function getCustomerReviews(): Promise<CustomerReview[]> {
  const api = process.env.NEXT_PUBLIC_API_URL;
  if (!api) return [];
  try { const response = await fetch(`${api.replace(/\/$/, "")}/reviews`, { next: { revalidate: 300 } }); if (!response.ok) return []; const payload = await response.json(); const rows = payload.data || payload; return Array.isArray(rows) ? rows : []; }
  catch { return []; }
}

export type StoreSection = { id?: number; page: string; section_key: string; title: string; subtitle?: string; body?: string; image_url?: string; mobile_image_url?: string; button_label?: string; button_url?: string; sort_order: number; is_active: boolean; settings?: Record<string, unknown> };
export async function getPageSections(page: "home" | "product"): Promise<StoreSection[]> {
  const api = process.env.NEXT_PUBLIC_API_URL;
  if (!api) return [];
  try { const response = await fetch(`${api.replace(/\/$/, "")}/content/${page}`, { next: { revalidate: 60 } }); if (!response.ok) return []; const payload = await response.json(); return payload.data || []; }
  catch { return []; }
}

export type StorefrontSettings = { [key: string]: string | number | boolean | null | undefined };
export async function getStorefrontSettings(): Promise<StorefrontSettings> {
  const defaults: StorefrontSettings = { footer_logo_url: "/brand/tr-crest.webp", footer_tagline: "Built for the match. Made for your team.", footer_address: "Dhaka, Bangladesh · Delivery nationwide", footer_phone: "", footer_email: "", footer_whatsapp: "8801700000000", footer_instagram: "https://instagram.com", footer_facebook: "https://facebook.com", product_page_badge: "Customize your match-day kit", product_page_delivery_note: "Delivery across Bangladesh in 2–4 days.", product_page_size_note: "Measurements are garment measurements in inches." };
  const api = process.env.NEXT_PUBLIC_API_URL;
  if (!api) return defaults;
  try { const response = await fetch(`${api.replace(/\/$/, "")}/settings/storefront`, { next: { revalidate: 60 } }); if (!response.ok) return defaults; const payload = await response.json(); return { ...defaults, ...(payload.data || payload) }; }
  catch { return defaults; }
}
