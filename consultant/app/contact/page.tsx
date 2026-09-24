import type { Metadata } from "next";
import Link from "next/link";

import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";

import styles from "./page.module.css";

export const metadata: Metadata = {
  title: "Contact Us | Consultant",
  description:
    "Get in touch with our international education consultants and take the next step towards studying abroad.",
};

export default function ContactPage() {
  return (
    <>
      <Navbar />

      <main className={styles.page}>
        {/* =========================
            HERO
        ========================== */}
        <section className={styles.hero}>
          <div className={styles.gridBackground}></div>

          <div className={`${styles.orb} ${styles.orbOne}`}></div>
          <div className={`${styles.orb} ${styles.orbTwo}`}></div>
          <div className={`${styles.orb} ${styles.orbThree}`}></div>

          <div className={styles.heroLines}>
            <span></span>
            <span></span>
            <span></span>
          </div>

          <div className={styles.container}>
            <div className={styles.heroContent}>
              <div className={styles.eyebrow}>
                <span className={styles.eyebrowDot}></span>
                LET'S TALK
              </div>

              <h1>
                YOUR NEXT
                <br />
                <span>MOVE STARTS HERE.</span>
              </h1>

              <p>
                Whether you are choosing a destination, exploring
                universities or planning your next academic step,
                our team is here to help you move forward with
                confidence.
              </p>

              <div className={styles.heroMeta}>
                <div>
                  <span>01</span>
                  <strong>PERSONALISED</strong>
                </div>

                <div>
                  <span>02</span>
                  <strong>PROFESSIONAL</strong>
                </div>

                <div>
                  <span>03</span>
                  <strong>STUDENT FOCUSED</strong>
                </div>
              </div>
            </div>
          </div>

          <div className={styles.scrollIndicator}>
            <span>SCROLL TO CONNECT</span>
            <i></i>
          </div>
        </section>

        {/* =========================
            CONTACT SECTION
        ========================== */}
        <section className={styles.contactSection}>
          <div className={styles.sectionGlow}></div>

          <div className={styles.container}>
            <div className={styles.contactLayout}>
              {/* LEFT */}
              <div className={styles.contactInfo}>
                <div className={styles.sectionEyebrow}>
                  GET IN TOUCH
                </div>

                <h2>
                  LET'S TURN
                  <br />
                  <span>YOUR PLANS</span>
                  <br />
                  INTO A PATH.
                </h2>

                <p className={styles.intro}>
                  Have a question about studying abroad? Tell us
                  what you are looking for and our team will help
                  you understand your options.
                </p>

                <div className={styles.infoList}>
                  <div className={styles.infoItem}>
                    <div className={styles.infoNumber}>01</div>

                    <div>
                      <span>EMAIL US</span>
                      <a href="mailto:hello@consultant.com">
                        hello@consultant.com
                      </a>
                    </div>
                  </div>

                  <div className={styles.infoItem}>
                    <div className={styles.infoNumber}>02</div>

                    <div>
                      <span>CALL US</span>
                      <a href="tel:+920000000000">
                        +92 000 0000000
                      </a>
                    </div>
                  </div>

                  <div className={styles.infoItem}>
                    <div className={styles.infoNumber}>03</div>

                    <div>
                      <span>OFFICE HOURS</span>
                      <p>
                        Monday — Friday
                        <br />
                        09:00 AM — 06:00 PM
                      </p>
                    </div>
                  </div>
                </div>

                <Link
                  href="/study-destinations"
                  className={styles.destinationLink}
                >
                  EXPLORE DESTINATIONS
                  <span>↗</span>
                </Link>
              </div>

              {/* RIGHT FORM */}
              <div className={styles.formWrapper}>
                <div className={styles.formTop}>
                  <div>
                    <span className={styles.formEyebrow}>
                      CONTACT FORM
                    </span>

                    <h3>
                      START A
                      <br />
                      CONVERSATION.
                    </h3>
                  </div>

                  <div className={styles.formMark}>↗</div>
                </div>

                <form
                  className={styles.form}
                  action="http://localhost/realCMS/api/contact.php"
                  method="POST"
                >
                  <div className={styles.inputRow}>
                    <div className={styles.inputGroup}>
                      <label htmlFor="name">
                        FULL NAME
                      </label>

                      <input
                        id="name"
                        name="name"
                        type="text"
                        placeholder="Your full name"
                        required
                      />

                      <span className={styles.inputLine}></span>
                    </div>

                    <div className={styles.inputGroup}>
                      <label htmlFor="email">
                        EMAIL ADDRESS
                      </label>

                      <input
                        id="email"
                        name="email"
                        type="email"
                        placeholder="you@example.com"
                        required
                      />

                      <span className={styles.inputLine}></span>
                    </div>
                  </div>

                  <div className={styles.inputRow}>
                    <div className={styles.inputGroup}>
                      <label htmlFor="phone">
                        PHONE NUMBER
                      </label>

                      <input
                        id="phone"
                        name="phone"
                        type="tel"
                        placeholder="+92 000 0000000"
                      />

                      <span className={styles.inputLine}></span>
                    </div>

                    <div className={styles.inputGroup}>
                      <label htmlFor="subject">
                        SUBJECT
                      </label>

                      <select
                        id="subject"
                        name="subject"
                        defaultValue=""
                        required
                      >
                        <option value="" disabled>
                          Select a subject
                        </option>

                        <option value="study-destinations">
                          Study Destinations
                        </option>

                        <option value="universities">
                          Universities
                        </option>

                        <option value="scholarships">
                          Scholarships
                        </option>

                        <option value="courses">
                          Courses
                        </option>

                        <option value="general">
                          General Enquiry
                        </option>
                      </select>

                      <span className={styles.inputLine}></span>
                    </div>
                  </div>

                  <div
                    className={`${styles.inputGroup} ${styles.messageGroup}`}
                  >
                    <label htmlFor="message">
                      YOUR MESSAGE
                    </label>

                    <textarea
                      id="message"
                      name="message"
                      rows={5}
                      placeholder="Tell us a little about what you are looking for..."
                      required
                    ></textarea>

                    <span className={styles.inputLine}></span>
                  </div>

                  <div className={styles.formBottom}>
                    <p>
                      By submitting this form, you agree to be
                      contacted by our team regarding your enquiry.
                    </p>

                    <button
                      type="submit"
                      className={styles.submitButton}
                    >
                      SEND MESSAGE
                      <span>↗</span>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </section>

        {/* =========================
            BOTTOM CTA
        ========================== */}
        <section className={styles.bottomCta}>
          <div className={styles.ctaGlow}></div>

          <div className={styles.container}>
            <div className={styles.ctaInner}>
              <span>
                YOUR JOURNEY STARTS WITH ONE CONVERSATION.
              </span>

              <h2>
                READY TO GO
                <br />
                <strong>FURTHER?</strong>
              </h2>

              <Link
                href="/courses"
                className={styles.ctaButton}
              >
                EXPLORE COURSES
                <span>↗</span>
              </Link>
            </div>
          </div>
        </section>
      </main>

      <Footer />
    </>
  );
}