import type { Config } from "tailwindcss";

export default {
  content: ["./app/**/*.{ts,tsx}", "./components/**/*.{ts,tsx}"],
  theme: { extend: { colors: { ink: "#0b0b0c", panel: "#161618", lime: "#ccff00", chalk: "#f4f4f1" }, fontFamily: { sans: ["var(--font-body)"], display: ["var(--font-display)"] } } },
  plugins: [],
} satisfies Config;
