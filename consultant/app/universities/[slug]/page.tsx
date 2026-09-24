import type { Metadata } from "next";
import Link from "next/link";

import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";

import styles from "./page.module.css";

type PageProps = {
  params: Promise<{
    slug: string;
  }>;
};

const UNIVERSITIES_API_URL =
  "http://localhost/realCMS/api/universities/index.php";

type ApiUniversity = {
  id: number;
  name: string;
  slug: string;
  country: string;
  city: string;
  description: string;
  programmes: string | string[] | null;
  tuition_fee: string | null;
  intake_dates: string | null;
  requirements: string | null;
  english_requirements: string | null;
  scholarships_available: string | boolean | null;
  official_url: string | null;
  image_url: string | null;
  status: string;
  created_at: string;
  updated_at?: string;
};

type ApiResponse = {
  success: boolean;
  count: number;
  data: ApiUniversity[];
};

type University = {
  id: number;
  universityName: string;
  slug: string;
  country: string;
  city: string;
  description: string;
  programmes: string[];
  tuitionFee: string;
  intakeDates: string;
  generalRequirements: string;
  englishRequirements: string;
  scholarshipsAvailable: boolean;
  officialWebsite: string;
  image: string;
  status: "Active" | "Inactive";
};

function parseProgrammes(
  programmes: string | string[] | null
): string[] {
  if (!programmes) {
    return [];
  }

  if (Array.isArray(programmes)) {
    return programmes.filter(Boolean);
  }

  const value = programmes.trim();

  if (!value) {
    return [];
  }

  /*
   * If programmes are stored as JSON:
   * ["Business", "Computer Science"]
   */
  try {
    const parsed = JSON.parse(value);

    if (Array.isArray(parsed)) {
      return parsed.filter(
        (item): item is string =>
          typeof item === "string" && item.trim().length > 0
      );
    }
  } catch {
    // Continue with normal text parsing.
  }

  /*
   * Also support comma-separated or line-separated values.
   */
  return value
    .split(/\r?\n|,/)
    .map((item) => item.trim())
    .filter(Boolean);
}

function normalizeUniversity(
  university: ApiUniversity
): University {
  const scholarshipValue = String(
    university.scholarships_available ?? ""
  ).toLowerCase();

  return {
    id: university.id,
    universityName: university.name,
    slug: university.slug,
    country: university.country,
    city: university.city,
    description: university.description,
    programmes: parseProgrammes(university.programmes),
    tuitionFee: university.tuition_fee || "Contact university",
    intakeDates: university.intake_dates || "Check university",
    generalRequirements:
      university.requirements || "Please contact us for admission requirements.",
    englishRequirements:
      university.english_requirements ||
      "Please contact us for English language requirements.",
    scholarshipsAvailable:
      scholarshipValue === "yes" ||
      scholarshipValue === "true" ||
      scholarshipValue === "1",
    officialWebsite: university.official_url || "",
    image:
      university.image_url ||
      "/images/services/university-selection.jpg",
    status:
      university.status?.toLowerCase() === "active"
        ? "Active"
        : "Inactive",
  };
}

async function getUniversities(): Promise<University[]> {
  try {
    const response = await fetch(UNIVERSITIES_API_URL, {
      cache: "no-store",
    });

    if (!response.ok) {
      return [];
    }

    const result: ApiResponse = await response.json();

    if (!result.success || !Array.isArray(result.data)) {
      return [];
    }

    return result.data.map(normalizeUniversity);
  } catch {
    return [];
  }
}

async function getUniversityBySlug(
  slug: string
): Promise<University | null> {
  const universities = await getUniversities();

  const university = universities.find(
    (item) =>
      item.slug === slug &&
      item.status === "Active"
  );

  return university || null;
}

export async function generateMetadata({
  params,
}: PageProps): Promise<Metadata> {
  const { slug } = await params;

  const university = await getUniversityBySlug(slug);

  if (!university) {
    return {
      title: "University Not Found",
    };
  }

  return {
    title: `${university.universityName} | Study Further`,
    description: university.description,
  };
}

