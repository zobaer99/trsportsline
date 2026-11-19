import Link from "next/link";
const categories = [["All kits", "/#shop"], ["Player Edition", "/category/player-edition"], ["Fan Edition", "/category/fan-edition"], ["Custom Team Jerseys", "/category/custom-team-jerseys"], ["Retro Classics", "/category/retro-classics"], ["Sports Gear", "/category/sports-gear"]];
export default function CategoryPills() { return <div className="category-scroll">{categories.map(([label, href], index) => <Link key={label} href={href} className={`pill ${index === 0 ? "active" : "hover:border-lime hover:text-lime"}`}>{label}</Link>)}</div>; }
