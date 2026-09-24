import type { Metadata } from "next";
import { notFound } from "next/navigation";

import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";

import styles from "./page.module.css";

type PageProps = {
  params: Promise<{
    slug: string;
  }>;
};

type ApiDestination = {
  id: number;
  country_name: string;
  slug: string;
  description: string;
  why_study: string;
  tuition_range: string;
  living_cost: string;
  visa_info: string;
  image_url: string | null;
  status: string;
  created_at?: string;
};

type Destination = {
  id: number;
  countryName: string;
  slug: string;
  description: string;
  whyStudy: string;
  tuitionRange: string;
  livingCost: string;
  visaInformation: string;
  image: string;
  status: "Active" | "Inactive";
};

const API_LIST_URL =
  "http://localhost/realCMS/api/destinations.php";

const API_DETAIL_URL =
  "http://localhost/realCMS/api/destinations-get.php";


function getImageUrl(
  imageUrl: string | null
): string {
  if (!imageUrl) {
    return "/images/services/pre-departure.jpg";
  }

  // Agar already complete URL hai
  if (
    imageUrl.startsWith("http://") ||
    imageUrl.startsWith("https://")
  ) {
    return imageUrl;
  }

  // CMS uploads path
  if (imageUrl.startsWith("/realCMS/")) {
    return `http://localhost${imageUrl}`;
  }

  if (imageUrl.startsWith("realCMS/")) {
    return `http://localhost/${imageUrl}`;
  }

  if (imageUrl.startsWith("/uploads/")) {
    return `http://localhost/realCMS${imageUrl}`;
  }

  if (imageUrl.startsWith("uploads/")) {
    return `http://localhost/realCMS/${imageUrl}`;
  }

  return `http://localhost/realCMS/${imageUrl.replace(/^\/+/, "")}`;
}




function normalizeDestination(
  destination: ApiDestination
): Destination {
  return {
    id: destination.id,
    countryName:
      destination.country_name || "Destination",
    slug:
      destination.slug || "",
    description:
      destination.description || "",
    whyStudy:
      destination.why_study || "",
    tuitionRange:
      destination.tuition_range || "",
    livingCost:
      destination.living_cost || "",
    visaInformation:
      destination.visa_info || "",
    image: getImageUrl(destination.image_url),
    status:
      destination.status?.toLowerCase() === "active"
        ? "Active"
        : "Inactive",
  };
}

