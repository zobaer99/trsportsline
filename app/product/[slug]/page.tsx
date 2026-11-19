import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { products, getCatalogProduct, getPageSections, getStorefrontSettings } from "@/lib/catalog";
import JerseyCustomizer from "@/components/JerseyCustomizer";

export function generateStaticParams() { return products.map(({ slug }) => ({ slug })); }
export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }): Promise<Metadata> {
  const { slug } = await params; const product = await getCatalogProduct(slug);
  if (!product) return { title: "Product not found" };
  const title = product.metaTitle || product.name; const description = product.metaDescription || product.description;
  return { title, description, openGraph: { title: `${title} | TR Sports Line`, description, images: [product.image] } };
}
export default async function ProductPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params; const product = await getCatalogProduct(slug); if (!product) notFound();
  const [productSections, storeSettings] = await Promise.all([getPageSections("product"), getStorefrontSettings()]);
  const productMessage = productSections.find((section) => section.section_key === "product_message" && section.is_active);
  const base = `${process.env.NEXT_PUBLIC_SITE_URL || "http://localhost:3000"}/product/${product.slug}`;
  const productSchema = { "@type": "Product", name: product.name, description: product.description, image: product.image, brand: { "@type": "Brand", name: "TR Sports Line" }, offers: { "@type": "Offer", priceCurrency: "USD", price: product.price, availability: "https://schema.org/InStock", url: base }, ...(product.reviewCount && product.ratingValue ? { aggregateRating: { "@type": "AggregateRating", ratingValue: product.ratingValue, reviewCount: product.reviewCount } } : {}) };
  const schema = { "@context": "https://schema.org", "@graph": [productSchema, { "@type": "BreadcrumbList", itemListElement: [{ "@type": "ListItem", position: 1, name: "Home", item: process.env.NEXT_PUBLIC_SITE_URL || "http://localhost:3000" }, { "@type": "ListItem", position: 2, name: "Shop", item: `${process.env.NEXT_PUBLIC_SITE_URL || "http://localhost:3000"}/#shop` }, { "@type": "ListItem", position: 3, name: product.name, item: base }] }] };
  return <main><script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(schema).replace(/</g, "\\u003c") }}/><div className="container pt-5 text-[10px] text-zinc-500"><a href="/">Home</a> / <a href="/#shop">Shop</a> / <span className="text-zinc-300">{product.name}</span></div><JerseyCustomizer product={product} pageSettings={storeSettings} pageSection={productMessage}/></main>;
}
