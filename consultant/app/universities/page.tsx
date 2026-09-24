import type { Metadata } from "next";
import Link from "next/link";

import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";

import styles from "./page.module.css";

export const metadata: Metadata = {
  title: "Universities | Study Further, Go Further",
  description:
    "Explore universities and discover study opportunities across leading destinations around the world.",
};

/*
|--------------------------------------------------------------------------
| CMS API
|--------------------------------------------------------------------------
*/

const API_URL =
  "http://localhost/realCMS/api/universities/index.php";

/*
|--------------------------------------------------------------------------
| CMS UNIVERSITY TYPE
|--------------------------------------------------------------------------
*/

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
  scholarships_available: string | boolean | number | null;
  official_url: string | null;
  image_url: string | null;
  status: string | null;
};

/*
|--------------------------------------------------------------------------
| FRONTEND UNIVERSITY TYPE
|--------------------------------------------------------------------------
| Ye tumhare existing universities data ke structure jaisa hi hai.
|--------------------------------------------------------------------------
*/

type University = {
  universityName: string;
  slug: string;
  country: string;
  city: string;
  description: string;
  programmes: string[];
  tuitionFee: string;
  intakeDates: string;
  scholarshipsAvailable: boolean;
  officialWebsite: string;
  image: string;
  status: "Active" | "Inactive";
};

/*
|--------------------------------------------------------------------------
| PROGRAMMES
|--------------------------------------------------------------------------
*/

function parseProgrammes(
  value: string | string[] | null
): string[] {
  if (!value) {
    return [];
  }

  if (Array.isArray(value)) {
    return value.filter(Boolean);
  }

  const trimmedValue = value.trim();

  if (!trimmedValue) {
    return [];
  }

  /*
  |--------------------------------------------------------------------------
  | JSON ARRAY
  |--------------------------------------------------------------------------
  */

  try {
    const parsed = JSON.parse(trimmedValue);

    if (Array.isArray(parsed)) {
      return parsed
        .filter(Boolean)
        .map(String);
    }
  } catch {
    // Normal text value hai, neeche handle hoga.
  }

  /*
  |--------------------------------------------------------------------------
  | COMMA / NEW LINE / PIPE SEPARATED
  |--------------------------------------------------------------------------
  */

  return trimmedValue
    .split(/[,|\n]+/)
    .map((item) => item.trim())
    .filter(Boolean);
}

/*
|--------------------------------------------------------------------------
| CONVERT CMS DATA TO EXISTING FRONTEND STRUCTURE
|--------------------------------------------------------------------------
*/

function normalizeUniversity(
  university: ApiUniversity
): University {
  const scholarshipValue = String(
    university.scholarships_available ?? ""
  ).toLowerCase();

  return {
    universityName:
      university.name || "University",

    slug:
      university.slug || "",

    country:
      university.country || "",

    city:
      university.city || "",

    description:
      university.description || "",

    programmes:
      parseProgrammes(university.programmes),

    tuitionFee:
      university.tuition_fee || "Contact university",

    intakeDates:
      university.intake_dates || "Check university",

    scholarshipsAvailable: [
      "yes",
      "true",
      "1",
      "on",
      "available",
    ].includes(scholarshipValue),

    officialWebsite:
      university.official_url || "",

    image:
      university.image_url ||
      "/images/services/university-selection.jpg",

    status:
      university.status?.toLowerCase() === "active"
        ? "Active"
        : "Inactive",
  };
}

/*
|--------------------------------------------------------------------------
| GET UNIVERSITIES FROM CMS
|--------------------------------------------------------------------------
*/

async function getUniversities(): Promise<University[]> {
  try {
    const response = await fetch(API_URL, {
      cache: "no-store",
    });

    if (!response.ok) {
      return [];
    }

    const result = await response.json();

    if (
      !result.success ||
      !Array.isArray(result.data)
    ) {
      return [];
    }

    return result.data
      .map(normalizeUniversity)
      .filter(
        (university: University) =>
          university.status === "Active"
      );
  } catch {
    return [];
  }
}

