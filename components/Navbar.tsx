"use client";

import Link from "next/link";
import { useState } from "react";
import { ArrowUpRight, Menu, Search, ShoppingBag, UserRound, X } from "lucide-react";
import { useShopStore, cartCount } from "@/lib/store";
import ThemeToggle from "@/components/ThemeToggle";

const links = [["Shop all", "/#shop"], ["Football", "/#shop"], ["Cricket", "/#shop"], ["Custom team kits", "/custom-jersey"], ["Track order", "/track-order"]];
export default function Navbar() {
  const [open, setOpen] = useState(false); const [mobile, setMobile] = useState(false);
  const count = useShopStore((state) => cartCount(state.items)); const setCartOpen = useShopStore((state) => state.setCartOpen);
  return <>
    <div className="hidden md:flex items-center justify-center bg-lime px-3 py-2 text-[10px] font-extrabold tracking-[.12em] text-black">FREE DELIVERY ON ORDERS OVER $75 <span className="mx-3">—</span> CASH ON DELIVERY AVAILABLE</div>
    <header className="sticky top-0 z-40 border-b border-white/10 bg-[#0b0b0c]/95 backdrop-blur-xl">
      <div className="container flex h-[68px] items-center justify-between gap-6">
        <button className="md:hidden p-2" aria-label="Open menu" onClick={() => setMobile(true)}><Menu size={22}/></button>
        <Link href="/" className="flex items-center gap-2.5"><img src="/brand/tr-crest.webp" alt="TR Sports Line crest" className="h-10 w-10 rounded-full object-cover"/><span className="font-display text-[19px] font-bold uppercase leading-[.86] tracking-tight">Sports<br/>Line</span></Link>
        <nav className="hidden items-center gap-7 md:flex">{links.slice(0,4).map(([label, href]) => <Link key={label} href={href} className="text-[11px] font-bold text-zinc-300 transition hover:text-lime">{label}</Link>)}</nav>
        <div className="flex items-center gap-1.5"><Link href="/#shop" className="hidden p-2 text-zinc-300 hover:text-lime sm:block" aria-label="Search products"><Search size={18}/></Link><Link href="/track-order" className="hidden px-2 text-[10px] font-bold text-zinc-300 hover:text-lime lg:block">TRACK ORDER</Link><ThemeToggle compact/><Link href="/account" className="p-2 text-zinc-300 hover:text-lime" aria-label="Customer account"><UserRound size={18}/></Link><button onClick={() => setCartOpen(true)} className="relative flex h-10 items-center gap-2 px-2 text-zinc-100" aria-label="Open cart"><ShoppingBag size={19}/><span className="hidden text-[10px] font-extrabold sm:block">CART</span>{count > 0 && <span className="absolute -right-0.5 -top-0.5 grid h-[17px] min-w-[17px] place-items-center rounded-full bg-lime px-1 text-[9px] font-black text-black">{count}</span>}</button></div>
      </div>
    </header>
    {mobile && <><div className="overlay md:hidden" onClick={() => setMobile(false)}/><aside className="drawer left-0 right-auto border-r border-l-0 md:hidden"><div className="drawer-head"><span className="font-display text-xl uppercase">Menu</span><button onClick={() => setMobile(false)} aria-label="Close menu"><X/></button></div><nav className="grid p-5">{links.map(([label, href]) => <Link onClick={() => setMobile(false)} key={label} href={href} className="flex items-center justify-between border-b border-white/10 py-5 text-sm font-bold">{label}<ArrowUpRight size={15}/></Link>)}</nav><div className="mt-auto p-5"><Link className="button button-lime w-full" href="/custom-jersey">Build your team kit <ArrowUpRight size={15}/></Link></div></aside></>}
  </>;
}
