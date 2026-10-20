// ─────────────────────────────────────────────────────────────
//  Notícies de la web: les del CMS (cms.dragonrcnc.com) més les
//  locals de src/content/actualitat. Es llegeixen en fer el build,
//  així que el CMS ha d'avisar GitHub (cms-update) per republicar.
// ─────────────────────────────────────────────────────────────
import { getCollection, type CollectionEntry } from "astro:content";

const CMS_URL = import.meta.env.CMS_URL || process.env.CMS_URL || "https://cms.dragonrcnc.com";
// Origen que el CMS té autoritzat per a la clau d'aquesta web
const CMS_ORIGIN = import.meta.env.CMS_ORIGIN || process.env.CMS_ORIGIN || "https://espanyolfutsal.es";

export type Lang = "ca" | "es";

export interface Noticia {
  slug: string;
  title: string;
  description: string;
  date: Date;
  image?: string;
  /** Contingut HTML (notícies del CMS) */
  html?: string;
  /** Entrada markdown (notícies locals) */
  entry?: CollectionEntry<"actualitat">;
}

interface CmsNoticia extends Omit<Noticia, "entry"> {
  lang?: Lang;
}

function toLang(value: unknown): Lang | undefined {
  const v = String(value ?? "").toLowerCase();
  if (v === "es") return "es";
  if (v === "ca" || v === "cat") return "ca";
  return undefined;
}

function slugify(text: string): string {
  return text
    .normalize("NFD")
    .replace(/[̀-ͯ]/g, "")
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "");
}

let cmsCache: Promise<CmsNoticia[]> | undefined;

function fetchCms(): Promise<CmsNoticia[]> {
  cmsCache ??= (async () => {
    const apiKey = import.meta.env.CMS_API_KEY || process.env.CMS_API_KEY;
    if (!apiKey) {
      console.warn("[CMS] Sense CMS_API_KEY: només es mostren les notícies locals.");
      return [];
    }
    try {
      const res = await fetch(`${CMS_URL}/api/noticias`, {
        headers: { "X-API-KEY": apiKey, Origin: CMS_ORIGIN, Accept: "application/json" },
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();
      if (!Array.isArray(data)) throw new Error("la resposta no és una llista");

      const items = data.map((n: any): CmsNoticia => {
        const image = n.imageUrl ? `${CMS_URL}${n.imageUrl}` : n.image || undefined;
        return {
          slug: slugify(String(n.slug || n.title || n.id)) || String(n.id),
          title: n.title,
          description: n.description || n.title,
          date: new Date(n.publishDate || n.publishedAt || Date.now()),
          image,
          html: n.content || "",
          lang: toLang(n.language),
        };
      });
      console.log(`[CMS] Notícies obtingudes: ${items.length}`);
      return items;
    } catch (error) {
      // Si el CMS falla, la web es publica igualment amb les notícies locals
      console.error("[CMS] Error obtenint notícies:", error);
      return [];
    }
  })();
  return cmsCache;
}

/** Notícies d'un idioma, de la més nova a la més antiga. */
export async function getNoticias(lang: Lang): Promise<Noticia[]> {
  const local: Noticia[] = (await getCollection("actualitat")).map((entry) => ({
    slug: entry.slug,
    title: entry.data.title,
    description: entry.data.description,
    date: entry.data.pubDate,
    image: entry.data.heroImage,
    entry,
  }));

  // Les notícies del CMS sense idioma es mostren en els dos
  const cms = (await fetchCms()).filter((n) => !n.lang || n.lang === lang);

  const used = new Set(local.map((n) => n.slug));
  const fromCms: Noticia[] = cms.map(({ lang: _lang, ...n }) => {
    let slug = n.slug;
    for (let i = 2; used.has(slug); i++) slug = `${n.slug}-${i}`;
    used.add(slug);
    return { ...n, slug };
  });

  return [...local, ...fromCms].sort((a, b) => b.date.valueOf() - a.date.valueOf());
}