/*
|--------------------------------------------------------------------------
| PAGE
|--------------------------------------------------------------------------
*/

export default async function UniversitiesPage() {
  const activeUniversities = await getUniversities();

  return (
    <>
      <Navbar />

      <main>

        {/* =====================================================
            HERO
        ===================================================== */}

        <section className={styles.hero}>

          <div className={styles.heroGlow} />

          <div className={styles.heroCircle} />

          <div className={styles.heroContent}>

            {/* BREADCRUMB */}
            <div className={styles.breadcrumb}>
              <Link href="/">
                HOME
              </Link>

              <span>/</span>

              <span>
                UNIVERSITIES
              </span>
            </div>

            <div className={styles.heroLabel}>
              <span />
              GLOBAL UNIVERSITIES
            </div>

            <h1>
              FIND YOUR
              <br />
              <span>UNIVERSITY.</span>
            </h1>

            <p>
              Explore leading universities, discover the right
              programmes and find an academic path that matches
              your ambitions.
            </p>

          </div>

        </section>


        {/* =====================================================
            UNIVERSITIES
        ===================================================== */}

        <section className={styles.universitiesSection}>

          <div className={styles.sectionHeader}>

            <div>
              <div className={styles.sectionLabel}>
                EXPLORE YOUR OPTIONS
              </div>

              <h2>
                WHERE WILL
                <br />
                <span>YOU STUDY?</span>
              </h2>
            </div>

            <p>
              Browse our selection of universities and explore
              programmes, tuition fees, intakes and scholarship
              opportunities.
            </p>

          </div>


          {/* =================================================
              UNIVERSITY GRID
          ================================================= */}

          <div className={styles.universityGrid}>

            {activeUniversities.map((university, index) => (

              <article
                key={university.slug}
                className={styles.universityCard}
              >

                <div className={styles.cardImage}>

                  <img
                    src={university.image}
                    alt={university.universityName}
                  />

                  <div className={styles.cardNumber}>
                    {String(index + 1).padStart(2, "0")}
                  </div>

                  {university.scholarshipsAvailable && (
                    <div className={styles.scholarshipBadge}>
                      SCHOLARSHIPS
                    </div>
                  )}

                </div>


                <div className={styles.cardContent}>

                  <div className={styles.location}>
                    {university.city}
                    <span>•</span>
                    {university.country}
                  </div>

                  <h3>
                    {university.universityName}
                  </h3>

                  <p>
                    {university.description}
                  </p>


                  <div className={styles.cardMeta}>

                    <div>
                      <span>PROGRAMMES</span>
                      <strong>
                        {university.programmes.length}+
                      </strong>
                    </div>

                    <div>
                      <span>TUITION</span>
                      <strong>
                        {university.tuitionFee}
                      </strong>
                    </div>

                    <div>
                      <span>INTAKES</span>
                      <strong>
                        {university.intakeDates}
                      </strong>
                    </div>

                  </div>


                  <Link
                    href={`/universities/${university.slug}`}
                    className={styles.viewButton}
                  >
                    VIEW UNIVERSITY
                    <span>↗</span>
                  </Link>

                </div>

              </article>

            ))}

          </div>

        </section>


        {/* =====================================================
            CTA
        ===================================================== */}

        <section className={styles.ctaSection}>

          <div className={styles.ctaGlow} />
          <div className={styles.ctaCircle} />

          <div className={styles.ctaContainer}>

            <div className={styles.ctaLabel}>
              READY WHEN YOU ARE
            </div>

            <h2>
              YOUR FUTURE
              <br />
              <span>STARTS HERE.</span>
            </h2>

            <p>
              Not sure which university or programme is right
              for you? Let&apos;s explore your options together.
            </p>

            <Link
              href="/#contact"
              className={styles.ctaButton}
            >
              START YOUR JOURNEY
              <span>↗</span>
            </Link>

          </div>

        </section>

      </main>

      <Footer />
    </>
  );
}