export default async function UniversityDetailPage({
  params,
}: PageProps) {
  const { slug } = await params;

  const university = await getUniversityBySlug(slug);

  if (!university) {
    return (
      <>
        <Navbar />

        <main className={styles.notFound}>
          <span>UNIVERSITY</span>

          <h1>UNIVERSITY NOT FOUND.</h1>

          <p>
            The university you are looking for could not be found.
          </p>

          <Link
            href="/universities"
            className={styles.backButton}
          >
            BACK TO UNIVERSITIES
          </Link>
        </main>

        <Footer />
      </>
    );
  }

  return (
    <>
      <Navbar />

      <main>

        {/* HERO */}

        <section className={styles.hero}>

          <div className={styles.heroGlow}></div>

          <div className={styles.container}>

            <Link
              href="/universities"
              className={styles.backLink}
            >
              ← ALL UNIVERSITIES
            </Link>

            <div className={styles.heroContent}>

              <div className={styles.heroText}>

                <span className={styles.eyebrow}>
                  {university.country} · {university.city}
                </span>

                <h1>
                  {university.universityName}
                </h1>

                <p>
                  {university.description}
                </p>

                <div className={styles.heroActions}>

                  <Link
                    href="/#contact"
                    className={styles.primaryButton}
                  >
                    GET GUIDANCE
                    <span>↗</span>
                  </Link>

                  {university.officialWebsite && (
                    <a
                      href={university.officialWebsite}
                      target="_blank"
                      rel="noopener noreferrer"
                      className={styles.secondaryButton}
                    >
                      OFFICIAL WEBSITE
                      <span>↗</span>
                    </a>
                  )}

                </div>

              </div>


              <div className={styles.heroImage}>

                <img
                  src={university.image}
                  alt={university.universityName}
                />

                <div className={styles.imageLabel}>

                  <span>LOCATION</span>

                  <strong>
                    {university.city}, {university.country}
                  </strong>

                </div>

              </div>

            </div>

          </div>

        </section>


        {/* OVERVIEW */}

        <section className={styles.overview}>

          <div className={styles.container}>

            <div className={styles.sectionIntro}>

              <span className={styles.eyebrow}>
                UNIVERSITY OVERVIEW
              </span>

              <h2>
                EVERYTHING YOU
                <br />
                NEED TO KNOW.
              </h2>

            </div>


            <div className={styles.infoGrid}>

              <div className={styles.infoCard}>
                <span>LOCATION</span>

                <h3>
                  {university.city}
                </h3>

                <p>
                  {university.country}
                </p>
              </div>


              <div className={styles.infoCard}>
                <span>TUITION FEE</span>

                <h3>
                  {university.tuitionFee}
                </h3>

                <p>
                  Estimated annual tuition
                </p>
              </div>


              <div className={styles.infoCard}>
                <span>INTAKES</span>

                <h3>
                  {university.intakeDates}
                </h3>

                <p>
                  Available intake periods
                </p>
              </div>


              <div className={styles.infoCard}>
                <span>SCHOLARSHIPS</span>

                <h3>
                  {university.scholarshipsAvailable
                    ? "AVAILABLE"
                    : "NOT LISTED"}
                </h3>

                <p>
                  Scholarship opportunities may vary by programme
                </p>
              </div>

            </div>

          </div>

        </section>


        {/* PROGRAMMES */}

        <section className={styles.programmes}>

          <div className={styles.container}>

            <div className={styles.sectionHeader}>

              <div>

                <span className={styles.eyebrow}>
                  STUDY OPTIONS
                </span>

                <h2>
                  AVAILABLE PROGRAMMES.
                </h2>

              </div>

              <p>
                Explore some of the study areas available at{" "}
                {university.universityName}.
              </p>

            </div>


            <div className={styles.programmeGrid}>

              {university.programmes.map(
                (programme, index) => (

                  <div
                    className={styles.programmeCard}
                    key={`${programme}-${index}`}
                  >

                    <span>
                      {String(index + 1).padStart(2, "0")}
                    </span>

                    <h3>
                      {programme}
                    </h3>

                    <div>
                      EXPLORE PROGRAMME ↗
                    </div>

                  </div>

                )
              )}

            </div>

          </div>

        </section>


        {/* REQUIREMENTS */}

        <section className={styles.requirements}>

          <div className={styles.container}>

            <div className={styles.requirementGrid}>

              <div className={styles.requirementIntro}>

                <span className={styles.eyebrow}>
                  ADMISSION
                </span>

                <h2>
                  WHAT DO YOU
                  <br />
                  NEED?
                </h2>

                <p>
                  Entry requirements can vary depending on the
                  programme and level of study.
                </p>

              </div>


              <div className={styles.requirementCards}>

                <article
                  className={styles.requirementCard}
                >

                  <span>01</span>

                  <div>

                    <h3>
                      GENERAL REQUIREMENTS
                    </h3>

                    <p>
                      {university.generalRequirements}
                    </p>

                  </div>

                </article>


                <article
                  className={styles.requirementCard}
                >

                  <span>02</span>

                  <div>

                    <h3>
                      ENGLISH LANGUAGE
                    </h3>

                    <p>
                      {university.englishRequirements}
                    </p>

                  </div>

                </article>


                <article
                  className={styles.requirementCard}
                >

                  <span>03</span>

                  <div>

                    <h3>
                      SCHOLARSHIP OPPORTUNITIES
                    </h3>

                    <p>
                      {university.scholarshipsAvailable
                        ? "Scholarship opportunities are available. Eligibility and application requirements may vary."
                        : "No scholarship information is currently listed for this university."}
                    </p>

                  </div>

                </article>

              </div>

            </div>

          </div>

        </section>


        {/* CTA */}

        <section className={styles.cta}>

          <div className={styles.container}>

            <div className={styles.ctaBox}>

              <span className={styles.eyebrow}>
                READY WHEN YOU ARE
              </span>

              <h2>
                START YOUR
                <br />
                APPLICATION JOURNEY.
              </h2>

              <p>
                Interested in studying at{" "}
                {university.universityName}?
                Get personalised guidance on programmes,
                requirements and your next steps.
              </p>

              <Link
                href="/#contact"
                className={styles.ctaButton}
              >
                START YOUR JOURNEY
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