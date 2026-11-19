import path from "node:path";
import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  images: { remotePatterns: [{ protocol: "https", hostname: "images.unsplash.com" }] },
  experimental: { optimizePackageImports: ["lucide-react"] },
  webpack(config) {
    config.resolve.alias = {
      ...config.resolve.alias,
      "@": path.resolve(process.cwd()),
    };
    return config;
  },
};

export default nextConfig;
