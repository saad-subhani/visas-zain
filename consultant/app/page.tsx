"use client";

import { useEffect, useState } from "react";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { GB, US, CA, AU } from "country-flag-icons/react/3x2";
import ContactFormSection from "@/components/ContactFormSection";

const typingWords = [
  "OUR GUIDANCE.",
  "YOUR SUCCESS.",
  "YOUR FUTURE.",
];

const destinations = [
  {
    country: "United Kingdom",
    code: "UK",
    number: "01",
    flag: GB,
    text: "Study at world-class universities and gain a globally recognised education.",
  },
  {
    country: "United States",
    code: "US",
    number: "02",
    flag: US,
    text: "Explore leading universities, diverse programs and global career opportunities.",
  },
  {
    country: "Canada",
    code: "CA",
    number: "03",
    flag: CA,
    text: "Build your future through quality education and a welcoming study environment.",
  },
  {
    country: "Australia",
    code: "AU",
    number: "04",
    flag: AU,
    text: "Experience innovative education, practical learning and international opportunities.",
  },
];

const services = [
  {
    number: "01",
    title: "University Selection",
    image: "/images/services/study.jpeg",
    text: "Find institutions and programs aligned with your academic and career goals.",
  },
  {
    number: "02",
    title: "Application Support",
    image: "/images/services/guide.jpeg",
    text: "Get structured guidance throughout your university application journey.",
  },
  {
    number: "03",
    title: "Visa Guidance",
    image: "/images/services/vissa.jpg",
    text: "Understand your visa requirements and prepare your documentation correctly.",
  },
  {
    number: "04",
    title: "Career Guidance",
    image: "/images/services/careerguide.jpg",
    text: "Make informed decisions about your education and long-term career direction.",
  },
  {
    number: "05",
    title: "Scholarship Guidance",
    image: "/images/services/stds.jpeg",
    text: "Explore scholarship opportunities that may match your academic profile.",
  },
  {
    number: "06",
    title: "Pre-Departure Support",
    image: "/images/services/bgplane.jpeg",
    text: "Get practical guidance before starting your international journey.",
  },
];

const typingPhrases = [
  "Beyond Borders.",
  "Global Dreams.",
  "New Horizons.",
  "Brighter Futures.",
];

