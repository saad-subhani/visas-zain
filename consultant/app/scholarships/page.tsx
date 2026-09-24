import type { Metadata } from "next";
import Link from "next/link";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import styles from "./page.module.css";

export const metadata: Metadata = {
  title: "Scholarships | Study Further",
  description:
    "Explore scholarship opportunities and funding options for international study.",
};

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

type ScholarshipsResponse = {
  success: boolean;
  data: Scholarship[];
  message?: string;
};

async function getScholarships(): Promise<Scholarship[]> {
  try {
    const response = await fetch(
      "http://localhost/realCMS/api/scholarships.php",
      {
        cache: "no-store",
      }
    );

    if (!response.ok) {
      return [];
    }

    const result: ScholarshipsResponse = await response.json();

    if (!result.success || !Array.isArray(result.data)) {
      return [];
    }

    return result.data;
  } catch {
    return [];
  }
}

export default async function ScholarshipsPage() {
  const scholarships = await getScholarships();

  const activeScholarships = scholarships.filter(
    (scholarship) =>
      scholarship.status.toLowerCase() === "active"
  );

  return (
    <>
      <Navbar />

      <main>
        {/* HERO */}
        <section className={styles.hero}>
          <div className={styles.heroOverlay}></div>

          <div className={styles.heroContent}>
            {/* BREADCRUMB */}
            <div className={styles.breadcrumb}>
              <Link href="/">HOME</Link>
              <span>/</span>
              <span>SCHOLARSHIPS</span>
            </div>

            <span className={styles.eyebrow}>
              FUND YOUR FUTURE
            </span>

            <h1>
              SCHOLARSHIPS
              <br />
              THAT OPEN DOORS.
            </h1>

            <p>
              Explore scholarship opportunities designed to help
              make your international education journey more
              achievable.
            </p>
          </div>
        </section>

        {/* SCHOLARSHIPS */}
        <section className={styles.scholarships}>
          <div className={styles.container}>
            <div className={styles.sectionIntro}>
              <div>
                <span className={styles.eyebrow}>
                  EXPLORE OPPORTUNITIES
                </span>

                <h2>
                  FIND FUNDING
                  <br />
                  FOR YOUR FUTURE.
                </h2>
              </div>

              <p>
                Browse available scholarship opportunities and
                understand their funding, eligibility requirements
                and application information.
              </p>
            </div>

            <div className={styles.scholarshipGrid}>
              {activeScholarships.map((scholarship, index) => (
                <article
                  className={styles.scholarshipCard}
                  key={scholarship.id}
                >
                  <div className={styles.cardTop}>
                    <span className={styles.number}>
                      {String(index + 1).padStart(2, "0")}
                    </span>

                    <span className={styles.status}>
                      ACTIVE
                    </span>
                  </div>

                  <h3>{scholarship.title}</h3>

                  <p className={styles.description}>
                    {scholarship.description}
                  </p>

                  <div className={styles.cardMeta}>
                    <div>
                      <span>FUNDING</span>
                      <strong>{scholarship.amount}</strong>
                    </div>

                    <div>
                      <span>DEADLINE</span>
                      <strong>{scholarship.deadline}</strong>
                    </div>

                    <div>
                      <span>STUDY LEVEL</span>
                      <strong>{scholarship.study_level}</strong>
                    </div>
                  </div>

                  <Link
                    href={`/scholarships/${scholarship.id}`}
                    className={styles.viewButton}
                  >
                    VIEW SCHOLARSHIP
                    <span>↗</span>
                  </Link>
                </article>
              ))}
            </div>
          </div>
        </section>

        {/* CTA */}
        <section className={styles.cta}>
          <div className={styles.container}>
            <div className={styles.ctaBox}>
              <span className={styles.eyebrow}>
                NEED GUIDANCE?
              </span>

              <h2>
                LET&apos;S FIND
                <br />
                YOUR OPPORTUNITY.
              </h2>

              <p>
                Not sure which scholarship you may be eligible
                for? Our team can help you understand your options
                and prepare for the application process.
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

      <Footer />
    </>
  );
}