import type { Metadata } from "next";
import Link from "next/link";

import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";

import styles from "./page.module.css";

export const metadata: Metadata = {
  title: "About Us | Consultant",
  description:
    "Learn more about our approach to international education, student guidance and studying abroad.",
};

const values = [
  {
    number: "01",
    title: "PERSONALISED",
    text: "Every student has a different ambition, academic background and destination. Our guidance starts with understanding yours.",
  },
  {
    number: "02",
    title: "CLEAR",
    text: "We simplify destinations, universities, courses and application steps so you can move forward with clarity.",
  },
  {
    number: "03",
    title: "STUDENT FOCUSED",
    text: "From your first conversation to preparing for your next chapter, our approach keeps your goals at the centre.",
  },
];

const process = [
  {
    number: "01",
    title: "DISCOVER",
    text: "Understand your goals, academic background and study ambitions.",
  },
  {
    number: "02",
    title: "PLAN",
    text: "Explore suitable destinations, universities and course options.",
  },
  {
    number: "03",
    title: "APPLY",
    text: "Move through the application journey with structured guidance.",
  },
  {
    number: "04",
    title: "PREPARE",
    text: "Get ready for the next stage of your international study journey.",
  },
];

const destinations = [
  "UNITED KINGDOM",
  "UNITED STATES",
  "CANADA",
  "AUSTRALIA",
  "GERMANY",
  "ITALY",
];

