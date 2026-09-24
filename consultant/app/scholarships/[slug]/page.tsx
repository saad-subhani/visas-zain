import Link from "next/link";
import { notFound } from "next/navigation";
import type { Metadata } from "next";

import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";

import styles from "./page.module.css";

type Scholarship = {
  id: number;
  title: string;
  description: string;
  amount: string;
  deadline: string;
  eligible_countries: string;
  study_level: string;
  requirements: string;
  official_source: string;
  how_to_apply: string;
  status: string;
  created_at: string;
};

type ScholarshipResponse = {
  success: boolean;
  data: Scholarship;
  message?: string;
};

async function getScholarship(
  id: string
): Promise<Scholarship | null> {
  try {
    const response = await fetch(
      `http://localhost/realCMS/api/scholarships-get.php?id=${encodeURIComponent(
        id
      )}`,
      {
        cache: "no-store",
      }
    );

    if (!response.ok) {
      return null;
    }

    const result: ScholarshipResponse = await response.json();

    if (!result.success || !result.data) {
      return null;
    }

    return result.data;
  } catch {
    return null;
  }
}

type Props = {
  params: Promise<{
    slug: string;
  }>;
};

export async function generateMetadata({
  params,
}: Props): Promise<Metadata> {
  const { slug } = await params;

  const scholarship = await getScholarship(slug);

  if (!scholarship) {
    return {
      title: "Scholarship Not Found | Consultant",
    };
  }

  return {
    title: `${scholarship.title} | Consultant`,
    description: scholarship.description,
  };
}

export default async function ScholarshipDetailPage({
  params,
}: Props) {
  const { slug } = await params;

  const scholarship = await getScholarship(slug);

  if (!scholarship) {
    notFound();
  }

  const eligibleCountries = scholarship.eligible_countries
    ? scholarship.eligible_countries
        .split(",")
        .map((country) => country.trim())
        .filter(Boolean)
    : [];

  return (
    <>
      {/* HEADER */}
      <Navbar />

      <main className={styles.page}>
        {/* HERO */}
        <section className={styles.hero}>
          <div className={styles.heroOverlay}></div>

          <div className={styles.heroContent}>
            <span className={styles.eyebrow}>
              SCHOLARSHIP OPPORTUNITY
            </span>

            <h1>{scholarship.title}</h1>

            <p>{scholarship.description}</p>

            <Link
              href="#details"
              className={styles.heroButton}
            >
              EXPLORE DETAILS
              <span>↓</span>
            </Link>
          </div>
        </section>

        {/* DETAILS */}
        <section
          className={styles.details}
          id="details"
        >
          <div className={styles.container}>
            <div className={styles.sectionIntro}>
              <div>
                <span className={styles.sectionEyebrow}>
                  SCHOLARSHIP DETAILS
                </span>

                <h2>
                  FUND YOUR
                  <br />
                  FUTURE.
                </h2>
              </div>

              <p>
                Explore the funding, eligibility and
                application information for this
                scholarship opportunity.
              </p>
            </div>

            {/* INFO CARDS */}
            <div className={styles.infoGrid}>
              <div className={styles.infoCard}>
                <span>FUNDING</span>

                <strong>
                  {scholarship.amount}
                </strong>
              </div>

              <div className={styles.infoCard}>
                <span>APPLICATION DEADLINE</span>

                <strong>
                  {scholarship.deadline}
                </strong>
              </div>

              <div className={styles.infoCard}>
                <span>STUDY LEVEL</span>

                <strong>
                  {scholarship.study_level}
                </strong>
              </div>

              <div className={styles.infoCard}>
                <span>STATUS</span>

                <strong className={styles.active}>
                  {scholarship.status}
                </strong>
              </div>
            </div>

            {/* ELIGIBILITY */}
            <div className={styles.contentGrid}>
              <div className={styles.contentBlock}>
                <span className={styles.blockEyebrow}>
                  ELIGIBILITY
                </span>

                <h3>
                  WHO CAN
                  <br />
                  APPLY?
                </h3>

                <p>
                  This scholarship is available to
                  applicants from the following countries:
                </p>

                <div className={styles.countryList}>
                  {eligibleCountries.map(
                    (country) => (
                      <span key={country}>
                        {country}
                      </span>
                    )
                  )}
                </div>
              </div>

              <div className={styles.contentBlock}>
                <span className={styles.blockEyebrow}>
                  REQUIREMENTS
                </span>

                <h3>
                  WHAT YOU
                  <br />
                  NEED.
                </h3>

                <p>
                  {scholarship.requirements}
                </p>
              </div>
            </div>

            {/* HOW TO APPLY */}
            <div className={styles.application}>
              <div className={styles.applicationContent}>
                <span className={styles.blockEyebrow}>
                  APPLICATION
                </span>

                <h3>
                  HOW TO
                  <br />
                  APPLY.
                </h3>

                <p>
                  {scholarship.how_to_apply}
                </p>
              </div>

              <div className={styles.sourceBox}>
                <span>OFFICIAL SOURCE</span>

                <a
                  href={scholarship.official_source}
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  VISIT OFFICIAL SOURCE
                  <span>↗</span>
                </a>
              </div>
            </div>

            {/* CTA */}
            <div className={styles.cta}>
              <span className={styles.ctaEyebrow}>
                NEED GUIDANCE?
              </span>

              <h2>
                LET&apos;S FIND YOUR
                <br />
                OPPORTUNITY.
              </h2>

              <p>
                Not sure where to start? Get guidance
                on scholarships, applications and your
                study-abroad journey.
              </p>

              <Link
                href="/#contact"
                className={styles.ctaButton}
              >
                GET GUIDANCE
                <span>↗</span>
              </Link>
            </div>
          </div>
        </section>
      </main>

      {/* FOOTER */}
      <Footer />
    </>
  );
}