async function getDestinations(): Promise<Destination[]> {
  try {
    const response = await fetch(API_LIST_URL, {
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

    return result.data.map(normalizeDestination);
  } catch {
    return [];
  }
}

async function getDestination(
  slug: string
): Promise<Destination | null> {
  try {
    const response = await fetch(
      `${API_DETAIL_URL}?slug=${encodeURIComponent(slug)}`,
      {
        cache: "no-store",
      }
    );

    if (!response.ok) {
      return null;
    }

    const result = await response.json();

    if (!result.success || !result.data) {
      return null;
    }

    const destination = normalizeDestination(
      result.data
    );

    if (destination.status !== "Active") {
      return null;
    }

    return destination;
  } catch {
    return null;
  }
}

export async function generateStaticParams() {
  const destinations = await getDestinations();

  return destinations
    .filter(
      (destination) =>
        destination.status === "Active"
    )
    .map((destination) => ({
      slug: destination.slug,
    }));
}

export async function generateMetadata({
  params,
}: PageProps): Promise<Metadata> {
  const { slug } = await params;

  const destination = await getDestination(slug);

  if (!destination) {
    return {
      title: "Destination Not Found",
    };
  }

  return {
    title: `Study in ${destination.countryName} | Consult`,
    description: destination.description,
  };
}

export default async function DestinationPage({
  params,
}: PageProps) {
  const { slug } = await params;

  const [destination, destinations] = await Promise.all([
    getDestination(slug),
    getDestinations(),
  ]);

  if (!destination) {
    notFound();
  }

  const activeDestinations = destinations.filter(
    (item) => item.status === "Active"
  );

  const destinationIndex =
    activeDestinations.findIndex(
      (item) => item.slug === slug
    );

  const destinationNumber = String(
    destinationIndex >= 0
      ? destinationIndex + 1
      : 1
  ).padStart(2, "0");

  return (
    <>
      <Navbar />

      <main className={styles.page}>

        {/* =====================================================
            HERO
        ===================================================== */}

        <section className={styles.hero}>

          <div className={styles.heroImage}>
            <div
              className={styles.heroImageBackground}
              style={{
                backgroundImage: `url("${destination.image}")`,
              }}
            />
          </div>

          <div className={styles.heroOverlay} />

          <div className={styles.heroGlow} />
          <div className={styles.heroGrid} />

          <div className={styles.heroCircleOne} />
          <div className={styles.heroCircleTwo} />

          <div className={styles.heroTopLine}>
            <span>
              STUDY DESTINATIONS
            </span>

            <span>
              / {destinationNumber}
            </span>
          </div>

          <div className={styles.heroContent}>

            <div className={styles.heroKicker}>
              <span />
              YOUR NEXT CHAPTER STARTS HERE
            </div>

            <h1>
              STUDY IN
              <strong>
                {destination.countryName}
              </strong>
            </h1>

            <div className={styles.heroBottom}>

              <p>
                {destination.description}
              </p>

              <a
                href="#destination-info"
                className={styles.heroButton}
              >
                EXPLORE DESTINATION

                <span>
                  ↓
                </span>
              </a>

            </div>
          </div>

          <div className={styles.heroMeta}>
            <span>
              EDUCATION
            </span>

            <span>
              EXPERIENCE
            </span>

            <span>
              OPPORTUNITY
            </span>
          </div>

          <div className={styles.heroScroll}>
            <span
              className={styles.scrollLine}
            />

            SCROLL TO EXPLORE
          </div>

        </section>


        {/* =====================================================
            WHY STUDY
        ===================================================== */}

        <section
          className={styles.whySection}
          id="destination-info"
        >
          <div className={styles.sectionContainer}>

            <div className={styles.sectionHeader}>

              <div className={styles.sectionNumber}>
                01
              </div>

              <div className={styles.sectionLabel}>
                WHY STUDY HERE?
              </div>

            </div>


            <div className={styles.whyLayout}>

              <div className={styles.whyTitle}>

                <span>
                  DISCOVER
                </span>

                <strong>
                  {destination.countryName}
                </strong>

              </div>


              <div className={styles.whyContent}>

                <div className={styles.accentLine} />

                <p>
                  {destination.whyStudy}
                </p>

                <div className={styles.miniStatement}>

                  <span>
                    GLOBAL EDUCATION
                  </span>

                  <span>
                    / BUILD YOUR FUTURE
                  </span>

                </div>

              </div>

            </div>

          </div>
        </section>


        {/* =====================================================
            DESTINATION VISUAL
        ===================================================== */}

        <section className={styles.visualSection}>

          <div className={styles.visualContainer}>

            <div className={styles.visualImage}>

              <div
                className={
                  styles.visualImageBackground
                }
                style={{
                  backgroundImage: `url("${destination.image}")`,
                }}
              />

              <div className={styles.visualOverlay} />


              <div className={styles.visualTop}>

                <span>
                  {destination.countryName.toUpperCase()}
                </span>

                <span>
                  / DESTINATION {destinationNumber}
                </span>

              </div>


              <div className={styles.visualCenter}>

                <div className={styles.visualPlus}>
                  +
                </div>

                <span>
                  YOUR JOURNEY
                </span>

              </div>


              <div className={styles.visualBottom}>
                INTERNATIONAL
                <br />
                EDUCATION
              </div>

            </div>

          </div>

        </section>


        {/* =====================================================
            COSTS & VISA
        ===================================================== */}

        <section className={styles.infoSection}>

          <div className={styles.sectionContainer}>

            <div className={styles.sectionHeader}>

              <div className={styles.sectionNumber}>
                02
              </div>

              <div className={styles.sectionLabel}>
                COSTS & VISA
              </div>

            </div>


            <div className={styles.infoIntro}>

              <h2>
                PLAN YOUR
                <span>
                  JOURNEY.
                </span>
              </h2>

              <p>
                Understanding the practical side
                of studying abroad helps you prepare
                with confidence.
              </p>

            </div>


            <div className={styles.infoGrid}>

              {/* TUITION */}

              <article className={styles.infoCard}>

                <div className={styles.cardTop}>

                  <span>
                    01
                  </span>

                  <span className={styles.cardArrow}>
                    ↗
                  </span>

                </div>

                <div className={styles.cardIcon}>
                  $
                </div>

                <div className={styles.cardContent}>

                  <span className={styles.cardLabel}>
                    TUITION RANGE
                  </span>

                  <h3>
                    {destination.tuitionRange}
                  </h3>

                  <p>
                    Estimated annual tuition range.
                    Actual costs vary by institution,
                    program and level of study.
                  </p>

                </div>

              </article>


              {/* LIVING COST */}

              <article className={styles.infoCard}>

                <div className={styles.cardTop}>

                  <span>
                    02
                  </span>

                  <span className={styles.cardArrow}>
                    ↗
                  </span>

                </div>

                <div className={styles.cardIcon}>
                  ◉
                </div>

                <div className={styles.cardContent}>

                  <span className={styles.cardLabel}>
                    LIVING COST
                  </span>

                  <h3>
                    {destination.livingCost}
                  </h3>

                  <p>
                    Estimated annual living expenses,
                    including everyday student costs.
                  </p>

                </div>

              </article>


              {/* VISA */}

              <article
                className={`${styles.infoCard} ${styles.visaCard}`}
              >

                <div className={styles.cardTop}>

                  <span>
                    03
                  </span>

                  <span className={styles.cardArrow}>
                    ↗
                  </span>

                </div>

                <div className={styles.cardIcon}>
                  →
                </div>

                <div className={styles.cardContent}>

                  <span className={styles.cardLabel}>
                    VISA INFORMATION
                  </span>

                  <p className={styles.visaText}>
                    {destination.visaInformation}
                  </p>

                </div>

              </article>

            </div>

          </div>

        </section>


        {/* =====================================================
            JOURNEY
        ===================================================== */}

        <section className={styles.processSection}>

          <div className={styles.sectionContainer}>

            <div className={styles.sectionHeader}>

              <div className={styles.sectionNumber}>
                03
              </div>

              <div className={styles.sectionLabel}>
                YOUR JOURNEY
              </div>

            </div>


            <div className={styles.processHeading}>

              <h2>
                FROM
                <span>
                  DREAM
                </span>
                TO
                <span>
                  DESTINATION.
                </span>
              </h2>

            </div>


            <div className={styles.processGrid}>

              <div className={styles.processItem}>

                <span className={styles.processNumber}>
                  01
                </span>

                <div>

                  <h3>
                    DISCOVER
                  </h3>

                  <p>
                    Explore universities, courses
                    and opportunities that match
                    your goals.
                  </p>

                </div>

              </div>


              <div className={styles.processItem}>

                <span className={styles.processNumber}>
                  02
                </span>

                <div>

                  <h3>
                    APPLY
                  </h3>

                  <p>
                    Prepare your application and
                    move forward with structured
                    guidance.
                  </p>

                </div>

              </div>


              <div className={styles.processItem}>

                <span className={styles.processNumber}>
                  03
                </span>

                <div>

                  <h3>
                    PREPARE
                  </h3>

                  <p>
                    Get ready for your visa, travel
                    and transition into international
                    study.
                  </p>

                </div>

              </div>


              <div className={styles.processItem}>

                <span className={styles.processNumber}>
                  04
                </span>

                <div>

                  <h3>
                    ARRIVE
                  </h3>

                  <p>
                    Start your international education
                    journey with confidence.
                  </p>

                </div>

              </div>

            </div>

          </div>

        </section>


        {/* =====================================================
            FINAL CTA
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

              <span>
                STARTS HERE.
              </span>
            </h2>

            <p>
              Take the first step towards studying
              in {destination.countryName}. Get
              guidance tailored to your goals.
            </p>

            <a
              href="#contact"
              className={styles.ctaButton}
            >
              START YOUR JOURNEY

              <span>
                ↗
              </span>
            </a>

          </div>

        </section>

      </main>

      <Footer />
    </>
  );
}