"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { ArrowLeft, ArrowRight } from "lucide-react";
import type { HeroBanner, Product, StoreSection } from "@/lib/catalog";
import { money, products as fallbackProducts } from "@/lib/catalog";

export default function HeroCarousel({ banners, products: catalog, section }: { banners: HeroBanner[]; products: Product[]; section?: StoreSection }) {
  const featured = catalog.length ? catalog.slice(0, 4) : fallbackProducts.slice(0, 4);
  const slideCount = Math.max(featured.length, banners.length, 1);
  const [active, setActive] = useState(0);
  const [touchStart, setTouchStart] = useState<number | null>(null);
  const product = featured[active % featured.length];
  const banner = banners.length ? banners[active % banners.length] : undefined;
  const titleLines = (banner?.title || section?.title || "Custom made.|Match ready.").split("|");

  useEffect(() => {
    if (slideCount < 2) return;
    const timer = window.setInterval(() => setActive((current) => (current + 1) % slideCount), 5200);
    return () => window.clearInterval(timer);
  }, [slideCount]);

  const move = (direction: number) => setActive((current) => (current + direction + slideCount) % slideCount);

  return <section className="hero hero-storefront">
    {(banner || section?.image_url) && <picture className="hero-admin-bg"><source media="(max-width: 767px)" srcSet={banner?.mobile_image || section?.mobile_image_url || banner?.desktop_image || section?.image_url}/><img src={banner?.desktop_image || section?.image_url || ""} alt="" fetchPriority="high"/></picture>}
    <div className="container hero-layout">
      <div className="hero-content">
        <div className="eyebrow">Built for your game · TR Sports Line</div>
        <h1 className="display hero-title">{titleLines.map((line, index) => <span key={`${line}-${index}`} className="block">{index === titleLines.length - 1 ? <em>{line}</em> : line}</span>)}</h1>
        <p className="hero-copy">{banner?.subtitle || section?.subtitle || "Premium jerseys for football, cricket and every squad in between. Designed around your colours, your crest and your name."}</p>
        <div className="hero-offer"><span className="hero-offer-mark">$</span><span><strong>{section?.settings?.offer_title ? String(section.settings.offer_title) : "Custom jerseys from $18"}</strong><small>{section?.settings?.offer_caption ? String(section.settings.offer_caption) : "Per piece · Team orders from 10"}</small></span></div>
        <div className="hero-actions">
          <Link href={banner?.button_url || section?.button_url || "/#shop"} className="button button-lime">{banner?.button_label || section?.button_label || "Shop the lineup"} <ArrowRight size={15}/></Link>
          <Link href="/custom-jersey" className="button button-outline">{section?.settings?.secondary_button ? String(section.settings.secondary_button) : "Build a team kit"}</Link>
        </div>
        <div className="hero-proof"><span className="hero-proof-dot"/><span>Made for players, clubs and fans across Bangladesh</span></div>
      </div>
      <div className="hero-visual" onTouchStart={(event) => setTouchStart(event.touches[0]?.clientX ?? null)} onTouchEnd={(event) => { const end = event.changedTouches[0]?.clientX; if (touchStart !== null && end !== undefined && Math.abs(end - touchStart) > 36) move(end < touchStart ? 1 : -1); setTouchStart(null); }}>
        <div className="hero-visual-glow"/>
        <div className="hero-collection-tag"><span>THE NEW LINEUP</span><b>{String(active + 1).padStart(2, "0")} / {String(slideCount).padStart(2, "0")}</b></div>
        <Link href={`/product/${product.slug}`} className="hero-product-stage" aria-label={`Shop ${product.name}`}>
          <div key={product.slug} className="hero-product-frame"><img src={product.image} alt={`${product.name} jersey`} fetchPriority="high"/><span className="hero-product-caption">{product.category}</span></div>
          <div className="hero-price-tag"><span>FEATURED KIT</span><strong>{money(product.price)}</strong></div>
        </Link>
        {slideCount > 1 && <div className="hero-controls"><div className="hero-dots">{Array.from({ length: slideCount }, (_, index) => <button key={index} onClick={() => setActive(index)} aria-label={`Show featured product ${index + 1}`} className={active === index ? "active" : ""}/>)}</div><div className="hero-arrows"><button onClick={() => move(-1)} aria-label="Previous product"><ArrowLeft size={15}/></button><button onClick={() => move(1)} aria-label="Next product"><ArrowRight size={15}/></button></div></div>}
      </div>
    </div>
  </section>;
}
