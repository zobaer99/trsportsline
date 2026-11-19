"use client";

import { create } from "zustand";
import { persist } from "zustand/middleware";
import type { Product } from "@/lib/catalog";

export type CartItem = { id: string; product: Product; size: string; edition: string; name?: string; number?: string; patches: string[]; unitPrice: number; quantity: number };
export type Customization = { edition: string; size: string; name: string; number: string; patches: string[] };
type Store = { items: CartItem[]; customizations: Record<string, Customization>; cartOpen: boolean; addItem: (item: CartItem) => void; removeItem: (id: string) => void; updateQuantity: (id: string, quantity: number) => void; setCartOpen: (open: boolean) => void; setCustomization: (slug: string, value: Customization) => void; clearCart: () => void };

export const useShopStore = create<Store>()(persist((set) => ({
  items: [], customizations: {}, cartOpen: false,
  addItem: (item) => set((state) => ({ items: [...state.items, item], cartOpen: true })),
  removeItem: (id) => set((state) => ({ items: state.items.filter((item) => item.id !== id) })),
  updateQuantity: (id, quantity) => set((state) => ({ items: state.items.map((item) => item.id === id ? { ...item, quantity: Math.max(1, quantity) } : item) })),
  setCartOpen: (cartOpen) => set({ cartOpen }),
  setCustomization: (slug, value) => set((state) => ({ customizations: { ...state.customizations, [slug]: value } })),
  clearCart: () => set({ items: [] }),
}), { name: "tr-sports-line-cart", partialize: (state) => ({ items: state.items, customizations: state.customizations }) as Store }));

export const cartCount = (items: CartItem[]) => items.reduce((count, item) => count + item.quantity, 0);
export const cartTotal = (items: CartItem[]) => items.reduce((total, item) => total + item.unitPrice * item.quantity, 0);
