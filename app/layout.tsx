import type { Metadata } from "next";
import "./globals.css";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import CartDrawer from "@/components/CartDrawer";
import TrackingPixels from "@/components/TrackingPixels";
import { ThemeProvider } from "@/components/ThemeProvider";

export async function generateMetadata(): Promise<Metadata> {
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "http://localhost:3000";
  let title = "TR Sports Line — Built for the Match";
  let description = "Premium football and cricket jerseys, custom team kits and sports gear. Made for match day in Bangladesh.";
  let keywords = ["football jerseys", "cricket jerseys", "custom team kits", "Bangladesh"];
  const api = process.env.NEXT_PUBLIC_API_URL;
  if (api) {
    try {
      const response = await fetch(`${api.replace(/\/$/, "")}/settings/storefront`, { next: { revalidate: 300 } });
      if (response.ok) {
        const payload = await response.json(); const settings = payload.data || payload;
        title = settings.site_title || title; description = settings.site_description || description;
        if (settings.site_keywords) keywords = String(settings.site_keywords).split(",").map((keyword: string) => keyword.trim()).filter(Boolean);
      }
    } catch { /* Keep a useful static SEO fallback if Laravel is unavailable. */ }
  }
  return { metadataBase: new URL(siteUrl), title: { default: title, template: "%s | TR Sports Line" }, description, keywords, openGraph: { type: "website", siteName: "TR Sports Line", title, description } };
}

export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return <html lang="en" suppressHydrationWarning><body><ThemeProvider><TrackingPixels /><Navbar />{children}<Footer /><CartDrawer /></ThemeProvider></body></html>;
}
