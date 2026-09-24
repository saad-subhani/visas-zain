import type { Metadata } from "next";
import Link from "next/link";

import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";

import styles from "./page.module.css";

export const metadata: Metadata = {
  title: "Study Destinations | Consult",
  description:
    "Explore study abroad destinations and discover opportunities for your international education journey.",
};

/*
|--------------------------------------------------------------------------
| CMS API
|--------------------------------------------------------------------------
*/

const API_URL =
  "http://localhost/realCMS/api/destinations.php";

/*
|--------------------------------------------------------------------------
| CMS DESTINATION TYPE
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| FRONTEND DESTINATION TYPE
|--------------------------------------------------------------------------
| Existing frontend structure ko preserve karne ke liye
| CMS fields ko isi structure mein convert kar rahe hain.
|--------------------------------------------------------------------------
*/

type Destination = {
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

/*
|--------------------------------------------------------------------------
| NORMALIZE CMS DATA
|--------------------------------------------------------------------------
*/

function normalizeDestination(
  destination: ApiDestination
): Destination {
  return {
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

    image:
      destination.image_url ||
      "/images/services/pre-departure.jpg",

    status:
      destination.status?.toLowerCase() === "active"
        ? "Active"
        : "Inactive",
  };
}

/*
|--------------------------------------------------------------------------
| GET DESTINATIONS FROM CMS
|--------------------------------------------------------------------------
*/

async function getDestinations(): Promise<Destination[]> {
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
      .map(normalizeDestination)
      .filter(
        (destination: Destination) =>
          destination.status === "Active"
      );
  } catch {
    return [];
  }
}

export default async function StudyDestinationsPage() {
  const activeDestinations = await getDestinations();

  return (
    <>
      <Navbar />

      <main className={styles.page}>

        {/* =====================================================
            HERO
        ===================================================== */}

        <section className={styles.hero}>

          <div className={styles.heroBackground} />

          <div className={styles.heroOverlay} />

          <div className={styles.heroGlow} />

          <div className={styles.heroGrid} />

          <div className={styles.heroCircle} />

          <div className={styles.heroTop}>
            

            
          </div>

          <div className={styles.heroContent}>

            {/* BREADCRUMB */}
            <div className={styles.breadcrumb}>
              <Link href="/">
                HOME
              </Link>

              <span>/</span>

              <span>
                STUDY DESTINATIONS
              </span>
            </div>

            <div className={styles.kicker}>
              <span />
              
            </div>

            <h1>
              STUDY
              <br />
              <span>THE WORLD.</span>
            </h1>

            <p>
              Discover destinations where world-class
              education, international experience and
              new opportunities come together.
            </p>

          </div>

          <div className={styles.heroBottom}>

            <span>
             /
            </span>

            <span>
              SCROLL TO EXPLORE ↓
            </span>

          </div>

        </section>


        {/* =====================================================
            DESTINATIONS
        ===================================================== */}

        <section className={styles.destinationsSection}>

          <div className={styles.container}>

            <div className={styles.sectionHeader}>

              <div className={styles.sectionNumber}>
                01
              </div>

              <div className={styles.sectionLabel}>
                CHOOSE YOUR DESTINATION
              </div>

            </div>


            <div className={styles.intro}>

              <h2>
                WHERE WILL
                <br />
                YOU <span>GO?</span>
              </h2>

              <p>
                Every destination offers a different
                experience. Explore your options and
                find the pathway that fits your academic
                goals and future ambitions.
              </p>

            </div>


            <div className={styles.destinationGrid}>

              {activeDestinations.map(
                (destination, index) => (
                  <a
                    href={`/study-destinations/${destination.slug}`}
                    className={styles.destinationCard}
                    key={destination.slug}
                  >

                    <div
                      className={styles.cardImage}
                      style={{
                        backgroundImage: `url("${destination.image}")`,
                      }}
                    />

                    <div className={styles.cardOverlay} />


                    <div className={styles.cardTop}>

                      <span>
                        {String(index + 1).padStart(
                          2,
                          "0"
                        )}
                      </span>

                      <span>
                        EXPLORE ↗
                      </span>

                    </div>


                    <div className={styles.cardContent}>

                      <div className={styles.cardLine} />

                      <h3>
                        {destination.countryName}
                      </h3>

                      <p>
                        {destination.description}
                      </p>

                      <span
                        className={styles.cardLink}
                      >
                        DISCOVER DESTINATION
                        <b>↗</b>
                      </span>

                    </div>

                  </a>
                )
              )}

            </div>

          </div>

        </section>


        {/* =====================================================
            FINAL CTA
        ===================================================== */}

        <section className={styles.cta}>

          <div className={styles.ctaGlow} />

          <div className={styles.ctaCircle} />

          <div className={styles.ctaContent}>

            <div className={styles.ctaLabel}>
              YOUR JOURNEY STARTS HERE
            </div>

            <h2>
              FIND YOUR
              <br />
              <span>PLACE.</span>
            </h2>

            <p>
              Not sure which destination is right
              for you? Let&apos;s explore your options
              together.
            </p>

            <a
              href="#contact"
              className={styles.ctaButton}
            >
              START YOUR JOURNEY
              <span>↗</span>
            </a>

          </div>

        </section>

      </main>

      <Footer />
    </>
  );
}