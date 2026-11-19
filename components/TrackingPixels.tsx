"use client";

import { useEffect, useRef } from "react";
import { usePathname } from "next/navigation";
import { initializePixel } from "@/lib/fbPixel";

export default function TrackingPixels() { const pathname = usePathname(); const firstPage = useRef(true); useEffect(() => { void initializePixel(); }, []); useEffect(() => { if (firstPage.current) { firstPage.current = false; return; } if (pathname) window.fbq?.("track", "PageView"); }, [pathname]); return null; }
