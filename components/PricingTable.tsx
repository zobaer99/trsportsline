"use client";

import { useEffect, useState } from "react";
import { ChevronRight, Shirt } from "lucide-react";
import { fallbackPriceGroups, formatTablePrice, type PriceAddon, type PriceGroup } from "@/lib/pricing";

function NeckShape({ index }: { index: number }) {
  const paths = ["M15 20 Q50 56 85 20", "M15 20 Q50 56 85 20 L85 40 L59 40 L59 67 L41 67 L41 40 L15 40Z", "M15 20 L50 65 L85 20", "M15 20 L50 65 L85 20 L76 20 L50 52 L24 20Z", "M15 20 Q50 56 85 20 L76 20 Q50 45 24 20Z", "M18 20 L50 58 L82 20 L68 20 L50 41 L32 20Z", "M15 20 Q50 56 85 20 L85 48 L68 48 L59 34 L50 48 L41 34 L32 48 L15 48Z", "M14 20 Q50 56 86 20 L78 20 Q50 45 22 20 L22 38 L40 52 L50 36 L60 52 L78 38 L78 20Z", "M15 20 L50 65 L85 20 L72 20 L50 48 L28 20Z"];
  return <svg viewBox="0 0 100 78" aria-hidden="true" className="h-16 w-full"><path d={paths[index % paths.length]} fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinejoin="round"/><path d="M15 20 Q50 32 85 20" fill="none" stroke="currentColor" strokeWidth="1" opacity=".55"/></svg>;
}

export default function PricingTable() {
  const [groups, setGroups] = useState<PriceGroup[]>(fallbackPriceGroups);
  const [activeSlug, setActiveSlug] = useState("jersey");
  useEffect(() => { fetch("/api/pricing").then((response) => response.ok ? response.json() : null).then((payload) => { const rows = payload?.data || payload; if (Array.isArray(rows) && rows.length) { setGroups(rows); setActiveSlug((current) => rows.some((row: PriceGroup) => row.slug === current) ? current : rows[0].slug); } }).catch(() => {}); }, []);
  const active = groups.find((group) => group.slug === activeSlug) || groups[0];
  if (!active) return null;
  const sleeves = active.addons.filter((addon) => addon.category === "sleeve");
  const necks = active.addons.filter((addon) => addon.category === "neck");
  const extras = active.addons.filter((addon) => addon.category === "addon");
  const addonLabel = (addon: PriceAddon) => addon.amount === 0 ? "Included" : `+${formatTablePrice(addon.amount, active.currency)}`;

  return <section id="price-table" className="pricing-section section"><div className="container">
    <div className="mx-auto max-w-3xl text-center"><div className="eyebrow">Clear, flexible pricing</div><h2 className="display mt-3 text-4xl sm:text-5xl">Price <span className="text-lime">table.</span></h2><p className="mt-3 text-xs font-semibold text-zinc-300">Premium jerseys with affordable pricing you can trust.</p><p className="mx-auto mt-4 max-w-2xl text-[11px] leading-5 text-zinc-500">{active.note || "Choose a fabric, then add the details your team needs. Your final quote depends on quantity, fit and finishing."}</p></div>
    <div className="pricing-layout mt-9">
      <div className="min-w-0">
        <div className="mb-4 flex gap-2 overflow-x-auto pb-1">{groups.map((group) => <button key={group.slug} onClick={() => setActiveSlug(group.slug)} className={`pricing-tab shrink-0 rounded-full border px-4 py-2 text-[10px] font-extrabold transition ${active.slug === group.slug ? "is-active" : ""}`}>{group.name}</button>)}</div>
        <div className="overflow-hidden rounded-xl border border-white/15">
          <div className="overflow-x-auto"><table className="w-full min-w-[500px] text-left text-[11px]"><thead className="bg-zinc-100 text-[9px] uppercase tracking-wider text-zinc-900"><tr><th className="px-3 py-3">SL</th><th className="px-3 py-3">Fabric</th><th className="px-3 py-3">MOQ</th><th className="px-3 py-3 text-right">Price</th><th className="px-3 py-3 text-right">Offer price</th></tr></thead><tbody>{active.entries.map((entry, index) => <tr key={entry.id || `${entry.fabric}-${index}`} className={`pricing-row ${index % 2 ? "alternate" : ""}`}><td className="px-3 py-3 text-zinc-500">{String(index + 1).padStart(2,"0")}</td><td className="px-3 py-3 font-bold text-white">{entry.fabric}</td><td className="px-3 py-3 text-zinc-400">{entry.moq ? `${entry.moq} pcs` : "—"}</td><td className={`px-3 py-3 text-right ${entry.offer_price ? "text-zinc-500 line-through" : "font-extrabold text-white"}`}>{formatTablePrice(entry.price, active.currency)}</td><td className="px-3 py-3 text-right font-extrabold text-lime">{entry.offer_price ? formatTablePrice(entry.offer_price, active.currency) : "—"}</td></tr>)}</tbody></table></div>
        </div>
        {sleeves.length > 0 && <div className="mt-5"><h3 className="mb-3 font-display text-xl uppercase">Sleeve add-ons</h3><div className="grid grid-cols-3 overflow-hidden rounded-xl border border-white/10">{sleeves.map((addon) => <div key={addon.id || addon.label} className="bg-zinc-100 px-2 py-3 text-center text-[10px] font-bold text-zinc-900"><span className="block">{addon.label}</span><span className="mt-2 block text-zinc-500">{addonLabel(addon)}</span></div>)}</div></div>}
        {extras.length > 0 && <div className="mt-5"><h3 className="mb-3 font-display text-xl uppercase">Add-ons</h3><div className="grid grid-cols-2 gap-2 sm:grid-cols-4">{extras.map((addon) => <div key={addon.id || addon.label} className="flex items-center justify-between border border-white/10 bg-[#141416] px-3 py-3 text-[10px]"><span>{addon.label}</span><strong className="text-lime">{addonLabel(addon)}</strong></div>)}</div></div>}
      </div>
      <div className="pricing-feature">
        <div className="mb-4 flex items-center gap-2 text-lime"><Shirt size={16}/><span className="eyebrow">Personalize the details</span></div>
        <h3 className="display mb-5 text-4xl italic sm:text-5xl">Affordable<br/>yet premium.</h3>
        {necks.length > 0 && <><p className="mb-3 text-[10px] font-bold uppercase tracking-wider text-zinc-400">Neck styles · sleeve options priced above</p><div className="grid grid-cols-3 gap-2 sm:grid-cols-4">{necks.map((addon, index) => <div key={addon.id || addon.label} className="rounded-xl border border-white/15 bg-[#121214] p-2"><div className="mb-2 grid aspect-square place-items-center bg-black p-2 text-zinc-200"><NeckShape index={index}/></div><div className="flex items-start justify-between gap-1 text-[8px] font-bold"><span>{addon.label}</span><span className="shrink-0 text-zinc-500">{addonLabel(addon)}</span></div></div>)}</div></>}
        <p className="mt-5 text-[11px] leading-6 text-zinc-400">Select a fabric and finishing style for a tailored team quote. Our production team can help with custom colors, crests, names and numbers.</p>
        <a href="/custom-jersey#quote" className="mt-4 inline-flex items-center gap-2 text-[10px] font-extrabold uppercase tracking-wider text-lime hover:text-white">Get a custom quote <ChevronRight size={14}/></a>
      </div>
    </div>
  </div></section>;
}
