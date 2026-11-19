"use client";

import { createContext, useContext, useEffect, useMemo, useState, type ReactNode } from "react";

export type Theme = "light" | "dark";

type ThemeContextValue = { theme: Theme; toggleTheme: () => void };
const ThemeContext = createContext<ThemeContextValue | null>(null);

export function ThemeProvider({ children }: { children: ReactNode }) {
  const [theme, setTheme] = useState<Theme>("dark");

  useEffect(() => {
    const saved = window.localStorage.getItem("tr-sports-theme");
    const initial: Theme = saved === "light" || saved === "dark"
      ? saved
      : window.location.pathname.startsWith("/admin") ? "light" : "dark";
    setTheme(initial);
    document.documentElement.dataset.theme = initial;
  }, []);

  const value = useMemo(() => ({
    theme,
    toggleTheme: () => {
      const next: Theme = theme === "dark" ? "light" : "dark";
      setTheme(next);
      document.documentElement.dataset.theme = next;
      window.localStorage.setItem("tr-sports-theme", next);
    },
  }), [theme]);

  return <ThemeContext.Provider value={value}>{children}</ThemeContext.Provider>;
}

export function useTheme() {
  const context = useContext(ThemeContext);
  if (!context) throw new Error("useTheme must be used inside ThemeProvider");
  return context;
}
