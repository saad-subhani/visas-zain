"use client";

import { useState } from "react";
import Link from "next/link";

export default function Navbar() {
  const [menuOpen, setMenuOpen] = useState(false);

  const closeMenu = () => setMenuOpen(false);

  return (
    <header className="navbar">
      <div className="container navbar-inner">
        <Link href="/" className="logo" onClick={closeMenu}>
          <img
            src="/newlogo2.jpeg"
            alt="Consult"
            className="logo-image"
          />
        </Link>

        <nav className={`nav-links ${menuOpen ? "mobile-open" : ""}`}>
          <Link href="/" className="nav-link" onClick={closeMenu}>
            Home
          </Link>

          <Link
            href="/study-destinations"
            className="nav-link"
            onClick={closeMenu}
          >
            Destinations
          </Link>

          <Link
            href="/universities"
            className="nav-link"
            onClick={closeMenu}
          >
            Universities
          </Link>

          <Link
  href="/scholarships"
  className="nav-link"
  onClick={closeMenu}
>
  Scholarships
</Link>

          <Link
            href="/courses"
            className="nav-link"
            onClick={closeMenu}
          >
           Courses
          </Link>

          <Link
            href="/about"
            className="nav-link"
            onClick={closeMenu}
          >
            About us
          </Link>

          <Link
            href="/contact"
            className="nav-cta"
            onClick={closeMenu}
          >
            Contact us
            <span>↗</span>
          </Link>
        </nav>

        <button
          type="button"
          className="menu-toggle"
          onClick={() => setMenuOpen((prev) => !prev)}
          aria-label="Toggle navigation"
          aria-expanded={menuOpen}
        >
          {menuOpen ? "✕" : "☰"}
        </button>
      </div>
    </header>
  );
}