"use client";

import { Moon, Sun } from "lucide-react";
import { useTheme } from "@/components/ThemeProvider";

export default function ThemeToggle({ compact = false }: { compact?: boolean }) {
  const { theme, toggleTheme } = useTheme();
  const nextTheme = theme === "dark" ? "light" : "dark";
  const Icon = nextTheme === "light" ? Sun : Moon;

  return (
    <button
      type="button"
      onClick={toggleTheme}
      aria-label={`Switch to ${nextTheme} theme`}
      aria-pressed={theme === "light"}
      title={`Switch to ${nextTheme} theme`}
      className={`theme-toggle ${compact ? "theme-toggle-compact" : ""}`}
    >
      <Icon size={16} aria-hidden="true" />
      {!compact && <span>{theme === "dark" ? "Light" : "Dark"}</span>}
    </button>
  );
}