export default function AboutPage() {
  return (
    <>
      <Navbar />

      <main className={styles.page}>
        {/* =========================
            HERO
        ========================== */}
        <section className={styles.hero}>
          <div className={styles.heroImage}></div>
          <div className={styles.heroOverlay}></div>
          <div className={styles.heroGrid}></div>

          <div className={styles.heroLine}></div>

          <div className={styles.container}>
            <div className={styles.heroContent}>
              {/* BREADCRUMB */}
              <div className={styles.breadcrumb}>
                <Link href="/">HOME</Link>
                <span>/</span>
                <span>ABOUT</span>
              </div>

              <div className={styles.eyebrow}>
                <span className={styles.dot}></span>
                ABOUT US
              </div>

              <h1>
                YOUR JOURNEY.
                <br />
                <span>OUR GUIDANCE.</span>
              </h1>

              <p>
                We help students navigate the journey from choosing
                the right destination to taking their next step
                towards an international education.
              </p>
            </div>
          </div>

          <div className={styles.heroBottom}>
            <span>STUDY FURTHER. GO FURTHER.</span>

            <div className={styles.heroArrow}>↓</div>
          </div>
        </section>

        {/* =========================
            INTRO
        ========================== */}
        <section className={styles.introSection}>
          <div className={styles.container}>
            <div className={styles.introGrid}>
              <div className={styles.sectionLabel}>
                <span>01</span>
                WHO WE ARE
              </div>

              <div className={styles.introContent}>
                <h2>
                  MORE THAN
                  <br />
                  <span>AN APPLICATION.</span>
                </h2>

                <p className={styles.lead}>
                  Studying abroad is a major decision. It is about
                  choosing a destination, finding the right
                  university, selecting a course and preparing for
                  an entirely new chapter.
                </p>

                <p>
                  Our role is to make that journey easier to
                  understand. We provide structured guidance so
                  students can explore their options, make informed
                  decisions and move through the process with
                  greater confidence.
                </p>

                <Link
                  href="/study-destinations"
                  className={styles.textButton}
                >
                  EXPLORE DESTINATIONS
                  <span>↗</span>
                </Link>
              </div>
            </div>
          </div>
        </section>

        {/* =========================
            VALUES
        ========================== */}
        <section className={styles.valuesSection}>
          <div className={styles.sectionGlow}></div>

          <div className={styles.container}>
            <div className={styles.sectionHeading}>
              <div>
                <span className={styles.orangeEyebrow}>
                  WHAT MATTERS
                </span>

                <h2>
                  BUILT AROUND
                  <br />
                  <span>YOUR GOALS.</span>
                </h2>
              </div>

              <p>
                Our approach is designed around clarity,
                personal attention and helping students understand
                what comes next.
              </p>
            </div>

            <div className={styles.valuesGrid}>
              {values.map((value) => (
                <article
                  className={styles.valueCard}
                  key={value.number}
                >
                  <div className={styles.valueTop}>
                    <span>{value.number}</span>
                    <div className={styles.valueArrow}>↗</div>
                  </div>

                  <h3>{value.title}</h3>

                  <p>{value.text}</p>
                </article>
              ))}
            </div>
          </div>
        </section>

        {/* =========================
            TRUST / RATING STYLE
        ========================== */}
        <section className={styles.trustSection}>
          <div className={styles.container}>
            <div className={styles.trustBox}>
              <div className={styles.trustStars} aria-hidden="true">
                <span>★</span>
                <span>★</span>
                <span>★</span>
                <span>★</span>
                <span>★</span>
              </div>

              <div className={styles.trustRating}>
                <strong>STUDENT-FIRST</strong>
                <span>GUIDANCE</span>
              </div>

              <div className={styles.trustDivider}></div>

              <p>
                A professional approach focused on helping you
                understand your choices and take the next step
                with confidence.
              </p>

              <div className={styles.trustMark}>✦</div>
            </div>
          </div>
        </section>

        {/* =========================
            PROCESS
        ========================== */}
        <section className={styles.processSection}>
          <div className={styles.container}>
            <div className={styles.processHeader}>
              <div>
                <span className={styles.orangeEyebrow}>
                  OUR APPROACH
                </span>

                <h2>
                  FROM IDEA
                  <br />
                  <span>TO ACTION.</span>
                </h2>
              </div>

              <p>
                A simple, structured approach that keeps the
                journey clear from the first conversation to the
                next chapter.
              </p>
            </div>

            <div className={styles.processList}>
              {process.map((item) => (
                <div
                  className={styles.processItem}
                  key={item.number}
                >
                  <span className={styles.processNumber}>
                    {item.number}
                  </span>

                  <h3>{item.title}</h3>

                  <p>{item.text}</p>

                  <span className={styles.processIcon}>↗</span>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* =========================
            DESTINATIONS
        ========================== */}
        <section className={styles.destinationSection}>
          <div className={styles.container}>
            <div className={styles.destinationHeader}>
              <span className={styles.orangeEyebrow}>
                WHERE CAN YOU GO?
              </span>

              <h2>
                YOUR WORLD
                <br />
                <span>STARTS HERE.</span>
              </h2>

              <p>
                Explore international study destinations and
                discover where your next academic chapter could
                take you.
              </p>
            </div>

            <div className={styles.destinationList}>
              {destinations.map((destination, index) => (
                <Link
                  href="/study-destinations"
                  className={styles.destinationItem}
                  key={destination}
                >
                  <span>
                    {String(index + 1).padStart(2, "0")}
                  </span>

                  <strong>{destination}</strong>

                  <i>↗</i>
                </Link>
              ))}
            </div>
          </div>
        </section>

        {/* =========================
            FINAL CTA
        ========================== */}
        <section className={styles.ctaSection}>
          <div className={styles.ctaGlow}></div>
          <div className={styles.ctaGrid}></div>

          <div className={styles.container}>
            <div className={styles.ctaContent}>
              <div className={styles.ctaEyebrow}>
                READY WHEN YOU ARE
              </div>

              <h2>
                YOUR NEXT
                <br />
                <span>CHAPTER AWAITS.</span>
              </h2>

              <p>
                Have a destination in mind or still exploring your
                options? Start the conversation and let&apos;s work
                out your next step together.
              </p>

              <div className={styles.ctaActions}>
                <Link
                  href="/#contact"
                  className={styles.primaryButton}
                >
                  START A CONVERSATION
                  <span>↗</span>
                </Link>

                <Link
                  href="/courses"
                  className={styles.secondaryButton}
                >
                  EXPLORE COURSES
                  <span>↗</span>
                </Link>
              </div>
            </div>
          </div>

          <div className={styles.ctaBottom}>
            <span>CONSULTANT</span>
            <span>STUDY FURTHER. GO FURTHER.</span>
          </div>
        </section>
      </main>

      <Footer />
    </>
  );
}