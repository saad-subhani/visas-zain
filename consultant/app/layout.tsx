
import type { Metadata } from "next";
import { Archivo, Sora } from "next/font/google";
import "./globals.css";

const archivo = Archivo({
  variable: "--font-archivo",
  subsets: ["latin"],
  display: "swap",
});

const sora = Sora({
  variable: "--font-sora",
  subsets: ["latin"],
  display: "swap",
});

export const metadata: Metadata = {
  title: "Consultant | Study Further, Go Further",
  description:
    "Professional education and global consultancy services for students planning their future abroad.",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body
        className={`${archivo.variable} ${sora.variable}`}
        suppressHydrationWarning
      >
        {children}
      </body>
    </html>
  );
}

