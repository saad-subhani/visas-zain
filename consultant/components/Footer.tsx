import Link from "next/link";

export default function Footer() {
  return (
    <footer className="footer" id="contact">
      <div className="container">
        <div className="footer-grid">
          <div className="footer-brand">
            <Link href="/" className="logo">
              <span className="logo-mark"></span>
              <span>
                FORRIGN STUDY <strong>CONSULTANTS</strong>
              </span>
            </Link>

            <p>
              Helping students turn international education goals into
              clear, confident and achievable plans for their future.
            </p>
          </div>

          <div>
            <h4>Explore</h4>

            <div className="footer-links">
              <Link href="/">Home</Link>

              <Link href="/study-destinations">
                Destinations
              </Link>

              <Link href="/universities">
                Universities
              </Link>

              <Link href="/scholarships">
                Scholarships
              </Link>
            </div>
          </div>

          <div>
            <h4>Destinations</h4>

            <div className="footer-links">
              <Link href="/study-destinations/united-kingdom">
                United Kingdom
              </Link>

              <Link href="/study-destinations/united-states">
                United States
              </Link>

              <Link href="/study-destinations/canada">
                Canada
              </Link>

              <Link href="/study-destinations/australia">
                Australia
              </Link>
            </div>
          </div>

          <div>
            <h4>Contact</h4>

            <div className="footer-links">
              <a href="mailto:hello@consultant.com">
                hello@consultant.com
              </a>

              <a href="tel:+920000000000">
                +92 000 0000000
              </a>

              <Link href="/contact">
                Book Consultation
              </Link>

              <Link href="/contact">
                Free Assessment
              </Link>
            </div>
          </div>
        </div>

        <div className="footer-bottom">
          <span>
            © {new Date().getFullYear()} Consultant. All rights reserved.
          </span>

          <span>
            Built for <strong>global ambitions.</strong>
          </span>
        </div>
      </div>
    </footer>
  );
}