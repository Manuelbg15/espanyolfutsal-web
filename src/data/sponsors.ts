// ─────────────────────────────────────────────────────────────
//  Patrocinadors i mitjans de comunicació del club.
//  Els logos són a /public/images/sponsors/. Si n'afegeixes un,
//  posa'l aquí i sortirà a la portada i a la pàgina d'El Club.
// ─────────────────────────────────────────────────────────────
export interface Sponsor {
  name: string;
  desc: string;
  url: string;
  img: string;
}

export const patrocinadors: Sponsor[] = [
  { name: "Edelweiss", desc: "Residència & Centre de Dia", url: "https://residenciasedelweiss.com/", img: "/images/sponsors/edelweiss.png" },
  { name: "NYO Energy", desc: "Energia Intel·ligent", url: "https://nyoenergy.es/", img: "/images/sponsors/nyo-energy.png" },
  { name: "LMA", desc: "Gestió i Assessoria", url: "https://www.lmatia.es/", img: "/images/sponsors/lma.png" },
  { name: "Fortius", desc: "Sports Management", url: "https://www.fortiusports.com/", img: "/images/sponsors/fortius.png" },
  { name: "Repcar Vallès", desc: "Serveis d'Automoció", url: "https://www.repcar.net/", img: "/images/sponsors/repcar-valles.png" },
];

export const mitjans: Sponsor[] = [
  { name: "Pericos Marca", desc: "Actualitat Perica", url: "https://pericos.es/", img: "/images/sponsors/pericos-marca.png" },
  { name: "Futbol IN!", desc: "Seguiment Futsal", url: "https://x.com/futbolin1900", img: "/images/sponsors/futbol-in.png" },
  { name: "La Grada", desc: "Diari Espanyolista", url: "https://lagrada.org/", img: "/images/sponsors/la-grada.png" },
  { name: "El 1900", desc: "Passió Blanc-i-Blava", url: "https://el1900.com/", img: "/images/sponsors/el-1900.png" },
];
