import Link from "next/link";
import { ArrowRight, BadgeCheck, CreditCard, MessageCircle, Truck } from "lucide-react";
import ProductCard from "@/components/ProductCard";
import CategoryPills from "@/components/CategoryPills";
import HeroCarousel from "@/components/HeroCarousel";
import { getCatalogProducts, getCustomerReviews, getHeroBanners, getPageSections, type StoreSection } from "@/lib/catalog";

const categories = [
  { title: "Player\nEdition", image: "/products/blue-heritage-kit.webp", href: "/category/player-edition" },
  { title: "Fan\nEdition", image: "/products/midnight-performance-kit.webp", href: "/category/fan-edition" },
  { title: "Custom Team\nJerseys", image: "/products/custom-team-kit.webp", href: "/custom-jersey" },
  { title: "Retro\nClassics", image: "/products/legacy-cross-kit.webp", href: "/category/retro-classics" },
  { title: "Sports\nGear", image: "/products/midnight-performance-kit.webp", href: "/category/sports-gear" },
];

export default async function HomePage() {
  const [products, banners, reviews, fetchedSections] = await Promise.all([getCatalogProducts(), getHeroBanners(), getCustomerReviews(), getPageSections("home")]);
  const defaults: StoreSection[] = [
    { page: "home", section_key: "hero", title: "Custom made.|Match ready.", subtitle: "Premium jerseys for football, cricket and every squad in between.", sort_order: 0, is_active: true },
    { page: "home", section_key: "categories", title: "Find your fit.", subtitle: "Your game. Your gear.", sort_order: 10, is_active: true },
    { page: "home", section_key: "trending", title: "Trending kits.", subtitle: "The line-up fans are wearing now.", sort_order: 20, is_active: true },
    { page: "home", section_key: "team_promo", title: "Your team. Your colours. Your kit.", subtitle: "Custom names, numbers and club crests. Team jerseys from $18 each for orders of 10 or more.", sort_order: 30, is_active: true },
    { page: "home", section_key: "new_drops", title: "New drops.", subtitle: "Fresh kits, just off the press.", sort_order: 40, is_active: true },
    { page: "home", section_key: "reviews", title: "Worn with pride.", subtitle: "From our stands to yours.", sort_order: 50, is_active: true },
    { page: "home", section_key: "features", title: "Store benefits", subtitle: "", body: "Nationwide delivery|Quality checked kits|bKash · Nagad · COD|Real human support", sort_order: 5, is_active: true },
    ...categories.map((category, i) => ({ page: "home", section_key: `category_${i}`, title: category.title.replace("\n", " "), subtitle: "", image_url: category.image, button_url: category.href, sort_order: 60 + i, is_active: true })),
  ];
  const sections = fetchedSections.length ? fetchedSections : defaults;
  const byKey = Object.fromEntries(sections.map((section) => [section.section_key, section]));
  const home = (key: string) => byKey[key];
  const visible = (key: string) => home(key)?.is_active !== false;
  const featureCopy = (home("features")?.body || "Nationwide delivery|Quality checked kits|bKash · Nagad · COD|Real human support").split("|").map((item) => item.trim()).filter(Boolean);
  const hasCategorySections = fetchedSections.some((section) => section.section_key.startsWith("category_"));
  const categoryCards = categories.map((category, index) => { const section = home(`category_${index}`); return { ...category, title: section?.title || category.title.replace("\n", " "), image: section?.image_url || category.image, href: section?.button_url || category.href, is_active: hasCategorySections ? Boolean(section?.is_active) : true }; }).filter((item) => item.is_active);
  const sorted = (keys: string[]) => keys.sort((a, b) => (home(a)?.sort_order ?? 999) - (home(b)?.sort_order ?? 999));
  const fixedKeys = ["categories", "trending", "team_promo", "new_drops", "reviews"];
  const extraKeys = sections.map((section) => section.section_key).filter((key) => !fixedKeys.includes(key) && key !== "hero" && key !== "features" && !key.startsWith("category_"));
  const contentKeys = sorted([...fixedKeys, ...extraKeys]);
  return <main>
    {visible("hero") && <HeroCarousel banners={banners} products={products} section={home("hero")}/>}
    {visible("features") && <div className="feature-strip container">{featureCopy.map((copy, index) => { const Icon = [Truck, BadgeCheck, CreditCard, MessageCircle][index % 4]; return <div className="feature" key={`${copy}-${index}`}><Icon size={17}/>{copy}</div>; })}</div>}
    {contentKeys.filter(visible).map((key) => key === "categories" ? <section id="categories" key={key} className="section pb-3"><div className="container">
      <div className="section-head"><div><div className="eyebrow">{home(key)?.subtitle || "Your game. Your gear."}</div><h2 className="display section-title">{home(key)?.title || "Find your fit."}</h2></div><Link href="/#shop" className="hidden items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-zinc-300 hover:text-lime sm:flex">All categories <ArrowRight size={14}/></Link></div>
      <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">{categoryCards.map((category) => <Link href={category.href} key={category.title} className="category-card"><img src={category.image} alt="" loading="lazy"/><span>{category.title.split(" ").map((line, i) => <span key={`${line}-${i}`} className="block">{line}</span>)}</span><span className="absolute right-3 top-3 z-10 grid h-7 w-7 place-items-center rounded-full border border-white/40 text-sm">↗</span></Link>)}</div>
    </div></section> : key === "trending" ? <section id="shop" key={key} className="section"><div className="container">
      <div className="section-head"><div><div className="eyebrow">Shop the lineup</div><h2 className="display section-title">{home(key)?.title || "Trending kits."}</h2><p className="mt-2 text-xs text-zinc-500">{home(key)?.subtitle}</p></div><Link href={home(key)?.button_url || "/#shop"} className="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-zinc-300 hover:text-lime">{home(key)?.button_label || "Shop all"} <ArrowRight size={14}/></Link></div>
      <div className="mb-6"><CategoryPills/></div><div className="product-grid">{products.slice(0,2).map((product) => <ProductCard key={product.slug} product={product}/>)}</div>
    </div></section> : key === "team_promo" ? <section key={key} className="container pb-12"><div className="promo-band" style={home(key)?.image_url ? { backgroundImage: `linear-gradient(90deg,rgba(204,255,0,.95),rgba(204,255,0,.72)),url(${home(key)?.image_url})`, backgroundSize: "cover", backgroundPosition: "center" } : undefined}><div><div className="eyebrow text-black/60">{home(key)?.subtitle || "Custom team jerseys · from $18 each"}</div><div className="display mt-2">{home(key)?.title || "Your team. Your colours. Your kit."}</div><div className="mt-3 max-w-lg text-xs leading-5 text-black/70">{home(key)?.body || "Custom names, numbers and club crests. Premium performance fabrics from 10 pieces."}</div></div><Link href={home(key)?.button_url || "/custom-jersey"} className="button">{home(key)?.button_label || "Create your kit"} <ArrowRight size={15}/></Link></div></section> : key === "new_drops" ? <section key={key} className="section bg-[#111113]"><div className="container">
      <div className="section-head"><div><div className="eyebrow">Fresh from the locker room</div><h2 className="display section-title">{home(key)?.title || "New drops."}</h2><p className="mt-2 text-xs text-zinc-500">{home(key)?.subtitle}</p></div><Link href={home(key)?.button_url || "/#shop"} className="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-zinc-300 hover:text-lime">{home(key)?.button_label || "View collection"} <ArrowRight size={14}/></Link></div>
      <div className="product-grid">{products.slice(2,4).map((product) => <ProductCard key={product.slug} product={product}/>)}</div>
    </div></section> : key === "reviews" ? <section key={key} className="section"><div className="container">
      <div className="section-head"><div><div className="eyebrow">{home(key)?.subtitle || "From our stands to yours"}</div><h2 className="display section-title">{home(key)?.title || "Worn with pride."}</h2></div><a href="https://instagram.com" className="hidden text-xs font-bold text-zinc-300 hover:text-lime sm:block">@trsportsline ↗</a></div>
      {reviews.length ? <><div className="grid grid-cols-2 gap-3 md:grid-cols-4">{reviews.filter((review) => review.photo_url).slice(0,4).map((review) => <div key={`photo-${review.id}`} className="relative aspect-square overflow-hidden bg-panel"><img src={review.photo_url} alt={`Jersey delivery photo from ${review.customer_name}`} loading="lazy" className="h-full w-full object-cover transition hover:scale-105"/><div className="reviews-photo-overlay absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 p-3 pt-10 text-[10px] font-semibold">{review.customer_name} · {review.district}{review.is_verified && <span className="ml-1 text-lime">✓ VERIFIED</span>}</div></div>)}</div><div className="mt-9 grid gap-3 md:grid-cols-3">{reviews.slice(0,3).map((review) => <blockquote key={review.id} className="border border-white/10 bg-[#111113] p-5"><div className="text-xs tracking-widest text-amber-300">{"★".repeat(Math.max(0, Math.min(5, review.rating)))}</div><p className="mt-3 text-xs leading-6 text-zinc-300">“{review.review}”</p><footer className="mt-4 text-[10px] font-bold text-zinc-500">{review.customer_name}, {review.district} {review.is_verified && <span className="text-lime">· VERIFIED PURCHASE</span>}</footer></blockquote>)}</div></> : <div className="border border-white/10 bg-[#111113] px-6 py-10 text-center"><div className="eyebrow">The community gallery</div><p className="mt-3 font-display text-3xl uppercase">Your match-day story could be next.</p><p className="mx-auto mt-2 max-w-sm text-xs leading-5 text-zinc-400">Tag @trsportsline or send your jersey photo to be featured here.</p><a href="https://wa.me/8801700000000?text=I%20want%20to%20share%20my%20TR%20Sports%20Line%20jersey%20photo" className="button button-outline mt-5">Share your photo ↗</a></div>}
    </div></section> : <section key={key} className="section"><div className="container"><div className="section-head"><div><div className="eyebrow">{home(key)?.subtitle}</div><h2 className="display section-title">{home(key)?.title || "More from TR Sports Line"}</h2></div>{home(key)?.button_label && <Link href={home(key)?.button_url || "/#shop"} className="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-zinc-300 hover:text-lime">{home(key)?.button_label}<ArrowRight size={14}/></Link>}</div>{home(key)?.image_url && <img src={home(key)?.image_url} alt="" className="mb-5 max-h-[460px] w-full object-cover"/>}<p className="max-w-3xl text-sm leading-7 text-zinc-400">{home(key)?.body}</p></div></section>)}
  </main>;
}
