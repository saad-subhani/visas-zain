"use client";

import Link from "next/link";
import { FormEvent, useState } from "react";

import styles from "./ContactFormSection.module.css";

type ContactFormSectionProps = {
  compact?: boolean;
};

type FormStatus = {
  type: "success" | "error" | "";
  message: string;
};

export default function ContactFormSection({
  compact = false,
}: ContactFormSectionProps) {
  const [isSubmitting, setIsSubmitting] = useState(false);

  const [formStatus, setFormStatus] = useState<FormStatus>({
    type: "",
    message: "",
  });

  const handleSubmit = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    if (isSubmitting) {
      return;
    }

    setIsSubmitting(true);

    setFormStatus({
      type: "",
      message: "",
    });

    const form = event.currentTarget;
    const formData = new FormData(form);

    const data = {
      name: String(formData.get("name") || "").trim(),
      email: String(formData.get("email") || "").trim(),
      phone: String(formData.get("phone") || "").trim(),
      subject: String(formData.get("subject") || "").trim(),
      message: String(formData.get("message") || "").trim(),
    };

    try {
      const response = await fetch(
        "http://localhost/realCMS/api/contact.php",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(data),
        }
      );

      const result = await response.json();

      if (!response.ok || !result.success) {
        throw new Error(
          result.message ||
            "Unable to send your message. Please try again."
        );
      }

      setFormStatus({
        type: "success",
        message:
          result.message ||
          "Your message has been sent successfully.",
      });

      form.reset();

    } catch (error) {

      setFormStatus({
        type: "error",
        message:
          error instanceof Error
            ? error.message
            : "Unable to send your message. Please try again.",
      });

    } finally {

      setIsSubmitting(false);
    }
  };

  return (
    <section
      className={`${styles.contactSection} ${
        compact ? styles.compact : ""
      }`}
      id="contact"
    >
      <div className={styles.sectionGlow}></div>

      <div className={styles.container}>
        <div className={styles.contactLayout}>
          {/* LEFT CONTENT */}
          <div className={styles.contactInfo}>
            <div className={styles.sectionEyebrow}>
              HAVE ANY QUESTIONS?
            </div>

            <h2>
              LET&apos;S START
              <br />
              <span>A CONVERSATION.</span>
            </h2>

            <p className={styles.intro}>
              Not sure where to start? Have questions about
              universities, courses or studying abroad? Our team
              is here to help you understand your options and
              take the next step with confidence.
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
              onSubmit={handleSubmit}
            >
              <div className={styles.inputRow}>
                <div className={styles.inputGroup}>
                  <label
                    htmlFor={
                      compact ? "home-name" : "contact-name"
                    }
                  >
                    FULL NAME
                  </label>

                  <input
                    id={
                      compact ? "home-name" : "contact-name"
                    }
                    name="name"
                    type="text"
                    placeholder="Your full name"
                    autoComplete="name"
                    required
                    disabled={isSubmitting}
                  />

                  <span className={styles.inputLine}></span>
                </div>

                <div className={styles.inputGroup}>
                  <label
                    htmlFor={
                      compact ? "home-email" : "contact-email"
                    }
                  >
                    EMAIL ADDRESS
                  </label>

                  <input
                    id={
                      compact
                        ? "home-email"
                        : "contact-email"
                    }
                    name="email"
                    type="email"
                    placeholder="you@example.com"
                    autoComplete="email"
                    required
                    disabled={isSubmitting}
                  />

                  <span className={styles.inputLine}></span>
                </div>
              </div>

              <div className={styles.inputRow}>
                <div className={styles.inputGroup}>
                  <label
                    htmlFor={
                      compact ? "home-phone" : "contact-phone"
                    }
                  >
                    PHONE NUMBER
                  </label>

                  <input
                    id={
                      compact ? "home-phone" : "contact-phone"
                    }
                    name="phone"
                    type="tel"
                    placeholder="+92 000 0000000"
                    autoComplete="tel"
                    disabled={isSubmitting}
                  />

                  <span className={styles.inputLine}></span>
                </div>

                <div className={styles.inputGroup}>
                  <label
                    htmlFor={
                      compact
                        ? "home-subject"
                        : "contact-subject"
                    }
                  >
                    SUBJECT
                  </label>

                  <select
                    id={
                      compact
                        ? "home-subject"
                        : "contact-subject"
                    }
                    name="subject"
                    defaultValue=""
                    required
                    disabled={isSubmitting}
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
                <label
                  htmlFor={
                    compact
                      ? "home-message"
                      : "contact-message"
                  }
                >
                  YOUR MESSAGE
                </label>

                <textarea
                  id={
                    compact
                      ? "home-message"
                      : "contact-message"
                  }
                  name="message"
                  rows={compact ? 3 : 5}
                  placeholder="Tell us a little about what you are looking for..."
                  required
                  disabled={isSubmitting}
                ></textarea>

                <span className={styles.inputLine}></span>
              </div>

              {formStatus.message && (
                <div
                  role="alert"
                  className={
                    formStatus.type === "success"
                      ? styles.successMessage
                      : styles.errorMessage
                  }
                >
                  {formStatus.message}
                </div>
              )}

              <div className={styles.formBottom}>
                <p>
                  By submitting this form, you agree to be
                  contacted by our team regarding your enquiry.
                </p>

                <button
                  type="submit"
                  className={styles.submitButton}
                  disabled={isSubmitting}
                >
                  {isSubmitting
                    ? "SENDING..."
                    : "SEND MESSAGE"}

                  <span>↗</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  );
}