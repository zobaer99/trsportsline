import type { Metadata } from "next";
import { notFound } from "next/navigation";
import ProductCard from "@/components/ProductCard";
import CategoryPills from "@/components/CategoryPills";
import { getCatalogProducts } from "@/lib/catalog";

const categories: Record<string, { title: string; description: string }> = {
  "player-edition": { title: "Player Edition Jerseys", description: "Slim-fit performance football and cricket jerseys made for serious match-day energy." },
  "fan-edition": { title: "Fan Edition Jerseys", description: "Comfortable fan-fit football and cricket jerseys for the stands and everyday wear." },
  "custom-team-jerseys": { title: "Custom Team Jerseys", description: "Personalized team jerseys with your colors, crest, names and numbers. Made in Bangladesh." },
  "retro-classics": { title: "Retro Classic Jerseys", description: "Iconic football and cricket jersey designs brought back for today's supporters." },
  "sports-gear": { title: "Sports Gear & Accessories", description: "Training essentials and sports gear for your next session." },
};
export function generateStaticParams() { return Object.keys(categories).map((slug) => ({ slug })); }
export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }): Promise<Metadata> { const { slug } = await params; const category = categories[slug]; return category ? { title: category.title, description: category.description, openGraph: { title: `${category.title} | TR Sports Line`, description: category.description } } : { title: "Category not found" }; }
export default async function CategoryPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params; const category = categories[slug]; if (!category) notFound();
  const all = await getCatalogProducts(); const match = slug.replaceAll("-", " "); const items = all.filter((product) => product.category.toLowerCase().replaceAll("-", " ") === match || (slug === "player-edition" && product.edition === "Player Edition") || (slug === "fan-edition" && product.edition === "Fan Edition"));
  return <main className="container min-h-[65vh] py-10 md:py-16"><div className="eyebrow">The TR lineup</div><h1 className="display mt-3 text-5xl md:text-7xl">{category.title}</h1><p className="mt-4 max-w-lg text-xs leading-6 text-zinc-400">{category.description}</p><div className="mt-7"><CategoryPills/></div>{items.length ? <div className="product-grid mt-8">{items.map((product) => <ProductCard key={product.slug} product={product}/>)}</div> : <div className="mt-10 border border-white/10 bg-[#121214] p-8 text-center text-sm text-zinc-400">This collection is being refreshed. Check back shortly.</div>}</main>;
}