export default function Home() {
  const [typedText, setTypedText] = useState("");
  const [wordIndex, setWordIndex] = useState(0);
  const [isTypingDeleting, setIsTypingDeleting] = useState(false);

  const [phraseIndex, setPhraseIndex] = useState(0);
  const [displayText, setDisplayText] = useState("");
  const [isDeleting, setIsDeleting] = useState(false);

  // HERO TYPEWRITER
  useEffect(() => {
    const currentWord = typingWords[wordIndex];

    let delay = isTypingDeleting ? 55 : 100;

    if (!isTypingDeleting && typedText === currentWord) {
      delay = 1200;
    }

    const timer = setTimeout(() => {
      if (!isTypingDeleting && typedText === currentWord) {
        setIsTypingDeleting(true);
        return;
      }

      if (isTypingDeleting && typedText === "") {
        setIsTypingDeleting(false);
        setWordIndex(
          (previous) =>
            (previous + 1) % typingWords.length
        );
        return;
      }

      const nextLength = isTypingDeleting
        ? typedText.length - 1
        : typedText.length + 1;

      setTypedText(currentWord.slice(0, nextLength));
    }, delay);

    return () => clearTimeout(timer);
  }, [typedText, isTypingDeleting, wordIndex]);

  // EXISTING PHRASE TYPEWRITER
  useEffect(() => {
    const currentPhrase = typingPhrases[phraseIndex];

    const typingSpeed = isDeleting ? 45 : 85;

    const timer = setTimeout(() => {
      if (!isDeleting) {
        const nextText = currentPhrase.slice(
          0,
          displayText.length + 1
        );

        setDisplayText(nextText);

        if (nextText === currentPhrase) {
          setIsDeleting(true);
        }
      } else {
        const nextText = currentPhrase.slice(
          0,
          displayText.length - 1
        );

        setDisplayText(nextText);

        if (nextText === "") {
          setIsDeleting(false);
          setPhraseIndex(
            (previous) =>
              (previous + 1) % typingPhrases.length
          );
        }
      }
    }, typingSpeed);

    return () => clearTimeout(timer);
  }, [displayText, isDeleting, phraseIndex]);

  return (
    <>
      <Navbar />

      <main>
        {/* =========================
            HERO
        ========================== */}

        <section
          className="consultant-hero"
          onMouseMove={(e) => {
            const rect =
              e.currentTarget.getBoundingClientRect();

            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const moveX =
              (x / rect.width - 0.5) * 12;

            const moveY =
              (y / rect.height - 0.5) * 12;

            const glowX =
              (x / rect.width) * 100;

            const glowY =
              (y / rect.height) * 100;

            e.currentTarget.style.setProperty(
              "--hero-x",
              `${moveX}px`
            );

            e.currentTarget.style.setProperty(
              "--hero-y",
              `${moveY}px`
            );

            e.currentTarget.style.setProperty(
              "--glow-x",
              `${glowX}%`
            );

            e.currentTarget.style.setProperty(
              "--glow-y",
              `${glowY}%`
            );
          }}
        >
          <video
  className="consultant-hero-video"
  src="/hero.mp4"
  autoPlay
  muted
  loop
  playsInline
/>

          <div className="consultant-hero-overlay" />

          <div className="consultant-hero-glow" />

          <div className="container consultant-hero-inner">
            <div className="consultant-hero-content">

              <div className="consultant-hero-kicker">
                GLOBAL EDUCATION CONSULTANCY
              </div>

              <h1>
                YOUR FUTURE.
                <br />

                <span className="consultant-typing">
                  {typedText}
                </span>

                <br />

                LIMITLESS
                <br />

                POSSIBILITIES.
              </h1>

              <p>
                We help ambitious students navigate
                international education with clear guidance,
                confident decisions and a pathway built
                around their future.
              </p>

              <a
                href="#contact"
                className="consultant-hero-button"
              >
                START YOUR JOURNEY
                <span>↗</span>
              </a>

            </div>
          </div>

          <div className="consultant-hero-bottom">
            <span>STUDY • EXPLORE • GROW</span>

            <div className="consultant-hero-line">
              <span />
            </div>

            <span>SCROLL TO EXPLORE</span>
          </div>
        </section>

        {/* =========================
            DESTINATIONS
        ========================== */}

        <section
          className="section"
          id="destinations"
        >
          <div className="container">
            <div className="section-heading">
              <div>
                <div className="section-kicker">
                  Where can you go?
                </div>

                <h2>
                  Choose a destination.
                  <br />
                  Build your future.
                </h2>
              </div>

              <p>
                Explore popular study destinations and
                discover opportunities designed around
                your ambitions.
              </p>
            </div>

            <div className="destination-grid">
              {destinations.map((destination) => {
                const Flag = destination.flag;

                return (
                  <a
                    href="/study-destinations"
                    className="destination-card"
                    key={destination.country}
                  >
                    <div className="destination-card-top">
                      <span>{destination.number}</span>
                      <span>{destination.code}</span>
                    </div>

                    <div className="destination-card-body">
                      <div className="flag-box">
                        <Flag className="country-flag" />
                      </div>

                      <h3>{destination.country}</h3>

                      <p>{destination.text}</p>
                    </div>

                    <div className="destination-card-bottom">
                      <span>
                        EXPLORE DESTINATION
                      </span>

                      <span>↗</span>
                    </div>
                  </a>
                );
              })}
            </div>
          </div>
        </section>

        {/* =========================
            SERVICES
        ========================== */}

        <section
          className="section section-dark"
          id="services"
        >
          <div className="container">
            <div className="section-heading">
              <div>
                <div className="section-kicker">
                  What we do
                </div>

                <h2>
                  Guidance at every
                  <br />
                  step of your journey.
                </h2>
              </div>

              <p>
                From your first consultation to your
                departure, our services are designed to
                make every important decision clearer.
              </p>
            </div>

            <div className="services-grid">
              {services.map((service) => (
                <div
                  className="service-card"
                  key={service.number}
                >
                  <div className="service-image">
                    <img
                      src={service.image}
                      alt={service.title}
                    />

                    <div className="service-image-overlay" />
                  </div>

                  <div className="service-card-content">
                    <div className="service-card-top">
                      <span>{service.number}</span>

                      <span className="service-arrow">
                        ↗
                      </span>
                    </div>

                    <h3>{service.title}</h3>

                    <p>{service.text}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* =========================
            ABOUT
        ========================== */}

        <section
          className="section"
          id="about"
        >
          <div className="container">
            <div className="about-layout">

              <div className="about-label">
                <span>01</span>
                <span>OUR APPROACH</span>
              </div>

              <div className="about-content">
                <h2>
                  Decisions become
                  <br />
                  easier with the
                  <br />
                  <span>right guidance.</span>
                </h2>

                <p className="about-lead">
                  Personal guidance. Global opportunities.
                </p>

                <p>
                  International education involves important
                  decisions. We help you understand your
                  options, plan your next step and move
                  forward with confidence.
                </p>

                <div className="about-points">
                  <div>
                    <span>01</span>
                    <strong>
                      Personalised Guidance
                    </strong>
                  </div>

                  <div>
                    <span>02</span>
                    <strong>
                      Clear Application Process
                    </strong>
                  </div>

                  <div>
                    <span>03</span>
                    <strong>
                      Global Opportunities
                    </strong>
                  </div>

                  <div>
                    <span>04</span>
                    <strong>
                      Student-Focused Support
                    </strong>
                  </div>
                </div>
              </div>

              <div className="about-visual">
                <div className="about-visual-main">
                  <span className="about-visual-index">
                    CONSULT / 01
                  </span>

                  <div className="about-visual-circle">
                    <video
                      className="about-global-video"
                      src="/global.mp4"
                      autoPlay
                      muted
                      loop
                      playsInline
                    />
                  </div>

                  <span className="about-visual-caption">
                    YOUR FUTURE / YOUR WAY
                  </span>
                </div>
              </div>

            </div>
          </div>
        </section>

        {/* =========================
    CONTACT FORM
========================== */}

<ContactFormSection compact />
{/* =========================
    PRE-FOOTER CTA
========================== */}

<section
  className="prefooter-section"
  id="universities"
>
  <div className="prefooter-inner">

    <div className="prefooter-content">

      <div className="prefooter-kicker">
        START YOUR JOURNEY
      </div>

      <h2>
        Ready to build
        <br />
        your <span>future?</span>
      </h2>

      <p>
        Your international education journey starts with
        the right guidance. Let&apos;s turn your ambitions
        into a clear path forward.
      </p>

      <a
        href="#contact"
        className="prefooter-button"
      >
        BOOK A FREE CONSULTATION
        <span>↗</span>
      </a>

    </div>

    <div className="prefooter-testimonial">

      <div className="prefooter-stars">
        ★★★★★
      </div>

      <blockquote>
        “Professional guidance, clear advice and support
        at every step made my study abroad journey much
        easier and more confident.”
      </blockquote>

      <div className="prefooter-reviewer">
        <span className="reviewer-initial">
          S
        </span>

        <div>
          <strong>
            Satisfied Student
          </strong>

          <span>
            International Education Journey
          </span>
        </div>
      </div>

    </div>

  </div>
</section>
      </main>

      <Footer />
    </>
  );
}